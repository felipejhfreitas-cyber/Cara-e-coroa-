<?php
/**
 * ==========================================================
 * Configurações Globais e Sessões Nativas
 * Arquivo: config.php
 * ==========================================================
 */

// Inicia sessão nativa PHP se ainda não iniciada
if (session_status() === PHP_SESSION_NONE) {
    // Configurações de segurança da sessão
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    session_start();
}

require_once __DIR__ . '/bd.php';

// Identificador único da sessão para registros e auditoria
if (!isset($_SESSION['usuario_sessao_id'])) {
    $_SESSION['usuario_sessao_id'] = 'user_' . bin2hex(random_bytes(12));
}

// Contador de trocas de escolha na sessão (para a regra do modal de anúncio)
if (!isset($_SESSION['trocas_escolha_count'])) {
    $_SESSION['trocas_escolha_count'] = 0;
}

if (!isset($_SESSION['ultima_escolha'])) {
    $_SESSION['ultima_escolha'] = null;
}

// Nomes de cookies utilizados
define('COOKIE_HISTORICO', 'cc_historico_jogos');
define('COOKIE_PERSONALIZACAO', 'cc_personalizacao');
define('COOKIE_ESTATISTICAS', 'cc_estatisticas');
define('COOKIE_LOGS_AUDITORIA', 'cc_logs_auditoria');
define('COOKIE_TROCAS', 'cc_trocas_escolha');

/**
 * Lê e decodifica um cookie JSON seguro.
 */
function obterCookieJson(string $nome, $padrao = []) {
    if (isset($_COOKIE[$nome])) {
        $valor = json_decode($_COOKIE[$nome], true);
        if (json_last_error() === JSON_ERROR_NONE && $valor !== null) {
            return $valor;
        }
    }
    return $padrao;
}

/**
 * Grava um cookie JSON seguro na máquina local.
 */
function definirCookieJson(string $nome, $dados, int $dias = 30): void {
    $expira = time() + ($dias * 86400);
    $json = json_encode($dados, JSON_UNESCAPED_UNICODE);
    
    // Define cookie para toda a aplicação
    setcookie($nome, $json, [
        'expires' => $expira,
        'path' => '/',
        'samesite' => 'Lax'
    ]);
    $_COOKIE[$nome] = $json;
}

/**
 * Obtém as configurações atuais de personalização da moeda (Cookie + Banco de Dados).
 */
function obterPersonalizacaoMoeda(): array {
    $padrao = [
        'nome_cara' => 'Cara',
        'nome_coroa' => 'Coroa',
        'tema_cor' => 'dourado',
        'icone_cara' => '👑',
        'icone_coroa' => '⭐',
        'imagem_cara' => '',
        'imagem_coroa' => ''
    ];

    // Tenta carregar do Cookie local primeiro
    $doCookie = obterCookieJson(COOKIE_PERSONALIZACAO, null);
    if (!empty($doCookie) && is_array($doCookie)) {
        return array_merge($padrao, $doCookie);
    }

    // Tenta carregar do Banco de Dados via PDO
    $pdo = obterConexaoBD();
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("SELECT nome_cara, nome_coroa, tema_cor, icone_cara, icone_coroa, imagem_cara, imagem_coroa FROM personalizacao_moeda WHERE sessao_id = :sessao LIMIT 1");
            $stmt->execute([':sessao' => $_SESSION['usuario_sessao_id']]);
            $resultado = $stmt->fetch();
            if ($resultado) {
                $dados = array_merge($padrao, array_filter($resultado));
                definirCookieJson(COOKIE_PERSONALIZACAO, $dados);
                return $dados;
            }
        } catch (PDOException $e) {
            error_log("Erro ao buscar personalização: " . $e->getMessage());
        }
    }

    return $padrao;
}

/**
 * Registra logs de qualquer alteração feita por qualquer usuário para auditorias futuras.
 * Regra CORE: Mantém sempre logs de qualquer alteração para auditorias futuras.
 * Salva tanto no Banco de Dados MySQL via PDO quanto nos Cookies locais.
 */
function registrarAuditoria(string $tipoAcao, string $descricao, $dadosAnteriores = null, $dadosNovos = null): void {
    $sessaoId = $_SESSION['usuario_sessao_id'] ?? 'desconhecido';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconhecido';
    $agora = date('Y-m-d H:i:s');

    $novoLog = [
        'id' => uniqid('log_'),
        'sessao_id' => $sessaoId,
        'tipo_acao' => $tipoAcao,
        'descricao' => $descricao,
        'dados_anteriores' => $dadosAnteriores,
        'dados_novos' => $dadosNovos,
        'ip_origem' => $ip,
        'user_agent' => $userAgent,
        'criado_em' => $agora
    ];

    // 1. Salva nos cookies locais para recuperação na máquina local
    $logsAtuais = obterCookieJson(COOKIE_LOGS_AUDITORIA, []);
    array_unshift($logsAtuais, $novoLog);
    // Limita tamanho do cookie aos 50 logs mais recentes para não estourar cabeçalhos
    if (count($logsAtuais) > 50) {
        $logsAtuais = array_slice($logsAtuais, 0, 50);
    }
    definirCookieJson(COOKIE_LOGS_AUDITORIA, $logsAtuais);

    // 2. Salva no MySQL via PDO
    $pdo = obterConexaoBD();
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO logs_auditoria (sessao_id, tipo_acao, descricao, dados_anteriores, dados_novos, ip_origem, user_agent, criado_em)
                VALUES (:sessao, :tipo, :desc, :anteriores, :novos, :ip, :ua, :criado)
            ");
            $stmt->execute([
                ':sessao' => $sessaoId,
                ':tipo' => $tipoAcao,
                ':desc' => $descricao,
                ':anteriores' => $dadosAnteriores ? json_encode($dadosAnteriores, JSON_UNESCAPED_UNICODE) : null,
                ':novos' => $dadosNovos ? json_encode($dadosNovos, JSON_UNESCAPED_UNICODE) : null,
                ':ip' => $ip,
                ':ua' => substr($userAgent, 0, 500),
                ':criado' => $agora
            ]);
        } catch (PDOException $e) {
            error_log("Erro ao salvar log de auditoria no BD: " . $e->getMessage());
        }
    }
}

/**
 * Obtém todos os jogos (combinando Cookie e Banco de Dados).
 */
function obterTodosJogos(): array {
    $jogosCookie = obterCookieJson(COOKIE_HISTORICO, []);

    $pdo = obterConexaoBD();
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("
                SELECT id, sessao_id, escolha, resultado, acertou, criado_em 
                FROM historico_jogos 
                WHERE sessao_id = :sessao 
                ORDER BY id DESC 
                LIMIT 100
            ");
            $stmt->execute([':sessao' => $_SESSION['usuario_sessao_id']]);
            $jogosBD = $stmt->fetchAll();
            if (!empty($jogosBD)) {
                return $jogosBD;
            }
        } catch (PDOException $e) {
            error_log("Erro ao obter jogos do BD: " . $e->getMessage());
        }
    }

    return $jogosCookie;
}

/**
 * Calcula métricas de dashboard a partir do histórico de jogos.
 */
function calcularMetricasDashboard(array $jogos): array {
    $total = count($jogos);
    $vitorias = 0;
    $derrotas = 0;
    $totalCara = 0;
    $totalCoroa = 0;
    $streakAtual = 0;
    $maiorStreak = 0;
    $tempStreak = 0;

    // Invertemos para percorrer em ordem cronológica para streak
    $jogosCronologicos = array_reverse($jogos);

    foreach ($jogosCronologicos as $j) {
        $acertou = (bool)($j['acertou'] ?? false);
        $res = strtolower($j['resultado'] ?? '');

        if ($acertou) {
            $vitorias++;
            $tempStreak++;
            if ($tempStreak > $maiorStreak) {
                $maiorStreak = $tempStreak;
            }
        } else {
            $derrotas++;
            $tempStreak = 0;
        }

        if ($res === 'cara') {
            $totalCara++;
        } elseif ($res === 'coroa') {
            $totalCoroa++;
        }
    }

    // Streak atual considerando as jogadas mais recentes
    foreach ($jogos as $j) {
        if (!empty($j['acertou'])) {
            $streakAtual++;
        } else {
            break;
        }
    }

    $taxaAcerto = $total > 0 ? round(($vitorias / $total) * 100, 1) : 0;
    $porcentagemCara = $total > 0 ? round(($totalCara / $total) * 100, 1) : 0;
    $porcentagemCoroa = $total > 0 ? round(($totalCoroa / $total) * 100, 1) : 0;

    return [
        'total_jogos' => $total,
        'vitorias' => $vitorias,
        'derrotas' => $derrotas,
        'taxa_acerto' => $taxaAcerto,
        'total_cara' => $totalCara,
        'total_coroa' => $totalCoroa,
        'porcentagem_cara' => $porcentagemCara,
        'porcentagem_coroa' => $porcentagemCoroa,
        'streak_atual' => $streakAtual,
        'maior_streak' => $maiorStreak
    ];
}

/**
 * Envia resposta em formato JSON para requisições AJAX/Fetch
 */
function responderJson(array $dados, int $statusCode = 200): void {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    exit;
}

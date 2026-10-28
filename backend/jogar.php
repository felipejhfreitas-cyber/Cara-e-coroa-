<?php
/**
 * ==========================================================
 * Processamento da Rolagem da Moeda (Cara ou Coroa)
 * Arquivo: backend/jogar.php
 * ==========================================================
 */

require_once __DIR__ . '/../config.php';

// Garante método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderJson([
        'sucesso' => false,
        'mensagem' => 'Método inválido. Apenas requisições POST são permitidas.'
    ], 405);
}

// Recebe dados JSON ou POST normal
$corpoRequisicao = file_get_contents('php://input');
$dados = json_decode($corpoRequisicao, true);

$escolha = strtolower(trim($dados['escolha'] ?? $_POST['escolha'] ?? ''));

// Validação da escolha do usuário
if ($escolha !== 'cara' && $escolha !== 'coroa') {
    responderJson([
        'sucesso' => false,
        'mensagem' => 'Escolha inválida! Selecione "cara" ou "coroa".'
    ], 400);
}

// Sorteio criptograficamente seguro (0 = cara, 1 = coroa)
try {
    $sorteio = random_int(0, 1);
} catch (Exception $e) {
    $sorteio = mt_rand(0, 1);
}
$resultado = ($sorteio === 0) ? 'cara' : 'coroa';

// Regra CORE: Verificar se a escolha equivale ao resultado
$acertou = ($escolha === $resultado);

$agora = date('Y-m-d H:i:s');
$sessaoId = $_SESSION['usuario_sessao_id'];

$novoJogo = [
    'id' => time() . '_' . mt_rand(100, 999),
    'sessao_id' => $sessaoId,
    'escolha' => $escolha,
    'resultado' => $resultado,
    'acertou' => $acertou ? 1 : 0,
    'criado_em' => $agora
];

// 1. Salva nos cookies da máquina local (Recuperados por cookies conforme objetivo)
$historicoCookie = obterCookieJson(COOKIE_HISTORICO, []);
array_unshift($historicoCookie, $novoJogo);
// Limita cookie a 100 jogos para otimizar tamanho
if (count($historicoCookie) > 100) {
    $historicoCookie = array_slice($historicoCookie, 0, 100);
}
definirCookieJson(COOKIE_HISTORICO, $historicoCookie);

// 2. Salva no Banco de Dados MySQL via PDO com Prepared Statements
$pdo = obterConexaoBD();
$idBD = null;
if ($pdo) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO historico_jogos (sessao_id, escolha, resultado, acertou, criado_em)
            VALUES (:sessao, :escolha, :resultado, :acertou, :criado_em)
        ");
        $stmt->execute([
            ':sessao' => $sessaoId,
            ':escolha' => $escolha,
            ':resultado' => $resultado,
            ':acertou' => $acertou ? 1 : 0,
            ':criado_em' => $agora
        ]);
        $idBD = $pdo->lastInsertId();
        $novoJogo['id'] = (int)$idBD;
    } catch (PDOException $e) {
        error_log("Erro ao salvar jogo no MySQL: " . $e->getMessage());
    }
}

// 3. Regra de Negócio CORE: Registrar log de auditoria
$mensagemResultado = $acertou ? "Vitória: Jogador escolheu {$escolha} e caiu {$resultado}." : "Derrota: Jogador escolheu {$escolha} e caiu {$resultado}.";
registrarAuditoria(
    'JOGO_REALIZADO',
    $mensagemResultado,
    null,
    [
        'escolha' => $escolha,
        'resultado' => $resultado,
        'acertou' => $acertou,
        'jogo_id' => $novoJogo['id']
    ]
);

// Atualiza métricas para retornar ao frontend
$metricas = calcularMetricasDashboard($historicoCookie);

// Resposta JSON completa
responderJson([
    'sucesso' => true,
    'mensagem' => $acertou ? 'Parabéns, você acertou!' : 'Que pena, você errou desta vez!',
    'escolha' => $escolha,
    'resultado' => $resultado,
    'acertou' => $acertou,
    'jogo' => $novoJogo,
    'metricas' => $metricas,
    'bd_conectado' => ($pdo !== null)
]);

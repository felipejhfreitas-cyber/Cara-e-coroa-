<?php
/**
 * ==========================================================
 * Personalização da Moeda (Cara e Coroa)
 * Arquivo: backend/personalizar.php
 * ==========================================================
 */

require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderJson(['sucesso' => false, 'mensagem' => 'Método não permitido.'], 405);
}

$dados = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$dadosAnteriores = obterPersonalizacaoMoeda();

// Sanitização e validação dos campos
$nomeCara = trim(strip_tags($dados['nome_cara'] ?? 'Cara'));
$nomeCoroa = trim(strip_tags($dados['nome_coroa'] ?? 'Coroa'));
$temaCor = trim(strip_tags($dados['tema_cor'] ?? 'dourado'));
$iconeCara = trim(strip_tags($dados['icone_cara'] ?? '👑'));
$iconeCoroa = trim(strip_tags($dados['icone_coroa'] ?? '⭐'));
$imagemCara = $dados['imagem_cara'] ?? '';
$imagemCoroa = $dados['imagem_coroa'] ?? '';

// Limita tamanhos
$nomeCara = mb_substr($nomeCara ?: 'Cara', 0, 30);
$nomeCoroa = mb_substr($nomeCoroa ?: 'Coroa', 0, 30);

$temasValidos = ['dourado', 'prateado', 'bronze', 'cyberpunk', 'rubi', 'esmeralda', 'dark'];
if (!in_array($temaCor, $temasValidos)) {
    $temaCor = 'dourado';
}

$novosDados = [
    'nome_cara' => $nomeCara,
    'nome_coroa' => $nomeCoroa,
    'tema_cor' => $temaCor,
    'icone_cara' => $iconeCara ?: '👑',
    'icone_coroa' => $iconeCoroa ?: '⭐',
    'imagem_cara' => $imagemCara,
    'imagem_coroa' => $imagemCoroa
];

// 1. Salva nos cookies locais da máquina
definirCookieJson(COOKIE_PERSONALIZACAO, $novosDados);

// 2. Salva no Banco de Dados MySQL via PDO
$pdo = obterConexaoBD();
if ($pdo) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO personalizacao_moeda 
                (sessao_id, nome_cara, nome_coroa, tema_cor, icone_cara, icone_coroa, imagem_cara, imagem_coroa)
            VALUES 
                (:sessao, :nome_c, :nome_cr, :tema, :ico_c, :ico_cr, :img_c, :img_cr)
            ON DUPLICATE KEY UPDATE 
                nome_cara = VALUES(nome_cara),
                nome_coroa = VALUES(nome_coroa),
                tema_cor = VALUES(tema_cor),
                icone_cara = VALUES(icone_cara),
                icone_coroa = VALUES(icone_coroa),
                imagem_cara = VALUES(imagem_cara),
                imagem_coroa = VALUES(imagem_coroa)
        ");

        $stmt->execute([
            ':sessao' => $_SESSION['usuario_sessao_id'],
            ':nome_c' => $nomeCara,
            ':nome_cr' => $nomeCoroa,
            ':tema' => $temaCor,
            ':ico_c' => $novosDados['icone_cara'],
            ':ico_cr' => $novosDados['icone_coroa'],
            ':img_c' => $imagemCara,
            ':img_cr' => $imagemCoroa
        ]);
    } catch (PDOException $e) {
        error_log("Erro ao salvar personalização no BD: " . $e->getMessage());
    }
}

// 3. Regra de Negócio CORE: Registro detalhado de auditoria da alteração
registrarAuditoria(
    'MOEDA_PERSONALIZADA',
    "Usuário personalizou a moeda. Tema: {$temaCor}, Cara: '{$nomeCara}', Coroa: '{$nomeCoroa}'.",
    $dadosAnteriores,
    $novosDados
);

responderJson([
    'sucesso' => true,
    'mensagem' => 'Personalização da moeda salva com sucesso!',
    'dados' => $novosDados
]);

<?php
/**
 * ==========================================================
 * Limpeza de Histórico de Jogos com Auditoria
 * Arquivo: backend/limpar_historico.php
 * ==========================================================
 */

require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderJson(['sucesso' => false, 'mensagem' => 'Método inválido.'], 405);
}

$jogosAtuais = obterTodosJogos();
$totalApagado = count($jogosAtuais);

// Limpa cookie local
definirCookieJson(COOKIE_HISTORICO, []);

// Limpa no MySQL via PDO
$pdo = obterConexaoBD();
if ($pdo) {
    try {
        $stmt = $pdo->prepare("DELETE FROM historico_jogos WHERE sessao_id = :sessao");
        $stmt->execute([':sessao' => $_SESSION['usuario_sessao_id']]);
    } catch (PDOException $e) {
        error_log("Erro ao limpar histórico no BD: " . $e->getMessage());
    }
}

// Regra CORE: Mantém sempre logs de qualquer alteração para auditorias futuras
registrarAuditoria(
    'HISTORICO_LIMPO',
    "Usuário solicitou a limpeza completa do histórico de jogos. Total de {$totalApagado} registros removidos.",
    ['total_registros_anteriores' => $totalApagado],
    ['status' => 'limpo']
);

responderJson([
    'sucesso' => true,
    'mensagem' => "Histórico de {$totalApagado} jogos limpo com sucesso!",
    'total_removido' => $totalApagado
]);

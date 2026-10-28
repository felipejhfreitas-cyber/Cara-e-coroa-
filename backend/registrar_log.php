<?php
/**
 * ==========================================================
 * Registro de Eventos e Auditoria Frontend
 * Arquivo: backend/registrar_log.php
 * ==========================================================
 */

require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderJson(['sucesso' => false, 'mensagem' => 'Método inválido.'], 405);
}

$dados = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$tipoAcao = trim(strip_tags($dados['tipo_acao'] ?? 'EVENTO_GENERICO'));
$descricao = trim(strip_tags($dados['descricao'] ?? 'Evento do sistema'));
$dadosNovos = $dados['dados_novos'] ?? null;
$dadosAnteriores = $dados['dados_anteriores'] ?? null;

registrarAuditoria($tipoAcao, $descricao, $dadosAnteriores, $dadosNovos);

responderJson([
    'sucesso' => true,
    'mensagem' => 'Log de auditoria registrado com sucesso.'
]);

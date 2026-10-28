<?php
/**
 * ==========================================================
 * Controle de Troca de Escolha e Regra do Anúncio de 10s
 * Arquivo: backend/trocar_escolha.php
 * Regra: a cada duas troca de escolha um modal de anuncio de 10 segundos
 * ==========================================================
 */

require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderJson(['sucesso' => false, 'mensagem' => 'Método inválido.'], 405);
}

$dados = json_decode(file_get_contents('php://input'), true);
$novaEscolha = strtolower(trim($dados['nova_escolha'] ?? $_POST['nova_escolha'] ?? ''));

if ($novaEscolha !== 'cara' && $novaEscolha !== 'coroa') {
    responderJson(['sucesso' => false, 'mensagem' => 'Escolha inválida.'], 400);
}

// Obtém contagem atual de trocas de cookies/sessão
$trocasSalvas = obterCookieJson(COOKIE_TROCAS, ['total_trocas' => 0, 'ultima_escolha' => null]);
$totalTrocas = (int)($trocasSalvas['total_trocas'] ?? 0);
$ultimaEscolha = $trocasSalvas['ultima_escolha'] ?? $_SESSION['ultima_escolha'] ?? null;

$houveTroca = ($ultimaEscolha !== null && $ultimaEscolha !== $novaEscolha);

$exibirAnuncio = false;

if ($houveTroca) {
    $totalTrocas++;
    
    // Regra CORE: a cada duas trocas de escolha um modal de anuncio de 10 segundos
    if ($totalTrocas % 2 === 0) {
        $exibirAnuncio = true;
    }

    // Atualiza cookie e sessão
    $dadosTroca = [
        'total_trocas' => $totalTrocas,
        'ultima_escolha' => $novaEscolha,
        'ultima_troca_em' => date('Y-m-d H:i:s')
    ];
    definirCookieJson(COOKIE_TROCAS, $dadosTroca);
    $_SESSION['trocas_escolha_count'] = $totalTrocas;
    $_SESSION['ultima_escolha'] = $novaEscolha;

    // Regra de Auditoria
    registrarAuditoria(
        'TROCA_ESCOLHA',
        "Usuário alterou a escolha de '{$ultimaEscolha}' para '{$novaEscolha}'. Total de trocas: {$totalTrocas}." . ($exibirAnuncio ? " [Modal de Anúncio 10s Ativado]" : ""),
        ['escolha_anterior' => $ultimaEscolha],
        ['nova_escolha' => $novaEscolha, 'total_trocas' => $totalTrocas, 'anuncio_disparado' => $exibirAnuncio]
    );
} else {
    // Primeira seleção
    $_SESSION['ultima_escolha'] = $novaEscolha;
    $dadosTroca = [
        'total_trocas' => $totalTrocas,
        'ultima_escolha' => $novaEscolha,
        'ultima_troca_em' => date('Y-m-d H:i:s')
    ];
    definirCookieJson(COOKIE_TROCAS, $dadosTroca);
}

responderJson([
    'sucesso' => true,
    'houve_troca' => $houveTroca,
    'total_trocas' => $totalTrocas,
    'escolha_atual' => $novaEscolha,
    'exibir_anuncio' => $exibirAnuncio,
    'tempo_anuncio_segundos' => 10,
    'proximo_anuncio_em' => (2 - ($totalTrocas % 2))
]);

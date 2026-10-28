<?php
/**
 * ==========================================================
 * Página de Histórico e Logs de Auditoria
 * Arquivo: historico.php
 * Regra CORE: Mantém sempre logs de qualquer alteração feita por qualquer usuário para auditorias futuras
 * ==========================================================
 */

$tituloPagina = 'Histórico e Auditoria';
require_once __DIR__ . '/includes/header.php';

$jogos = obterTodosJogos();
$logsAuditoriaCookie = obterCookieJson(COOKIE_LOGS_AUDITORIA, []);

// Busca logs do MySQL via PDO se conectado
$logsAuditoriaBD = [];
$pdo = obterConexaoBD();
if ($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM logs_auditoria WHERE sessao_id = :sessao ORDER BY id DESC LIMIT 100");
        $stmt->execute([':sessao' => $_SESSION['usuario_sessao_id']]);
        $logsAuditoriaBD = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Erro ao buscar logs de auditoria: " . $e->getMessage());
    }
}

// Combina logs priorizando BD se disponível, caso contrário usa Cookies
$logsExibicao = !empty($logsAuditoriaBD) ? $logsAuditoriaBD : $logsAuditoriaCookie;
?>

<div class="max-w-6xl mx-auto space-y-8">

    <!-- Cabeçalho -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-6">
        <div>
            <h1 class="font-display font-black text-3xl sm:text-4xl tracking-tight bg-gradient-to-r from-amber-300 via-amber-100 to-amber-400 bg-clip-text text-transparent">
                Histórico & Auditoria
            </h1>
            <p class="text-slate-400 text-sm mt-1">
                Registros locais salvos em cookies e base de dados para auditorias futuras de conformidade.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="exportarDados()" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs transition flex items-center gap-1.5">
                <span>📥</span> Exportar JSON
            </button>
            <button onclick="solicitarLimpezaHistorico()" class="px-4 py-2.5 rounded-xl bg-rose-950/80 hover:bg-rose-900 border border-rose-800/60 text-rose-300 font-semibold text-xs transition flex items-center gap-1.5">
                <span>🗑️</span> Limpar Histórico
            </button>
        </div>
    </div>

    <!-- Navegação de Abas (Tabs) -->
    <div class="flex border-b border-slate-800 gap-2">
        <button id="tab-btn-jogos" onclick="alternarAba('jogos')" 
            class="px-5 py-3 text-sm font-bold border-b-2 border-amber-400 text-amber-400 flex items-center gap-2 transition">
            <span>🎲</span>
            <span>Histórico de Jogos (<?= count($jogos) ?>)</span>
        </button>
        <button id="tab-btn-auditoria" onclick="alternarAba('auditoria')" 
            class="px-5 py-3 text-sm font-bold border-b-2 border-transparent text-slate-400 hover:text-slate-200 flex items-center gap-2 transition">
            <span>🛡️</span>
            <span>Logs de Auditoria (<?= count($logsExibicao) ?>)</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] bg-amber-500/10 text-amber-400 border border-amber-500/30">CORE</span>
        </button>
    </div>

    <!-- ABA 1: Histórico de Jogos -->
    <div id="aba-jogos" class="space-y-4">
        <!-- Barra de Filtros -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 bg-slate-900/60 p-3 rounded-2xl border border-slate-800">
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <span class="text-xs text-slate-400 font-medium">Filtrar:</span>
                <button onclick="filtrarJogos('todos')" class="filtro-btn px-3 py-1.5 rounded-xl text-xs font-semibold bg-amber-500 text-slate-950">Todos</button>
                <button onclick="filtrarJogos('vitorias')" class="filtro-btn px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-800 text-slate-300 hover:text-white">Vitórias</button>
                <button onclick="filtrarJogos('derrotas')" class="filtro-btn px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-800 text-slate-300 hover:text-white">Derrotas</button>
            </div>
            <div class="text-xs text-slate-400">
                Armazenado via: <strong class="text-amber-400">Cookies Locais</strong> + <strong class="text-emerald-400">MySQL PDO</strong>
            </div>
        </div>

        <!-- Tabela de Jogos -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl">
            <?php if (empty($jogos)): ?>
                <div class="text-center py-16 text-slate-500">
                    <div class="text-5xl mb-3">🪙</div>
                    <h3 class="text-base font-bold text-slate-300">Nenhuma rolagem gravada ainda</h3>
                    <p class="text-xs mt-1">Os resultados das rolagens serão salvos em cookies e exibidos aqui.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="text-xs uppercase bg-slate-950/70 text-slate-400 border-b border-slate-800">
                            <tr>
                                <th class="px-4 py-3 rounded-l-xl">ID</th>
                                <th class="px-4 py-3">Data e Hora</th>
                                <th class="px-4 py-3">Sua Escolha</th>
                                <th class="px-4 py-3">Resultado Moeda</th>
                                <th class="px-4 py-3 text-right rounded-r-xl">Status Verificação</th>
                            </tr>
                        </thead>
                        <tbody id="lista-jogos-corpo" class="divide-y divide-slate-800/60 font-sans">
                            <?php foreach ($jogos as $j): 
                                $acertou = !empty($j['acertou']);
                                $statusClasse = $acertou ? 'jogo-vitoria' : 'jogo-derrota';
                                $escNome = ($j['escolha'] === 'cara') ? $moedaConfig['nome_cara'] : $moedaConfig['nome_coroa'];
                                $escIco = ($j['escolha'] === 'cara') ? $moedaConfig['icone_cara'] : $moedaConfig['icone_coroa'];
                                $resNome = ($j['resultado'] === 'cara') ? $moedaConfig['nome_cara'] : $moedaConfig['nome_coroa'];
                                $resIco = ($j['resultado'] === 'cara') ? $moedaConfig['icone_cara'] : $moedaConfig['icone_coroa'];
                            ?>
                            <tr class="linha-jogo <?= $statusClasse ?> hover:bg-slate-800/40 transition">
                                <td class="px-4 py-3 text-xs text-slate-500 font-mono">
                                    #<?= htmlspecialchars($j['id'] ?? '—') ?>
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-400 font-mono">
                                    <?= htmlspecialchars($j['criado_em'] ?? date('Y-m-d H:i:s')) ?>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-800 text-xs font-semibold">
                                        <span><?= $escIco ?></span>
                                        <span><?= htmlspecialchars($escNome) ?></span>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-800 text-xs font-semibold">
                                        <span><?= $resIco ?></span>
                                        <span><?= htmlspecialchars($resNome) ?></span>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <?php if ($acertou): ?>
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-950 text-emerald-300 border border-emerald-600/40">
                                            <span>✓</span> Acertou
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-rose-950 text-rose-300 border border-rose-600/40">
                                            <span>✕</span> Errou
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ABA 2: Logs de Auditoria (Regra de Negócio CORE) -->
    <div id="aba-auditoria" class="hidden space-y-4">
        
        <!-- Alerta de Auditoria Conforme Requisitos -->
        <div class="bg-amber-950/40 border border-amber-600/40 rounded-2xl p-4 flex items-start gap-3 text-xs text-amber-200">
            <span class="text-xl shrink-0">🛡️</span>
            <div>
                <strong class="font-bold text-amber-300 text-sm">Registro de Auditoria de Conformidade (CORE)</strong>
                <p class="mt-0.5 text-amber-200/80">
                    O sistema mantém sempre os logs de qualquer alteração ou evento realizado por qualquer usuário (jogadas, trocas de escolha, disparos de anúncios e personalizações) para auditorias futuras.
                </p>
            </div>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl">
            <?php if (empty($logsExibicao)): ?>
                <div class="text-center py-16 text-slate-500">
                    <div class="text-5xl mb-3">📑</div>
                    <h3 class="text-base font-bold text-slate-300">Nenhum log de auditoria registrado</h3>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="text-xs uppercase bg-slate-950/70 text-slate-400 border-b border-slate-800">
                            <tr>
                                <th class="px-4 py-3 rounded-l-xl">Data / Hora</th>
                                <th class="px-4 py-3">Tipo de Ação</th>
                                <th class="px-4 py-3">Descrição da Alteração</th>
                                <th class="px-4 py-3">IP / Sessão</th>
                                <th class="px-4 py-3 text-right rounded-r-xl">Detalhes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 font-sans">
                            <?php foreach ($logsExibicao as $log): 
                                $tipo = $log['tipo_acao'] ?? 'DESCONHECIDO';
                                $badgeTipo = 'bg-slate-800 text-slate-300';
                                if ($tipo === 'JOGO_REALIZADO') $badgeTipo = 'bg-emerald-950 text-emerald-300 border border-emerald-700/50';
                                elseif ($tipo === 'TROCA_ESCOLHA') $badgeTipo = 'bg-amber-950 text-amber-300 border border-amber-700/50';
                                elseif ($tipo === 'ANUNCIO_EXIBIDO') $badgeTipo = 'bg-indigo-950 text-indigo-300 border border-indigo-700/50';
                                elseif ($tipo === 'MOEDA_PERSONALIZADA') $badgeTipo = 'bg-purple-950 text-purple-300 border border-purple-700/50';
                                elseif ($tipo === 'HISTORICO_LIMPO') $badgeTipo = 'bg-rose-950 text-rose-300 border border-rose-700/50';
                            ?>
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="px-4 py-3 text-xs text-slate-400 font-mono">
                                    <?= htmlspecialchars($log['criado_em'] ?? date('Y-m-d H:i:s')) ?>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-mono font-bold <?= $badgeTipo ?>">
                                        <?= htmlspecialchars($tipo) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-200">
                                    <?= htmlspecialchars($log['descricao'] ?? '') ?>
                                </td>
                                <td class="px-4 py-3 text-xs font-mono text-slate-500">
                                    <?= htmlspecialchars($log['ip_origem'] ?? '127.0.0.1') ?>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button type="button" onclick='verDetalhesLog(<?= json_encode($log, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="px-3 py-1 rounded-lg text-xs font-medium bg-slate-800 hover:bg-slate-700 text-amber-400 transition">
                                        Inspecionar
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    </div>

</div>

<!-- Modal de Inspeção de Log de Auditoria -->
<div id="modal-log-detalhe" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
    <div class="bg-slate-900 border border-slate-700 rounded-3xl p-6 sm:p-8 max-w-xl w-full text-white shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <div class="flex items-center gap-2">
                <span class="text-xl">🛡️</span>
                <h3 class="font-bold text-lg">Inspeção do Registro de Auditoria</h3>
            </div>
            <button onclick="fecharModalLog()" class="text-slate-400 hover:text-white p-1">✕</button>
        </div>
        <div class="space-y-3 text-xs">
            <div>
                <span class="text-slate-400">Tipo de Ação:</span>
                <span id="log-detalhe-tipo" class="font-mono font-bold text-amber-400 ml-1"></span>
            </div>
            <div>
                <span class="text-slate-400">Data/Hora:</span>
                <span id="log-detalhe-data" class="font-mono text-slate-200 ml-1"></span>
            </div>
            <div>
                <span class="text-slate-400">Descrição:</span>
                <p id="log-detalhe-desc" class="text-slate-200 mt-1 p-2 bg-slate-950 rounded-lg"></p>
            </div>
            <div>
                <span class="text-slate-400">Dados da Alteração (JSON):</span>
                <pre id="log-detalhe-json" class="mt-1 p-3 bg-slate-950 rounded-xl text-emerald-400 font-mono text-[11px] overflow-x-auto max-h-48 border border-slate-800"></pre>
            </div>
        </div>
        <div class="flex justify-end pt-2">
            <button onclick="fecharModalLog()" class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs transition">
                Fechar
            </button>
        </div>
    </div>
</div>

<!-- Scripts da Página de Histórico -->
<script>
    function alternarAba(aba) {
        AppAudio.playClick();
        const abaJogos = document.getElementById('aba-jogos');
        const abaAuditoria = document.getElementById('aba-auditoria');
        const btnJogos = document.getElementById('tab-btn-jogos');
        const btnAuditoria = document.getElementById('tab-btn-auditoria');

        if (aba === 'jogos') {
            abaJogos.classList.remove('hidden');
            abaAuditoria.classList.add('hidden');
            btnJogos.className = 'px-5 py-3 text-sm font-bold border-b-2 border-amber-400 text-amber-400 flex items-center gap-2 transition';
            btnAuditoria.className = 'px-5 py-3 text-sm font-bold border-b-2 border-transparent text-slate-400 hover:text-slate-200 flex items-center gap-2 transition';
        } else {
            abaAuditoria.classList.remove('hidden');
            abaJogos.classList.add('hidden');
            btnAuditoria.className = 'px-5 py-3 text-sm font-bold border-b-2 border-amber-400 text-amber-400 flex items-center gap-2 transition';
            btnJogos.className = 'px-5 py-3 text-sm font-bold border-b-2 border-transparent text-slate-400 hover:text-slate-200 flex items-center gap-2 transition';
        }
    }

    function filtrarJogos(tipo) {
        AppAudio.playClick();
        const linhas = document.querySelectorAll('.linha-jogo');
        const botoes = document.querySelectorAll('.filtro-btn');

        botoes.forEach(b => {
            b.className = 'filtro-btn px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-800 text-slate-300 hover:text-white';
        });
        event.target.className = 'filtro-btn px-3 py-1.5 rounded-xl text-xs font-semibold bg-amber-500 text-slate-950';

        linhas.forEach(l => {
            if (tipo === 'todos') {
                l.classList.remove('hidden');
            } else if (tipo === 'vitorias') {
                if (l.classList.contains('jogo-vitoria')) l.classList.remove('hidden');
                else l.classList.add('hidden');
            } else if (tipo === 'derrotas') {
                if (l.classList.contains('jogo-derrota')) l.classList.remove('hidden');
                else l.classList.add('hidden');
            }
        });
    }

    // Regra Global: "sempre trate as mensagens de caixa de mensagens nativas do navegador em um MODAL"
    async function solicitarLimpezaHistorico() {
        const confirmou = await AppModal.confirm(
            'Confirmar Limpeza de Registros',
            'Tem certeza que deseja apagar todo o histórico de lançamentos? Esta ação será registrada permanentemente nos logs de auditoria.',
            'Sim, Limpar Tudo',
            'Cancelar'
        );

        if (!confirmou) return;

        try {
            const resp = await fetch('backend/limpar_historico.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            });
            const data = await resp.json();

            if (data.sucesso) {
                AppToast.success(data.mensagem);
                setTimeout(() => location.reload(), 1200);
            } else {
                AppToast.error(data.mensagem || 'Erro ao limpar histórico.');
            }
        } catch (e) {
            console.error(e);
            AppToast.error('Erro de conexão ao solicitar limpeza.');
        }
    }

    function exportarDados() {
        AppAudio.playClick();
        const dadosExportacao = {
            usuario_sessao: '<?= htmlspecialchars($_SESSION['usuario_sessao_id']) ?>',
            exportado_em: new Date().toISOString(),
            jogos: <?= json_encode($jogos, JSON_UNESCAPED_UNICODE) ?>,
            logs_auditoria: <?= json_encode($logsExibicao, JSON_UNESCAPED_UNICODE) ?>
        };

        const blob = new Blob([JSON.stringify(dadosExportacao, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `cara_e_coroa_auditoria_${Date.now()}.json`;
        a.click();
        URL.revokeObjectURL(url);

        AppToast.success('Arquivo de auditoria exportado com sucesso!');
    }

    function verDetalhesLog(log) {
        AppAudio.playClick();
        document.getElementById('log-detalhe-tipo').innerText = log.tipo_acao || 'N/A';
        document.getElementById('log-detalhe-data').innerText = log.criado_em || 'N/A';
        document.getElementById('log-detalhe-desc').innerText = log.descricao || 'Sem descrição';

        const jsonDados = {
            dados_anteriores: log.dados_anteriores,
            dados_novos: log.dados_novos,
            ip_origem: log.ip_origem,
            user_agent: log.user_agent
        };
        document.getElementById('log-detalhe-json').innerText = JSON.stringify(jsonDados, null, 2);

        const modal = document.getElementById('modal-log-detalhe');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function fecharModalLog() {
        AppAudio.playClick();
        const modal = document.getElementById('modal-log-detalhe');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

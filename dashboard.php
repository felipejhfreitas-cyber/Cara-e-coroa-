<?php
/**
 * ==========================================================
 * Dashboard Dedicado de Uso de Cara ou Coroa
 * Arquivo: dashboard.php
 * ==========================================================
 */

$tituloPagina = 'Dashboard de Uso';
require_once __DIR__ . '/includes/header.php';

$jogos = obterTodosJogos();
$metricas = calcularMetricasDashboard($jogos);

$trocasSalvas = obterCookieJson(COOKIE_TROCAS, ['total_trocas' => 0]);
$totalTrocas = (int)($trocasSalvas['total_trocas'] ?? 0);
$totalAnunciosDisparados = (int)floor($totalTrocas / 2);
?>

<div class="max-w-6xl mx-auto space-y-8">

    <!-- Cabeçalho -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-6">
        <div>
            <h1 class="font-display font-black text-3xl sm:text-4xl tracking-tight bg-gradient-to-r from-amber-300 via-amber-100 to-amber-400 bg-clip-text text-transparent">
                Dashboard de Uso
            </h1>
            <p class="text-slate-400 text-sm mt-1">
                Estatísticas analíticas recuperadas de cookies locais e banco de dados.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="index.php" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-sm tracking-wide shadow-lg shadow-amber-500/20 transition">
                🎲 Ir Jogar
            </a>
            <a href="historico.php" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-sm transition">
                📜 Ver Histórico Completo
            </a>
        </div>
    </div>

    <!-- Cards Principais (MobileFirst 2 colunas no celular, 4 no desktop) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total de Jogos -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-lg relative overflow-hidden">
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Total de Rolagens</div>
            <div class="font-display font-black text-3xl sm:text-4xl text-white"><?= $metricas['total_jogos'] ?></div>
            <div class="text-xs text-slate-500 mt-2 flex items-center gap-1">
                <span>🪙</span> Lançamentos registrados
            </div>
        </div>

        <!-- Taxa de Vitória -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-lg relative overflow-hidden">
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Taxa de Acertos</div>
            <div class="font-display font-black text-3xl sm:text-4xl text-amber-400"><?= $metricas['taxa_acerto'] ?>%</div>
            <div class="text-xs text-slate-500 mt-2 flex items-center gap-1">
                <span>🎯</span> <?= $metricas['vitorias'] ?> acertos de <?= $metricas['total_jogos'] ?>
            </div>
        </div>

        <!-- Maior Sequência de Vitórias (Streak) -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-lg relative overflow-hidden">
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Maior Sequência</div>
            <div class="font-display font-black text-3xl sm:text-4xl text-emerald-400">🔥 <?= $metricas['maior_streak'] ?></div>
            <div class="text-xs text-slate-500 mt-2 flex items-center gap-1">
                <span>Atual:</span> <?= $metricas['streak_atual'] ?> vitórias seguidas
            </div>
        </div>

        <!-- Trocas de Escolha e Anúncios -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-lg relative overflow-hidden">
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Trocas / Anúncios</div>
            <div class="font-display font-black text-3xl sm:text-4xl text-indigo-400">
                <?= $totalTrocas ?> <span class="text-sm font-normal text-slate-500">/ <?= $totalAnunciosDisparados ?> ads</span>
            </div>
            <div class="text-xs text-slate-500 mt-2 flex items-center gap-1">
                <span>⚡</span> Regra 10s aplicada
            </div>
        </div>

    </div>

    <!-- Gráficos Visuais de Proporção (Donuts e Barras em Tailwind) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- 1. Distribuição de Resultados (Cara vs Coroa) -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-display font-bold text-base text-slate-200">Distribuição: Cara vs Coroa</h3>
                <span class="text-xs font-mono text-slate-400">Sorteios</span>
            </div>

            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-xs font-medium text-slate-300 mb-1.5">
                        <span class="flex items-center gap-1.5">
                            <span><?= htmlspecialchars($moedaConfig['icone_cara']) ?></span>
                            <span><?= htmlspecialchars($moedaConfig['nome_cara']) ?></span>
                        </span>
                        <span class="font-mono text-amber-400 font-bold"><?= $metricas['total_cara'] ?> (<?= $metricas['porcentagem_cara'] ?>%)</span>
                    </div>
                    <div class="w-full bg-slate-950 h-3 rounded-full overflow-hidden border border-slate-800">
                        <div class="bg-gradient-to-r from-amber-500 to-amber-300 h-full rounded-full transition-all duration-500" style="width: <?= $metricas['porcentagem_cara'] ?>%;"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-xs font-medium text-slate-300 mb-1.5">
                        <span class="flex items-center gap-1.5">
                            <span><?= htmlspecialchars($moedaConfig['icone_coroa']) ?></span>
                            <span><?= htmlspecialchars($moedaConfig['nome_coroa']) ?></span>
                        </span>
                        <span class="font-mono text-indigo-400 font-bold"><?= $metricas['total_coroa'] ?> (<?= $metricas['porcentagem_coroa'] ?>%)</span>
                    </div>
                    <div class="w-full bg-slate-950 h-3 rounded-full overflow-hidden border border-slate-800">
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-400 h-full rounded-full transition-all duration-500" style="width: <?= $metricas['porcentagem_coroa'] ?>%;"></div>
                    </div>
                </div>
            </div>

            <!-- Resumo -->
            <div class="mt-6 pt-4 border-t border-slate-800/80 grid grid-cols-2 gap-4 text-center">
                <div class="p-3 bg-slate-950/60 rounded-xl border border-slate-800">
                    <div class="text-xs text-slate-400">Total Cara</div>
                    <div class="font-bold text-lg text-white mt-0.5"><?= $metricas['total_cara'] ?></div>
                </div>
                <div class="p-3 bg-slate-950/60 rounded-xl border border-slate-800">
                    <div class="text-xs text-slate-400">Total Coroa</div>
                    <div class="font-bold text-lg text-white mt-0.5"><?= $metricas['total_coroa'] ?></div>
                </div>
            </div>
        </div>

        <!-- 2. Performance: Vitórias vs Derrotas -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-display font-bold text-base text-slate-200">Desempenho: Vitórias vs Derrotas</h3>
                <span class="text-xs font-mono text-slate-400">Acertos</span>
            </div>

            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-xs font-medium text-slate-300 mb-1.5">
                        <span class="flex items-center gap-1.5 text-emerald-400">
                            <span>🏆</span>
                            <span>Vitórias (Acertos)</span>
                        </span>
                        <span class="font-mono text-emerald-400 font-bold"><?= $metricas['vitorias'] ?> (<?= $metricas['taxa_acerto'] ?>%)</span>
                    </div>
                    <div class="w-full bg-slate-950 h-3 rounded-full overflow-hidden border border-slate-800">
                        <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-full rounded-full transition-all duration-500" style="width: <?= $metricas['taxa_acerto'] ?>%;"></div>
                    </div>
                </div>

                <?php 
                $taxaDerrota = $metricas['total_jogos'] > 0 ? (100 - $metricas['taxa_acerto']) : 0;
                ?>
                <div>
                    <div class="flex justify-between text-xs font-medium text-slate-300 mb-1.5">
                        <span class="flex items-center gap-1.5 text-rose-400">
                            <span>💥</span>
                            <span>Derrotas (Erros)</span>
                        </span>
                        <span class="font-mono text-rose-400 font-bold"><?= $metricas['derrotas'] ?> (<?= $taxaDerrota ?>%)</span>
                    </div>
                    <div class="w-full bg-slate-950 h-3 rounded-full overflow-hidden border border-slate-800">
                        <div class="bg-gradient-to-r from-rose-500 to-red-400 h-full rounded-full transition-all duration-500" style="width: <?= $taxaDerrota ?>%;"></div>
                    </div>
                </div>
            </div>

            <!-- Resumo -->
            <div class="mt-6 pt-4 border-t border-slate-800/80 grid grid-cols-2 gap-4 text-center">
                <div class="p-3 bg-slate-950/60 rounded-xl border border-slate-800">
                    <div class="text-xs text-slate-400">Total Vitórias</div>
                    <div class="font-bold text-lg text-emerald-400 mt-0.5"><?= $metricas['vitorias'] ?></div>
                </div>
                <div class="p-3 bg-slate-950/60 rounded-xl border border-slate-800">
                    <div class="text-xs text-slate-400">Total Derrotas</div>
                    <div class="font-bold text-lg text-rose-400 mt-0.5"><?= $metricas['derrotas'] ?></div>
                </div>
            </div>
        </div>

    </div>

    <!-- Tabela Recente dos Últimos 10 Jogos -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="font-display font-bold text-lg text-slate-200">Últimos Lançamentos Registrados</h3>
            <span class="text-xs text-slate-400">Recuperado por Cookies & BD</span>
        </div>

        <?php if (empty($jogos)): ?>
            <div class="text-center py-12 text-slate-500">
                <div class="text-4xl mb-3">🎲</div>
                <p class="text-sm">Nenhum lançamento registrado ainda.</p>
                <a href="index.php" class="inline-block mt-3 px-4 py-2 rounded-xl bg-amber-500/20 text-amber-400 text-xs font-bold hover:bg-amber-500/30 transition">
                    Lançar Moeda Agora
                </a>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="text-xs uppercase bg-slate-950/70 text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="px-4 py-3 rounded-l-xl">Data / Hora</th>
                            <th class="px-4 py-3">Sua Escolha</th>
                            <th class="px-4 py-3">Resultado Moeda</th>
                            <th class="px-4 py-3 text-right rounded-r-xl">Status Verificação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 font-sans">
                        <?php 
                        $ultimos = array_slice($jogos, 0, 10);
                        foreach ($ultimos as $j): 
                            $acertou = !empty($j['acertou']);
                            $escNome = ($j['escolha'] === 'cara') ? $moedaConfig['nome_cara'] : $moedaConfig['nome_coroa'];
                            $escIco = ($j['escolha'] === 'cara') ? $moedaConfig['icone_cara'] : $moedaConfig['icone_coroa'];
                            $resNome = ($j['resultado'] === 'cara') ? $moedaConfig['nome_cara'] : $moedaConfig['nome_coroa'];
                            $resIco = ($j['resultado'] === 'cara') ? $moedaConfig['icone_cara'] : $moedaConfig['icone_coroa'];
                        ?>
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="px-4 py-3 text-xs text-slate-400 font-mono">
                                <?= htmlspecialchars($j['criado_em'] ?? date('d/m/Y H:i')) ?>
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
                                        <span>✓</span> Vitória
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-rose-950 text-rose-300 border border-rose-600/40">
                                        <span>✕</span> Derrota
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

<?php require_once __DIR__ . '/includes/footer.php'; ?>

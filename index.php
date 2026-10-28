<?php
/**
 * ==========================================================
 * Página Principal: Jogo de Cara ou Coroa com Rolagem 3D
 * Arquivo: index.php
 * ==========================================================
 */

$tituloPagina = 'Lançar Moeda';
require_once __DIR__ . '/includes/header.php';

// Carrega dados da sessão e cookies
$trocasSalvas = obterCookieJson(COOKIE_TROCAS, ['total_trocas' => 0, 'ultima_escolha' => 'cara']);
$totalTrocas = (int)($trocasSalvas['total_trocas'] ?? 0);
$escolhaAtual = $trocasSalvas['ultima_escolha'] ?? 'cara';

// Carrega histórico para métricas rápidas
$jogos = obterTodosJogos();
$metricas = calcularMetricasDashboard($jogos);
?>

<div class="max-w-4xl mx-auto space-y-8">

    <!-- Cabeçalho da Seção -->
    <div class="text-center space-y-2">
        <h1 class="font-display font-black text-3xl sm:text-5xl tracking-tight bg-gradient-to-r from-amber-300 via-amber-100 to-amber-400 bg-clip-text text-transparent">
            Lançar Cara ou Coroa
        </h1>
        <p class="text-slate-400 text-sm sm:text-base max-w-lg mx-auto">
            Faça sua escolha, role a moeda 3D e verifique se o resultado equivale à sua previsão com auditoria em tempo real!
        </p>
    </div>

    <!-- Banner Informativo de Regra de Negócio: Anúncio a cada 2 Trocas -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-300">
        <div class="flex items-center gap-2">
            <span class="p-1.5 bg-amber-500/10 text-amber-400 rounded-lg">⚡</span>
            <span><strong>Regra do Jogo:</strong> A cada <strong>2 trocas de escolha</strong>, um anúncio obrigatório de 10s será exibido.</span>
        </div>
        <div class="flex items-center gap-2 bg-slate-950/80 px-3 py-1.5 rounded-xl border border-slate-800">
            <span class="text-slate-400">Trocas efetuadas:</span>
            <span id="badge-total-trocas" class="font-mono font-bold text-amber-400"><?= $totalTrocas ?></span>
            <span class="text-slate-500">|</span>
            <span class="text-slate-400">Próximo anúncio em:</span>
            <span id="badge-proximo-anuncio" class="font-mono font-bold text-emerald-400"><?= (2 - ($totalTrocas % 2)) ?></span>
        </div>
    </div>

    <!-- Arena Central: Moeda 3D e Botão de Lançar -->
    <div class="bg-gradient-to-b from-slate-900/90 to-slate-950/90 border border-slate-800/80 rounded-3xl p-6 sm:p-10 shadow-2xl relative overflow-hidden text-center">
        
        <!-- Luz ambiente no topo -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-96 h-32 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Seletor de Escolha (MobileFirst) -->
        <div class="max-w-md mx-auto mb-8">
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-3">
                1. Selecione a sua aposta:
            </label>
            <div class="grid grid-cols-2 gap-3 p-1.5 bg-slate-950/90 rounded-2xl border border-slate-800">
                <button type="button" id="btn-escolha-cara" onclick="selecionarEscolha('cara')" 
                    class="py-3 px-4 rounded-xl font-bold text-sm sm:text-base flex items-center justify-center gap-2 transition-all duration-200 <?= $escolhaAtual === 'cara' ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/25 ring-2 ring-amber-400' : 'text-slate-400 hover:text-white hover:bg-slate-900' ?>">
                    <span class="text-lg"><?= htmlspecialchars($moedaConfig['icone_cara']) ?></span>
                    <span><?= htmlspecialchars($moedaConfig['nome_cara']) ?></span>
                </button>
                <button type="button" id="btn-escolha-coroa" onclick="selecionarEscolha('coroa')" 
                    class="py-3 px-4 rounded-xl font-bold text-sm sm:text-base flex items-center justify-center gap-2 transition-all duration-200 <?= $escolhaAtual === 'coroa' ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/25 ring-2 ring-amber-400' : 'text-slate-400 hover:text-white hover:bg-slate-900' ?>">
                    <span class="text-lg"><?= htmlspecialchars($moedaConfig['icone_coroa']) ?></span>
                    <span><?= htmlspecialchars($moedaConfig['nome_coroa']) ?></span>
                </button>
            </div>
        </div>

        <!-- Componente da Moeda 3D -->
        <div class="py-6">
            <div class="coin-container">
                <div id="coin" class="coin coin-theme-<?= htmlspecialchars($moedaConfig['tema_cor']) ?>" onclick="lancarMoeda()">
                    <!-- Face Cara (Frente) -->
                    <div class="coin-face coin-front">
                        <div class="coin-rim"></div>
                        <?php if (!empty($moedaConfig['imagem_cara'])): ?>
                            <img src="<?= htmlspecialchars($moedaConfig['imagem_cara']) ?>" alt="Cara" class="w-16 h-16 rounded-full object-cover mb-1 border border-white/30">
                        <?php else: ?>
                            <span class="text-4xl mb-1 filter drop-shadow"><?= htmlspecialchars($moedaConfig['icone_cara']) ?></span>
                        <?php endif; ?>
                        <span class="font-display font-extrabold text-sm uppercase tracking-wider"><?= htmlspecialchars($moedaConfig['nome_cara']) ?></span>
                    </div>

                    <!-- Face Coroa (Verso) -->
                    <div class="coin-face coin-back">
                        <div class="coin-rim"></div>
                        <?php if (!empty($moedaConfig['imagem_coroa'])): ?>
                            <img src="<?= htmlspecialchars($moedaConfig['imagem_coroa']) ?>" alt="Coroa" class="w-16 h-16 rounded-full object-cover mb-1 border border-white/30">
                        <?php else: ?>
                            <span class="text-4xl mb-1 filter drop-shadow"><?= htmlspecialchars($moedaConfig['icone_coroa']) ?></span>
                        <?php endif; ?>
                        <span class="font-display font-extrabold text-sm uppercase tracking-wider"><?= htmlspecialchars($moedaConfig['nome_coroa']) ?></span>
                    </div>
                </div>
            </div>
            <!-- Sombra da Moeda -->
            <div id="coin-shadow" class="coin-shadow"></div>
        </div>

        <!-- Botão Principal de Lançamento -->
        <div class="mt-6 flex flex-col items-center gap-3">
            <button id="btn-lancar" onclick="lancarMoeda()" 
                class="w-full sm:w-auto px-10 py-4 rounded-2xl bg-gradient-to-r from-amber-500 via-amber-400 to-amber-500 hover:from-amber-400 hover:to-amber-300 text-slate-950 font-display font-black text-lg sm:text-xl tracking-tight shadow-xl shadow-amber-500/20 active:scale-95 transition-all duration-150">
                🎲 Lançar Moeda Agora
            </button>
            <span class="text-xs text-slate-500">Ou clique diretamente na moeda para rolar</span>
        </div>

        <!-- Painel de Resultado do Lançamento -->
        <div id="resultado-painel" class="hidden mt-8 p-6 rounded-2xl border transition-all duration-300">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div id="resultado-icone" class="text-4xl p-3 rounded-2xl bg-slate-800/80">🪙</div>
                    <div class="text-left">
                        <div id="resultado-titulo" class="font-display font-extrabold text-xl sm:text-2xl"></div>
                        <div id="resultado-detalhe" class="text-sm text-slate-300"></div>
                    </div>
                </div>
                <div id="resultado-badge" class="px-5 py-2.5 rounded-xl font-bold text-sm tracking-wide shadow-lg"></div>
            </div>
        </div>
    </div>

    <!-- Estatísticas Rápidas (MobileFirst Grid) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 text-center">
            <div class="text-xs text-slate-400 mb-1">Total Jogos</div>
            <div id="stat-total" class="font-display font-black text-2xl text-white"><?= $metricas['total_jogos'] ?></div>
        </div>
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 text-center">
            <div class="text-xs text-slate-400 mb-1">Taxa de Vitória</div>
            <div id="stat-taxa" class="font-display font-black text-2xl text-amber-400"><?= $metricas['taxa_acerto'] ?>%</div>
        </div>
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 text-center">
            <div class="text-xs text-slate-400 mb-1">Vitórias / Erros</div>
            <div class="font-display font-bold text-lg text-white">
                <span id="stat-vitorias" class="text-emerald-400"><?= $metricas['vitorias'] ?></span> / 
                <span id="stat-derrotas" class="text-rose-400"><?= $metricas['derrotas'] ?></span>
            </div>
        </div>
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 text-center">
            <div class="text-xs text-slate-400 mb-1">Sequência Atual</div>
            <div id="stat-streak" class="font-display font-black text-2xl text-indigo-400">🔥 <?= $metricas['streak_atual'] ?></div>
        </div>
    </div>

</div>

<!-- Script Específico do Jogo -->
<script>
    let escolhaAtual = '<?= $escolhaAtual ?>';
    let totalTrocas = <?= $totalTrocas ?>;
    let isFlipping = false;
    let currentRotation = 0;

    // Função de Troca de Escolha (com regra dos 10 segundos a cada 2 trocas)
    async function selecionarEscolha(novaEscolha) {
        if (isFlipping) return;

        AppAudio.playClick();

        if (novaEscolha === escolhaAtual) {
            return; // Mesma escolha, não conta como troca
        }

        try {
            const resp = await fetch('backend/trocar_escolha.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nova_escolha: novaEscolha })
            });
            const data = await resp.json();

            if (data.sucesso) {
                escolhaAtual = data.escolha_atual;
                totalTrocas = data.total_trocas;

                // Atualiza botões
                atualizarBotoesEscolha();

                // Atualiza badges
                document.getElementById('badge-total-trocas').innerText = totalTrocas;
                document.getElementById('badge-proximo-anuncio').innerText = data.proximo_anuncio_em;

                // Regra CORE: a cada duas trocas dispara modal de anúncio de 10s
                if (data.exibir_anuncio) {
                    AppToast.warning('Regra ativada: Anúncio obrigatório de 10 segundos!');
                    AppAdModal.start(() => {
                        AppToast.success('Anúncio concluído! Escolha confirmada: ' + escolhaAtual.toUpperCase());
                    });
                } else {
                    AppToast.info(`Escolha alterada para: ${escolhaAtual.toUpperCase()}`);
                }
            } else {
                AppToast.error(data.mensagem || 'Erro ao alterar escolha.');
            }
        } catch (err) {
            console.error(err);
            AppToast.error('Erro de conexão ao salvar troca de escolha.');
        }
    }

    function atualizarBotoesEscolha() {
        const btnCara = document.getElementById('btn-escolha-cara');
        const btnCoroa = document.getElementById('btn-escolha-coroa');

        const classeAtiva = 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/25 ring-2 ring-amber-400';
        const classeInativa = 'text-slate-400 hover:text-white hover:bg-slate-900';

        if (escolhaAtual === 'cara') {
            btnCara.className = `py-3 px-4 rounded-xl font-bold text-sm sm:text-base flex items-center justify-center gap-2 transition-all duration-200 ${classeAtiva}`;
            btnCoroa.className = `py-3 px-4 rounded-xl font-bold text-sm sm:text-base flex items-center justify-center gap-2 transition-all duration-200 ${classeInativa}`;
        } else {
            btnCoroa.className = `py-3 px-4 rounded-xl font-bold text-sm sm:text-base flex items-center justify-center gap-2 transition-all duration-200 ${classeAtiva}`;
            btnCara.className = `py-3 px-4 rounded-xl font-bold text-sm sm:text-base flex items-center justify-center gap-2 transition-all duration-200 ${classeInativa}`;
        }
    }

    // Função de Rolagem da Moeda
    async function lancarMoeda() {
        if (isFlipping) return;
        if (AppAdModal.isOpen) {
            AppToast.warning('Aguarde o término do anúncio de 10 segundos!');
            return;
        }

        isFlipping = true;
        const coin = document.getElementById('coin');
        const coinShadow = document.getElementById('coin-shadow');
        const btnLancar = document.getElementById('btn-lancar');
        const painelResultado = document.getElementById('resultado-painel');

        btnLancar.disabled = true;
        btnLancar.classList.add('opacity-50', 'cursor-not-allowed');
        painelResultado.classList.add('hidden');

        // Toca som inicial de giro
        AppAudio.playCoinFlip();
        coinShadow.classList.add('animate-pulse');

        try {
            // Chama backend com validação PDO e cookies
            const resp = await fetch('backend/jogar.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ escolha: escolhaAtual })
            });

            const data = await resp.json();

            if (!data.sucesso) {
                AppToast.error(data.mensagem || 'Erro ao processar jogada.');
                isFlipping = false;
                btnLancar.disabled = false;
                btnLancar.classList.remove('opacity-50', 'cursor-not-allowed');
                return;
            }

            // Cálculo físico do ângulo de rotação 3D
            // Cara = múltiplos de 360deg
            // Coroa = múltiplos de 360deg + 180deg
            const voltasExtras = 5 * 360; // 5 voltas completas
            const anguloDestino = (data.resultado === 'cara') ? 0 : 180;
            currentRotation = currentRotation + voltasExtras + ((anguloDestino - (currentRotation % 360) + 360) % 360);

            coin.style.transform = `rotateY(${currentRotation}deg)`;

            // Aguarda tempo da animação física da moeda
            setTimeout(() => {
                isFlipping = false;
                btnLancar.disabled = false;
                btnLancar.classList.remove('opacity-50', 'cursor-not-allowed');
                coinShadow.classList.remove('animate-pulse');

                AppAudio.playCoinLand();

                // Exibe resultado e verifica se escolha equivale ao resultado
                exibirResultado(data);

            }, 850);

        } catch (err) {
            console.error(err);
            AppToast.error('Falha de conexão com o servidor.');
            isFlipping = false;
            btnLancar.disabled = false;
            btnLancar.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }

    function exibirResultado(data) {
        const painel = document.getElementById('resultado-painel');
        const titulo = document.getElementById('resultado-titulo');
        const detalhe = document.getElementById('resultado-detalhe');
        const icone = document.getElementById('resultado-icone');
        const badge = document.getElementById('resultado-badge');

        painel.classList.remove('hidden', 'bg-emerald-950/40', 'border-emerald-600/40', 'bg-rose-950/40', 'border-rose-600/40');

        const acertou = data.acertou;
        const resNome = (data.resultado === 'cara') ? '<?= addslashes(htmlspecialchars($moedaConfig['nome_cara'])) ?>' : '<?= addslashes(htmlspecialchars($moedaConfig['nome_coroa'])) ?>';
        const escNome = (data.escolha === 'cara') ? '<?= addslashes(htmlspecialchars($moedaConfig['nome_cara'])) ?>' : '<?= addslashes(htmlspecialchars($moedaConfig['nome_coroa'])) ?>';

        if (acertou) {
            AppAudio.playWin();
            painel.classList.add('bg-emerald-950/40', 'border-emerald-600/40');
            icone.innerText = '🏆';
            titulo.className = 'font-display font-extrabold text-xl sm:text-2xl text-emerald-300';
            titulo.innerText = 'Você Acertou!';
            detalhe.innerText = `Sua escolha (${escNome}) equivale exatamente ao resultado sorteado (${resNome})!`;
            badge.className = 'px-5 py-2.5 rounded-xl font-bold text-sm tracking-wide shadow-lg bg-emerald-500 text-slate-950';
            badge.innerText = 'VITÓRIA';
            AppToast.success('Parabéns! Vitória confirmada no sorteio.');
        } else {
            AppAudio.playLose();
            painel.classList.add('bg-rose-950/40', 'border-rose-600/40');
            icone.innerText = '💥';
            titulo.className = 'font-display font-extrabold text-xl sm:text-2xl text-rose-300';
            titulo.innerText = 'Você Errou!';
            detalhe.innerText = `Você escolheu ${escNome}, mas a moeda caiu em ${resNome}.`;
            badge.className = 'px-5 py-2.5 rounded-xl font-bold text-sm tracking-wide shadow-lg bg-rose-600 text-white';
            badge.innerText = 'DERROTA';
            AppToast.error('Não foi dessa vez! Tente novamente.');
        }

        // Atualiza estatísticas em tela
        if (data.metricas) {
            document.getElementById('stat-total').innerText = data.metricas.total_jogos;
            document.getElementById('stat-taxa').innerText = data.metricas.taxa_acerto + '%';
            document.getElementById('stat-vitorias').innerText = data.metricas.vitorias;
            document.getElementById('stat-derrotas').innerText = data.metricas.derrotas;
            document.getElementById('stat-streak').innerText = '🔥 ' + data.metricas.streak_atual;
        }
    }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

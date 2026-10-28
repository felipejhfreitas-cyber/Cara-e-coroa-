<?php
/**
 * ==========================================================
 * Página de Personalização da Moeda
 * Arquivo: personalizar.php
 * ==========================================================
 */

$tituloPagina = 'Personalizar Moeda';
require_once __DIR__ . '/includes/header.php';

$config = obterPersonalizacaoMoeda();
?>

<div class="max-w-4xl mx-auto space-y-8">

    <!-- Cabeçalho -->
    <div class="text-center space-y-2">
        <h1 class="font-display font-black text-3xl sm:text-5xl tracking-tight bg-gradient-to-r from-amber-300 via-amber-100 to-amber-400 bg-clip-text text-transparent">
            Personalizar Moeda
        </h1>
        <p class="text-slate-400 text-sm sm:text-base max-w-lg mx-auto">
            Configure nomes, ícones, temas metálicos ou carregue imagens próprias para cada face da sua moeda.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Coluna da Esquerda: Pré-visualização 3D ao Vivo (MobileFirst) -->
        <div class="lg:col-span-5 bg-gradient-to-b from-slate-900 to-slate-950 border border-slate-800 rounded-3xl p-6 sm:p-8 text-center sticky top-24 shadow-2xl">
            <h3 class="font-display font-bold text-lg text-slate-200 mb-2">Prévia em Tempo Real</h3>
            <p class="text-xs text-slate-400 mb-6">Clique na moeda ou use os botões para virar e conferir as duas faces</p>

            <div class="coin-container my-4">
                <div id="preview-coin" class="coin coin-theme-<?= htmlspecialchars($config['tema_cor']) ?>" onclick="alternarPreviaFace()">
                    <!-- Face Cara -->
                    <div class="coin-face coin-front">
                        <div class="coin-rim"></div>
                        <div id="preview-img-cara-box" class="<?= empty($config['imagem_cara']) ? 'hidden' : '' ?> mb-1">
                            <img id="preview-img-cara" src="<?= htmlspecialchars($config['imagem_cara']) ?>" alt="Cara" class="w-16 h-16 rounded-full object-cover border border-white/40">
                        </div>
                        <span id="preview-ico-cara" class="text-4xl mb-1 filter drop-shadow <?= !empty($config['imagem_cara']) ? 'hidden' : '' ?>">
                            <?= htmlspecialchars($config['icone_cara']) ?>
                        </span>
                        <span id="preview-txt-cara" class="font-display font-extrabold text-sm uppercase tracking-wider">
                            <?= htmlspecialchars($config['nome_cara']) ?>
                        </span>
                    </div>

                    <!-- Face Coroa -->
                    <div class="coin-face coin-back">
                        <div class="coin-rim"></div>
                        <div id="preview-img-coroa-box" class="<?= empty($config['imagem_coroa']) ? 'hidden' : '' ?> mb-1">
                            <img id="preview-img-coroa" src="<?= htmlspecialchars($config['imagem_coroa']) ?>" alt="Coroa" class="w-16 h-16 rounded-full object-cover border border-white/40">
                        </div>
                        <span id="preview-ico-coroa" class="text-4xl mb-1 filter drop-shadow <?= !empty($config['imagem_coroa']) ? 'hidden' : '' ?>">
                            <?= htmlspecialchars($config['icone_coroa']) ?>
                        </span>
                        <span id="preview-txt-coroa" class="font-display font-extrabold text-sm uppercase tracking-wider">
                            <?= htmlspecialchars($config['nome_coroa']) ?>
                        </span>
                    </div>
                </div>
            </div>

            <div id="preview-shadow" class="coin-shadow mb-6"></div>

            <div class="flex justify-center gap-2">
                <button type="button" onclick="virarPreviaPara(0)" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 transition">
                    Ver Cara
                </button>
                <button type="button" onclick="virarPreviaPara(180)" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 transition">
                    Ver Coroa
                </button>
            </div>
        </div>

        <!-- Coluna da Direita: Formulário de Personalização -->
        <div class="lg:col-span-7 bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">
            <form id="form-personalizar" onsubmit="salvarPersonalizacao(event)" class="space-y-6">
                
                <!-- 1. Tema de Cor / Metal -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-3">
                        1. Tema Visual do Metal:
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                        <?php 
                        $temas = [
                            'dourado' => ['Dourado Real', 'from-amber-400 to-yellow-600'],
                            'prateado' => ['Prata Clássica', 'from-slate-200 to-slate-400'],
                            'bronze' => ['Bronze Antigo', 'from-amber-600 to-orange-800'],
                            'cyberpunk' => ['Cyber Neon', 'from-cyan-400 to-indigo-600'],
                            'rubi' => ['Rubi Luxo', 'from-rose-500 to-red-800'],
                            'esmeralda' => ['Esmeralda', 'from-emerald-400 to-teal-800'],
                            'dark' => ['Dark Ônix', 'from-slate-700 to-slate-950']
                        ];
                        foreach ($temas as $chave => [$rotulo, $gradiente]): 
                            $ativo = ($config['tema_cor'] === $chave);
                        ?>
                        <label class="relative flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition <?= $ativo ? 'border-amber-400 bg-amber-500/10 text-white' : 'border-slate-800 bg-slate-950/60 text-slate-300 hover:border-slate-700' ?>">
                            <input type="radio" name="tema_cor" value="<?= $chave ?>" <?= $ativo ? 'checked' : '' ?> onchange="atualizarTema(this.value)" class="sr-only">
                            <span class="w-4 h-4 rounded-full bg-gradient-to-tr <?= $gradiente ?> shrink-0 shadow"></span>
                            <span class="text-xs font-semibold"><?= $rotulo ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- 2. Configurações da Face CARA -->
                <div class="border-t border-slate-800 pt-5 space-y-4">
                    <div class="flex items-center gap-2 text-sm font-bold text-amber-400">
                        <span>👑</span>
                        <span>Personalização da Face: CARA</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">Nome da Face Cara:</label>
                            <input type="text" id="input-nome-cara" name="nome_cara" value="<?= htmlspecialchars($config['nome_cara']) ?>" maxlength="25"
                                oninput="atualizarNomeCara(this.value)"
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-amber-500 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">Ícone ou Emoji:</label>
                            <div class="flex gap-2">
                                <input type="text" id="input-ico-cara" name="icone_cara" value="<?= htmlspecialchars($config['icone_cara']) ?>" maxlength="4"
                                    oninput="atualizarIcoCara(this.value)"
                                    class="w-20 bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-center text-lg text-white focus:outline-none focus:border-amber-500 transition">
                                <div class="flex-1 flex items-center gap-1.5 overflow-x-auto py-1">
                                    <?php foreach (['👑', '🦁', '⚡', '🦅', '☀️', '🥇'] as $e): ?>
                                        <button type="button" onclick="setPresetEmoji('cara', '<?= $e ?>')" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-sm transition">
                                            <?= $e ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Imagem Customizada Cara -->
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">Imagem personalizada (opcional):</label>
                        <div class="flex items-center gap-3">
                            <input type="file" id="file-img-cara" accept="image/*" onchange="processarUploadImagem(event, 'cara')" class="text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-slate-200 hover:file:bg-slate-700 cursor-pointer">
                            <button type="button" onclick="removerImagem('cara')" class="text-xs text-rose-400 hover:underline">Remover</button>
                        </div>
                        <input type="hidden" id="input-img-cara" name="imagem_cara" value="<?= htmlspecialchars($config['imagem_cara']) ?>">
                    </div>
                </div>

                <!-- 3. Configurações da Face COROA -->
                <div class="border-t border-slate-800 pt-5 space-y-4">
                    <div class="flex items-center gap-2 text-sm font-bold text-amber-400">
                        <span>⭐</span>
                        <span>Personalização da Face: COROA</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">Nome da Face Coroa:</label>
                            <input type="text" id="input-nome-coroa" name="nome_coroa" value="<?= htmlspecialchars($config['nome_coroa']) ?>" maxlength="25"
                                oninput="atualizarNomeCoroa(this.value)"
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-amber-500 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">Ícone ou Emoji:</label>
                            <div class="flex gap-2">
                                <input type="text" id="input-ico-coroa" name="icone_coroa" value="<?= htmlspecialchars($config['icone_coroa']) ?>" maxlength="4"
                                    oninput="atualizarIcoCoroa(this.value)"
                                    class="w-20 bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-center text-lg text-white focus:outline-none focus:border-amber-500 transition">
                                <div class="flex-1 flex items-center gap-1.5 overflow-x-auto py-1">
                                    <?php foreach (['⭐', '🌙', '💎', '🛡️', '🍀', '🥈'] as $e): ?>
                                        <button type="button" onclick="setPresetEmoji('coroa', '<?= $e ?>')" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-sm transition">
                                            <?= $e ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Imagem Customizada Coroa -->
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">Imagem personalizada (opcional):</label>
                        <div class="flex items-center gap-3">
                            <input type="file" id="file-img-coroa" accept="image/*" onchange="processarUploadImagem(event, 'coroa')" class="text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-slate-200 hover:file:bg-slate-700 cursor-pointer">
                            <button type="button" onclick="removerImagem('coroa')" class="text-xs text-rose-400 hover:underline">Remover</button>
                        </div>
                        <input type="hidden" id="input-img-coroa" name="imagem_coroa" value="<?= htmlspecialchars($config['imagem_coroa']) ?>">
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="border-t border-slate-800 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <button type="button" onclick="restaurarPadrao()" class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-400 hover:text-white bg-slate-800/80 hover:bg-slate-800 transition">
                        Restaurar Padrões
                    </button>
                    <button type="submit" id="btn-salvar" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-sm tracking-wide shadow-xl shadow-amber-500/20 active:scale-95 transition">
                        💾 Salvar Personalização
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>

<!-- Scripts da Página de Personalização -->
<script>
    let currentPreviewRotation = 0;
    const previewCoin = document.getElementById('preview-coin');

    function alternarPreviaFace() {
        currentPreviewRotation += 180;
        previewCoin.style.transform = `rotateY(${currentPreviewRotation}deg)`;
        AppAudio.playClick();
    }

    function virarPreviaPara(deg) {
        currentPreviewRotation = deg;
        previewCoin.style.transform = `rotateY(${currentPreviewRotation}deg)`;
        AppAudio.playClick();
    }

    function atualizarTema(tema) {
        previewCoin.className = `coin coin-theme-${tema}`;
        AppAudio.playClick();
    }

    function atualizarNomeCara(val) {
        document.getElementById('preview-txt-cara').innerText = val || 'Cara';
    }

    function atualizarNomeCoroa(val) {
        document.getElementById('preview-txt-coroa').innerText = val || 'Coroa';
    }

    function atualizarIcoCara(val) {
        document.getElementById('preview-ico-cara').innerText = val || '👑';
    }

    function atualizarIcoCoroa(val) {
        document.getElementById('preview-ico-coroa').innerText = val || '⭐';
    }

    function setPresetEmoji(face, emoji) {
        if (face === 'cara') {
            document.getElementById('input-ico-cara').value = emoji;
            atualizarIcoCara(emoji);
        } else {
            document.getElementById('input-ico-coroa').value = emoji;
            atualizarIcoCoroa(emoji);
        }
        AppAudio.playClick();
    }

    // Processamento de Upload e Conversão em Base64
    function processarUploadImagem(event, face) {
        const file = event.target.files[0];
        if (!file) return;

        // Limite 500KB para caber confortavelmente em cookie
        if (file.size > 500 * 1024) {
            AppModal.alert('Arquivo Muito Grande', 'Selecione uma imagem de até 500 KB para armazenamento em cookies.', '⚠️');
            event.target.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const b64 = e.target.result;
            if (face === 'cara') {
                document.getElementById('input-img-cara').value = b64;
                document.getElementById('preview-img-cara').src = b64;
                document.getElementById('preview-img-cara-box').classList.remove('hidden');
                document.getElementById('preview-ico-cara').classList.add('hidden');
            } else {
                document.getElementById('input-img-coroa').value = b64;
                document.getElementById('preview-img-coroa').src = b64;
                document.getElementById('preview-img-coroa-box').classList.remove('hidden');
                document.getElementById('preview-ico-coroa').classList.add('hidden');
            }
            AppToast.success(`Imagem de ${face} carregada com sucesso!`);
        };
        reader.readAsDataURL(file);
    }

    function removerImagem(face) {
        if (face === 'cara') {
            document.getElementById('input-img-cara').value = '';
            document.getElementById('file-img-cara').value = '';
            document.getElementById('preview-img-cara-box').classList.add('hidden');
            document.getElementById('preview-ico-cara').classList.remove('hidden');
        } else {
            document.getElementById('input-img-coroa').value = '';
            document.getElementById('file-img-coroa').value = '';
            document.getElementById('preview-img-coroa-box').classList.add('hidden');
            document.getElementById('preview-ico-coroa').classList.remove('hidden');
        }
        AppToast.info(`Imagem de ${face} removida.`);
    }

    async function salvarPersonalizacao(e) {
        e.preventDefault();
        const form = document.getElementById('form-personalizar');
        const formData = new FormData(form);
        const dataObj = Object.fromEntries(formData.entries());

        const btnSalvar = document.getElementById('btn-salvar');
        btnSalvar.disabled = true;
        btnSalvar.innerText = 'Salvando...';

        try {
            const resp = await fetch('backend/personalizar.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(dataObj)
            });

            const data = await resp.json();

            if (data.sucesso) {
                AppToast.success('Moeda personalizada e salva em Cookies & BD!');
                AppAudio.playWin();
            } else {
                AppToast.error(data.mensagem || 'Erro ao salvar personalização.');
            }
        } catch (err) {
            console.error(err);
            AppToast.error('Erro de conexão ao salvar personalização.');
        } finally {
            btnSalvar.disabled = false;
            btnSalvar.innerText = '💾 Salvar Personalização';
        }
    }

    async function restaurarPadrao() {
        const confirmou = await AppModal.confirm(
            'Restaurar Padrão',
            'Deseja realmente voltar a moeda para as configurações originais (Cara e Coroa Dourada)?',
            'Sim, Restaurar',
            'Cancelar'
        );

        if (!confirmou) return;

        document.getElementById('input-nome-cara').value = 'Cara';
        document.getElementById('input-nome-coroa').value = 'Coroa';
        document.getElementById('input-ico-cara').value = '👑';
        document.getElementById('input-ico-coroa').value = '⭐';
        removerImagem('cara');
        removerImagem('coroa');
        atualizarNomeCara('Cara');
        atualizarNomeCoroa('Coroa');
        atualizarIcoCara('👑');
        atualizarIcoCoroa('⭐');

        const radioDourado = document.querySelector('input[name="tema_cor"][value="dourado"]');
        if (radioDourado) radioDourado.checked = true;
        atualizarTema('dourado');

        // Dispara salvamento
        salvarPersonalizacao(new Event('submit'));
    }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<!-- ==========================================================
     Modal de Anúncio de 10 Segundos
     Regra de Negócio CORE: "a cada duas troca de escolha um modal de anuncio de 10 segundos"
     ========================================================== -->
<div id="ad-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/85 backdrop-blur-md">
    <div class="relative bg-gradient-to-b from-slate-900 to-slate-950 border border-amber-500/30 text-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl overflow-hidden">
        
        <!-- Faixa de aviso de auditoria e regra -->
        <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                Regra de Negócio CORE: Anúncio Obrigatório
            </span>
            <div class="text-xs text-slate-400 font-mono">
                Troca de Escolha
            </div>
        </div>

        <!-- Banner Publicitário Fictício Atraente -->
        <div class="bg-gradient-to-r from-indigo-900 via-purple-900 to-pink-900 rounded-2xl p-6 text-center shadow-inner relative overflow-hidden mb-6 border border-white/10">
            <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
            <div class="text-xs uppercase tracking-widest text-indigo-200 font-bold mb-1">Patrocinador Oficial</div>
            <h4 class="text-2xl font-black tracking-tight text-white mb-2">💎 SuperCrypto Prime</h4>
            <p class="text-sm text-indigo-100 max-w-xs mx-auto mb-4">
                Dobre suas chances! A plataforma descentralizada mais segura para rolagens e apostas justas com auditoria em tempo real.
            </p>
            <div class="inline-block px-4 py-2 bg-white text-indigo-950 font-bold rounded-xl text-xs shadow-lg uppercase tracking-wider">
                Bônus de Boas-Vindas 100%
            </div>
        </div>

        <!-- Cronômetro de 10 Segundos -->
        <div class="bg-slate-800/60 rounded-2xl p-4 border border-slate-700/60 flex items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-amber-500/20 border-2 border-amber-400 flex items-center justify-center font-black text-amber-300 text-xl font-mono shadow-inner" id="ad-countdown-number">
                    10
                </div>
                <div>
                    <div class="text-sm font-semibold text-white">Temporizador de Anúncio</div>
                    <div class="text-xs text-slate-400">Obrigatório 10 segundos para prosseguir</div>
                </div>
            </div>
            <div class="text-right">
                <span class="text-xs text-amber-400 font-medium">Auto-bloqueio</span>
            </div>
        </div>

        <!-- Barra de Progresso dos 10 Segundos -->
        <div class="w-full bg-slate-800 h-2 rounded-full overflow-hidden mb-6">
            <div id="ad-progress-bar" class="bg-gradient-to-r from-amber-500 to-amber-300 h-full transition-all duration-1000 ease-linear" style="width: 100%;"></div>
        </div>

        <!-- Botão de Fechar (Desabilitado até 10s passarem) -->
        <div class="flex justify-center">
            <button id="ad-close-btn" disabled class="w-full py-3.5 px-6 rounded-xl font-bold text-sm transition-all duration-300 bg-slate-800 text-slate-500 cursor-not-allowed">
                Aguarde 10s...
            </button>
        </div>
    </div>
</div>

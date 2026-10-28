/**
 * ==========================================================
 * Utilitários do Frontend: Áudio Sintetizado, Toasts, Modais e Moeda
 * Arquivo: assets/js/app.js
 * ==========================================================
 */

// ----------------------------------------------------------
// 1. Áudio Sintetizado com Web Audio API (Offline, 100% confiável)
// ----------------------------------------------------------
const AppAudio = {
    ctx: null,
    init() {
        if (!this.ctx) {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (AudioContext) {
                this.ctx = new AudioContext();
            }
        }
        if (this.ctx && this.ctx.state === 'suspended') {
            this.ctx.resume();
        }
    },
    // Som metálico de giro no ar
    playCoinFlip() {
        try {
            this.init();
            if (!this.ctx) return;
            const now = this.ctx.currentTime;
            
            // Sequência de pequenos "clinks" simulando rotação rápida no ar
            for (let i = 0; i < 6; i++) {
                const osc = this.ctx.createOscillator();
                const gain = this.ctx.createGain();
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(800 + (i * 220), now + (i * 0.1));
                gain.gain.setValueAtTime(0.12, now + (i * 0.1));
                gain.gain.exponentialRampToValueAtTime(0.001, now + (i * 0.1) + 0.08);
                osc.connect(gain);
                gain.connect(this.ctx.destination);
                osc.start(now + (i * 0.1));
                osc.stop(now + (i * 0.1) + 0.09);
            }
        } catch (e) {
            console.warn('Erro de áudio:', e);
        }
    },
    // Som da moeda pousando na mão ou mesa
    playCoinLand() {
        try {
            this.init();
            if (!this.ctx) return;
            const now = this.ctx.currentTime;
            const osc = this.ctx.createOscillator();
            const gain = this.ctx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(1200, now);
            osc.frequency.exponentialRampToValueAtTime(450, now + 0.25);
            gain.gain.setValueAtTime(0.3, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 0.3);
            osc.connect(gain);
            gain.connect(this.ctx.destination);
            osc.start(now);
            osc.stop(now + 0.35);
        } catch (e) {}
    },
    // Som de Vitória (Acerto)
    playWin() {
        try {
            this.init();
            if (!this.ctx) return;
            const notes = [523.25, 659.25, 783.99, 1046.50]; // Acorde C Maior
            const now = this.ctx.currentTime;
            notes.forEach((freq, idx) => {
                const osc = this.ctx.createOscillator();
                const gain = this.ctx.createGain();
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(freq, now + (idx * 0.08));
                gain.gain.setValueAtTime(0.2, now + (idx * 0.08));
                gain.gain.exponentialRampToValueAtTime(0.001, now + (idx * 0.08) + 0.4);
                osc.connect(gain);
                gain.connect(this.ctx.destination);
                osc.start(now + (idx * 0.08));
                osc.stop(now + (idx * 0.08) + 0.45);
            });
        } catch (e) {}
    },
    // Som de Derrota (Erro)
    playLose() {
        try {
            this.init();
            if (!this.ctx) return;
            const notes = [440, 370, 311];
            const now = this.ctx.currentTime;
            notes.forEach((freq, idx) => {
                const osc = this.ctx.createOscillator();
                const gain = this.ctx.createGain();
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(freq, now + (idx * 0.12));
                gain.gain.setValueAtTime(0.15, now + (idx * 0.12));
                gain.gain.exponentialRampToValueAtTime(0.001, now + (idx * 0.12) + 0.25);
                osc.connect(gain);
                gain.connect(this.ctx.destination);
                osc.start(now + (idx * 0.12));
                osc.stop(now + (idx * 0.12) + 0.26);
            });
        } catch (e) {}
    },
    playClick() {
        try {
            this.init();
            if (!this.ctx) return;
            const osc = this.ctx.createOscillator();
            const gain = this.ctx.createGain();
            osc.frequency.setValueAtTime(600, this.ctx.currentTime);
            gain.gain.setValueAtTime(0.05, this.ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, this.ctx.currentTime + 0.05);
            osc.connect(gain);
            gain.connect(this.ctx.destination);
            osc.start();
            osc.stop(this.ctx.currentTime + 0.05);
        } catch (e) {}
    }
};

// ----------------------------------------------------------
// 2. Sistema de Notificações Toast
// Regra Global: "Retorne sempre as mensagens de erros de forma claras na interface para o usuário (TOAST)"
// ----------------------------------------------------------
const AppToast = {
    container: null,
    getContainer() {
        if (!this.container) {
            let el = document.getElementById('toast-container');
            if (!el) {
                el = document.createElement('div');
                el.id = 'toast-container';
                el.className = 'fixed bottom-5 right-5 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none px-4 sm:px-0';
                document.body.appendChild(el);
            }
            this.container = el;
        }
        return this.container;
    },
    show(message, type = 'info', duration = 4000) {
        const container = this.getContainer();
        const toast = document.createElement('div');
        toast.className = 'pointer-events-auto flex items-center gap-3 p-4 rounded-xl shadow-2xl text-sm font-medium transition-all duration-300 transform translate-y-4 opacity-0 border';

        let icon = 'ℹ️';
        let bg = 'bg-slate-900/95 text-white border-slate-700';

        if (type === 'success') {
            icon = '✅';
            bg = 'bg-emerald-950/95 text-emerald-100 border-emerald-600/50';
        } else if (type === 'error') {
            icon = '⚠️';
            bg = 'bg-rose-950/95 text-rose-100 border-rose-600/50';
        } else if (type === 'warning') {
            icon = '⚡';
            bg = 'bg-amber-950/95 text-amber-100 border-amber-600/50';
        }

        toast.className += ` ${bg}`;
        toast.innerHTML = `
            <span class="text-xl shrink-0">${icon}</span>
            <div class="flex-1 leading-snug">${message}</div>
            <button class="shrink-0 text-white/60 hover:text-white p-1 ml-2 text-xs" onclick="this.parentElement.remove()">✕</button>
        `;

        container.appendChild(toast);

        // Animação de entrada
        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-4', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
        });

        // Auto remove
        setTimeout(() => {
            toast.classList.remove('translate-y-0', 'opacity-100');
            toast.classList.add('translate-y-4', 'opacity-0');
            setTimeout(() => toast.remove(), 350);
        }, duration);
    },
    success(msg) { this.show(msg, 'success'); },
    error(msg) { this.show(msg, 'error', 5000); },
    warning(msg) { this.show(msg, 'warning'); },
    info(msg) { this.show(msg, 'info'); }
};

// ----------------------------------------------------------
// 3. Sistema de Modal Customizado
// Regra Global: "sempre trate as mensagens de caixa de mensagens nativas do navegador em um MODAL"
// ----------------------------------------------------------
const AppModal = {
    // Alerta customizado substituindo window.alert
    alert(titulo, mensagem, icone = 'ℹ️') {
        return new Promise((resolve) => {
            const overlay = document.createElement('div');
            overlay.className = 'fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm animate-pop';
            overlay.innerHTML = `
                <div class="bg-slate-900 border border-slate-700 text-white rounded-2xl p-6 max-w-md w-full shadow-2xl">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="text-3xl">${icone}</span>
                        <h3 class="text-xl font-bold">${titulo}</h3>
                    </div>
                    <div class="text-slate-300 text-sm mb-6 leading-relaxed">
                        ${mensagem}
                    </div>
                    <div class="flex justify-end">
                        <button id="modal-alert-ok" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-semibold transition shadow-lg shadow-amber-500/20">
                            Entendido
                        </button>
                    </div>
                </div>
            `;
            document.body.appendChild(overlay);
            AppAudio.playClick();

            document.getElementById('modal-alert-ok').onclick = () => {
                overlay.remove();
                resolve(true);
            };
        });
    },

    // Confirmação customizada substituindo window.confirm
    confirm(titulo, mensagem, textoConfirmar = 'Confirmar', textoCancelar = 'Cancelar') {
        return new Promise((resolve) => {
            const overlay = document.createElement('div');
            overlay.className = 'fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm animate-pop';
            overlay.innerHTML = `
                <div class="bg-slate-900 border border-slate-700 text-white rounded-2xl p-6 max-w-md w-full shadow-2xl">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="text-3xl">❓</span>
                        <h3 class="text-xl font-bold">${titulo}</h3>
                    </div>
                    <div class="text-slate-300 text-sm mb-6 leading-relaxed">
                        ${mensagem}
                    </div>
                    <div class="flex justify-end gap-3">
                        <button id="modal-confirm-cancel" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium transition">
                            ${textoCancelar}
                        </button>
                        <button id="modal-confirm-ok" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-semibold transition shadow-lg shadow-rose-600/20">
                            ${textoConfirmar}
                        </button>
                    </div>
                </div>
            `;
            document.body.appendChild(overlay);
            AppAudio.playClick();

            document.getElementById('modal-confirm-cancel').onclick = () => {
                overlay.remove();
                resolve(false);
            };
            document.getElementById('modal-confirm-ok').onclick = () => {
                overlay.remove();
                resolve(true);
            };
        });
    }
};

// Substitui nativamente window.alert e window.confirm caso sejam chamados
window.alert = (msg) => AppModal.alert('Aviso', msg, '⚠️');

// ----------------------------------------------------------
// 4. Modal de Anúncio de 10 Segundos
// Regra CORE: "a cada duas troca de escolha um modal de anuncio de 10 segundos"
// ----------------------------------------------------------
const AppAdModal = {
    timerInterval: null,
    restante: 10,
    isOpen: false,

    start(onComplete = null) {
        if (this.isOpen) return;
        this.isOpen = true;
        this.restante = 10;

        const modalEl = document.getElementById('ad-modal');
        const countdownEl = document.getElementById('ad-countdown-number');
        const closeBtn = document.getElementById('ad-close-btn');
        const progressBar = document.getElementById('ad-progress-bar');

        if (!modalEl) {
            console.warn('Elemento de modal de anúncio não encontrado.');
            this.isOpen = false;
            if (onComplete) onComplete();
            return;
        }

        modalEl.classList.remove('hidden');
        modalEl.classList.add('flex');
        closeBtn.disabled = true;
        closeBtn.classList.add('opacity-50', 'cursor-not-allowed');
        closeBtn.innerHTML = `Aguarde ${this.restante}s...`;
        countdownEl.innerText = this.restante;
        if (progressBar) progressBar.style.width = '100%';

        // Registra log de exibição de anúncio
        fetch('backend/registrar_log.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                tipo_acao: 'ANUNCIO_EXIBIDO',
                descricao: 'Modal de anúncio obrigatório de 10s disparado após troca de escolha.'
            })
        }).catch(() => {});

        clearInterval(this.timerInterval);
        this.timerInterval = setInterval(() => {
            this.restante--;
            countdownEl.innerText = this.restante;
            if (progressBar) {
                const perc = (this.restante / 10) * 100;
                progressBar.style.width = `${perc}%`;
            }

            if (this.restante > 0) {
                closeBtn.innerHTML = `Aguarde ${this.restante}s...`;
            } else {
                // Fim dos 10 segundos
                clearInterval(this.timerInterval);
                closeBtn.disabled = false;
                closeBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                closeBtn.classList.add('bg-emerald-500', 'hover:bg-emerald-400', 'text-slate-950');
                closeBtn.innerHTML = '✕ Fechar Anúncio';
                AppToast.info('Você já pode fechar o anúncio e continuar jogando!');
            }
        }, 1000);

        closeBtn.onclick = () => {
            if (this.restante <= 0) {
                modalEl.classList.add('hidden');
                modalEl.classList.remove('flex');
                this.isOpen = false;
                // Registra conclusão
                fetch('backend/registrar_log.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        tipo_acao: 'ANUNCIO_CONCLUIDO',
                        descricao: 'Usuário cumpriu os 10s obrigatórios do anúncio e fechou o modal.'
                    })
                }).catch(() => {});
                if (onComplete) onComplete();
            }
        };
    }
};

// ----------------------------------------------------------
// 5. Utilitários de Cookies Locais
// Regra: "os registros devem ser feitos todos salvos na maquina local recuperados por cookies"
// ----------------------------------------------------------
const CookieHelper = {
    set(name, value, days = 30) {
        const d = new Date();
        d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
        const expires = "expires=" + d.toUTCString();
        const jsonStr = typeof value === 'object' ? JSON.stringify(value) : value;
        document.cookie = `${name}=${encodeURIComponent(jsonStr)};${expires};path=/;SameSite=Lax`;
    },
    get(name, defaultValue = null) {
        const cname = name + "=";
        const decodedCookie = decodeURIComponent(document.cookie);
        const ca = decodedCookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i].trim();
            if (c.indexOf(cname) === 0) {
                const raw = c.substring(cname.length, c.length);
                try {
                    return JSON.parse(raw);
                } catch (e) {
                    return raw;
                }
            }
        }
        return defaultValue;
    }
};

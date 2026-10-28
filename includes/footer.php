    </main>

    <!-- Rodapé -->
    <footer class="border-t border-slate-800/80 bg-slate-900/40 py-6 mt-12 text-slate-400 text-xs text-center">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span>🪙</span>
                <span class="font-semibold text-slate-300">Cara e Coroa</span>
                <span>— Sistema com Auditoria e Cookies</span>
            </div>
            <div class="flex items-center gap-4 text-slate-400">
                <span>Stack: PHP • MySQL PDO • Tailwind CSS</span>
                <a href="https://github.com/felipejhfreitas-cyber/Cara-e-coroa-.git" target="_blank" rel="noopener" class="hover:text-amber-400 transition underline underline-offset-2">
                    Repositório GitHub
                </a>
            </div>
        </div>
    </footer>

    <!-- Contêiner de Toasts (Regra: Retorne sempre as mensagens de erros de forma claras na interface para o usuário) -->
    <div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none px-4 sm:px-0"></div>

    <!-- Inclusão do Modal de Anúncio de 10s (Regra CORE: a cada duas trocas de escolha) -->
    <?php require_once __DIR__ . '/modal_anuncio.php'; ?>

    <!-- Scripts JavaScript Base -->
    <script src="assets/js/app.js"></script>
    <script>
        // Menu Mobile Toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }
    </script>
</body>
</html>

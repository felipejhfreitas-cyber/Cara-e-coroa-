<?php
/**
 * ==========================================================
 * Cabeçalho e Navegação Responsiva (MobileFirst)
 * Arquivo: includes/header.php
 * ==========================================================
 */

require_once __DIR__ . '/../config.php';

$paginaAtual = basename($_SERVER['PHP_SELF'], '.php');
$moedaConfig = obterPersonalizacaoMoeda();
$bdAtivo = verificarConexaoBD();
?>
<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($tituloPagina) ? htmlspecialchars($tituloPagina) . ' | ' : '' ?>Cara e Coroa</title>
    <!-- Tailwind CSS CDN oficial -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309'
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="assets/css/moeda.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .font-display {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col antialiased selection:bg-amber-500 selection:text-slate-950">

    <!-- Barra de Navegação -->
    <header class="sticky top-0 z-40 bg-slate-900/80 backdrop-blur-md border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logotipo -->
                <a href="index.php" class="flex items-center gap-2.5 group">
                    <span class="text-2xl transform transition group-hover:rotate-12">🪙</span>
                    <span class="font-display font-extrabold text-xl tracking-tight bg-gradient-to-r from-amber-400 via-amber-200 to-amber-500 bg-clip-text text-transparent">
                        Cara ou Coroa
                    </span>
                </a>

                <!-- Navegação Desktop -->
                <nav class="hidden md:flex items-center gap-1">
                    <a href="index.php" class="px-3.5 py-2 rounded-xl text-sm font-medium transition <?= $paginaAtual === 'index' ? 'bg-amber-500/15 text-amber-400 border border-amber-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' ?>">
                        🎮 Jogar
                    </a>
                    <a href="personalizar.php" class="px-3.5 py-2 rounded-xl text-sm font-medium transition <?= $paginaAtual === 'personalizar' ? 'bg-amber-500/15 text-amber-400 border border-amber-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' ?>">
                        🎨 Personalizar Moeda
                    </a>
                    <a href="dashboard.php" class="px-3.5 py-2 rounded-xl text-sm font-medium transition <?= $paginaAtual === 'dashboard' ? 'bg-amber-500/15 text-amber-400 border border-amber-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' ?>">
                        📊 Dashboard
                    </a>
                    <a href="historico.php" class="px-3.5 py-2 rounded-xl text-sm font-medium transition <?= $paginaAtual === 'historico' ? 'bg-amber-500/15 text-amber-400 border border-amber-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' ?>">
                        📜 Histórico & Auditoria
                    </a>
                </nav>

                <!-- Status de Armazenamento -->
                <div class="hidden lg:flex items-center gap-3 text-xs">
                    <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-800/80 border border-slate-700/80 text-slate-300" title="Registros locais salvos e recuperados por cookies">
                        <span>🍪</span>
                        <span>Cookies: <strong class="text-amber-400">Ativos</strong></span>
                    </div>
                    <?php if ($bdAtivo): ?>
                        <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-950/60 border border-emerald-700/50 text-emerald-300">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>MySQL PDO</span>
                        </div>
                    <?php else: ?>
                        <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-950/60 border border-amber-700/50 text-amber-300" title="Executando com persistência em cookies locais">
                            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                            <span>Modo Cookie Local</span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Botão Menu Mobile -->
                <button id="mobile-menu-btn" class="md:hidden p-2 rounded-xl bg-slate-800 text-slate-300 hover:text-white focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Menu Mobile Dropdown -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-slate-800 bg-slate-900 px-4 pt-2 pb-4 space-y-1">
            <a href="index.php" class="block px-3 py-2 rounded-lg text-base font-medium <?= $paginaAtual === 'index' ? 'bg-amber-500/20 text-amber-400' : 'text-slate-300 hover:bg-slate-800' ?>">
                🎮 Jogar
            </a>
            <a href="personalizar.php" class="block px-3 py-2 rounded-lg text-base font-medium <?= $paginaAtual === 'personalizar' ? 'bg-amber-500/20 text-amber-400' : 'text-slate-300 hover:bg-slate-800' ?>">
                🎨 Personalizar Moeda
            </a>
            <a href="dashboard.php" class="block px-3 py-2 rounded-lg text-base font-medium <?= $paginaAtual === 'dashboard' ? 'bg-amber-500/20 text-amber-400' : 'text-slate-300 hover:bg-slate-800' ?>">
                📊 Dashboard
            </a>
            <a href="historico.php" class="block px-3 py-2 rounded-lg text-base font-medium <?= $paginaAtual === 'historico' ? 'bg-amber-500/20 text-amber-400' : 'text-slate-300 hover:bg-slate-800' ?>">
                📜 Histórico & Auditoria
            </a>
            <div class="pt-3 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
                <span>🍪 Cookies Locais: Ativos</span>
                <span><?= $bdAtivo ? '🟢 MySQL PDO' : '🟡 Modo Cookies' ?></span>
            </div>
        </div>
    </header>

    <!-- Conteúdo Principal -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">

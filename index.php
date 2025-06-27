<?php
session_start();
require 'config/config.php';

$error = $_GET['error'] ?? null;

// Si déjà connecté
if (isset($_SESSION['pseudo'])) {
    header("Location: pages/carte.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <title>Connexion Admin - Système de Gestion de Marché</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#3B82F6',
                            light: '#60A5FA',
                            dark: '#2563EB'
                        },
                        secondary: {
                            DEFAULT: '#10B981',
                            light: '#34D399',
                            dark: '#059669'
                        },
                        market: {
                            DEFAULT: '#F59E0B',
                            light: '#FBBF24',
                            dark: '#D97706',
                            vibrant: '#FF6B35'
                        },
                        fruits: {
                            DEFAULT: '#FF9F1C',
                            light: '#FFBF69',
                            dark: '#E88B0C'
                        },
                        vegetables: {
                            DEFAULT: '#2EC4B6',
                            light: '#CBF3F0',
                            dark: '#1A9B8F'
                        }
                    },
                    backgroundImage: {
                        'market-pattern': "url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23f59e0b' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')",
                        'market-pattern-dark': "url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23fbbf24' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-full bg-gray-50 dark:bg-gray-900 flex flex-col bg-market-pattern dark:bg-market-pattern-dark">
    <!-- Theme Toggle -->
    <div class="absolute top-4 right-4">
        <button id="themeToggle" class="p-2 rounded-full bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm shadow-md hover:bg-vegetables-light hover:dark:bg-vegetables-dark transition-colors duration-300">
            <svg id="sunIcon" class="w-5 h-5 text-yellow-500 hidden" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"></path>
            </svg>
            <svg id="moonIcon" class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
            </svg>
        </button>
    </div>

    <main class="flex-grow flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-8 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm rounded-xl shadow-2xl p-10 border-2 border-market-light dark:border-market-dark">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gradient-to-br from-market-vibrant to-fruits-light shadow-lg mb-4">
                    <svg class="h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h2 class="mt-2 text-3xl font-extrabold text-gray-900 dark:text-white bg-gradient-to-r from-market-dark to-fruits-dark bg-clip-text text-transparent">
                    Système de Gestion de Marché
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Connectez-vous à votre espace administrateur
                </p>
            </div>

            <?php if ($error): ?>
                <div class="rounded-lg bg-red-50/80 dark:bg-red-900/30 p-4 border-l-4 border-red-500 dark:border-red-400 backdrop-blur-sm">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                                Erreur de connexion
                            </h3>
                            <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                <?= htmlspecialchars($error) ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form class="mt-8 space-y-6" method="post" action="api/login.php">
                <div class="rounded-md shadow-sm space-y-4">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Nom d'utilisateur
                        </label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-market-dark dark:text-market-light" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input id="username" name="username" type="text" required class="block w-full pl-10 pr-3 py-2 border-2 border-market-light dark:border-market-dark rounded-lg bg-white/70 dark:bg-gray-700/70 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-market-500 focus:border-transparent focus:dark:ring-market-400 transition-all duration-200 sm:text-sm" placeholder="Votre pseudo">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Mot de passe
                        </label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-market-dark dark:text-market-light" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input id="password" name="password" type="password" required class="block w-full pl-10 pr-3 py-2 border-2 border-market-light dark:border-market-dark rounded-lg bg-white/70 dark:bg-gray-700/70 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-market-500 focus:border-transparent focus:dark:ring-market-400 transition-all duration-200 sm:text-sm" placeholder="Votre mot de passe">
                        </div>
                    </div>
                </div>

                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-xl text-white bg-gradient-to-r from-market-dark to-fruits-dark hover:from-market-vibrant hover:to-fruits-light focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-market-500 shadow-lg hover:shadow-market/30 transition-all duration-300">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-orange-200 group-hover:text-white transition-colors duration-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        Se connecter
                    </button>
                </div>
            </form>
        </div>
    </main>

    <footer class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">
        &copy; <?= date('Y') ?> Système de Gestion de Marché. Tous droits réservés.
    </footer>

    <script>
        // Gestion du thème
        const themeToggle = document.getElementById('themeToggle');
        const sunIcon = document.getElementById('sunIcon');
        const moonIcon = document.getElementById('moonIcon');
        
        // Vérifier le thème préféré de l'utilisateur
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            sunIcon.classList.remove('hidden');
            moonIcon.classList.add('hidden');
        } else {
            document.documentElement.classList.remove('dark');
            sunIcon.classList.add('hidden');
            moonIcon.classList.remove('hidden');
        }
        
        // Basculer entre les thèmes
        themeToggle.addEventListener('click', function() {
            // Basculer l'icône
            sunIcon.classList.toggle('hidden');
            moonIcon.classList.toggle('hidden');
            
            // Si défini dans le localStorage
            if (localStorage.getItem('color-theme')) {
                if (localStorage.getItem('color-theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                }
            } else {
                // Si pas défini
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            }
        });
    </script>
</body>
</html>
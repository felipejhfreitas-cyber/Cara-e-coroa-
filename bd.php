<?php
/**
 * ==========================================================
 * Conexão com o Banco de Dados MySQL via PDO
 * Arquivo: bd.php
 * Segue as regras globais de segurança (PDO, Prepared Statements)
 * ==========================================================
 */

// Configurações do Banco de Dados
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'cara_e_coroa');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

/**
 * Retorna a instância PDO de conexão com o banco de dados.
 * Implementa conexão segura e captura amigável de falhas.
 * 
 * @return PDO|null
 */
function obterConexaoBD(): ?PDO {
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $opcoes = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false, // Garante prepared statements nativos
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $opcoes);
        return $pdo;
    } catch (PDOException $e) {
        // Registra o erro internamente sem expor credenciais na tela
        error_log("Erro de Conexão PDO: " . $e->getMessage());
        return null;
    }
}

/**
 * Verifica se a conexão com o banco de dados está ativa.
 * 
 * @return bool
 */
function verificarConexaoBD(): bool {
    return obterConexaoBD() !== null;
}

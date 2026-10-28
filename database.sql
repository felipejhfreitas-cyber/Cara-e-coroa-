-- ==========================================================
-- Banco de Dados: cara_e_coroa
-- Estrutura para o sistema Cara e Coroa com Auditoria e Cookies
-- ==========================================================

CREATE DATABASE IF NOT EXISTS `cara_e_coroa` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `cara_e_coroa`;

-- ----------------------------------------------------------
-- 1. Tabela de Histórico de Jogos (Rolagens de Moeda)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `historico_jogos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `sessao_id` VARCHAR(100) NOT NULL,
    `escolha` ENUM('cara', 'coroa') NOT NULL,
    `resultado` ENUM('cara', 'coroa') NOT NULL,
    `acertou` TINYINT(1) NOT NULL COMMENT '1 = Acertou (Vitória), 0 = Errou (Derrota)',
    `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_sessao` (`sessao_id`),
    INDEX `idx_resultado` (`resultado`),
    INDEX `idx_acertou` (`acertou`),
    INDEX `idx_criado_em` (`criado_em`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 2. Tabela de Personalização da Moeda
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `personalizacao_moeda` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `sessao_id` VARCHAR(100) NOT NULL UNIQUE,
    `nome_cara` VARCHAR(50) NOT NULL DEFAULT 'Cara',
    `nome_coroa` VARCHAR(50) NOT NULL DEFAULT 'Coroa',
    `tema_cor` VARCHAR(30) NOT NULL DEFAULT 'dourado',
    `icone_cara` VARCHAR(100) NOT NULL DEFAULT '👑',
    `icone_coroa` VARCHAR(100) NOT NULL DEFAULT '⭐',
    `imagem_cara` MEDIUMTEXT NULL COMMENT 'Base64 ou URL de imagem customizada para Cara',
    `imagem_coroa` MEDIUMTEXT NULL COMMENT 'Base64 ou URL de imagem customizada para Coroa',
    `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `atualizado_em` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_sessao_pers` (`sessao_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 3. Tabela de Logs de Auditoria (Regra de Negócio CORE)
-- Registra todas as ações e alterações realizadas por usuários
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `logs_auditoria` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `sessao_id` VARCHAR(100) NOT NULL,
    `tipo_acao` VARCHAR(50) NOT NULL COMMENT 'JOGO_REALIZADO, TROCA_ESCOLHA, ANUNCIO_EXIBIDO, MOEDA_PERSONALIZADA, HISTORICO_LIMPO, ERRO_SISTEMA',
    `descricao` TEXT NOT NULL,
    `dados_anteriores` JSON NULL COMMENT 'Valores antes da alteração',
    `dados_novos` JSON NULL COMMENT 'Novos valores gravados',
    `ip_origem` VARCHAR(45) DEFAULT '127.0.0.1',
    `user_agent` TEXT NULL,
    `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_tipo_acao` (`tipo_acao`),
    INDEX `idx_log_sessao` (`sessao_id`),
    INDEX `idx_log_data` (`criado_em`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

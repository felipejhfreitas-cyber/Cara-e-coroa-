# 🪙 Cara e Coroa — Sistema Web Oficial

Sistema completo de **Cara ou Coroa** desenvolvido de acordo com as especificações e regras de negócio do repositório [felipejhfreitas-cyber/Cara-e-coroa-](https://github.com/felipejhfreitas-cyber/Cara-e-coroa-.git).

---

## 🎯 Atendimento aos Requisitos e Regras do Projeto

| Requisito / Regra | Status | Descrição da Implementação |
|---|---|---|
| **Site de Cara ou Coroa para rolagem** | Concluído | Moeda 3D interativa com física realista de giro, sombras dinâmicas e síntese de áudio metálico via Web Audio API. |
| **Personalização das Moedas** | Concluído | Painel completo para definir nomes customizados para as faces, ícones/emojis, temas visuais (Dourado, Prata, Bronze, Cyberpunk, Rubi, Esmeralda, Dark) e upload de imagem própria em base64. |
| **Registros salvos localmente e recuperados por cookies** | Concluído | Todos os jogos, personalizações, contagens de trocas e logs de auditoria são salvos em cookies (`cc_historico_jogos`, `cc_personalizacao`, `cc_trocas_escolha`, `cc_logs_auditoria`) e recuperados dinamicamente na máquina local. |
| **Dashboard de Uso dedicado** | Concluído | Página dedicada (`dashboard.php`) com taxa de acerto/vitória (%), gráficos de proporção Cara vs Coroa, contagem de vitórias e derrotas, maior sequência (streak) e histórico recente. |
| **Verificação: Escolha equivale ao resultado** | Concluído | Validação explícita da aposta do usuário contra o resultado sorteado (`escolha === resultado`), com feedback visual imediato (Vitória/Derrota) e sonoro. |
| **Regra CORE: Logs de Auditoria para auditorias futuras** | Concluído | Sistema de auditoria completo que registra toda alteração ou evento (jogada, troca de aposta, disparo de anúncio, personalização e limpeza de histórico) tanto em MySQL (`logs_auditoria`) quanto em cookies locais. |
| **Regra CORE: Anúncio de 10 segundos a cada 2 trocas de escolha** | Concluído | Contador de trocas de escolha que, a cada 2 alternâncias, dispara automaticamente um Modal com temporizador de 10s bloqueando a tela até a conclusão obrigatória. |
| **Regra Global: PDO com Prepared Statements** | Concluído | Arquivo `bd.php` isolado utilizando conexão PDO, Prepared Statements contra SQL Injection e suporte a UTF-8 mb4. |
| **Regra Global: Arquitetura desacoplada (Não Monolito)** | Concluído | Separação estrita em camadas: `bd.php`, `config.php`, `backend/` isolado para cada rota da API, `includes/` para componentes e views em HTML5/PHP. |
| **Regra Global: Tailwind CSS Responsivo (MobileFirst)** | Concluído | Layout moderno estilizado em Tailwind CSS, priorizando telas móveis e se adaptando perfeitamente a tablets e desktops. |
| **Regra Global: Mensagens de erro claras (TOAST)** | Concluído | Sistema nativo de notificações Toast com animações suaves e ícones temáticos para todas as respostas. |
| **Regra Global: Diálogos nativos em MODAL** | Concluído | Substituição de `alert()` e `confirm()` nativos do navegador por Modais customizados elegantes em Tailwind. |

---

## 📂 Estrutura de Arquivos

```text
├── assets/
│   ├── css/
│   │   └── moeda.css            # Estilos 3D, perspectiva, bordas e gradientes metálicos
│   └── js/
│       └── app.js               # Síntese Web Audio, Toasts, Modais e controlador da Moeda
├── backend/
│   ├── jogar.php                # Processa sorteio, verificação, gravações em BD e cookies
│   ├── trocar_escolha.php       # Contador de trocas e disparo do anúncio de 10s
│   ├── personalizar.php         # Salva configuração customizada com log de auditoria
│   ├── limpar_historico.php     # Limpeza de registros com rastreamento auditável
│   └── registrar_log.php        # Endpoint de auditoria para eventos do frontend
├── includes/
│   ├── header.php               # Barra de navegação responsiva e status do sistema
│   ├── footer.php               # Rodapé e carregamento de scripts
│   └── modal_anuncio.php        # Modal obrigatório de 10s com temporizador regressivo
├── bd.php                       # Conexão PDO segura com MySQL
├── config.php                   # Sessões nativas, helpers de cookies e auditoria
├── database.sql                 # Script SQL de criação das tabelas e índices
├── index.php                    # Página principal do jogo (PHP)
├── personalizar.php             # Página de personalização da moeda (PHP)
├── dashboard.php                # Dashboard dedicado de métricas de uso (PHP)
├── historico.php                # Histórico de jogadas e tabela de auditoria (PHP)
├── index.html                   # Versão Web Standalone (Pode ser aberta com 2 cliques)
├── abrir_no_navegador.bat       # Atalho para abrir no navegador
├── iniciar_servidor_php.bat     # Inicializador do servidor embutido do PHP
└── requisitos.md                # Requisitos originais do repositório
```

---

## 🚀 Como Executar

### Opção 1: Execução Direta no Navegador (Sem servidor prévio)
1. Dê dois cliques no arquivo `abrir_no_navegador.bat` (ou abra `index.html` no Chrome / Edge).
2. O sistema executará imediatamente todas as funcionalidades com armazenamento em cookies e Web Audio API.

### Opção 2: Com Servidor PHP Local
1. Dê dois cliques em `iniciar_servidor_php.bat` (ou execute no terminal: `php -S localhost:8080`).
2. Acesse: `http://localhost:8080/index.php`.

### Opção 3: Com XAMPP / Laragon / WAMP
1. Copie a pasta do projeto para o diretório de sites do XAMPP:
   `C:\xampp\htdocs\cara_e_coroa\`
2. No phpMyAdmin ou terminal MySQL, execute o arquivo `database.sql` para criar o banco de dados.
3. Inicie os serviços Apache e MySQL no painel do XAMPP.
4. Acesse no navegador: `http://localhost/cara_e_coroa/`.

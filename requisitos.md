# Cara e Coroa

## Objetivos
- criar um site de cara ou coroa para rolagem
- criar uma opção em que eu possa personalizar as moedas.
- os registros devem ser feitos todos salvos na maquina local recuperados por cookies
- ter uma area dedicada tipo um dashboard de uso de cara ou coroa.
- verificar se a escolha equivale ao resultado

### Stack Tecnlógico
- Backend: PHP estruturado com sessões nativas
- Banco de dados: MySQL (PDO para segurança)
- Frontend: HTML5, PHP, CSS, Tailwind CSS

#### Regras de negócio (CORE)
O sistema deve ter uma página de históricos e manter sempre os logs de qualquer alteração feita por 
qualquer usuário, para auditorias futuras.
- a cada duas troca de escolha um modal de anuncio de 10 segundos
- verificar se a escolha equivale ao resultado
  



##### Regras Globais
- Use sempre PDO para conexão e queries no MySQL para evitar SQL Injections
- Mantenha o código limpo e comente apenas logicas complexas.
- Separe os arquivos de forma lógica: um arquivo para conexão com a base (bd.php) e scripts de backend isolados e views em HTML5/PHP, nunca faça o sistema como um monolito, deixe sempre separados todas as regras para facilitar os futuros upgrades.
 - Estilize as telas em Tailwind de forma responsiva priorizando o MobileFrist.
 - Retorne sempre as mensagens de erros de forma claras na interface para o usuário (TOAST)
 - sempre trate as mensagens de caixa de mensagens nativas do navegador em um MODAL

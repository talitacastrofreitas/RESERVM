# 🏢 ReservM - Sistema de Gestão & Agendamento de Reservas
O **ReservM** é uma aplicação corporativa robusta desenvolvida em **PHP** e **Bootstrap** voltada ao gerenciamento, solicitação e aprovação de reservas de salas, espaços físicos ou recursos organizacionais.
O projeto conta com uma arquitetura de segurança reforçada, suporte a variáveis de ambiente (.env), autoloading com Composer e integração com banco de dados **Microsoft SQL Server**.

---
## 🎯 Funcionalidades do Sistema
- **Painel Geral (Dashboard):** Tela de entrada (`painel.php`) para monitorar o status das reservas em tempo real.
- **Novas Solicitações (`nova_solicitacao.php`):** Formulário completo contendo regras para reserva de salas, especificando horários de início e término, motivos de uso e dados do solicitante.
- **Fluxo de Aprovações:**
  - `aprovacoes.php`: Grid de controle para gestores revisarem solicitações pendentes.
  - `aprovacoes_single.php`: Módulo de detalhamento individual de cada solicitação para validação de segurança (aprovação ou reprovação).
- **Gerenciamento de Agendamentos:**
  - `reservas_confirmadas.php`: Visualização das reservas validadas no calendário.
  - `canceladas.php`: Listagem de solicitações rejeitadas ou canceladas.
  - `programacao_diaria.php`: Consolidação da agenda operacional diária do estabelecimento.
- **Área Administrativa (`admin/`):** CRUDs restritos para cadastrar salas, gerenciar perfis de usuários e configurações globais.
- **Segurança de Usuário:** Rotinas para recuperação de senha (`us-forgot-pass.php`, `us-validcod.php`, `us-creatpass.php`).
- **Validação de Certificados (`validar_certificado.php`):** Sistema para verificação e validação de autenticidade de comprovantes de saúde ou vacinação (Covid-19), garantindo a conformidade sanitária.
---
## 🔒 Diretrizes de Segurança Aplicadas
- **Segurança de Cabeçalho HTTP:** Implementação rígida de headers de proteção diretamente no inicializador de requisições:
  - `Content-Security-Policy` (CSP) para travar fontes e scripts maliciosos.
  - `X-Frame-Options: DENY` para mitigar ataques de clickjacking.
  - `X-Content-Type-Options: nosniff` para bloquear sniffs de MIME types.
  - `X-XSS-Protection` ativado em modo de bloqueio.
- **Variáveis de Ambiente (.env) Protegidas:**
  - Utiliza `vlucas/phpdotenv` para afastar credenciais críticas do código-fonte.
  - O sistema busca o arquivo `.env` fora da raiz pública para evitar varreduras web, priorizando os seguintes caminhos do sistema operacional:
    1. `/etc/reservm` (Linux recomendado)
    2. `C:\xampp\etc\reservm` (Windows/XAMPP)
- **Conectividade Multi-Ambiente:** O arquivo de conexão altera automaticamente os parâmetros do DSN SQL Server dependendo do ambiente configurado na variável `APP_ENV` (como `local`, `homologacao` ou `producao`).
- **Logs de Erro Seguros:** Em ambientes de produção, falhas de banco de dados não são impressas na tela (prevenindo vazamento de dados de infraestrutura) — elas são gravadas no log interno via `error_log` do PHP.
---
## 📂 Estrutura de Pastas
```bash
reservm-web-php/
├── admin/                 # Painel e controles específicos para superadministradores
├── assets/                # Arquivos estáticos de front-end (Bootstrap, CSS customizado e JS)
├── banco/                 # Scripts SQL contendo dumps de tabelas e procedures
├── conexao/
│   ├── conexao.php        # Inicializador global do Dotenv, cabeçalhos de segurança e PDO SQL Server
│   ├── email.php          # Driver para envio de e-mails via PHPMailer
│   └── send_course_email.php
├── controller/            # Lógicas de negócios e tratamento de requisições do sistema
├── includes/              # Componentes de layouts comuns (cabeçalho, menu e rodapé)
├── relatorios/            # Módulos para exportação de dados e relatórios de reservas
├── router/                # Mecanismo auxiliar de redirecionamento de rotas
├── index.php              # Formulário de login e tela de boas-vindas do sistema
├── painel.php             # Dashboard principal do usuário logado
├── nova_solicitacao.php   # Solicitação de agendamento de recursos
├── aprovacoes.php         # Grid de aprovação de reservas por gestores
├── reservas_confirmadas.php# Agenda de compromissos validados
├── validar_certificado.php# Validador de comprovantes de conformidade sanitária
└── [arquivos de suporte]
```
---
## 🛠️ Tecnologias Utilizadas
- **PHP 7.4+ / 8.0+**
- **Microsoft SQL Server (Driver `sqlsrv`):** Banco de dados relacional oficial.
- **Composer:** PSR-4 autoloading das classes.
- **phpdotenv:** Segurança nas credenciais do banco e chaves de envio.
- **PHPMailer:** Envio de alertas de reservas e redefinições de acesso.
- **Bootstrap:** Design responsivo e painéis CSS.
---
## ⚙️ Como Instalar e Configurar o Projeto
1. **Configure o arquivo `.env`:**
   Crie um arquivo `.env` nos diretórios candidatos do seu sistema operacional (ex: `C:\xampp\etc\reservm\.env` no Windows ou `/etc/reservm/.env` no Linux) com a seguinte estrutura:
   ```env
   APP_ENV=local
   APP_URL=http://localhost/reservm-web-php
   EMAIL_SAAP=suporte@suaempresa.com.br
   
   # Conexão Banco Local
   DB_HOST=127.0.0.1
   DB_PORT=1433
   DB_DATABASE=reservm_db
   DB_USERNAME=sa
   DB_PASSWORD=sua_senha_sql_server
   ```
2. **Execute o Composer:**
   Abra o terminal na raiz do projeto e instale as dependências:
   ```bash
   composer install
   ```
3. **Execute o Servidor Apache:**
   Certifique-se de configurar o IIS ou Apache (XAMPP) com suporte aos drivers de PHP do SQL Server. Acesso ao painel administrativo via `http://localhost/reservm-web-php`.

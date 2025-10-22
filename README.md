# Controle de Acesso para Órgão Público

Este repositório contém uma implementação de referência para um sistema de controle
 de acesso com backend em Laravel 11 e frontend em React + Tailwind CSS.
O projeto foi desenhado para atender requisitos de segurança, LGPD e
operacionalização de entradas/saídas com QR Code.

## Funcionalidades

- Autenticação com Laravel Sanctum e controle de acesso baseado em papéis (RBAC) com Spatie Permissions.
- Cadastro de visitantes com captura de foto via webcam (WebRTC) e armazenamento seguro no disco.
- Emissão automática de crachá em PDF contendo QR Code para liberação de saída.
- Endpoint `/scan` que registra automaticamente a saída ao ler o QR Code.
- Relatórios consolidados por setor e data, com filtros e exportação visual no frontend.
- Job assíncrono para anonimização de visitantes inativos respeitando a LGPD.
- Log de auditoria detalhado das operações sensíveis.
- Deploy simplificado com Docker Compose (Nginx + PHP-FPM + MariaDB) rodando em HTTPS.

## Estrutura

```
app/                # Código Laravel (Models, Controllers, Jobs, Services)
routes/api.php      # Rotas REST protegidas por Sanctum
resources/views/pdf # Templates para geração de PDFs
frontend/           # Aplicação React + Tailwind (Vite)
docker/             # Arquivos de infraestrutura (Nginx, PHP-FPM, certificados)
```

## Inicialização Rápida

1. Gere o arquivo `.env` a partir de `.env.example` e ajuste as variáveis conforme necessário.
2. Execute `docker compose up -d --build` para subir os serviços.
3. Dentro do container `app`, rode `./scripts/init.sh` para migrar o banco e criar o usuário `admin@admin` (senha `admin`).
4. Execute `php artisan storage:link` para publicar os arquivos públicos (fotos e crachás).
5. Acesse `https://acesso.local` no navegador (importando o certificado de desenvolvimento em `docker/nginx/certs`).
6. No diretório `frontend`, instale dependências (`npm install`) e execute `npm run dev` para iniciar o frontend.

## Execução no VS Code

Para facilitar o desenvolvimento local sem depender exclusivamente do Docker, o projeto traz uma pasta `.vscode` com tarefas e configurações pré-definidas. Antes de rodar os serviços certifique-se de ter instalado no host:

- PHP 8.3 com extensões `fileinfo`, `gd` e `pdo_mysql` habilitadas.
- Composer 2.x.
- Node.js 18+ e npm.
- MariaDB local (ou utilize `docker compose up -d`).

Em seguida:

1. Abra a paleta de comandos (`Ctrl+Shift+P` / `Cmd+Shift+P`) e execute **Tasks: Run Task**.
2. Rode `Setup: composer install` e `Setup: npm install` na primeira vez para baixar as dependências.
3. Se estiver usando banco de dados local via Docker, execute `Docker: up`. Para inicializar a base pela primeira vez, rode `DB: initialize (Unix)` ou `DB: initialize (Windows)` conforme seu sistema operacional.
4. Inicie os servidores de desenvolvimento com a tarefa `Dev: Start Fullstack`. Ela abre dois terminais dedicados, um para `php artisan serve` (API em http://127.0.0.1:8000) e outro para o Vite (frontend em http://127.0.0.1:5173).
5. (Opcional) Pressione `F5` e escolha **Run fullstack and open browser** para executar a tarefa anterior e abrir o frontend no Chrome integrado do VS Code.

Caso prefira rodar manualmente, você ainda pode iniciar cada tarefa individualmente (`Dev: Laravel API`, `Dev: React Vite`, etc.). As extensões recomendadas (`Intelephense`, `Tailwind CSS IntelliSense`, `Docker`, entre outras) são sugeridas automaticamente ao abrir o projeto.

## Testes e Qualidade

- Execute `php artisan test` para testes de backend.
- Utilize `npm run lint` (configure conforme sua preferência) para o frontend.
- Ajuste os comandos nos pipelines CI/CD conforme necessidade.

## LGPD e Auditoria

- Toda operação crítica gera registro em `audit_logs` via `AuditLogger`.
- O campo `consent_at` registra a data do aceite do visitante.
- O job `AnonymizeVisitorsJob` anonimiza visitantes inativos após 6 meses.

## Scripts Úteis

- `scripts/init.sh`: aplica migrations, seeders e imprime o usuário padrão.

## Licença

Disponibilizado para uso interno. Adapte as políticas de acordo com a legislação vigente.

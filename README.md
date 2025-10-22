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

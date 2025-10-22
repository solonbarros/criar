#!/bin/bash
set -euo pipefail

# Script de inicialização para preparar banco e dados base.
php artisan key:generate --force
php artisan migrate --force
php artisan db:seed --class=RolePermissionSeeder --force
php artisan storage:link

cat <<'MSG'
Usuário padrão criado:
  Email: admin@admin
  Senha: admin
MSG

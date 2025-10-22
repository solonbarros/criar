#!/usr/bin/env pwsh
Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

Write-Host "Gerando APP_KEY e preparando banco de dados..." -ForegroundColor Cyan
php artisan key:generate --force
php artisan migrate --force
php artisan db:seed --class=RolePermissionSeeder --force
php artisan storage:link

Write-Host "" 
Write-Host "Usuário padrão criado:" -ForegroundColor Green
Write-Host "  Email: admin@admin"
Write-Host "  Senha: admin"

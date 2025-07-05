# 🚀 Guía de Deployment - Kampus

Guía completa para desplegar el sistema Kampus en producción.

## 📋 Tabla de Contenidos

1. [Requisitos del Servidor](#requisitos-del-servidor)
2. [Configuración del Servidor](#configuración-del-servidor)
3. [Deployment del Backend](#deployment-del-backend)
4. [Deployment del Frontend](#deployment-del-frontend)
5. [Configuración de Base de Datos](#configuración-de-base-de-datos)
6. [Configuración de SSL](#configuración-de-ssl)
7. [Monitoreo y Logs](#monitoreo-y-logs)
8. [Backup y Recuperación](#backup-y-recuperación)
9. [Escalabilidad](#escalabilidad)

## 🖥️ Requisitos del Servidor

### Mínimos
- **CPU**: 2 cores
- **RAM**: 4GB
- **Almacenamiento**: 50GB SSD
- **Sistema Operativo**: Ubuntu 20.04 LTS o superior

### Recomendados
- **CPU**: 4+ cores
- **RAM**: 8GB+
- **Almacenamiento**: 100GB+ SSD
- **Sistema Operativo**: Ubuntu 22.04 LTS

### Software Requerido
- **PHP 8.2+**
- **Composer 2.0+**
- **MySQL 8.0+**
- **Nginx 1.18+**
- **Node.js 18+**
- **Git**

## ⚙️ Configuración del Servidor

### 1. Actualizar el Sistema

```bash
# Actualizar paquetes
sudo apt update && sudo apt upgrade -y

# Instalar herramientas básicas
sudo apt install -y curl wget git unzip software-properties-common
```

### 2. Instalar PHP

```bash
# Agregar repositorio de PHP
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Instalar PHP y extensiones
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-curl \
php8.2-mbstring php8.2-zip php8.2-gd php8.2-bcmath php8.2-intl php8.2-redis

# Verificar instalación
php -v
```

### 3. Instalar Composer

```bash
# Descargar e instalar Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Verificar instalación
composer --version
```

### 4. Instalar MySQL

```bash
# Instalar MySQL
sudo apt install -y mysql-server

# Configurar seguridad
sudo mysql_secure_installation

# Crear usuario y base de datos
sudo mysql -u root -p
```

```sql
-- En MySQL
CREATE DATABASE kampus_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'kampus_user'@'localhost' IDENTIFIED BY 'tu_password_seguro';
GRANT ALL PRIVILEGES ON kampus_db.* TO 'kampus_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 5. Instalar Nginx

```bash
# Instalar Nginx
sudo apt install -y nginx

# Iniciar y habilitar Nginx
sudo systemctl start nginx
sudo systemctl enable nginx
```

### 6. Instalar Node.js

```bash
# Instalar Node.js usando NodeSource
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Verificar instalación
node --version
npm --version
```

## 🏗️ Deployment del Backend

### 1. Clonar el Repositorio

```bash
# Crear directorio para la aplicación
sudo mkdir -p /var/www/kampus
sudo chown $USER:$USER /var/www/kampus

# Clonar el repositorio
cd /var/www/kampus
git clone https://github.com/victorpuello/kampus-api.git .
```

### 2. Configurar Laravel

```bash
# Instalar dependencias
composer install --no-dev --optimize-autoloader

# Configurar variables de entorno
cp .env.example .env
php artisan key:generate
```

### 3. Configurar .env de Producción

```env
# .env
APP_NAME=Kampus
APP_ENV=production
APP_KEY=base64:tu_key_generado
APP_DEBUG=false
APP_URL=https://kampus.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kampus_db
DB_USERNAME=kampus_user
DB_PASSWORD=tu_password_seguro

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

SANCTUM_STATEFUL_DOMAINS=kampus.com,www.kampus.com
SESSION_DOMAIN=.kampus.com
```

### 4. Configurar Base de Datos

```bash
# Ejecutar migraciones
php artisan migrate --force

# Ejecutar seeders (solo si es necesario)
php artisan db:seed --force

# Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Configurar Permisos

```bash
# Configurar permisos
sudo chown -R www-data:www-data /var/www/kampus
sudo chmod -R 755 /var/www/kampus
sudo chmod -R 775 /var/www/kampus/storage
sudo chmod -R 775 /var/www/kampus/bootstrap/cache
```

### 6. Configurar Nginx para Backend

```nginx
# /etc/nginx/sites-available/kampus-api
server {
    listen 80;
    server_name api.kampus.com;
    root /var/www/kampus/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Habilitar el sitio
sudo ln -s /etc/nginx/sites-available/kampus-api /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## 🎨 Deployment del Frontend

### 1. Configurar el Proyecto

```bash
# Navegar al directorio del frontend
cd /var/www/kampus/kampus-frontend

# Instalar dependencias
npm ci --production

# Configurar variables de entorno
echo "VITE_API_URL=https://api.kampus.com/api/v1" > .env.production
```

### 2. Build de Producción

```bash
# Crear build optimizado
npm run build

# Verificar que se creó el directorio dist
ls -la dist/
```

### 3. Configurar Nginx para Frontend

```nginx
# /etc/nginx/sites-available/kampus-frontend
server {
    listen 80;
    server_name kampus.com www.kampus.com;
    root /var/www/kampus/kampus-frontend/dist;
    index index.html;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied expired no-cache no-store private must-revalidate auth;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    location / {
        try_files $uri $uri/ /index.html;
    }

    # Cache static assets
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Proxy API requests to backend
    location /api {
        proxy_pass https://api.kampus.com;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

```bash
# Habilitar el sitio
sudo ln -s /etc/nginx/sites-available/kampus-frontend /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## 🔐 Configuración de SSL

### 1. Instalar Certbot

```bash
# Instalar Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obtener certificados SSL
sudo certbot --nginx -d kampus.com -d www.kampus.com -d api.kampus.com

# Configurar renovación automática
sudo crontab -e
```

Agregar esta línea al crontab:
```
0 12 * * * /usr/bin/certbot renew --quiet
```

### 2. Configuración SSL en Nginx

```nginx
# Configuración SSL automática con Certbot
server {
    listen 443 ssl http2;
    server_name kampus.com www.kampus.com;
    
    ssl_certificate /etc/letsencrypt/live/kampus.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/kampus.com/privkey.pem;
    
    # SSL configuration
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;
    
    # HSTS
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    
    # ... resto de la configuración
}

# Redirect HTTP to HTTPS
server {
    listen 80;
    server_name kampus.com www.kampus.com;
    return 301 https://$server_name$request_uri;
}
```

## 📊 Monitoreo y Logs

### 1. Configurar Logs de Laravel

```bash
# Configurar rotación de logs
sudo nano /etc/logrotate.d/kampus
```

```conf
/var/www/kampus/storage/logs/*.log {
    daily
    missingok
    rotate 52
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
    postrotate
        /usr/bin/systemctl reload php8.2-fpm
    endscript
}
```

### 2. Monitoreo con Supervisor

```bash
# Instalar Supervisor
sudo apt install -y supervisor

# Configurar queue worker
sudo nano /etc/supervisor/conf.d/kampus-worker.conf
```

```ini
[program:kampus-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/kampus/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/kampus/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
# Habilitar supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start kampus-worker:*
```

### 3. Monitoreo del Sistema

```bash
# Instalar herramientas de monitoreo
sudo apt install -y htop iotop nethogs

# Configurar monitoreo de recursos
sudo nano /usr/local/bin/monitor.sh
```

```bash
#!/bin/bash
echo "=== Kampus System Monitor ==="
echo "Date: $(date)"
echo "CPU Usage: $(top -bn1 | grep "Cpu(s)" | awk '{print $2}' | cut -d'%' -f1)%"
echo "Memory Usage: $(free | grep Mem | awk '{printf("%.2f%%", $3/$2 * 100.0)}')"
echo "Disk Usage: $(df -h / | awk 'NR==2 {print $5}')"
echo "Active Connections: $(netstat -an | grep :80 | wc -l)"
echo "================================"
```

```bash
# Hacer ejecutable
sudo chmod +x /usr/local/bin/monitor.sh

# Agregar al crontab para monitoreo cada 5 minutos
sudo crontab -e
```

```
*/5 * * * * /usr/local/bin/monitor.sh >> /var/log/kampus-monitor.log
```

## 💾 Backup y Recuperación

### 1. Script de Backup Automático

```bash
# Crear script de backup
sudo nano /usr/local/bin/kampus-backup.sh
```

```bash
#!/bin/bash

# Configuración
BACKUP_DIR="/var/backups/kampus"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="kampus_db"
DB_USER="kampus_user"
DB_PASS="tu_password_seguro"
APP_DIR="/var/www/kampus"

# Crear directorio de backup
mkdir -p $BACKUP_DIR

# Backup de la base de datos
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/db_backup_$DATE.sql

# Backup de archivos de la aplicación
tar -czf $BACKUP_DIR/app_backup_$DATE.tar.gz -C $APP_DIR .

# Backup de archivos subidos
tar -czf $BACKUP_DIR/uploads_backup_$DATE.tar.gz -C $APP_DIR/storage/app/public .

# Comprimir backups
gzip $BACKUP_DIR/db_backup_$DATE.sql

# Eliminar backups antiguos (mantener últimos 7 días)
find $BACKUP_DIR -name "*.gz" -mtime +7 -delete

echo "Backup completado: $DATE"
```

```bash
# Hacer ejecutable
sudo chmod +x /usr/local/bin/kampus-backup.sh

# Agregar al crontab para backup diario
sudo crontab -e
```

```
0 2 * * * /usr/local/bin/kampus-backup.sh >> /var/log/kampus-backup.log 2>&1
```

### 2. Script de Recuperación

```bash
# Crear script de recuperación
sudo nano /usr/local/bin/kampus-restore.sh
```

```bash
#!/bin/bash

if [ $# -eq 0 ]; then
    echo "Uso: $0 <fecha_backup>"
    echo "Ejemplo: $0 20241201_143000"
    exit 1
fi

BACKUP_DATE=$1
BACKUP_DIR="/var/backups/kampus"
APP_DIR="/var/www/kampus"
DB_NAME="kampus_db"
DB_USER="kampus_user"
DB_PASS="tu_password_seguro"

echo "Iniciando recuperación desde backup: $BACKUP_DATE"

# Restaurar base de datos
echo "Restaurando base de datos..."
gunzip -c $BACKUP_DIR/db_backup_$BACKUP_DATE.sql.gz | mysql -u$DB_USER -p$DB_PASS $DB_NAME

# Restaurar archivos de la aplicación
echo "Restaurando archivos de la aplicación..."
tar -xzf $BACKUP_DIR/app_backup_$BACKUP_DATE.tar.gz -C $APP_DIR

# Restaurar archivos subidos
echo "Restaurando archivos subidos..."
tar -xzf $BACKUP_DIR/uploads_backup_$BACKUP_DATE.tar.gz -C $APP_DIR/storage/app/public

# Limpiar caché
cd $APP_DIR
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "Recuperación completada"
```

```bash
# Hacer ejecutable
sudo chmod +x /usr/local/bin/kampus-restore.sh
```

## 📈 Escalabilidad

### 1. Configuración de Redis

```bash
# Instalar Redis
sudo apt install -y redis-server

# Configurar Redis
sudo nano /etc/redis/redis.conf
```

```conf
# Configuración de producción
maxmemory 256mb
maxmemory-policy allkeys-lru
save 900 1
save 300 10
save 60 10000
```

```bash
# Reiniciar Redis
sudo systemctl restart redis-server
sudo systemctl enable redis-server
```

### 2. Configuración de Queue

```bash
# Configurar queue en .env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Crear jobs para tareas pesadas
php artisan make:job ProcessStudentImport
php artisan make:job GenerateReport
```

### 3. Load Balancer (Opcional)

```nginx
# Configuración de upstream para múltiples servidores
upstream kampus_backend {
    server 192.168.1.10:8000;
    server 192.168.1.11:8000;
    server 192.168.1.12:8000;
}

server {
    listen 80;
    server_name api.kampus.com;
    
    location / {
        proxy_pass http://kampus_backend;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

## 🔧 Comandos Útiles de Mantenimiento

```bash
# Verificar estado de servicios
sudo systemctl status nginx php8.2-fpm mysql redis-server

# Ver logs en tiempo real
sudo tail -f /var/log/nginx/access.log
sudo tail -f /var/www/kampus/storage/logs/laravel.log

# Limpiar caché
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Verificar permisos
sudo chown -R www-data:www-data /var/www/kampus
sudo chmod -R 755 /var/www/kampus
sudo chmod -R 775 /var/www/kampus/storage

# Reiniciar servicios
sudo systemctl restart nginx php8.2-fpm mysql redis-server
```

---

**¡Sistema Kampus desplegado y listo para producción! 🚀** 
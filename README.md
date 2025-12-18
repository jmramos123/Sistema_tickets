# Sistema de Tickets

Sistema de gestión de turnos/tickets con pantalla TV en tiempo real, desarrollado con Laravel 12, Livewire 3, y Laravel Reverb.

## 📋 Requisitos del Sistema

### Desarrollo Local
- PHP 8.2 o superior
- Composer 2.x
- Node.js 18+ y NPM
- MySQL 8.0+ o MariaDB 10.3+
- Extensiones PHP requeridas:
  - BCMath
  - Ctype
  - JSON
  - Mbstring
  - OpenSSL
  - PDO
  - Tokenizer
  - XML

### Producción
- PostgreSQL 13+ (usado en Render.com)
- Todas las extensiones PHP anteriores

## 🚀 Instalación Local

### 1. Clonar el repositorio
```bash
git clone <repository-url>
cd Sistema-de-tickets
```

### 2. Instalar dependencias
```bash
composer install
npm install
```

### 3. Configurar el entorno
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configurar la base de datos
Edita el archivo `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=Tickets_DB
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Crear la base de datos
```bash
# MySQL
mysql -u root -p
CREATE DATABASE Tickets_DB;
exit;
```

### 6. Ejecutar migraciones y seeders
```bash
php artisan migrate
php artisan db:seed
```

Esto creará:
- Usuario admin: `admin@sistema.test` / `password`
- Roles: admin, empleado
- Áreas y escritorios de ejemplo

### 7. Configurar Reverb (WebSockets)
El archivo `.env` debe tener:
```env
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=local
REVERB_APP_KEY=local
REVERB_APP_SECRET=local
REVERB_PORT=6001
REVERB_SCHEME=http
```

### 8. Configurar IP local para red LAN
Obtén tu IP local:
```bash
ipconfig  # Windows
# Busca "IPv4 Address" (ej: 192.168.1.100)
```

Actualiza en `.env`:
```env
VITE_PUSHER_HOST="TU_IP_LOCAL"  # ej: "192.168.1.100"
VITE_PUSHER_PORT="6001"
VITE_PUSHER_SCHEME="http"
```

### 9. Iniciar el sistema
```bash
composer dev
```

Esto inicia 4 servicios simultáneamente:
- **Server**: Laravel en `http://localhost:8000`
- **Queue**: Worker de colas
- **Reverb**: Servidor WebSocket en puerto 6001
- **Vite**: Compilación de assets en tiempo real

## 🌐 Acceso al Sistema

### URLs Principales
- **Inicio**: `http://localhost:8000/`
- **Generar Tickets**: `http://localhost:8000/tickets`
- **Pantalla TV**: `http://localhost:8000/tv`
- **Login Admin/Empleado**: `http://localhost:8000/login`

### Usuarios por Defecto
- **Admin**: `admin@sistema.test` / `password`

## 📱 Flujo de Uso

### Para Clientes
1. Acceder a `/tickets`
2. Seleccionar área
3. Seleccionar tipo (Normal o Adulto Mayor)
4. Imprimir ticket

### Para Empleados
1. Login en `/login`
2. Seleccionar escritorio
3. Llamar siguiente ticket
4. Marcar como atendido

### Pantalla TV
- Mostrar en `/tv` 
- Actualización automática en tiempo real
- Muestra video configurado y últimos tickets llamados

## 🔧 Comandos Útiles

### Desarrollo
```bash
# Iniciar todo (recomendado)
composer dev

# O manualmente en terminales separadas:
php artisan serve
php artisan queue:listen --tries=1
php artisan reverb:start
npm run dev
```

### Producción
```bash
# Compilar assets para producción
npm run build

# Limpiar cachés
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Base de Datos
```bash
# Reset completo
php artisan migrate:fresh --seed

# Solo seeders
php artisan db:seed
```

### Tests
```bash
# Ejecutar tests con Pest
php artisan test
# o
./vendor/bin/pest
```

## 🌍 Despliegue a Producción (Render.com)

### Configuración en render.yaml
El proyecto incluye `render.yaml` pre-configurado para Render.com con:
- Web service (Laravel + Reverb + Nginx)
- Worker service (Queue worker)
- PostgreSQL database

### Pasos para Desplegar

1. **Crear cuenta en Render.com**
   - Ir a https://render.com

2. **Crear PostgreSQL Database**
   - New → PostgreSQL
   - Copiar credenciales

3. **Crear Web Service**
   - New → Web Service
   - Conectar repositorio Git
   - Render detectará automáticamente `render.yaml`

4. **Configurar Variables de Entorno**
   En el dashboard de Render, actualizar:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=<tu-host-postgres>
   DB_DATABASE=<tu-database>
   DB_USERNAME=<tu-usuario>
   DB_PASSWORD=<tu-password>
   
   VITE_PUSHER_HOST=<tu-dominio-render>.onrender.com
   VITE_PUSHER_PORT=443
   VITE_PUSHER_SCHEME=https
   ```

5. **Deploy**
   - El deploy se ejecuta automáticamente
   - Reverb se inicia via Supervisor
   - Nginx proxy maneja WebSockets

### Comandos Post-Deploy
```bash
# Ejecutar migraciones
php artisan migrate --force

# Ejecutar seeders (primera vez)
php artisan db:seed --force
```

## 🔌 Arquitectura de Red

### Desarrollo Local
```
Cliente → Laravel (8000)
       → Reverb (6001) ← WebSocket
       → Vite (5173) ← Hot reload
```

### Producción (Render)
```
Cliente → Nginx (443/80)
       → Laravel
       → Reverb (interno)
       → PostgreSQL
```

Nginx hace proxy de `/reverb` al puerto interno 6001.

## 🛠️ Solución de Problemas

### Error: "Failed to connect to 192.168.X.X port 6001"
**Solución**: Actualiza `VITE_PUSHER_HOST` en `.env` con tu IP actual y reinicia `composer dev`.

### Error: "Call to undefined function socket_create()"
**Solución**: Ya está resuelto en el código. Usa `gethostbyname()` en su lugar.

### La TV no actualiza en tiempo real
**Verificar**:
1. Reverb está corriendo (`composer dev` incluye reverb)
2. IP en `.env` es correcta
3. Puerto 6001 no está bloqueado por firewall

### Cambio de red WiFi
1. Obtener nueva IP: `ipconfig`
2. Actualizar `VITE_PUSHER_HOST` en `.env`
3. Reiniciar: `composer dev`

### Los videos no se reproducen
**Verificar**:
1. Storage link: `php artisan storage:link`
2. Permisos en `storage/app/public`
3. Formato de video compatible (mp4, webm, avi, mov)

## 📁 Estructura del Proyecto

```
app/
├── Events/           # Eventos de broadcasting
├── Livewire/         # Componentes Livewire
│   ├── AreaManagement.php
│   ├── ClientTicket.php
│   ├── DeskSelection.php
│   ├── TicketQueue.php
│   ├── TvDisplay.php
│   └── ...
└── Models/           # Modelos Eloquent

config/
├── broadcasting.php  # Configuración Reverb
└── reverb.php        # Configuración servidor WebSocket

database/
├── migrations/       # Estructura de BD
└── seeders/          # Datos iniciales

resources/
├── js/
│   └── app.js       # Echo + Pusher JS
└── views/
    └── livewire/    # Vistas Blade

routes/
├── web.php          # Rutas principales
└── channels.php     # Canales de broadcasting
```

## 🔐 Seguridad

- Cambiar `APP_KEY` en producción
- Actualizar credenciales admin por defecto
- Configurar `APP_ENV=production` y `APP_DEBUG=false`
- Usar HTTPS en producción
- Validar todos los inputs (ya implementado)

## 📊 Características

- ✅ Gestión de áreas y escritorios
- ✅ Generación de tickets (Normal/Adulto Mayor)
- ✅ Cola de atención con prioridad
- ✅ Pantalla TV en tiempo real (WebSockets)
- ✅ Sistema de estadísticas
- ✅ Gestión de videos publicitarios
- ✅ Audio TTS con ElevenLabs (opcional)
- ✅ Roles y permisos (Spatie)
- ✅ Tests con Pest

## 🤝 Contribuciones

Sistema desarrollado como proyecto académico.

## 📝 Licencia

MIT License

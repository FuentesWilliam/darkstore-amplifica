# 🛒 Darkstore-Amplifica Integration

Este proyecto permite integrar Shopify con la plataforma Darkstore-Amplifica utilizando Laravel. Proporciona funcionalidades para gestionar la sincronización de datos y la exportación de información relevante de forma eficiente.

## 📋 Requisitos

Antes de comenzar, asegúrate de tener instalado lo siguiente en tu entorno local:

- PHP 8.2
- Laravel 12
- Composer
- npm

### Otros requisitos:

- laravel/jetstream: 5.3
- livewire/livewire: 3.6.4

## ⚙️ Instalación

Sigue estos pasos para instalar y ejecutar el entorno de desarrollo local:

Clona este repositorio:

```bash
git clone https://github.com/FuentesWilliam/darkstore-amplifica
cd darkstore-amplifica
```

Instala las dependencias de PHP y JavaScript:

```bash
composer install
npm install
```

Crea y configura el archivo .env:

Copia el archivo .env.example y renómbralo como .env, luego modifica las siguientes variables con tus credenciales de Shopify:

```bash
SHOPIFY_SHOP_DOMAIN="tu-dominio.myshopify.com"
SHOPIFY_ACCESS_TOKEN="tu-access-token"
```
ejecuta migraciones:

```bash
php artisan migrate
```

Compila los recursos de frontend:

```bash
npm run dev
```

Configura el Virtual Host de Apache:

Asegúrate de tener configurado un virtual host apuntando a la carpeta public/ del proyecto. Un ejemplo de configuración sería:

```bash
<VirtualHost *:80>
    ServerName darkstore.local
    DocumentRoot /ruta/a/darkstore-amplifica/public

    <Directory /ruta/a/darkstore-amplifica/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```


Luego, edita tu archivo hosts (Linux/Mac: /etc/hosts, Windows: C:\Windows\System32\drivers\etc\hosts) para incluir:

```bash
127.0.0.1   darkstore.local
```

## 📝 Justificación Técnica

Autenticación con Jetstream
Este proyecto utiliza Laravel Jetstream con Livewire para gestionar la autenticación de usuarios.
Durante la instalación y migración de base de datos, se crea un usuario de prueba por defecto:
Puedes iniciar sesión con estas credenciales o crear un nuevo usuario desde el formulario de registro.


Para esta prueba técnica se evaluó inicialmente el uso de librerías especializadas para exportación de datos en formato Excel como:

- maatwebsite/excel
- PhpSpreadsheet
- OpenSpout

No obstante, debido a incompatibilidades con PHP 8.2 y Laravel 12, y con el objetivo de evitar invertir tiempo en resolver conflictos de dependencias, se optó por una solución más simple y eficaz utilizando la función nativa fputcsv() de PHP.

Ventajas de esta solución:
✅ Exporta datos en formato CSV, totalmente compatible con Microsoft Excel.

✅ Elimina dependencias innecesarias, manteniendo el código ligero y portable.

✅ Permite una fácil extensibilidad para modelos como Order, Product, etc.

En un entorno de producción más estable, se podría considerar el uso de PhpSpreadsheet o OpenSpout para ofrecer soporte a formatos más avanzados como .xlsx.

# Wallet Integration

Módulo de HumHub para generar y distribuir pases digitales (Google Wallet y Apple Wallet) destinados, por ejemplo, a tarjetas de empleado.

## Características

- Generación de pases para Google Wallet
- Generación de pases para Apple Wallet
- Integración con el sistema de usuarios de HumHub
- Panel de administración para configuración centralizada

## Requisitos

- HumHub >= 1.8
- PHP >= 7.1 (recomendado PHP >= 7.4)
- Composer
- Extensiones PHP: `openssl`, `json`, `mbstring`, `curl`

## Dependencias

- `firebase/php-jwt` (^6.0) — Firma y verificación JWT
- `tschoffelen/php-pkpass` (^1.5) — Generación de paquetes Apple Wallet

## Instalación

1. Copia el módulo a la carpeta de módulos de HumHub:

```bash
cp -r wallet /ruta/a/humhub/protected/modules/
```

2. Instala las dependencias del módulo:

```bash
cd /ruta/a/humhub/protected/modules/wallet
composer install
```

3. Activa el módulo desde el panel de administración de HumHub: _Módulos > Administrar módulos_.

Si prefieres instalar dependencias explícitas en el proyecto principal, puedes ejecutar:

```bash
composer require firebase/php-jwt tschoffelen/php-pkpass
```

## Configuración

La configuración principal se realiza en `config.php` del módulo o mediante la interfaz de administración del módulo. Las claves y rutas más importantes son:

- `APPLE_CERT_PATH` — Ruta al certificado `.p12` de Apple (Pass Type ID)
- `APPLE_P12_PASSWORD` — Contraseña del `.p12` (si aplica)
- `APPLE_TEAM_ID` — Team ID de Apple
- `GOOGLE_SERVICE_ACCOUNT_JSON` — Ruta al archivo JSON de la cuenta de servicio de Google
- `GOOGLE_ISSUER_ID` — Issuer ID de Google Wallet

Coloca los ficheros sensibles (como `.p12` y JSON de Google) en una carpeta fuera del control de versiones y con permisos restringidos.

## Configuración de Google Wallet (resumen de pasos)

1. Crear un proyecto en Google Cloud Console.
2. Habilitar las APIs necesarias (Google Wallet API).
3. Crear una Service Account y descargar el JSON de credenciales.
4. Registrar un Issuer en Google Wallet Console y anotar el `issuerId`.
5. Configurar `GOOGLE_SERVICE_ACCOUNT_JSON` y `GOOGLE_ISSUER_ID` en el módulo.

## Configuración de Apple Wallet (resumen de pasos)

1. Crear un Pass Type ID en Apple Developer.
2. Generar y descargar el certificado asociado al Pass Type ID.
3. Exportar el certificado a `.p12` (con clave) y convertirlo a `pem` si la librería lo requiere.
4. Configurar `APPLE_CERT_PATH`, `APPLE_P12_PASSWORD` y `APPLE_TEAM_ID` en el módulo.

## Estructura del Proyecto

```
wallet/
├── controllers/
│   ├── AdminController.php
│   └── WalletController.php
├── models/
│   ├── AdminSettingsForm.php
│   ├── AppleWalletPass.php
│   └── GoogleWalletPass.php
├── resources/
│   └── views/
│       └── admin/
│           └── index.php
├── Module.php
├── config.php
├── module.json
└── composer.json
```

## Uso

### Para Administradores

Accede a la configuración del módulo en la administración de HumHub para:

- Introducir las credenciales y rutas de Apple y Google
- Ajustar plantillas y campos que se incluyen en los pases

### Para Usuarios

Los usuarios podrán generar sus pases desde la interfaz del módulo o desde su perfil, según la integración habilitada por el administrador.

## Desarrollo

1. Instala dependencias locales:

```bash
composer install
```

2. Activa el módulo en HumHub en modo desarrollo y realiza pruebas locales en un entorno controlado.

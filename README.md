# Wallet Integration

Un módulo de HumHub que permite añadir tarjetas de empleado a Google Wallet y Apple Wallet.

## Características

- Generación de pases digitales para Google Wallet
- Generación de pases digitales para Apple Wallet
- Integración con el sistema de autenticación de HumHub
- Panel de administración para configuración

## Requisitos

- HumHub >= 1.8
- PHP >= 7.1
- Composer

## Dependencias

- `firebase/php-jwt`: ^6.0 - Para firma JWT
- `tschoffelen/php-pkpass`: ^1.5 - Para generación de pases Apple Wallet

## Instalación

1. Coloca el módulo en la carpeta de módulos de HumHub:

```bash
cp -r wallet /ruta/a/humhub/protected/modules/wallet
```

2. Instala las dependencias:

```bash
cd /ruta/a/humhub/protected/modules/wallet
composer install
```

3. Activa el módulo desde el panel de administración de HumHub

## Estructura del Proyecto

```
wallet/
├── controllers/
│   ├── AdminController.php      # Controlador de administración
│   └── WalletController.php      # Controlador principal
├── models/
│   ├── AdminSettingsForm.php    # Formulario de configuración
│   ├── AppleWalletPass.php      # Generador de pases Apple
│   └── GoogleWalletPass.php     # Generador de pases Google
├── resources/
│   └── views/
│       └── admin/
│           └── index.php         # Vista de administración
├── Module.php                    # Clase principal del módulo
├── config.php                    # Configuración
├── module.json                   # Metadatos del módulo
└── composer.json                 # Dependencias PHP
```

## Uso

### Para Administradores

Accede a la sección de configuración del módulo en la administración de HumHub para:

- Configurar las credenciales de Apple Wallet
- Configurar las credenciales de Google Wallet
- Personalizar los datos de los pases

### Para Usuarios

Los usuarios pueden generar sus pases digitales desde su perfil o desde el módulo Wallet.

## Desarrollo

### Configurar el entorno

```bash
composer install
```

### Activar el módulo en desarrollo

1. Actívalo desde la administración de HumHub
2. Los cambios se recargan automáticamente

## Licencia

Propietario - Vegalsa

## Contacto

Para reportar problemas o sugerencias, contacta con el equipo de desarrollo de Vegalsa.

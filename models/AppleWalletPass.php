<?php

namespace humhub\modules\wallet\models;

use Yii;
use PKPass\PKPass;

class AppleWalletPass
{
    private string $certPath;
    private string $certPass;
    private string $wwdrPath;
    private string $passTypeId;
    private string $teamId;

    public function __construct()
    {
        /** @var \humhub\modules\wallet\Module $m */
        $m = Yii::$app->getModule('wallet');

        $this->certPath   = $m->getAppleCertPath();
        $this->certPass   = $m->getAppleCertPass();
        $this->wwdrPath   = $m->getAppleWwdrPath();
        $this->passTypeId = $m->getApplePassTypeId();
        $this->teamId     = $m->getAppleTeamId();
    }

    /**
     * Genera el archivo .pkpass firmado para iPhone
     */
    public function createPass(int $userId, string $fullName, string $ean): ?string
    {
        $pass = new PKPass($this->certPath, $this->certPass);
        $pass->setWWDRcertPath($this->wwdrPath);

        // Estructura del pase (Diseño Vegalsa)
        $pass->setData([
            'formatVersion'      => 1,
            'passTypeIdentifier' => $this->passTypeId,
            'teamIdentifier'     => $this->teamId,
            'serialNumber'       => "user_{$userId}",
            'backgroundColor'    => 'rgb(212, 46, 24)', // Rojo Vegalsa #D42E18
            'foregroundColor'    => 'rgb(255, 255, 255)', // Texto Blanco
            'labelColor'         => 'rgb(255, 255, 255)', // Etiquetas Blancas
            'logoText'           => 'Vegalsa Eroski',
            'description'        => 'Tarjeta de Empleado Vegalsa',
            'sharingProhibited'  => true,

            // Usamos 'storeCard' para un diseño limpio con QR central
            'storeCard' => [
                'primaryFields' => [
                    [
                        'key'   => 'titular',
                        'label' => 'EMPLEADO',
                        'value' => mb_strtoupper($fullName), // Nombre en mayúsculas
                    ],
                ],
                'secondaryFields' => [
                    [
                        'key'   => 'numero',
                        'label' => 'Nº TARJETA',
                        'value' => $ean,
                    ],
                ],
            ],
            'barcode' => [
                'message'         => $ean,
                'format'          => 'PKBarcodeFormatQR', // Cambiado a QR para coincidir con la imagen
                'messageEncoding' => 'iso-8859-1',
            ],
        ]);

        // Añadir imágenes (Deben estar en la carpeta resources/apple del módulo)
        $modulePath = Yii::getAlias('@wallet/resources/apple');
        $pass->addPlaceholderImage("{$modulePath}/icon.png"); // Icono para notificaciones
        $pass->addPlaceholderImage("{$modulePath}/logo.png"); // Logo superior izquierda

        $fileContent = $pass->create();

        if (!$fileContent) {
            Yii::error("Error al crear Apple Pass: " . $pass->getError(), 'wallet');
            return null;
        }

        return $fileContent;
    }
}

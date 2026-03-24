<?php

namespace humhub\modules\wallet\models;

use Yii;
use PKPass\PKPass;

require_once Yii::getAlias('@wallet') . '/vendor/autoload.php';

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

        $this->certPath = Yii::getAlias('@wallet/resources/apple/certificado_vegalsa.p12');
        $this->wwdrPath   = Yii::getAlias('@wallet/resources/apple/wwdr.pem');

        $this->certPass   = $m->getAppleCertPass();
        $this->passTypeId = $m->getApplePassTypeId();
        $this->teamId     = $m->getAppleTeamId();
    }

    /**
     * Genera el archivo .pkpass firmado para iPhone
     */
    public function createPass(int $userId, string $fullName, string $ean): ?string
    {
        $pass = new PKPass($this->certPath, $this->certPass);
        $pass->setWWDRCertificatePath($this->wwdrPath);

        // Estructura del pase (Diseño tarjeta de empleado)
        $pass->setData([
            'formatVersion'      => 1,
            'passTypeIdentifier' => $this->passTypeId,
            'teamIdentifier'     => $this->teamId,
            'serialNumber'       => "user_{$userId}",
            'organizationName'   => 'Vegalsa Eroski',

            'backgroundColor'    => 'rgb(212, 46, 18)',
            'foregroundColor'    => 'rgb(255, 255, 255)',
            'labelColor'         => 'rgb(255, 255, 255)',
            'description'        => 'Tarjeta de Empleado Vegalsa',
            'sharingProhibited'  => true,

            'generic' => [
                'primaryFields' => [
                    [
                        'key'   => 'titular',
                        'label' => '',
                        'value' => mb_strtoupper($fullName),
                    ],
                ],
                'secondaryFields' => [
                    [
                        'key'   => 'numero_empleado',
                        'label' => '',
                        'value' => (string)$ean,
                    ],
                ],
            ],

            'barcodes' => [
                [
                    'message'         => (string)$ean,
                    'format'          => 'PKBarcodeFormatQR',
                    'messageEncoding' => 'iso-8859-1',
                    'altText'         => ''
                ]
            ],
        ]);

        // Añadir imágenes (Deben estar en la carpeta resources/apple del módulo)
        $modulePath = Yii::getAlias('@wallet/resources/apple');
        $pass->addFile("{$modulePath}/icon.png", 'icon.png');
        $pass->addFile("{$modulePath}/icon@2x.png", 'icon@2x.png');
        $pass->addFile("{$modulePath}/logo.png", 'logo.png');
        $pass->addFile("{$modulePath}/logo@2x.png", 'logo@2x.png');

        $fileContent = $pass->create();

        if (!$fileContent) {
            Yii::error("Error al crear Apple Pass: " . $pass->getError(), 'wallet');
            return null;
        }

        return $fileContent;
    }

    public function getLastError()
    {
        return $this->error;
    }
}

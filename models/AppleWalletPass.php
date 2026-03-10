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
     * Genera el .pkpass y devuelve la ruta al fichero temporal.
     */
    public function generate(int $userId, string $fullName, string $ean): string
    {
        $pass = new PKPass($this->certPath, $this->certPass);
        $pass->setWwdrCertPath($this->wwdrPath);

        $pass->setData([
            'description'        => 'Tarjeta de empleado Vegalsa Eroski',
            'formatVersion'      => 1,
            'organizationName'   => 'Vegalsa Eroski',
            'passTypeIdentifier' => $this->passTypeId,
            'serialNumber'       => 'user-' . $userId,
            'teamIdentifier'     => $this->teamId,
            'backgroundColor'    => 'rgb(46, 158, 107)',
            'foregroundColor'    => 'rgb(255, 255, 255)',
            'labelColor'         => 'rgb(255, 255, 255)',
            'logoText'           => 'Eroski',
            'storeCard' => [
                'primaryFields' => [
                    ['key' => 'titular', 'label' => 'Titular', 'value' => $fullName],
                ],
                'secondaryFields' => [
                    ['key' => 'numero', 'label' => 'Nº de tarjeta', 'value' => $ean],
                ],
            ],
            'barcode' => [
                'message'         => $ean,
                'format'          => 'PKBarcodeFormatEAN13',
                'messageEncoding' => 'iso-8859-1',
                'altText'         => $ean,
            ],
        ]);

        // Añade los assets gráficos del pass
        $resPath = Yii::getAlias('@wallet/resources/apple');
        if (file_exists("$resPath/icon.png"))  $pass->addFile('icon.png',  "$resPath/icon.png");
        if (file_exists("$resPath/icon@2x.png")) $pass->addFile('icon@2x.png', "$resPath/icon@2x.png");
        if (file_exists("$resPath/logo.png"))  $pass->addFile('logo.png',  "$resPath/logo.png");
        if (file_exists("$resPath/logo@2x.png")) $pass->addFile('logo@2x.png', "$resPath/logo@2x.png");

        $tmpFile = sys_get_temp_dir() . "/tarjeta_{$userId}_" . time() . '.pkpass';
        file_put_contents($tmpFile, $pass->create(true));

        return $tmpFile;
    }
}

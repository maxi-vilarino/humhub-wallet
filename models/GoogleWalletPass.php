<?php

namespace humhub\modules\wallet\models;

use Yii;
use Firebase\JWT\JWT;

class GoogleWalletPass
{
    private string $issuerId;
    private string $classId;
    private array  $credentials;

    public function __construct()
    {
        /** @var \humhub\modules\wallet\Module $m */
        $m = Yii::$app->getModule('wallet');
        $this->issuerId    = $m->getGoogleIssuerId();
        $this->classId     = $m->getGoogleClassId();
        $this->credentials = json_decode($m->getGoogleCredentials(), true);
    }

    /**
     * Construye el objeto Generic Pass, lo firma en JWT y
     * devuelve la URL "https://pay.google.com/gp/v/save/{jwt}".
     */
    public function createSaveUrl(int $userId, string $fullName, string $ean): string
    {
        $objectId = "{$this->issuerId}.user_{$userId}_" . time();

        $genericObject = [
            'id'                 => $objectId,
            'classId'            => "{$this->issuerId}.{$this->classId}",
            'genericType'        => 'GENERIC_TYPE_UNSPECIFIED',
            'hexBackgroundColor' => '#2e9e6b',
            'cardTitle' => [
                'defaultValue' => ['language' => 'es-ES', 'value' => 'Vegalsa Eroski'],
            ],
            'subheader' => [
                'defaultValue' => ['language' => 'es-ES', 'value' => 'Tarjeta de empleado'],
            ],
            'header' => [
                'defaultValue' => ['language' => 'es-ES', 'value' => $fullName],
            ],
            'barcode' => [
                'type'  => 'EAN_13',
                'value' => $ean,
            ],
            'textModulesData' => [
                ['header' => 'Nº de tarjeta', 'body' => $ean, 'id' => 'ean'],
            ],
            'state' => 'ACTIVE',
        ];

        $claims = [
            'iss'     => $this->credentials['client_email'],
            'aud'     => 'google',
            'typ'     => 'savetowallet',
            'iat'     => time(),
            'payload' => ['genericObjects' => [$genericObject]],
        ];

        $jwt = JWT::encode($claims, $this->credentials['private_key'], 'RS256');

        return "https://pay.google.com/gp/v/save/{$jwt}";
    }
}

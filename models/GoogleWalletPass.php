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
     * Construye el objeto Generic Pass con diseño corporativo Vegalsa,
     * lo firma en JWT y devuelve la URL de guardado.
     */
    public function createSaveUrl(int $userId, string $fullName, string $ean, string $logoUrl): string
    {
        $objectId = "{$this->issuerId}.user_{$userId}";

        $genericObject = [
            'id'                 => $objectId,
            'classId'            => "{$this->issuerId}.{$this->classId}",
            'genericType'        => 'GENERIC_TYPE_UNSPECIFIED',

            // ROJO CORPORATIVO EXACTO (Pantone 485 C / RGB 212, 46, 18)
            'hexBackgroundColor' => '#D42E12',

            'logo' => [
                'sourceUri' => [
                    'uri' => $logoUrl,
                ]
            ],

            'cardTitle' => [
                'defaultValue' => ['language' => 'es-ES', 'value' => 'Vegalsa Eroski'],
            ],

            // HEADER: Nombre del empleado en grande
            'header' => [
                'defaultValue' => ['language' => 'es-ES', 'value' => mb_strtoupper($fullName)],
            ],

            // CÓDIGO QR: Con el número EAN debajo
            'barcode' => [
                'type'          => 'QR_CODE',
                'value'         => (string)$ean,
                'alternateText' => (string)$ean,
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

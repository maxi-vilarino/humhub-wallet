<?php

namespace humhub\modules\wallet\models;

use Yii;
use yii\base\Model;

class AdminSettingsForm extends Model
{
    // Campos para Google Wallet
    public $googleIssuerId;
    public $googleClassId;
    public $googleCredentialsJson;

    // Campos para Apple Wallet
    public $appleCertPath;
    public $appleCertPass;
    public $appleWwdrPath;
    public $applePassTypeId;
    public $appleTeamId;

    public function rules()
    {
        return [
            [['googleIssuerId', 'googleClassId', 'googleCredentialsJson'], 'safe'],
            [['appleCertPath', 'appleWwdrPath', 'applePassTypeId', 'appleTeamId'], 'safe'],
            ['appleCertPass', 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'googleIssuerId'        => 'Google Issuer ID (Numérico)',
            'googleClassId'         => 'Google Class ID (ej: tarjeta_empleados)',
            'googleCredentialsJson' => 'JSON de Credenciales de Google',
            'appleCertPath'         => 'Ruta absoluta al certificado Apple (.pem)',
            'appleCertPass'         => 'Contraseña del certificado Apple (si tiene)',
            'appleWwdrPath'         => 'Ruta absoluta al WWDR de Apple (.pem)',
            'applePassTypeId'       => 'Apple Pass Type ID (pass.com...)',
            'appleTeamId'           => 'Apple Team ID',
        ];
    }

    /**
     * Carga los ajustes actuales desde la base de datos de HumHub
     */
    public function loadSettings()
    {
        $settings = Yii::$app->getModule('wallet')->settings;

        $this->googleIssuerId        = $settings->get('googleIssuerId');
        $this->googleClassId         = $settings->get('googleClassId');
        $this->googleCredentialsJson = $settings->get('googleCredentialsJson');

        $this->appleCertPath         = $settings->get('appleCertPath');
        $this->appleCertPass         = $settings->get('appleCertPass');
        $this->appleWwdrPath         = $settings->get('appleWwdrPath');
        $this->applePassTypeId       = $settings->get('applePassTypeId');
        $this->appleTeamId = $settings->get('appleTeamId');

        return true;
    }

    /**
     * Guarda los nuevos ajustes en la base de datos
     */
    public function save()
    {
        $settings = Yii::$app->getModule('wallet')->settings;

        $settings->set('googleIssuerId',        $this->googleIssuerId);
        $settings->set('googleClassId',         $this->googleClassId);
        $settings->set('googleCredentialsJson', $this->googleCredentialsJson);

        $settings->set('appleCertPath',         $this->appleCertPath);
        $settings->set('appleCertPass',         $this->appleCertPass);
        $settings->set('appleWwdrPath',         $this->appleWwdrPath);
        $settings->set('applePassTypeId',       $this->applePassTypeId);
        $settings->set('appleTeamId',           $this->appleTeamId);

        return true;
    }
}

<?php

namespace humhub\modules\wallet\models;

use Yii;
use yii\base\Model;

class AdminSettingsForm extends Model
{
    public $googleCredentialsJson = '';
    public $googleIssuerId = '';
    public $googleClassId = '';
    public $appleCertPath = '';
    public $appleCertPass = '';
    public $appleWwdrPath = '';
    public $applePassTypeId = '';
    public $appleTeamId = '';

    public function init()
    {
        parent::init();
        $m = Yii::$app->getModule('wallet');
        $this->googleCredentialsJson = $m->getGoogleCredentials();
        $this->googleIssuerId  = $m->getGoogleIssuerId();
        $this->googleClassId   = $m->getGoogleClassId();
        $this->appleCertPath   = $m->getAppleCertPath();
        $this->appleCertPass   = $m->getAppleCertPass();
        $this->appleWwdrPath   = $m->getAppleWwdrPath();
        $this->applePassTypeId = $m->getApplePassTypeId();
        $this->appleTeamId     = $m->getAppleTeamId();
    }

    public function rules()
    {
        return [
            [array_keys($this->attributes), 'string'],
        ];
    }

    public function save(): void
    {
        $m = Yii::$app->getModule('wallet');
        foreach ($this->attributes as $key => $value) {
            $m->settings->set($key, $value);
        }
    }
}

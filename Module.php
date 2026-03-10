<?php

namespace humhub\modules\wallet;

use humhub\components\Module as BaseModule;

class Module extends BaseModule
{
    public $resourcesPath = 'resources';

    const S_GOOGLE_CREDENTIALS = 'googleCredentialsJson';
    const S_GOOGLE_ISSUER_ID   = 'googleIssuerId';
    const S_GOOGLE_CLASS_ID    = 'googleClassId';
    const S_APPLE_CERT_PATH    = 'appleCertPath';
    const S_APPLE_CERT_PASS    = 'appleCertPass';
    const S_APPLE_WWDR_PATH    = 'appleWwdrPath';
    const S_APPLE_PASS_TYPE_ID = 'applePassTypeId';
    const S_APPLE_TEAM_ID      = 'appleTeamId';

    public function getGoogleCredentials(): string
    {
        return $this->settings->get(self::S_GOOGLE_CREDENTIALS, '');
    }
    public function getGoogleIssuerId(): string
    {
        return $this->settings->get(self::S_GOOGLE_ISSUER_ID, '');
    }
    public function getGoogleClassId(): string
    {
        return $this->settings->get(self::S_GOOGLE_CLASS_ID, '');
    }
    public function getAppleCertPath(): string
    {
        return $this->settings->get(self::S_APPLE_CERT_PATH, '');
    }
    public function getAppleCertPass(): string
    {
        return $this->settings->get(self::S_APPLE_CERT_PASS, '');
    }
    public function getAppleWwdrPath(): string
    {
        return $this->settings->get(self::S_APPLE_WWDR_PATH, '');
    }
    public function getApplePassTypeId(): string
    {
        return $this->settings->get(self::S_APPLE_PASS_TYPE_ID, '');
    }
    public function getAppleTeamId(): string
    {
        return $this->settings->get(self::S_APPLE_TEAM_ID, '');
    }
}

<?php

namespace humhub\modules\vegalsa_wallet\controllers;

use Yii;
use humhub\components\Controller;
use humhub\modules\wallet\models\GoogleWalletPass;
use humhub\modules\wallet\models\AppleWalletPass;
use humhub\modules\barcode\components\BarcodeGenerator;

class WalletController extends Controller
{
    /**
     * Genera el JWT y redirige a la URL "Añadir a Google Wallet".
     */
    public function actionGoogle()
    {
        $this->requireLogin();
        $user     = Yii::$app->user->identity;
        $ean      = BarcodeGenerator::completarEAN13($user->profile->fax ?? '');
        $fullName = $user->displayName ?? $user->username ?? '';

        $saveUrl = (new GoogleWalletPass())->createSaveUrl($user->id, $fullName, $ean);

        return $this->redirect($saveUrl);
    }

    /**
     * Genera el .pkpass y lo devuelve como descarga.
     */
    public function actionApple()
    {
        $this->requireLogin();
        $user     = Yii::$app->user->identity;
        $ean      = BarcodeGenerator::completarEAN13($user->profile->fax ?? '');
        $fullName = $user->displayName ?? $user->username ?? '';

        $tmpFile = (new AppleWalletPass())->generate($user->id, $fullName, $ean);

        return Yii::$app->response->sendFile(
            $tmpFile,
            'tarjeta-vegalsa.pkpass',
            ['mimeType' => 'application/vnd.apple.pkpass', 'inline' => false]
        );
    }
}

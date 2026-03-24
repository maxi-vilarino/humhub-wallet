<?php

namespace humhub\modules\wallet\controllers;

use Yii;
use humhub\components\Controller;
use humhub\modules\wallet\models\GoogleWalletPass;
use humhub\modules\wallet\models\AppleWalletPass;
// Usamos el mismo generador que usas en tu vista para evitar errores
use humhub\modules\qrcode\QrGenerator;

class WalletController extends Controller
{
    /**
     * Genera el JWT y redirige a la URL de Google Wallet.
     */
    public function actionGoogle()
    {
        $this->requireLogin();
        $user     = Yii::$app->user->identity;

        // Sincronizamos con el generador de tu vista
        $ean      = QrGenerator::completarEAN13($user->profile->fax ?? '');
        $fullName = $user->displayName ?? $user->username ?? '';

        $saveUrl = (new GoogleWalletPass())->createSaveUrl($user->id, $fullName, $ean);

        return $this->redirect($saveUrl);
    }

    /**
     * Genera el archivo .pkpass y lo sirve al iPhone.
     */
    public function actionApple()
    {
        $this->requireLogin();
        $user     = Yii::$app->user->identity;

        $ean      = QrGenerator::completarEAN13($user->profile->fax ?? '');
        $fullName = $user->displayName ?? $user->username ?? '';

        // Importante: Llamamos a 'createPass' que devuelve el contenido binario
        $passContent = (new AppleWalletPass())->createPass($user->id, $fullName, $ean);

        if (!$passContent) {
            throw new \yii\web\ServerErrorHttpException("No se pudo generar la tarjeta de Apple.");
        }

        // Enviamos el contenido directamente como un archivo descargable
        return Yii::$app->response->sendContentAsFile(
            $passContent,
            'tarjeta-empleado.pkpass',
            [
                'mimeType' => 'application/vnd.apple.pkpass',
                'inline'   => false
            ]
        );
    }
}

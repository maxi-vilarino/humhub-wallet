<?php

namespace humhub\modules\wallet\controllers;

use Yii;
use humhub\components\Controller;
use humhub\modules\wallet\models\GoogleWalletPass;
use humhub\modules\wallet\models\AppleWalletPass;
use humhub\modules\qrcode\QrGenerator;
use yii\web\HttpException;
use yii\web\Response;

class WalletController extends Controller
{
    /**
     * Definimos que para entrar a CUALQUIER acción de este 
     * controlador, el usuario debe estar logueado.
     */
    public function getAccessRules()
    {
        return [
            ['login']
        ];
    }

    /**
     * Acción para Apple Wallet (.pkpass)
     */
    public function actionApple($ean)
    {
        $user = Yii::$app->user->identity;

        $applePass = new AppleWalletPass();
        $content = $applePass->createPass($user->id, $user->displayName, $ean);

        if ($content) {
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_RAW;
            $response->headers->add('Content-Type', 'application/vnd.apple.pkpass');
            $response->headers->add('Content-Disposition', 'attachment; filename="tarjeta_vegalsa.pkpass"');

            return $content;
        } else {
            Yii::error("ERROR DETALLADO APPLE: " . $applePass->getLastError(), 'wallet');
        }

        throw new HttpException(500, "Error al generar el archivo Apple Wallet.");
    }

    /**
     * Acción para Google Wallet (Redirección)
     */
    public function actionGoogle()
    {
        $user = Yii::$app->user->identity;

        // Recuperamos el EAN igual que en tu vista
        $fax = $user->profile->fax ?? '';
        $ean = QrGenerator::completarEAN13($fax);
        $fullName = $user->displayName ?? $user->username ?? '';

         // Publica resources/vegalsa/ en web/assets/ y obtiene su URL
        [, $publishedUrl] = Yii::$app->assetManager->publish(
            Yii::getAlias('@wallet/resources/vegalsa')
        );

        $logoUrl = Yii::$app->urlManager->getHostInfo() . $publishedUrl . '/logo_v3.png';

        $saveUrl = (new GoogleWalletPass())->createSaveUrl($user->id, $fullName, $ean, $logoUrl);

        // Redirigimos al usuario a la página de Google
        return $this->redirect($saveUrl);
    }
}

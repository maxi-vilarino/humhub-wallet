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
            ['login', 'except' => ['apple-serve', 'apple-landing']],
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

        // URL absoluta: https://tudominio.com/assets/XXXXXXXX/logo_v3.png
        $logoUrl = Yii::$app->urlManager->getHostInfo() . $publishedUrl . '/logo_v3.png';

        $saveUrl = (new GoogleWalletPass())->createSaveUrl($user->id, $fullName, $ean, $logoUrl);

        // Redirigimos al usuario a la página de Google
        return $this->redirect($saveUrl);
    }

        /**
     * Genera el .pkpass, lo guarda en runtime con un token temporal
     * y devuelve la URL pública (sin auth) para que Safari lo descargue.
     */
    public function actionApplePrepare(): Response
    {
        $user     = Yii::$app->user->identity;
        $fax      = $user->profile->fax ?? '';
        $ean      = QrGenerator::completarEAN13($fax);
        $fullName = $user->displayName ?? $user->username ?? '';

        $applePass = new AppleWalletPass();
        $content   = $applePass->createPass($user->id, $fullName, $ean);

        if (!$content) {
            Yii::error("Error Apple Pass: " . $applePass->getLastError(), 'wallet');
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $this->asJson(['error' => 'Error al generar el archivo']);
        }

        // Token de un solo uso, expira en 5 minutos
        $token   = \Yii::$app->security->generateRandomString(32);
        $dir     = Yii::getAlias('@runtime/wallet');
        $expiry  = time() + 300;

        if (!is_dir($dir)) {
            mkdir($dir, 0750, true);
        }

        // Guarda el .pkpass y los metadatos del token
        file_put_contents("{$dir}/{$token}.pkpass", $content);
        file_put_contents("{$dir}/{$token}.json", json_encode([
            'expires' => $expiry,
            'userId'  => $user->id,
        ]));

        $serveUrl = \yii\helpers\Url::to(['/wallet/wallet/apple-serve', 'token' => $token], true);

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->asJson(['url' => $serveUrl]);
    }

    /**
     * Sirve el .pkpass con token temporal — NO requiere autenticación.
     * Safari lo descarga y iOS lo abre directamente en Wallet.
     */
    public function actionAppleServe(string $token): Response
    {
        if (!preg_match('/^[a-zA-Z0-9_-]{32}$/', $token)) {
            throw new HttpException(400, 'Token inválido.');
        }

        $dir      = Yii::getAlias('@runtime/wallet');
        $pkpass   = "{$dir}/{$token}.pkpass";
        $metaFile = "{$dir}/{$token}.json";

        if (!file_exists($pkpass) || !file_exists($metaFile)) {
            throw new HttpException(404, 'Token no encontrado o expirado.');
        }

        $meta = json_decode(file_get_contents($metaFile), true);

        if (time() > $meta['expires']) {
            @unlink($pkpass);
            @unlink($metaFile);
            throw new HttpException(410, 'El enlace ha expirado. Vuelve a intentarlo.');
        }

        $content = file_get_contents($pkpass);

        $response = Yii::$app->response;
        $response->format = Response::FORMAT_RAW;
        $response->headers->add('Content-Type', 'application/vnd.apple.pkpass');
        $response->headers->add('Content-Disposition', 'inline; filename="tarjeta_vegalsa.pkpass"');

        $response->content = $content;
        return $response;
    }

}
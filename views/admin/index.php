<?php

use humhub\widgets\Button;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin();
?>
<div class="panel panel-default">
    <div class="panel-heading"><strong>Google Wallet</strong></div>
    <div class="panel-body">
        <?= $form->field($model, 'googleIssuerId')->textInput(['placeholder' => 'Ej: 3388000000012345678']) ?>
        <?= $form->field($model, 'googleClassId')->textInput(['placeholder' => 'Ej: tarjeta_empleado']) ?>
        <?= $form->field($model, 'googleCredentialsJson')->textarea(['rows' => 6, 'placeholder' => 'Pega aquí el JSON de la cuenta de servicio']) ?>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading"><strong>Apple Wallet</strong></div>
    <div class="panel-body">
        <?= $form->field($model, 'applePassTypeId')->textInput(['placeholder' => 'pass.com.vegalsa.tarjeta']) ?>
        <?= $form->field($model, 'appleTeamId')->textInput(['placeholder' => 'Tu Team ID de Apple Developer']) ?>
        <?= $form->field($model, 'appleCertPath')->textInput(['placeholder' => '/ruta/absoluta/certificate.p12']) ?>
        <?= $form->field($model, 'appleCertPass')->passwordInput() ?>
        <?= $form->field($model, 'appleWwdrPath')->textInput(['placeholder' => '/ruta/absoluta/AppleWWDRCA.pem']) ?>
    </div>
</div>
<?= Button::save()->submit() ?>
<?php ActiveForm::end(); ?>
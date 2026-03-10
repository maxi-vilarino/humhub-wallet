<?php

namespace humhub\modules\wallet\controllers;

use Yii;
use humhub\modules\admin\components\Controller;
use humhub\modules\wallet\models\AdminSettingsForm;

class AdminController extends Controller
{
    public function actionIndex()
    {
        $form = new AdminSettingsForm();

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $form->save();
            $this->view->saved();
        }

        return $this->render('index', ['model' => $form]);
    }
}

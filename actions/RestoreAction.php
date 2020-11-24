<?php

namespace app\actions;

use Yii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;
use cornernote\returnurl\ReturnUrl;

/**
 * generic action to delete existing model
 *
 * @author Fredy Nurman Saleh <email@fredyns.net>
 */
class RestoreAction extends Action
{
    public $modelClass;
    public $redirectUrl;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if (empty($this->modelClass)) {
            throw new InvalidConfigException('Model class must be defined.');
        }
    }

    public function run($id)
    {
        $model = $this->modelClass::findOne($id);

        if (empty($model)) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        try {
            $model->restore();
        } catch (\Exception $exception) {
            Yii::$app->getSession()->addFlash('error', $exception->getMessage());

            return $this->controller->redirect(ReturnUrl::getUrl());
        }

        $redirectUrl = $this->resolveRedirectUrl($model);
        return $this->controller->redirect($redirectUrl);
    }

    /**
     * resolve url to redirect when creation successful
     *
     * @param \yii\db\ActiveRecord $model
     * @return array
     */
    private function resolveRedirectUrl($model)
    {
        if (is_array($this->redirectUrl) or is_string($this->redirectUrl)) {
            return $this->redirectUrl;
        }

        if (is_callable($this->redirectUrl)) {
            return call_user_func($this->redirectUrl, $model);
        }

        $altUrl = $this->viewUrl();
        return ReturnUrl::getUrl($altUrl);
    }

    /**
     * @param \yii\db\ActiveRecord $model
     * @return array
     */
    private function viewUrl($model)
    {
        $params = (array)$model->getPrimaryKey(true);
        $params[0] = 'view';
        return $params;
    }

}
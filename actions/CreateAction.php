<?php

namespace app\actions;

use cornernote\returnurl\ReturnUrl;
use Yii;
use yii\base\Action;
use yii\base\InvalidConfigException;

/**
 * generic action to create new model
 *
 * @author Fredy Nurman Saleh <email@fredyns.net>
 */
class CreateAction extends Action
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

    /**
     * @return string
     * @throws InvalidConfigException
     */
    public function run()
    {
        $model = Yii::createObject($this->modelClass);

        if ($this->save($model)) {
            $redirectUrl = $this->resolveRedirectUrl($model);
            return $this->controller->redirect($redirectUrl);
        }

        return $this->controller->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * load form values & save model or load get params as default value
     * @param \yii\db\ActiveRecord $model to save
     * @return bool
     */
    private function save($model)
    {
        if (!Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->get());
            return false;
        }

        if ($model->load(Yii::$app->request->post()) === false) {
            return false;
        }

        try {
            return $model->save();
        } catch (\Exception $e) {
            $model->addError('_exception', $e->getMessage());
            return false;
        }
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

        $altUrl = $this->viewUrl($model);
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
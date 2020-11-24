<?php

namespace app\actions;

use Yii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;

/**
 * Generic action to view model details
 *
 * @author Fredy Nurman Saleh <email@fredyns.net>
 */
class ViewAction extends Action
{
    public $modelClass;
    public $view = 'view';

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
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function run($id)
    {
        $model = $this->modelClass::findOne($id);

        if (empty($model)) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        return $this->controller->render($this->view, [
            'model' => $model,
        ]);
    }

}
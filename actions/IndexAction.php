<?php

namespace app\actions;

use Yii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;

/**
 * Generic action to index/browse models
 *
 * @author Fredy Nurman Saleh <email@fredyns.net>
 */
class IndexAction extends Action
{
    public $searchClass;
    public $view = 'index';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if (empty($this->searchClass)) {
            throw new InvalidConfigException('Search model must be defined.');
        }
    }

    /**
     * @return string
     * @throws InvalidConfigException
     */
    public function run()
    {
        /* @var $searchModel ActiveRecord */
        $searchModel = Yii::createObject($this->searchClass);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->controller->render($this->view, [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

}
<?php

namespace app\actions;

use Yii;
use yii\helpers\ArrayHelper;
use yii\db\Query;

/**
 * Description of Select2Options
 *
 * @author Fredy Nurman Saleh <email@fredyns.net>
 */
class Select2Options extends \yii\rest\Action
{
    public $modelClass;
    public $minInputLength = 2;
    public $key_field = 'id';
    public $text_field = 'name';
    public $search_field;
    public $limit = 20;
    public $andWhere;

    /**
     * providing model list for select2 component
     *
     * @param string $q
     * @param integer $id
     * @return mixed
     * @throws \Exception
     */
    public function run($q = null, $id = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $results = [];

        if (!is_null($q) && strlen($q) >= $this->minInputLength) {
            $results = $this->findAll($q);
        } elseif ($id > 0) {
            if ($model = $this->modelClass::findOne($id)) {
                $results[] = [
                    'id' => $id,
                    'text' => ArrayHelper::getValue($model, $this->text_field),
                ];
            }
        }

        if (empty($results)) {
            $results[] = [
                'id' => '',
                'text' => '',
            ];
        }

        return compact('results');
    }

    public function findAll($q)
    {
        $query = new Query;
        $query
            ->select($this->columns())
            ->from($this->modelClass::tableName())
            ->where($this->conditions($q))
            ->limit($this->limit);

        if ($this->andWhere instanceof \Closure) {
            call_user_func_array($this->andWhere, [$query, $q]);
        }

        $command = $query->createCommand();
        $data = $command->queryAll();

        return array_values($data);
    }

    public function columns()
    {
        $cols = [];

        if ($this->key_field === 'id') {
            $cols[] = 'id';
        } else {
            $cols['id'] = $this->key_field;
        }

        if ($this->text_field === 'text') {
            $cols[] = 'text';
        } else {
            $cols['text'] = $this->text_field;
        }

        return $cols;
    }

    protected function conditions($q)
    {
        $operator = (Yii::$app->db->driverName == 'pgsql') ? 'ilike' : 'like';
        if (is_array($this->search_field)) {
            $conditions = ['or'];

            foreach ($this->search_field as $field) {
                if (is_string($field)) {
                    $conditions[] = [$operator, $field, $q];
                }
            }

            if (count($conditions) > 1) {
                return $conditions;
            }
        } elseif (is_string($this->search_field)) {
            return [$operator, $this->search_field, $q];
        }

        return [$operator, $this->text_field, $q];
    }

}
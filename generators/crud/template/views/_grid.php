<?php

/* @var $this yii\web\View  */
/* @var $generator app\generators\crud\Generator  */
/* resource */
/* @var $tableName string  */
/* @var $tableSchema TableSchema  */
/* @var $model yii\db\ActiveRecord  */
/* scope */
/* @var $moduleId string  */
/* @var $subNamespace string  */
/* translation */
/* @var $i18n bool  */
/* @var $messageCategory string  */
/* model */
/* @var $modelNamespace string  */
/* @var $modelClassName string  */
/* @var $modelClass string  */
/* @var $isSoftDelete bool  */
/* @var $modelNameSingular string  */
/* @var $modelNamePlural string  */
/* @var $labels string[]  */
/* @var $dictionaries array  */
/* @var $relations array  */
/* @var $groupedRules array  */
/* @var $nameAttribute string  */
/* @var $safeAttributes string[]  */
/* search */
/* @var $searchNamespace string  */
/* @var $searchClassName string  */
/* @var $searchClass string  */
/* @var $dateFilters array  */
/* @var $timestampFilters array  */
/* form */
/* @var $formNamespace string  */
/* @var $formClassName string  */
/* @var $formClass string  */
/* controller */
/* @var $controllerNamespace string  */
/* @var $controllerClassName string  */
/* @var $controllerClass string  */
/* @var $useGenericActions string  */
/* @var $useTransaction bool  */
/* view */
/* @var $viewPath string  */
/* @var $indexWidgetType string  */
/* @var $gridColumns string[]  */
/* @var $formLayout string  */
/* @var $modelSlug string  */
/* @var $urlParams string  */

echo "<?php\n";
?>

use <?= $searchClass ?>;
use <?= $modelClass ?>;
use cornernote\returnurl\ReturnUrl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel <?= $searchClassName ?> */
?>

<div>
    <?= "<?=\n" ?>
    \kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'pager' => [
            'class' => \yii\widgets\LinkPager::class,
            'firstPageLabel' => Yii::t('cruds', "First"),
            'lastPageLabel' => Yii::t('cruds', "Last"),
        ],
        'filterModel' => $searchModel,
        'responsive' => false,
        'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
        'headerRowOptions' => ['class' => 'x'],
        'columns' => [
            [
                'class' => \kartik\grid\SerialColumn::class,
            ],
<?php foreach ($gridColumns as $attribute): ?>
<?php if ($rangeKey = array_search($attribute, $dateFilters)):?>
            [
                'attribute' => '<?= $attribute ?>',
                'format' => [
                    'date',
                    'format' => 'eee, d MMM Y',
                ],
                'filter' => $searchModel-><?= $rangeKey ?>Search->filterWidget(),
            ],
<?php continue; ?>
<?php elseif ($rangeKey = array_search($attribute, $timestampFilters)):?>
            [
                'attribute' => '<?= $attribute ?>',
                'format' => [
                    'datetime',
                    'format' => 'eee, d MMM Y, H:m',
                ],
                'filter' => $searchModel-><?= $rangeKey ?>Search->filterWidget(),
            ],
<?php continue; ?>
<?php endif;?>
<?php
$format = trim($generator->columnFormat($attribute, $model));
if ($format === false) {
    continue;
} else {
    echo "            " . $format . ",\n";
}
?>
<?php endforeach;?>
            [
                'class' => \kartik\grid\ActionColumn::class,
                'width' => '100px',
                'template' => '{view}&nbsp; {update}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        $label = '<span class="glyphicon glyphicon-eye-open"></span>';
                        $hoverText = Yii::t('cruds', 'view this record');
                        $options = [
                            'title' => $hoverText,
                            'aria-label' => $hoverText,
                            'data-pjax' => '0',
                        ];
                        return Html::a($label, $url, $options);
                    },
                    'update' => function ($url, $model, $key) {
                        $label = '<span class="glyphicon glyphicon-pencil"></span>';
                        $hoverText = Yii::t('cruds', 'update this record');
                        $options = [
                            'title' => $hoverText,
                            'aria-label' => $hoverText,
                            'data-pjax' => '0',
                        ];
                        return Html::a($label, $url, $options);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    // using the column name as key, not mapping to 'id' like the standard generator
                    $params = is_array($key) ? $key : [$model->primaryKey()[0] => (string)$key];
                    $params[0] = \Yii::$app->controller->id ? \Yii::$app->controller->id . '/' . $action : $action;
                    $params['ru'] = ReturnUrl::getToken();
                    return Url::toRoute($params);
                },
                'contentOptions' => ['nowrap' => 'nowrap'],
            ],
        ],
    ]);
    ?>
</div>

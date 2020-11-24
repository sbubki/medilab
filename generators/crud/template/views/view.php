<?php

use yii\helpers\Inflector;

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

use <?= $modelClass ?>;
use cornernote\returnurl\ReturnUrl;
use yii\bootstrap\ButtonDropdown;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model <?= $modelClassName ?> */

$this->title = <?= $generator->generateString("View {$modelNameSingular}") ?> . ' #' . $model->id;
<?php if ($moduleId !== 'app'): ?>
$this->params['breadcrumbs'][] = Yii::t('<?= $moduleId ?>', '<?= Inflector::camel2words($moduleId) ?>');
<?php endif; ?>
<?php if ($subNamespace): ?>
$this->params['breadcrumbs'][] = <?= $generator->generateString(Inflector::camel2words($subNamespace)) ?>;
<?php endif; ?>
$this->params['breadcrumbs'][] = ['label' => $model->modelLabel(true), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model-><?= $nameAttribute ?>, 'url' => ['view', <?= $urlParams ?>]];
$this->params['breadcrumbs'][] = Yii::t('cruds', "View");
?>
<div class="app-crud <?= $modelSlug ?>-view">

    <div class="clearfix crud-navigation" style="padding-top: 30px;">
        <div class="pull-left">
            <h1 style="margin-top: 0;">
                <?= '<?=' ?> $model->modelLabel() ?>
                <small>
                    #<?= '<?='?> $model-><?= $nameAttribute ?> ?>
<?php if($isSoftDelete):?>
                    <?= '<?php' ?> if ($model->is_deleted): ?>
                        <span class="badge"><?= '<?=' ?> Yii::t('cruds', "Deleted") ?></span>
                    <?= '<?php' ?> endif; ?>
<?php endif;?>
                </small>
            </h1>
        </div>

        <!-- menu buttons -->
        <div class="pull-right">
            <div>
                <?= "<?php\n" ?>
                $text = Yii::t('cruds', 'Edit');
                $label = '<span class="glyphicon glyphicon-pencil"></span> ' . $text;
                $url = [
                    'update',
                    'id' => $model->id,
                    'ru' => ReturnUrl::getToken(),
                ];
                $options = [
                    'class' => 'btn btn-info',
                    'title' => $text,
                    'aria-label' => $text,
                ];
                echo Html::a($label, $url, $options);
                ?>

            </div>
        </div>

    </div>

    <hr/>

<?= $generator->partialView('detail_prepend', $model); ?>
    <?= "<?=\n" ?>
    DetailView::widget([
        'model' => $model,
        'attributes' => [
<?php
    foreach ($safeAttributes as $attribute) {
        $format = $generator->attributeFormat($attribute);
        if (!$format) {
            continue;
        } else {
            echo str_repeat(' ', 4).$format.",\n";
        }
    }
?>
        ],
        'options' => [
            'tag' => "div",
            'class' => "detail-view",
        ],
        'template' => '
            <div class="row form-group">
                <div class="col-sm-2" align="right">
                    <label {captionOptions}>{label}</label>
                </div>
                <div class="col-sm-10">
                    <div {contentOptions}>{value}</div>
                </div>
            </div>',
    ]);
    ?>

<?= $generator->partialView('detail_append', $model); ?>
    <hr/>
<?php if (isset($relations['hasMany']) && count($relations['hasMany']) > 0): ?>
<?php foreach ($relations['hasMany'] as $name => $relation): ?>
<?php
if (method_exists($model, 'get'.$name) === FALSE) {
    continue;
}
?>
    <br/>
    <h3><?= '<?= '.$generator->generateString(Inflector::camel2words($name, TRUE)) ?> ?></h3>
    <div class="table-responsive">
        <?= "<?=\n" ?>
        \kartik\grid\GridView::widget([
            'layout' => '{summary}{pager}<br/>{items}{pager}',
            'dataProvider' => new \yii\data\ActiveDataProvider([
                'query' => $model->get<?= $name ?>(),
                'pagination' => [
                    'pageSize' => 20,
                    'pageParam' => 'page-<?= Inflector::slug($name) ?>',
                ],
            ]),
            'columns' => [
                [
                    'class' => \kartik\grid\SerialColumn::class,
                ],
<?php
$relModelClass = $relation['namespace'] . "\\" . $relation['className'];
$relModel = new $relModelClass;
$allAttributes = $relModel->safeAttributes();
$skipCols = ['id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'is_deleted', 'deleted_at', 'deleted_by'];
$safeAttributes = array_diff($allAttributes, $skipCols);

$max_columns = 12;
$count = 0;
foreach ($safeAttributes as $attribute) {
    $format = trim($generator->columnFormat($attribute, $relModel));
    if ($format == false) {
        $format = "'{$attribute}'";
    }
    if (++$count < $max_columns) {
        echo str_repeat(' ', 16).str_replace("\n", "\n".str_repeat(' ', 16), $format) . ",\n";
    } else {
        echo str_repeat(' ', 16) . "/* //\n"
            .str_repeat(' ', 16).str_replace("\n", "\n".str_repeat(' ', 16), $format) . ",\n"
            .str_repeat(' ', 16) . "// */\n";
    }
}
?>
            ],
        ]);
        ?>
    </div>
<?php endforeach; ?>
<?php endif; ?>
    <br/>
    <hr/>

    <div class="clearfix">
        <!-- danger menu buttons -->
        <div class='pull-right'>
<?php if ($isSoftDelete): ?>
            <?= "<?php" ?> if ($model->is_deleted): ?>
                <?= "<?php\n" ?>
                $label = '<span class="glyphicon glyphicon-refresh"></span> ' . Yii::t('cruds', 'Restore');
                $options = [
                    'class' => 'btn btn-warning',
                    'title' => Yii::t('cruds', 'Restore'),
                    'aria-label' => Yii::t('cruds', 'Restore'),
                    'data-confirm' => Yii::t('cruds', 'Are you sure to restore this item?'),
                    'data-method' => 'post',
                    'data-pjax' => FALSE,
                ];
                $url = [
                    'restore',
                    'id' => $model->id,
                    'ru' => ReturnUrl::urlToToken(Url::to(['view', 'id' => $model->id])),
                ];
                echo Html::a($label, $url, $options);
                ?>
            <?= "<?php" ?> else: ?>
                <?= "<?php\n" ?>
                $text = Yii::t('cruds', 'Delete');
                $label = '<span class="glyphicon glyphicon-trash"></span> ' . $text;
                $options = [
                    'class' => 'btn btn-danger',
                    'title' => $text,
                    'aria-label' => $text,
                    'data-confirm' => Yii::t('cruds', 'Are you sure to delete this item?'),
                    'data-method' => 'post',
                    'data-pjax' => FALSE,
                ];
                $url = [
                    'delete',
                    'id' => $model->id,
                    'ru' => ReturnUrl::urlToToken(Url::to(['index'])),
                ];
                echo Html::a($label, $url, $options);
                ?>
            <?= "<?php" ?> endif; ?>
<?php else: ?>
            <?= "<?php\n" ?>
            $text = Yii::t('cruds', 'Delete');
            $label = '<span class="glyphicon glyphicon-trash"></span> ' . $text;
            $options = [
                'class' => 'btn btn-danger',
                'title' => $text,
                'aria-label' => $text,
                'data-confirm' => Yii::t('cruds', 'Are you sure to delete this item?'),
                'data-method' => 'post',
                'data-pjax' => FALSE,
            ];
            $url = [
                'delete',
                'id' => $model->id,
                'ru' => ReturnUrl::urlToToken(Url::to(['index'])),
            ];
            echo Html::a($label, $url, $options);
            ?>
<?php endif; ?>
        </div>
    </div>

<?php if ($tableSchema->getColumn('created_at') !== null): ?>

    <div style="font-size: 75%; font-style: italic;">
        <?= '<?=' ?> Yii::t('record-info', 'Created') ?>
        <?= '<?=' ?> Yii::$app->formatter->asDate($model->created_at, "eeee, d MMMM Y '" . Yii::t('record-info', 'at') . "' HH:mm") ?>
<?php if ($tableSchema->getColumn('created_by') !== null): ?>
        <?= '<?=' ?> Yii::t('record-info', 'by') ?>
        <?= '<?=' ?> ArrayHelper::getValue($model, 'createdBy.name', Yii::t('app', 'Guest')) ?>
<?php endif; ?>
<?php if ($tableSchema->getColumn('updated_at') !== null): ?>
        <br/>
        <?= '<?=' ?> Yii::t('record-info', 'Updated') ?>
        <?= '<?=' ?> Yii::$app->formatter->asDate($model->updated_at, "eeee, d MMMM Y '" . Yii::t('record-info', 'at') . "' HH:mm") ?>
<?php if ($tableSchema->getColumn('updated_by') !== null): ?>
        <?= '<?=' ?> Yii::t('record-info', 'by') ?>
        <?= '<?=' ?> ArrayHelper::getValue($model, 'updatedBy.name', Yii::t('app', 'Guest')) ?>
<?php endif; ?>
<?php endif; ?>
<?php if ($tableSchema->getColumn('deleted_at') !== null): ?>
        <?='<?php'?> if ($model->is_deleted): ?>
            <br/>
            <?= '<?=' ?> Yii::t('record-info', 'Deleted') ?>
            <?= '<?=' ?> Yii::$app->formatter->asDate($model->deleted_at, "eeee, d MMMM Y '" . Yii::t('record-info', 'at') . "' HH:mm") ?>
<?php if ($tableSchema->getColumn('deleted_by') !== null): ?>
            <?= '<?=' ?> Yii::t('record-info', 'by') ?>
            <?= '<?=' ?> ArrayHelper::getValue($model, 'deletedBy.name', Yii::t('app', 'Guest')) ?>
<?php endif; ?>
        <?='<?php'?> endif; ?>
<?php endif; ?>
    </div>
<?php endif; ?>

</div>

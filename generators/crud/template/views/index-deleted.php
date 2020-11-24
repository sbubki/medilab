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

<?php if ($formClass): ?>
use <?= $formClass ?>;
<?php endif; ?>
use <?= $searchClass ?>;
use <?= $modelClass ?>;
use cornernote\returnurl\ReturnUrl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel <?= $searchClassName ?> */

$this->title = <?= $generator->generateString("Deleted {$modelNamePlural}") ?>;
<?php if ($moduleId !== 'app'): ?>
$this->params['breadcrumbs'][] = Yii::t('<?= $moduleId ?>', '<?= Inflector::camel2words($moduleId) ?>');
<?php endif; ?>
<?php if ($subNamespace): ?>
$this->params['breadcrumbs'][] = Yii::t('<?= $messageCategory ?>', '<?= Inflector::camel2words($subNamespace) ?>');
<?php endif; ?>
$this->params['breadcrumbs'][] = Yii::t('cruds', "Deleted");
?>

<div class="app-crud <?= $modelSlug ?>-index-deleted">

    <div class="clearfix crud-navigation" style="padding-top: 30px;">
        <div class="pull-left">
            <h1 style="margin-top: 0;">
                <?= '<?=' ?> $searchModel->modelLabel(TRUE) ?>
                <small>
                    <?= '<?=' ?> Yii::t('cruds', "Deleted") ?>
                </small>
            </h1>
        </div>
        <div class="pull-right">
            <div>

                <?= "<?php\n" ?>
                $text = Yii::t('cruds', 'Archive');
                $label = '<span class="glyphicon glyphicon-hdd"></span> ' . $text;
                $options = [
                    'class' => 'btn btn-default',
                    'title' => $text,
                    'aria-label' => $text,
                ];
                echo Html::a($label, ['index-all'], $options);
                ?>

                <?= "<?php\n" ?>
                $text = Yii::t('cruds', 'Main List');
                $label = '<span class="glyphicon glyphicon-list"></span> ' . $text;
                $options = [
                    'class' => 'btn btn-primary',
                    'title' => $text,
                    'aria-label' => $text,
                ];
                echo Html::a($label, ['index'], $options);
                ?>

            </div>
        </div>
    </div>

    <?= '<?php' ?> //= $this->render('_search', ['model' => $searchModel]); ?>

    <hr style="margin-top: 0;"/>

    <?= "<?php\n" ?>
    \yii\widgets\Pjax::begin([
        'id' => 'pjax-main',
        'enableReplaceState' => false,
        'linkSelector' => '#pjax-main ul.pagination a, th a',
        'clientOptions' => [
            'pjax:success' => 'function(){alert("yo")}',
        ],
    ]);
    ?>

    <?= '<?=' ?> $this->render('_grid', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]); ?>

    <?= "<?php" ?> \yii\widgets\Pjax::end() ?>

</div>

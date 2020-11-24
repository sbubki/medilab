<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

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

use cornernote\returnurl\ReturnUrl;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model <?= $modelClass ?> */

$this->title = <?= $generator->generateString("Edit {$modelNameSingular}") ?> . ' #' . $model->id;
<?php if ($moduleId !== 'app'): ?>
$this->params['breadcrumbs'][] = Yii::t('<?= $moduleId ?>', '<?= Inflector::camel2words($moduleId) ?>');
<?php endif; ?>
<?php if ($subNamespace): ?>
$this->params['breadcrumbs'][] = <?= $generator->generateString(Inflector::camel2words($subNamespace)) ?>;
<?php endif; ?>
$this->params['breadcrumbs'][] = ['label' => $model->modelLabel(true), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model-><?= $nameAttribute ?>, 'url' => ['view', <?= $urlParams ?>]];
$this->params['breadcrumbs'][] = Yii::t('cruds', "Edit");
?>
<div class="app-crud <?= $modelSlug ?>-update">

    <div class="clearfix crud-navigation" style="padding-top: 30px;">
        <div class="pull-left">
            <h1 style="margin-top: 0;">
                <?= $i18n ? '<?= ' . $generator->generateString("Edit {$modelNameSingular}") . " ?>\n" : "Edit {$modelNameSingular}\n" ?>
                <small>
                    #<?= '<?='?> $model->id ?>
                </small>
            </h1>
        </div>
        <div class="pull-right">
            <?= '<?=' ?> Html::a('<span class="glyphicon glyphicon-file"></span> ' . Yii::t('cruds', "View"), ['view', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
            <?= '<?=' ?> Html::a('<span class="glyphicon glyphicon-remove"></span> ' . Yii::t('cruds', "Cancel"), ReturnUrl::getUrl(Url::previous()), ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <hr/>

    <?= '<?=' ?> $this->render('_form', ['model' => $model]); ?>

</div>

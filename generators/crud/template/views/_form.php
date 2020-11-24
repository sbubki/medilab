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
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model <?= $modelClass ?> */
?>

<div class="<?= $modelSlug ?>-form">

    <?= "<?php\n" ?>
    $form = ActiveForm::begin([
        'id' => '<?= $model->formName() ?>',
        'layout' => '<?= $formLayout ?>',
        'enableClientValidation' => true,
        'errorSummaryCssClass' => 'error-summary alert alert-danger',
        'fieldConfig' => [
            'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
            'horizontalCssClasses' => [
                'label' => 'col-sm-2',
                #'offset' => 'col-sm-offset-4',
                'wrapper' => 'col-sm-8',
                'error' => '',
                'hint' => '',
            ],
        ],
    ]);
    echo Html::hiddenInput('ru', ReturnUrl::getRequestToken());
    ?>

    <div class="">
        <div class="">
<?php
foreach ($safeAttributes as $attribute) {
    echo "\n" . str_repeat(' ', 12)."<!-- attribute {$attribute} -->\n";
    $prepend = $generator->prependActiveField($attribute, $model);
    $field = $generator->activeField($attribute, $model);
    $append = $generator->appendActiveField($attribute, $model);

    if ($prepend) {
        echo str_repeat(' ', 12).str_replace("\n","\n".str_repeat(' ', 12),$prepend)."\n";
    }
    if (strpos($field,"\n")!==FALSE) {
        echo str_repeat(' ', 12)."<?=".str_replace("\n","\n".str_repeat(' ', 12),$field).'?>'."\n";
    } elseif ($field) {
        echo str_repeat(' ', 12)."<?= ".$field.' ?>'."\n";
    }
    if ($append) {
        echo str_repeat(' ', 12).str_replace("\n","\n".str_repeat(' ', 12),$append)."\n";
    }
}
?>

        </div>

        <hr/>

        <?= '<?= ' ?>$form->errorSummary($model); ?>

        <div class="form-group">
            <div class="col-sm-8 col-sm-offset-2">

                <?= "<?=\n" ?>
                Html::submitButton(
                    '<span class="glyphicon glyphicon-check"></span> '
                    . ($model->isNewRecord ? Yii::t('cruds', "Create") : Yii::t('cruds', "Save"))
                    , [
                        'id' => 'save-' . $model->formName(),
                        'class' => 'btn btn-success',
                    ]
                );
                ?>

            </div>
        </div>

    </div>

    <?= '<?php' ?> ActiveForm::end(); ?>

</div>
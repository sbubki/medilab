<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $generator app\generators\crud\Generator */
?>
<style>
    #generator-tablenames > label {display: block;}
</style>
<?= $form->field($generator, 'tableNames')->checkboxList($generator->tableOptions);?>
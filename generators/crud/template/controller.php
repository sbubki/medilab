<?php

use Brick\VarExporter\VarExporter;
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
/* @var $gridColumns string[]  */
/* @var $formLayout string  */
/* @var $modelSlug string  */
/* @var $urlParams string  */

$primaryKeys = $tableSchema->primaryKey;
$actionParams = $generator->generateActionParams();
$actionParamComments = $generator->generateActionParamComments();
$modelGenerator = new \app\generators\model\Generator();
$gotManyRelations = (isset($relations['hasMany']) && count($relations['hasMany']) > 0);

/**
 * generating generic actions
 */
$actions = [];
if (in_array('index',$useGenericActions) && $isSoftDelete) {
    $actions['index'] = [
        'class' => \app\actions\IndexAction::class,
        'searchClass' => [
            'class' => $searchClass,
            'is_deleted' => false,
        ],
    ];
} else if (in_array('index',$useGenericActions)) {
    $actions['index'] = [
        'class' => \app\actions\IndexAction::class,
        'searchClass' => $searchClass,
    ];
}
$genericActions = [
    'create' => [
        'class' => \app\actions\CreateAction::class,
        'modelClass' => $formClass ?: $modelClass,
    ],
    'view' => [
        'class' => \app\actions\ViewAction::class,
        'modelClass' => $modelClass,
    ],
    'update' => [
        'class' => \app\actions\UpdateAction::class,
        'modelClass' => $formClass ?: $modelClass,
    ],
    'delete' => [
        'class' => \app\actions\DeleteAction::class,
        'modelClass' => $modelClass,
    ],
    'restore' => [
        'class' => \app\actions\RestoreAction::class,
        'modelClass' => $modelClass,
    ],
    'index-deleted' => [
        'class' => \app\actions\IndexAction::class,
        'searchClass' => [
            'class' => $searchClass,
            'is_deleted' => true,
        ],
        'view' => "index-deleted",
    ],
    'index-all' => [
        'class' => \app\actions\IndexAction::class,
        'searchClass' => $searchClass,
        'view' => "index-all",
    ],
];
foreach ($genericActions as $actionId => $actionConfig) {
    if (in_array($actionId,$useGenericActions)) {
        $actions[$actionId] = $actionConfig;
    }
}
if ($actions) {
    $actionCodes = VarExporter::export($actions);
    $translations = [
        "'" . addslashes(\app\actions\IndexAction::class) . "'" => "\\" . \app\actions\IndexAction::class . "::class",
        "'" . addslashes(\app\actions\CreateAction::class) . "'" => "\\" . \app\actions\CreateAction::class . "::class",
        "'" . addslashes(\app\actions\ViewAction::class) . "'" => "\\" . \app\actions\ViewAction::class . "::class",
        "'" . addslashes(\app\actions\UpdateAction::class) . "'" => "\\" . \app\actions\UpdateAction::class . "::class",
        "'" . addslashes(\app\actions\DeleteAction::class) . "'" => "\\" . \app\actions\DeleteAction::class . "::class",
        "'" . addslashes(\app\actions\RestoreAction::class) . "'" => "\\" . \app\actions\RestoreAction::class . "::class",
        "'" . addslashes($modelClass) . "'" => "\\{$modelClass}::class",
        "'" . addslashes($searchClass) . "'" => "\\{$searchClass}::class",
        "\n" => "\n        ", // indent
    ];
    $actionCodes = str_replace(array_keys($translations), $translations, $actionCodes);
} else {
    $actionCodes = null;
}

echo "<?php\n";
?>

namespace <?= $controllerNamespace ?>;

<?php if ($formClass): ?>
use <?= $formClass ?>;
<?php endif; ?>
use <?= $generator->searchModelClass ?>;
use <?= $generator->modelClass ?>;
use cornernote\returnurl\ReturnUrl;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * This is the class for controller "<?= $controllerClassName ?>".
 */
class <?= $controllerClassName ?> extends Controller
{

<?php if ($actionCodes): ?>
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return <?= $actionCodes ?>;
    }

<?php endif; ?>
<?php if (!in_array('index', $useGenericActions)): ?>
    /**
     * Indexing all available <?= $modelClassName ?> models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new <?= $searchClassName . ($isSoftDelete ? "(['is_deleted' => FALSE])" : "") ?>;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

<?php endif; ?>
<?php if (!in_array('index-deleted', $useGenericActions)): ?>
    /**
     * List deleted <?= $modelClassName ?>.
     * @return mixed
     */
    public function actionIndexDeleted()
    {
        $searchModel = new <?= $searchClassName ?>(['is_deleted' => TRUE]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index-deleted', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

<?php endif; ?>
<?php if (!in_array('index-all', $useGenericActions)): ?>
    /**
     * Lists all <?= $modelClassName ?> models.
     * @return mixed
     */
    public function actionIndexArchive()
    {
        $searchModel = new <?= $searchClassName ?>;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index-archive', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

<?php endif; ?>
<?php if (array_diff(['view', 'update', 'delete', 'restore'], $useGenericActions)): ?>
    /**
     * Finds the <?= $modelClassName ?> model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * <?= implode("\n     * ", $actionParamComments)."\n" ?>
     * @return <?= $modelClassName ?> the loaded model
     * @throws HttpException if the model cannot be found
     */
    protected function findModel(<?= $actionParams ?>)
    {
<?php
    if (count($primaryKeys) === 1) {
        $condition = '$'.$primaryKeys[0];
    } else {
        $condition = [];
        foreach ($primaryKeys as $primaryKey) {
            $condition[] = "'{$primaryKey}' => \${$primaryKey}";
        }
        $condition = '['.implode(', ', $condition).']';
    }
?>
        if (($model = <?= $modelClassName ?>::findOne(<?= $condition ?>)) !== null) {
            return $model;
        } else {
            throw new HttpException(404, 'The requested page does not exist.');
        }
    }

<?php endif; ?>
<?php if (!in_array('view', $useGenericActions)): ?>
    /**
     * Display detail of <?= $modelClassName ?> model.
     * @return mixed
     */
    public function actionView(<?= $actionParams ?>)
    {
        $model = $this->findModel(<?= $actionParams ?>);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

<?php endif; ?>
<?php if (array_diff(['create', 'update'], $useGenericActions)): ?>
    /**
     * @param <?= $formClass ?: $modelClass ?> $model
     * @return boolean
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

        if ($model->validate() === false) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // $isNew = $model->getIsNewRecord();
            $model->save(false);
            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $model->addError('_exception', $e->getMessage());
            $transaction->rollBack();
            return false;
        }
    }

<?php endif; ?>
<?php if (!in_array('create', $useGenericActions)): ?>
    /**
     * Creates a new <?= $formClassName ?: $modelClassName ?> model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new <?= ($formClassName ?: $modelClassName) . ($isSoftDelete ? "(['is_deleted' => FALSE])" : "") ?>;

        if ($this->save($model)) {
            return $this->redirect(ReturnUrl::getUrl(['view', 'id' => $model->id]));
        }

        return $this->render('create', ['model' => $model]);
    }

<?php endif; ?>
<?php if (!in_array('update', $useGenericActions)): ?>
    /**
     * Updates an existing <?= $formClassName ?: $modelClassName ?> model.
     * If update is successful, the browser will be redirected to prev page or the 'view' page.
     * <?= implode("\n\t * ", $actionParamComments)."\n" ?>
     * @return mixed
     */
    public function actionUpdate(<?= $actionParams ?>)
    {
        $model = <?= $formClassName ?: $modelClassName ?>::findOne($id);
        if (empty($model)) {
            throw new HttpException(404, 'The requested page does not exist.');
        }

        if ($this->save($model)) {
            return $this->redirect(ReturnUrl::getUrl(['view', 'id' => $model->id]));
        }

        return $this->render('update', ['model' => $model]);
    }

<?php endif; ?>
<?php if (!in_array('delete', $useGenericActions)): ?>
    /**
     * Deletes an existing <?= $modelClassName ?> model.
     * If deletion is successful, the browser will be redirected to the previous page.
     * <?= implode("\n\t * ", $actionParamComments)."\n" ?>
     * @return mixed
     * @throws \Throwable
     */
    public function actionDelete(<?= $actionParams ?>)
    {
        $model = $this->findModel(<?= $actionParams ?>);

        try {
            $model->delete();
        } catch (\Exception $e) {
            Yii::$app->getSession()->addFlash('error', $e->getMessage());
        }

        return $this->redirect(ReturnUrl::getUrl(['index']));
    }

<?php endif; ?>
<?php if (!in_array('restore', $useGenericActions)): ?>
    /**
     * Restore previously deleted <?= $modelClassName ?> model.
     * If restoration is successful, the browser will be redirected to the previous page.
     * <?= implode("\n\t * ", $actionParamComments)."\n" ?>
     * @return mixed
     */
    public function actionRestore(<?= $actionParams ?>)
    {
        $model = $this->findModel(<?= $actionParams ?>);

        try {
            $model->restore();
        } catch (\Exception $e) {
            Yii::$app->getSession()->addFlash('error', $e->getMessage());
        }

        return $this->redirect(ReturnUrl::getUrl(['view', <?= $urlParams ?>]));
    }

<?php endif; ?>
}

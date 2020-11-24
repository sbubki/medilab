<?php

use app\generators\params\GiiHelper;
use Brick\VarExporter\VarExporter;
use fredyns\stringcleaner\StringCleaner;
use yii\db\TableSchema;

/* @var $this yii\web\View  */
/* @var $generator app\generators\model\Generator  */
/* resource */
/* @var $tableName string  */
/* @var $tableSchema TableSchema  */
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

$blamable = ($tableSchema->getColumn('created_by') !== null) OR ($tableSchema->getColumn('updated_by') !== null);
$timestamp = ($tableSchema->getColumn('created_at') !== null) OR ($tableSchema->getColumn('updated_at') !== null);

echo "<?php\n";
?>

namespace <?= $modelNamespace ?>;

<?php if ($blamable OR $tableSchema->getColumn('deleted_by') !== null): ?>
use app\models\Profile;
<?php endif; ?>
use Yii;

/**
 * This is the model class for table "<?= $tableName ?>".
 * define model structure as specified in database.
 *
 * @author Fredy Nurman Saleh <email@fredyns.net>
 *
<?php foreach ($tableSchema->columns as $column): ?>
 * @property <?= "{$column->phpType} \${$column->name}\n" ?>
<?php endforeach; ?>
 *
<?php if ($tableSchema->getColumn('created_by') !== null): ?>
 * @property Profile $createdBy
<?php endif; ?>
<?php if ($tableSchema->getColumn('updated_by') !== null): ?>
 * @property Profile $updatedBy
<?php endif; ?>
<?php if ($tableSchema->getColumn('deleted_by') !== null): ?>
 * @property Profile $deletedBy
<?php endif; ?>
<?php if (isset($relations['hasOne']) && !empty($relations['hasOne'])): ?>
 *
<?php foreach ($relations['hasOne'] as $name => $relation): ?>
<?php
        $isSameNamespace = ($modelNamespace === $relation['namespace']);
        $relatedModelMention = $isSameNamespace ? $relation['className'] : '\\'.$relation['namespace'].'\\'.$relation['className'];
        $relations['hasOne'][$name]['modelMention'] = $relatedModelMention;
?>
 * @property <?= $relatedModelMention.' $'.lcfirst($name)."\n" ?>
<?php endforeach; ?>
<?php endif; ?>
<?php if (isset($relations['hasMany']) && !empty($relations['hasMany'])): ?>
 *
<?php foreach ($relations['hasMany'] as $name => $relation): ?>
<?php
        $isSameNamespace = ($modelNamespace === $relation['namespace']);
        $relatedModelMention = $isSameNamespace ? $relation['className'] : '\\'.$relation['namespace'].'\\'.$relation['className'];
        $relations['hasMany'][$name]['modelMention'] = $relatedModelMention;
?>
 * @property <?= $relatedModelMention.'[] $'.lcfirst($name)."\n" ?>
<?php endforeach; ?>
<?php endif; ?>
<?php if (isset($relations['hasJunction']) && !empty($relations['hasJunction'])): ?>
 *
<?php foreach ($relations['hasJunction'] as $name => $relation): ?>
<?php
        $isSameNamespace = ($modelNamespace === $relation['namespace']);
        $relatedModelMention = $isSameNamespace ? $relation['className'] : '\\'.$relation['namespace'].'\\'.$relation['className'];
        $relations['hasJunction'][$name]['modelMention'] = $relatedModelMention;
?>
 * @property <?= $relatedModelMention.'[] $'.lcfirst($name)."\n" ?>
<?php endforeach; ?>
<?php endif; ?>
<?php if ($isSoftDelete): ?>
 *
 * @method void softDelete() move to trash
 * @method void restore() bring back form trash
<?php endif; ?>
 */
class <?= $modelClassName ?> extends \yii\db\ActiveRecord
{
<?php if ($tableSchema->getColumn('created_by') !== null): ?>
    const CREATEDBY = 'createdBy';
<?php endif; ?>
<?php if ($tableSchema->getColumn('updated_by') !== null): ?>
    const UPDATEDBY = 'updatedBy';
<?php endif; ?>
<?php if ($tableSchema->getColumn('deleted_by') !== null): ?>
    const DELETEDBY = 'deletedBy';
<?php endif; ?>
<?php if (isset($relations['hasOne']) && !empty($relations['hasOne'])): ?>
<?php foreach ($relations['hasOne'] as $name => $relation): ?>
<?php if ($relation['alias']): ?>
    const <?= strtoupper($name) ?> = '<?= strtolower($name) ?>';
<?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>
<?php if (isset($relations['hasMany']) && !empty($relations['hasMany'])): ?>
<?php foreach ($relations['hasMany'] as $name => $relation): ?>
<?php if ($relation['alias']): ?>
    const <?= strtoupper($name) ?> = '<?= strtolower($name) ?>';
<?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>

    /* -------------------------- Static -------------------------- */

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '<?= $tableName ?>';
    }
    ##

    /* -------------------------- Labels -------------------------- */

    /**
     * model label as display title
     *
     * @param bool $plural
     * @return string
     */
    public function modelLabel($plural = false)
    {
<?php if ($modelNamePlural === $modelNameSingular): ?>
        return <?= $generator->generateString($modelNamePlural) ?>;
<?php else: ?>
        return $plural ? <?= $generator->generateString($modelNamePlural) ?> : <?= $generator->generateString($modelNameSingular) ?>;
<?php endif; ?>
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
<?php
$recordInfoFields = [
            'id', 'uid',
            'created_at', 'created_by',
            'updated_at', 'updated_by',
            'is_deleted', 'deleted_at', 'deleted_by',
        ];
foreach ($labels as $name => $label) {
    if (in_array($name, $recordInfoFields) && $i18n) {
        $label = "Yii::t('record-info', '{$label}')";
    } else {
        $label = $generator->generateString($label);
    }
    echo "            '{$name}' => {$label},\n";
}
?>
        ];
    }
    ##

    /* -------------------------- Meta -------------------------- */
<?php if ($blamable OR $timestamp OR $isSoftDelete): ?>

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
<?php if ($blamable): ?>
            'blameable' => [
                'class' => \yii\behaviors\BlameableBehavior::class,
<?php if ($tableSchema->getColumn('created_by') === null): ?>
                'createdByAttribute' => false,
<?php endif; ?>
<?php if ($tableSchema->getColumn('updated_by') === null): ?>
                'updatedByAttribute' => false,
<?php endif; ?>
            ],
<?php endif; ?>
<?php if ($timestamp): ?>
            'timestamp' => [
                'class' => \yii\behaviors\TimestampBehavior::class,
<?php if ($tableSchema->getColumn('created_at') === null): ?>
                'createdAtAttribute' => false,
<?php endif; ?>
<?php if ($tableSchema->getColumn('updated_at') === null): ?>
                'updatedAtAttribute' => false,
<?php endif; ?>
            ],
<?php endif; ?>
<?php if ($isSoftDelete): ?>
            'softdelete' => [
                'class' => \yii2tech\ar\softdelete\SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                    'is_deleted' => TRUE,
                    'deleted_at' => time(),
                    'deleted_by' => Yii::$app->user->id,
                ],
                'restoreAttributeValues' => [
                    'is_deleted' => FALSE,
                ],
            ],
<?php endif; ?>
        ];
    }
<?php endif; ?>

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
<?php foreach ($groupedRules as $ruleGroup => $rules): ?>
            # <?= $ruleGroup . "\n" ?>
<?php
    foreach ($rules as $rule) {
        $ruleCodes = VarExporter::export($rule, VarExporter::INLINE_NUMERIC_SCALAR_ARRAY);
        $ruleCodes = str_replace("\n", "\n            ", $ruleCodes); // indentation
        $ruleCodes = str_replace("\n                0 => ","\n                ", $ruleCodes); // column names in rule params
        $ruleCodes = str_replace("\n                1 => ","\n                ", $ruleCodes); // rule name in rule params
        $timeZone = Yii::$app->timeZone;
        $ruleCodes = str_replace("'timeZone' => '{$timeZone}'","'timeZone' => Yii::\$app->timeZone", $ruleCodes); // timezone param
        // replace class of related table
        $search = "'targetClass' => '";
        $replace = "'targetClass' => ";
        $ruleCodes = str_replace($search, $replace, $ruleCodes);
        $search = <<<CODE
',
                'targetAttribute'
CODE;
        $replace = <<<CODE
::class,
                'targetAttribute'
CODE;
        $ruleCodes = str_replace($search, $replace, $ruleCodes);
        // minify spaces in relation rule
        $search = <<<CODE
'targetAttribute' => [
                    
CODE;
        $replace = <<<CODE
'targetAttribute' => [
CODE;
        $ruleCodes = str_replace($search, $replace, $ruleCodes);
        $search = <<<CODE
'
                ]
            ]
CODE;
        $replace = <<<CODE
']
            ]
CODE;
        $ruleCodes = str_replace($search, $replace, $ruleCodes);
        // replace dictionary codes
        if ($dictionaries) {
            foreach ($dictionaries as $dictionaryClass) {
                $search = "'dictionary' => '" . addslashes($dictionaryClass) . "'"; // class name as string
                $replace = "'range' => \\" . $dictionaryClass . "::values()"; // actual function for validation
                $ruleCodes = str_replace($search,$replace, $ruleCodes);
            }
        }
        $minimizable = ['default', 'required', 'string', 'integer', 'date', 'in', 'unique', 'safe'];
        if (in_array($rule[1], $minimizable)) {
            $ruleCodes = GiiHelper::minimizeArrayCode($ruleCodes);
        }
        echo '            ' . $ruleCodes . ",\n";
    }
?>
<?php endforeach; ?>
        ];
    }
    ##

    /* -------------------------- Properties -------------------------- */
<?php if ($tableSchema->getColumn('created_by') !== null): ?>

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(Profile::class, ['user_id' => 'created_by'])->alias(static::CREATEDBY);
    }
<?php endif; ?>
<?php if ($tableSchema->getColumn('updated_by') !== null): ?>

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(Profile::class, ['user_id' => 'updated_by'])->alias(static::UPDATEDBY);
    }
<?php endif; ?>
<?php if ($tableSchema->getColumn('deleted_by') !== null): ?>

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeletedBy()
    {
        return $this->hasOne(Profile::class, ['user_id' => 'deleted_by'])->alias(static::DELETEDBY);
    }
<?php endif; ?>
    ##

    /* -------------------------- Has One -------------------------- */
<?php if (isset($relations['hasOne']) && !empty($relations['hasOne'])): ?>
<?php foreach ($relations['hasOne'] as $name => $relation): ?>

    /**
     * @return \yii\db\ActiveQuery
     */
    public function get<?= $name ?>()
    {
<?php
        $queryCode = "\$this->hasOne({$relation['modelMention']}::class, "
            . VarExporter::export($relation['foreignKey']) . ")";

        if ($relation['alias']) {
            $queryCode .= "->alias(static::" . strtoupper($name). ")";
        }

        $queryCode = StringCleaner::cleanSpaces($queryCode);
        $queryCode = str_replace('[ ', '[', $queryCode);
        $queryCode = str_replace(' ]', ']', $queryCode);
?>
        return <?= $queryCode ?>;
    }
<?php endforeach; ?>
<?php endif; ?>
    ##

    /* -------------------------- Has Many -------------------------- */
<?php if (isset($relations['hasMany']) && !empty($relations['hasMany'])): ?>
<?php foreach ($relations['hasMany'] as $name => $relation): ?>

    /**
     * @return \yii\db\ActiveQuery
     */
<?php if ($relation['hasSoftDelete']): ?>
    public function get<?= $name ?>($filter = ['is_deleted' => FALSE])
<?php else: ?>
    public function get<?= $name ?>()
<?php endif; ?>
    {
<?php
        $queryCode = "\$this->hasMany({$relation['modelMention']}::class, "
            . VarExporter::export($relation['foreignKey']) . ")";

        if ($relation['alias']) {
            $queryCode .= "->alias(static::" . strtoupper($name). ")";
        }

        if ($relation['hasSoftDelete']) {
            $queryCode .= "->andFilterWhere(\$filter)";
        }

        $queryCode = StringCleaner::cleanSpaces($queryCode);
        $queryCode = str_replace('[ ', '[', $queryCode);
        $queryCode = str_replace(' ]', ']', $queryCode);
?>
        return <?= $queryCode ?>;
    }
<?php endforeach; ?>
<?php endif; ?>
<?php if (isset($relations['hasJunction']) && !empty($relations['hasJunction'])): ?>
    ##

    /* -------------------------- Has Junction -------------------------- */
<?php foreach ($relations['hasJunction'] as $name => $relation): ?>

    /**
     * @return \yii\db\ActiveQuery
     */
    public function get<?= $name ?>()
    {
        <?= $relation['query'] . "\n" ?>
    }
<?php endforeach; ?>
<?php endif; ?>
    ##

}
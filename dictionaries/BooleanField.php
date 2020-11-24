<?php

namespace app\dictionaries;

use Yii;

/**
 * Dictionary for boolean fields
 *
 * @author Fredy Nurman Saleh <email@fredyns.net>
 */
class BooleanField extends \app\base\BaseDictionary
{
    const NO = false;
    const YES = true;

    /**
     * @inheritdoc
     */
    public static function all()
    {
        return [
            [
                'value' => static::NO,
                'label' => Yii::t('dictionaries/boolean', "No"),
            ],
            [
                'value' => static::YES,
                'label' => Yii::t('dictionaries/boolean', "Yes"),
            ],
        ];
    }

}
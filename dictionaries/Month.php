<?php

namespace app\dictionaries;

use Yii;

/**
 * Description of BooleanField
 *
 * @author Fredy Nurman Saleh <email@fredyns.net>
 */
class Month extends \app\base\BaseDictionary
{
    const JAN = 1;
    const FEB = 2;
    const MAR = 3;
    const APR = 4;
    const MAY = 5;
    const JUN = 6;
    const JUL = 7;
    const AGT = 8;
    const SEP = 9;
    const OCT = 10;
    const NOV = 11;
    const DES = 12;

    /**
     * @inheritdoc
     */
    public static function all()
    {
        if (!empty(static::$all)) {
            return static::$all;
        }

        $date = new \DateTime('first day');

        for ($i = 1; $i <= 12; $i++) {
            static::$all[] = [
                'value' => $i,
                'label' => Yii::$app->formatter->asDate($date, 'MMMM'), # auto translated by ICU
                'abbr' => Yii::$app->formatter->asDate($date, 'MMM'),
            ];
            $date->modify('+1 month');
        }

        return static::$all;
    }

    protected static $all = [];

}
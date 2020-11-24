<?php

namespace app\base;

use yii\helpers\ArrayHelper;

/**
 * Base class to create dictionary
 *
 * all class extending this shall:
 * - create constants representing all value available
 * - extend all() function to declare all data
 *
 * @author Fredy Nurman Saleh <email@fredyns.net>
 */
abstract class BaseDictionary
{
    /**
     * all class extending this shall have some constant represent each value
     *
     * For example,
     * ```php
     *   const NO = 0;
     *   const YES = 1;
     * ```
     */

    /**
     * resides all available data
     *
     * For example,
     *
     * ```php
     *   return [
     *       [
     *           'value' => static::NO,
     *           'label' => Yii::t('dialog', "No"),
     *       ],
     *       [
     *           'value' => static::YES,
     *           'label' => Yii::t('dialog', "Yes"),
     *       ],
     *   ];
     * ```
     *
     * @return array
     */
    public static function all()
    {
        return [];
    }

    /**
     * get options available to select
     *
     * @param string $labelAttr
     * @param string $groupByAttr
     * @return array
     */
    public static function options($labelAttr = 'label', $groupByAttr = null)
    {
        return ArrayHelper::map(static::all(), 'value', $labelAttr, $groupByAttr);
    }

    /**
     * get all values available
     *
     * @return int[]
     */
    public static function values()
    {
        return ArrayHelper::getColumn(static::all(), 'value');
    }

    /**
     * get all labels available
     *
     * @param string $labelAttr
     * @return int[]
     */
    public static function labels($labelAttr = 'label')
    {
        return ArrayHelper::getColumn(static::all(), $labelAttr);
    }

    /**
     * get label for arbitrary value
     *
     * @param int $value
     * @param string $labelAttr
     * @param null $defaultValue
     * @return string
     */
    public static function getLabel($value, $labelAttr = 'label', $defaultValue = null)
    {
        return static::searchAndGet($value, 'value', $labelAttr, $defaultValue);
    }

    /**
     * search in label column & get value
     *
     * @param int $label
     * @param string $labelAttr
     * @param null $defaultValue
     * @return string
     */
    public static function getValue($label, $labelAttr = 'label', $defaultValue = null)
    {
        return static::searchAndGet($label, $labelAttr, 'value', $defaultValue);
    }

    /**
     * search text on particular column and return value on coresponding column
     *
     * @param string $search
     * @param string $onColumn
     * @param string $getColumn
     * @param null $defaultValue
     * @return string
     */
    public static function searchAndGet($search, $onColumn, $getColumn = 'label', $defaultValue = null)
    {
        foreach (static::all() as $item) {
            if (isset($item[$onColumn]) && isset($item[$getColumn]) && $item[$onColumn] === $search) {
                return $item[$getColumn];
            }
        }

        return $defaultValue;
    }

}
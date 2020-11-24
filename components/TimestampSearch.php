<?php

namespace app\components;

use DateTime;
use DateTimeZone;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use kartik\daterange\DateRangePicker;

/**
 * Description of TimestampSearch
 * @property-read Boolean $isValid
 *
 * @author Fredy Nurman Saleh <email@fredyns.net>
 */
class TimestampSearch extends \yii\base\BaseObject
{
    // mandatory
    public $attribute;

    /**
     * @var string of database field to filter
     */
    public $field;
    // optional
    public $separator = 'to';
    public $format = 'Y-m-d H.i'; // format for second auto added later
    public $formatSecond = '.s';
    public $timezone; // this will override apps timezone
    // result
    public $value;

    /**
     * @var DateTime object of minimum timestamp filter
     */
    public $from;

    /**
     * @var DateTime object of maximum timestamp filter
     */
    public $to;

    /**
     * apply timestamp filter to query
     *
     * @param ActiveQuery $query
     * @param string $range
     * @return ActiveQuery
     */
    public function applyFilter(ActiveQuery $query, $range)
    {
        if (empty($range) OR strpos($range, $this->separator) === false) {
            return $query;
        }

        if ($this->load($range)) {
            $this->value = $range;
            return $query->andFilterWhere([
                    'between',
                    $this->field,
                    $this->from->getTimestamp(),
                    $this->to->getTimestamp(),
            ]);
        }

        return $query;
    }

    /**
     * get timezone as object
     * @return DateTimeZone
     */
    protected function getTimeZone()
    {
        if ($this->timezone) {
            return new DateTimeZone($this->timezone);
        }

        return new DateTimeZone(Yii::$app->timeZone);
    }

    /**
     * @param string $range
     * @return Boolean
     */
    public function load($range)
    {
        // extract from search param
        list($from_date, $to_date) = explode($this->separator, $range);

        // seconds suffix
        $from_suffix = str_replace('s', '00', $this->formatSecond);
        $to_suffix = str_replace('s', '59', $this->formatSecond);

        // remove space & format value
        $from_date = trim($from_date).$from_suffix;
        $to_date = trim($to_date).$to_suffix;

        // full format
        $format = $this->format.$this->formatSecond;

        // timezone
        $tz = $this->getTimeZone();

        // date object
        $this->from = DateTime::createFromFormat($format, $from_date, $tz);
        $this->to = DateTime::createFromFormat($format, $to_date, $tz);

        return $this->isValid;
    }

    /**
     * check whether range filter is valid
     * @return Boolean
     */
    public function getIsValid()
    {
        return $this->from && $this->to;
    }

    /**
     * render filter widget
     * @param array $options
     * @return string
     */
    public function filterWidget($options = [])
    {
        $default_options = [
            'name' => $this->attribute,
            'value' => $this->value,
            'convertFormat' => true,
            'pluginOptions' => [
                "opens" => "left",
                'timePicker' => true,
                'timePicker24Hour' => true,
                'timePickerIncrement' => 15,
                'locale' => [
                    'format' => $this->format,
                    'separator' => ' '.$this->separator.' ',
                ]
            ]
        ];

        $final_options = ArrayHelper::merge($default_options, $options);

        return DateRangePicker::widget($final_options);
    }

}
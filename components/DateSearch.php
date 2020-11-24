<?php

namespace app\components;

use DateTime;
use DateTimeZone;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use kartik\daterange\DateRangePicker;

/**
 * Description of DateSearch
 * @property-read Boolean $isValid
 *
 * @author Fredy Nurman Saleh <email@fredyns.net>
 */
class DateSearch extends \yii\base\BaseObject
{
    // mandatory
    public $attribute;

    /**
     * @var string of database field to filter
     */
    public $field;
    // optional
    public $separator = 'to';
    public $format = 'Y-m-d';
    public $timezone; // this will override apps timezone
    // result
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
            return $query->andFilterWhere([
                    'between',
                    $this->field,
                    $this->from->format('Y-m-d'),
                    $this->to->format('Y-m-d'),
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

        // remove space & format value
        $from_date = trim($from_date);
        $to_date = trim($to_date);

        // timezone
        $tz = $this->getTimeZone();

        // date object
        $this->from = DateTime::createFromFormat($this->format, $from_date, $tz);
        $this->to = DateTime::createFromFormat($this->format, $to_date, $tz);

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
            'convertFormat' => true,
            'pluginOptions' => [
                "opens" => "left",
                'timePicker' => FALSE,
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
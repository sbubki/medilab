<?php

namespace app\widgets;

/**
 * Description of Tabs
 *
 * @author Fredy Nurman Saleh <email@fredyns.net>
 */
class Tabs extends \dmstr\bootstrap\Tabs
{
    /**
     * Remember active tab state for this URL
     */
    public static function rememberActiveState($element_id = "relation-tabs")
    {
        static::registerAssets();
        $js = <<<JS
            jQuery("#{$element_id} > li > a").on("click", function () {
                setStorage(this);
            });

            jQuery(document).on('pjax:end', function() {
               setStorage($('#{$element_id} .active A'));
            });

            jQuery(window).on("load", function () {
               initialSelect();
            });
JS;

        if (\Yii::$app->request->isAjax) {
            return "<script type='text/javascript'>{$js}</script>";
        } else {
            // Register cookie script
            \Yii::$app->controller->getView()->registerJs(
                $js,
                View::POS_END,
                'rememberActiveState'
            );
        }
    }


}
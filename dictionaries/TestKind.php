<?php

namespace app\dictionaries;

use Yii;

/**
 * Description of BooleanField
 *
 * @author Fredy Nurman Saleh <email@fredyns.net>
 */
class TestKind extends \app\base\BaseDictionary
{
    const TENSILE = 1; # TARIK
    const BENDING = 2; # LENGKUNG
    const IMPACT_CHARPY = 3; # IMPACT CHARPY
    const HARDNESS = 4; # KEKERASAN
    const FRACTOGRAPHY = 5; # FRACTURE
    const BREAKING_AND_PROOF_LOAD = 6; # BREAKING & PROOF LOAD
    const SHEAR = 7; # GESER
    const MACRO_ETSA = 8; # MACRO ETSA
    const MICROSTRUCTURE = 9; # MICROSTRUCTURE
    const FIBER_CONTENT = 10; # FIBER CONTENT

    /**
     * @inheritdoc
     */
    public static function all()
    {
        return [
            [
                'value' => static::TENSILE,
                'label' => Yii::t('dictionaries/test-kind', "Tensile"),
            ],
            [
                'value' => static::BENDING,
                'label' => Yii::t('dictionaries/test-kind', "Bending"),
            ],
            [
                'value' => static::IMPACT_CHARPY,
                'label' => Yii::t('dictionaries/test-kind', "Impact Charpy"),
            ],
            [
                'value' => static::HARDNESS,
                'label' => Yii::t('dictionaries/test-kind', "Hardness"),
            ],
            [
                'value' => static::FRACTOGRAPHY,
                'label' => Yii::t('dictionaries/test-kind', "Fractography"),
            ],
            [
                'value' => static::BREAKING_AND_PROOF_LOAD,
                'label' => Yii::t('dictionaries/test-kind', "Breaking and Proof load"),
            ],
            [
                'value' => static::SHEAR,
                'label' => Yii::t('dictionaries/test-kind', "Shear"),
            ],
            [
                'value' => static::MACRO_ETSA,
                'label' => Yii::t('dictionaries/test-kind', "Macro Etsa"),
            ],
            [
                'value' => static::MICROSTRUCTURE,
                'label' => Yii::t('dictionaries/test-kind', "Microstructure"),
            ],
            [
                'value' => static::FIBER_CONTENT,
                'label' => Yii::t('dictionaries/test-kind', "Fiber Content"),
            ],
        ];
    }

}
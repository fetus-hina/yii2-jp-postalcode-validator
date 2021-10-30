<?php

/**
 * @author AIZAWA Hina <hina@fetus.jp>
 * @copyright 2015-2019 by AIZAWA Hina <hina@fetus.jp>
 * @license https://github.com/fetus-hina/yii2-jp-postalcode-validator/blob/master/LICENSE MIT
 * @since 1.0.0
 */

namespace jp3cki\yii2\jppostalcode;

use Yii;
use yii\validators\Validator;

/**
 * Validate Postal Code (JAPAN spec)
 */
class JpPostalCodeValidator extends Validator
{
    /**
     * ハイフンの許可
     *
     * @var bool|null null=気にしない, true=要求, false=許可しない
     */
    public $hyphen = null;

    /** @inheritdoc */
    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = Yii::t('jp3ckiJpPostalCode', '{attribute} is not a valid postal code.');
        }
    }

    /** @inheritdoc */
    public function validateAttribute($model, $attribute)
    {
        if (!$this->isValid($model->$attribute)) {
            $this->addError($model, $attribute, $this->message);
        }
    }

    /** @inheritdoc */
    protected function validateValue($value)
    {
        if (!$this->isValid($value)) {
            return [$this->message, []];
        }
        return null;
    }

    /** @param mixed $value */
    private function isValid($value): bool
    {
        if (!is_scalar($value)) {
            return false;
        }

        return $this->isValidFormat((string)$value) && $this->isValidNumber((string)$value);
    }

    private function isValidFormat(string $value): bool
    {
        if (preg_match('/^\d{3}-?\d{4}$/', $value)) {
            if ($this->hyphen === true) {
                return strpos($value, '-') !== false;
            }
            if ($this->hyphen === false) {
                return strpos($value, '-') === false;
            }
            return true;
        }
        return false;
    }

    private function isValidNumber(string $value): bool
    {
        $value = preg_replace('/[^0-9]+/', '', $value);
        $code1 = substr($value, 0, 3);
        $code2 = substr($value, 3, 4);
        $list = $this->loadJson($code1);
        return !!in_array($code2, $list, true);
    }

    /** @return string[] */
    private function loadJson(string $code1): array
    {
        $path = __DIR__ . '/../data/postalcode/jp/' . $code1 . '.json.gz';
        if (!file_exists($path)) {
            return [];
        }
        $ret = @json_decode(file_get_contents('compress.zlib://' . $path));
        return is_array($ret) ? $ret : [];
    }
}

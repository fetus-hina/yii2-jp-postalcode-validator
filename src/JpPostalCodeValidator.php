<?php

/**
 * @author AIZAWA Hina <hina@fetus.jp>
 * @copyright 2015-2025 by AIZAWA Hina <hina@fetus.jp>
 * @license https://github.com/fetus-hina/yii2-jp-postalcode-validator/blob/master/LICENSE MIT
 * @since 1.0.0
 */

declare(strict_types=1);

namespace jp3cki\yii2\jppostalcode;

use LogicException;
use RuntimeException;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\validators\Validator;

use function file_exists;
use function file_get_contents;
use function in_array;
use function is_array;
use function is_scalar;
use function is_string;
use function preg_match;
use function preg_replace;
use function strpos;
use function substr;

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

    /**
     * @inheritdoc
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        if ($this->message === null) {
            $this->message = Yii::t('jp3ckiJpPostalCode', '{attribute} is not a valid postal code.');
        }
    }

    /**
     * @inheritdoc
     *
     * @return void
     */
    public function validateAttribute($model, $attribute)
    {
        if (!$this->isValid($model->$attribute)) {
            $this->addError($model, $attribute, (string)$this->message);
        }
    }

    /**
     * @inheritdoc
     *
     * @return array{string, array<string, mixed>}|null
     */
    protected function validateValue($value)
    {
        if (!$this->isValid($value)) {
            return [(string)$this->message, []];
        }
        return null;
    }

    private function isValid(mixed $value): bool
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
        if ($value === null) {
            throw new LogicException();
        }

        $code1 = substr($value, 0, 3);
        $code2 = substr($value, 3, 4);
        $list = $this->loadJson($code1);
        return in_array($code2, $list, true);
    }

    /**
     * @return string[]
     */
    private function loadJson(string $code1): array
    {
        $path = __DIR__ . '/../data/postalcode/jp/' . $code1 . '.json.gz';
        if (!file_exists($path)) {
            return [];
        }

        $jsonText = file_get_contents('compress.zlib://' . $path);
        if ($jsonText === false) {
            throw new RuntimeException('Failed to load postal-code JSON');
        }

        $ret = Json::decode($jsonText, true);
        if (
            !is_array($ret) ||
            !ArrayHelper::isIndexed($ret, true)
        ) {
            throw new RuntimeException('Failed to load postal-code JSON');
        }

        foreach ($ret as $entry) {
            if (!is_string($entry)) {
                throw new RuntimeException('Failed to load postal-code JSON');
            }
        }

        return $ret;
    }
}

<?php

/**
 * @author AIZAWA Hina <hina@fetus.jp>
 * @copyright 2015-2025 by AIZAWA Hina <hina@fetus.jp>
 * @license https://github.com/fetus-hina/yii2-jp-postalcode-validator/blob/master/LICENSE MIT
 * @since 1.0.0
 */

declare(strict_types=1);

namespace jp3cki\yii2\jppostalcode;

use Override;
use RuntimeException;
use Yii;
use yii\helpers\Json;
use yii\validators\Validator;

use function array_is_list;
use function file_exists;
use function file_get_contents;
use function in_array;
use function is_array;
use function is_scalar;
use function is_string;
use function preg_match;
use function str_contains;

/**
 * Validate Postal Code (JAPAN spec)
 */
class JpPostalCodeValidator extends Validator
{
    /**
     * ハイフンの許可
     *
     * null=気にしない, true=要求, false=許可しない
     */
    public ?bool $hyphen = null;

    #[Override]
    public function init(): void
    {
        parent::init();

        if ($this->message === null) {
            $this->message = Yii::t('jp3ckiJpPostalCode', '{attribute} is not a valid postal code.');
        }
    }

    #[Override]
    public function validateAttribute($model, $attribute): void
    {
        if (!$this->isValid($model->$attribute)) {
            $this->addError($model, $attribute, (string)$this->message);
        }
    }

    /**
     * @return array{string, array<string, mixed>}|null
     */
    #[Override]
    protected function validateValue($value): ?array
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
                return str_contains($value, '-');
            }
            if ($this->hyphen === false) {
                return !str_contains($value, '-');
            }
            return true;
        }
        return false;
    }

    private function isValidNumber(string $value): bool
    {
        if (!preg_match('/^(\d{3})-?(\d{4})$/', $value, $m)) {
            return false;
        }
        $list = $this->loadJson($m[1]);
        return in_array($m[2], $list, true);
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
        if (!is_array($ret) || !array_is_list($ret)) {
            throw new RuntimeException('Failed to load postal-code JSON');
        }

        foreach ($ret as $entry) {
            if (!is_string($entry)) {
                throw new RuntimeException('Failed to load postal-code JSON');
            }
        }

        /** @var string[] $ret */
        return $ret;
    }
}

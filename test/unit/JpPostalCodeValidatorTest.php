<?php

declare(strict_types=1);

namespace jp3cki\yii2\jppostalcode\test;

use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Yii;
use jp3cki\yii2\jppostalcode\HyphenMode;
use jp3cki\yii2\jppostalcode\JpPostalCodeValidator as Target;
use stdClass;
use yii\base\DynamicModel;
use yii\i18n\PhpMessageSource;

#[Group('postalcode')]
class JpPostalCodeValidatorTest extends TestCase
{
    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        $this->mockApplication();
    }

    #[DataProvider('dataProvider')]
    public function testValidator(bool $expected, bool|null $hyphen, string $value): void
    {
        $o = new Target();
        $o->hyphen = $hyphen;
        $o->init();
        $this->assertSame($expected, $o->validate($value));
    }

    #[DataProvider('dataProvider')]
    public function testWithModel(bool $expected, bool|null $hyphen, string $value): void
    {
        $model = DynamicModel::validateData(
            ['value' => $value],
            [
                [['value'], Target::class, 'hyphen' => $hyphen],
            ],
        );
        $this->assertSame($expected, !$model->hasErrors());
    }

    #[DataProvider('dataProvider')]
    public function testValidatorWithHyphenMode(bool $expected, bool|null $hyphen, string $value): void
    {
        $o = new Target();
        $o->hyphen = self::toHyphenMode($hyphen);
        $o->init();
        $this->assertSame($expected, $o->validate($value));
    }

    #[DataProvider('dataProvider')]
    public function testWithModelWithHyphenMode(bool $expected, bool|null $hyphen, string $value): void
    {
        $model = DynamicModel::validateData(
            ['value' => $value],
            [
                [['value'], Target::class, 'hyphen' => self::toHyphenMode($hyphen)],
            ],
        );
        $this->assertSame($expected, !$model->hasErrors());
    }

    public function testHyphenPropertyIsNotNormalized(): void
    {
        $o = new Target(['hyphen' => true]);
        $o->validate('100-0005');
        $this->assertTrue($o->hyphen);

        $o = new Target(['hyphen' => HyphenMode::Required]);
        $o->validate('100-0005');
        $this->assertSame(HyphenMode::Required, $o->hyphen);
    }

    #[DataProvider('nonStringProvider')]
    public function testNonStringValue(mixed $value): void
    {
        $o = new Target();
        $o->init();
        $this->assertFalse($o->validate($value));
    }

    #[DataProvider('nonStringProvider')]
    public function testNonStringValueWithModel(mixed $value): void
    {
        $model = DynamicModel::validateData(
            ['value' => $value],
            [
                [['value'], Target::class],
            ],
        );
        $this->assertTrue($model->hasErrors('value'));
    }

    #[DataProvider('emptyValueProvider')]
    public function testEmptyValueIsSkippedWithModel(mixed $value): void
    {
        $model = DynamicModel::validateData(
            ['value' => $value],
            [
                [['value'], Target::class],
            ],
        );
        $this->assertFalse($model->hasErrors('value'));
    }

    #[DataProvider('emptyValueProvider')]
    public function testEmptyValueIsInvalidWithoutSkipOnEmpty(mixed $value): void
    {
        $model = DynamicModel::validateData(
            ['value' => $value],
            [
                [['value'], Target::class, 'skipOnEmpty' => false],
            ],
        );
        $this->assertTrue($model->hasErrors('value'));
    }

    public function testDefaultMessage(): void
    {
        $o = new Target();
        $o->init();
        $error = null;
        $this->assertFalse($o->validate('999-9999', $error));
        $this->assertSame('the input value is not a valid postal code.', $error);

        $model = DynamicModel::validateData(
            ['zip' => '999-9999'],
            [
                [['zip'], Target::class],
            ],
        );
        $this->assertSame('Zip is not a valid postal code.', $model->getFirstError('zip'));
    }

    public function testCustomMessage(): void
    {
        $model = DynamicModel::validateData(
            ['zip' => '999-9999'],
            [
                [['zip'], Target::class, 'message' => 'Custom message for {attribute}.'],
            ],
        );
        $this->assertSame('Custom message for Zip.', $model->getFirstError('zip'));
    }

    public function testBootstrapKeepsUserDefinedTranslation(): void
    {
        $translation = [
            'class' => PhpMessageSource::class,
            'sourceLanguage' => 'en-US',
            'basePath' => '@app/custom-messages',
        ];

        $this->destroyApplication();
        $this->mockApplication('en-US', [
            'components' => [
                'i18n' => [
                    'translations' => [
                        'jp3ckiJpPostalCode' => $translation,
                    ],
                ],
            ],
        ]);

        $this->assertSame($translation, Yii::$app->getI18n()->translations['jp3ckiJpPostalCode']);
    }

    public function testJapaneseTranslation(): void
    {
        $this->destroyApplication();
        $this->mockApplication('ja-JP');

        $o = new Target();
        $o->init();
        $error = null;
        $this->assertFalse($o->validate('999-9999', $error));
        $this->assertSame('入力値 は正しい郵便番号ではありません。', $error);
    }

    /**
     * @return list<array{bool, bool|null, string}>
     */
    public static function dataProvider(): array
    {
        return [
            // 基本パターン
            [true, null, '100-0005'],
            [true, null, '1000005'],
            [true, true, '100-0005'],
            [true, false, '1000005'],

            // ハイフン指定違反
            [false, true, '1000005'],
            [false, false, '100-0005'],

            // 事業所
            [true, null, '100-8994'],
            [true, null, '1008994'],
            [true, true, '100-8994'],
            [true, false, '1008994'],

            // 前半部存在しない
            [false, null, '008-0000'],

            // 後半部存在しない
            [false, null, '999-9999'],

            // 桁数異常
            [false, null, '100-00'],
            [false, null, '10000'],
            [false, null, '10000050'],

            // 先頭が 0 の郵便番号
            [true, null, '060-0000'],
            [true, null, '0600000'],
            [true, null, '001-0000'],
            [true, true, '060-0000'],
            [true, false, '0600000'],

            // ハイフンの位置・個数の異常
            [false, null, '1000-005'],
            [false, null, '10-00005'],
            [false, null, '100--0005'],
            [false, null, '-1000005'],
            [false, null, '1000005-'],

            // 全角数字・ハイフン以外の記号
            [false, null, '１００－０００５'],
            [false, null, '１０００００５'],
            [false, null, '100－0005'],
            [false, null, '100ー0005'],
            [false, null, '100−0005'],
            [false, null, '100‐0005'],
            [false, null, '100 0005'],

            // 前後の空白・改行
            [false, null, "1000005\n"],
            [false, null, "100-0005\n"],
            [false, null, "\n1000005"],
            [false, null, ' 1000005'],
            [false, null, '1000005 '],
        ];
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function nonStringProvider(): array
    {
        return [
            'int' => [1000005],
            'float' => [1000005.0],
            'true' => [true],
            'false' => [false],
            'array' => [['1000005']],
            'object' => [new stdClass()],
        ];
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function emptyValueProvider(): array
    {
        return [
            'empty string' => [''],
            'null' => [null],
        ];
    }

    private static function toHyphenMode(bool|null $hyphen): HyphenMode
    {
        return match ($hyphen) {
            null => HyphenMode::Optional,
            true => HyphenMode::Required,
            false => HyphenMode::Forbidden,
        };
    }
}

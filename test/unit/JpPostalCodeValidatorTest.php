<?php

declare(strict_types=1);

namespace jp3cki\yii2\jppostalcode\test;

use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use jp3cki\yii2\jppostalcode\JpPostalCodeValidator as Target;
use yii\base\DynamicModel;

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
    public function testValidator(bool $expected, ?bool $hyphen, string $value)
    {
        $o = new Target();
        $o->hyphen = $hyphen;
        $o->init();
        $this->assertSame($expected, $o->validate($value));
    }

    #[DataProvider('dataProvider')]
    public function testWithModel(bool $expected, ?bool $hyphen, string $value)
    {
        $model = DynamicModel::validateData(
            ['value' => $value],
            [
                [['value'], Target::class, 'hyphen' => $hyphen],
            ],
        );
        $this->assertSame($expected, !$model->hasErrors());
    }

    /**
     * @return array<int, mixed>[]
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
        ];
    }
}

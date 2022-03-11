<?php

declare(strict_types=1);

namespace jp3cki\yii2\jppostalcode\test;

use jp3cki\yii2\jppostalcode\JpPostalCodeValidator as Target;
use yii\base\DynamicModel;

/**
 * @group postalcode
 */
class JpPostalCodeValidatorTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->mockApplication();
    }

    /**
     * @dataProvider dataProvider
     */
    public function testValidator(bool $expected, ?bool $hyphen, string $value)
    {
        $o = new Target();
        $o->hyphen = $hyphen;
        $o->init();
        $this->assertEquals($expected, $o->validate($value));
    }

    /**
     * @dataProvider dataProvider
     */
    public function testWithModel(bool $expected, ?bool $hyphen, string $value)
    {
        $model = DynamicModel::validateData(
            ['value' => $value],
            [
                [['value'], Target::class, 'hyphen' => $hyphen],
            ]
        );
        $this->assertEquals($expected, !$model->hasErrors());
    }

    /** @return array<int, mixed>[] */
    public function dataProvider(): array
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

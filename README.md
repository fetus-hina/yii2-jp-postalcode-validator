yii2-jp-postalcode-validator
============================

日本の郵便番号をチェックする Yii Framework2 用のバリデータです。

[![License](https://poser.pugx.org/jp3cki/yii2-jp-postalcode-validator/license.svg)](https://packagist.org/packages/jp3cki/yii2-jp-postalcode-validator)
[![Latest Stable Version](https://poser.pugx.org/jp3cki/yii2-jp-postalcode-validator/v/stable.svg)](https://packagist.org/packages/jp3cki/yii2-jp-postalcode-validator)

動作環境
--------

- PHP 7.1 以上
- Yii framework 2.0

インストール
------------

1. [Composer](https://getcomposer.org/) をダウンロードして使用可能な状態にします。
2. 必要であれば Yii Framework2 のプロジェクトを作成します。
3. `php composer.phar require jp3cki/yii2-jp-postalcode-validator`

使い方
------

### JpPostalCodeValidator ###

このバリデータは入力が日本の郵便番号らしい文字列であることを検証します。

通常の郵便番号の他、事業所等に割り当てられた番号も検証することができます。

Model class example:
```php
namespace app\models;

use yii\base\Model;
use jp3cki\yii2\validators\JpPostalCodeValidator;

class YourCustomForm extends Model
{
    public $value;

    public function rules()
    {
        return [
            [['value'], JpPostalCodeValidator::class,
                'hyphen' => null, // 意味は後述
            ],
        ];
    }
}
```

`hyphen`: ハイフンの許可状況を設定します。

  * `null`: ハイフンの有無を気にしません（ハイフンが記入されている場合は正しい位置にハイフンがある必要があります）。
  * `true`: ハイフンを必須とします。（正しい位置にハイフンがある必要があります）
  * `false`: ハイフンを許容しません。（数字のみの羅列である必要があります）


ライセンス
----------

[The MIT License](https://github.com/fetus-hina/yii2-jp-postalcode-validator/blob/master/LICENSE).

```
The MIT License (MIT)

Copyright (c) 2015-2022 AIZAWA Hina <hina@fetus.jp>

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

非互換の更新
------------

  - v1.0 → v2.0
    - PHPの要求バージョンを引き上げました。コード上の非互換はありません。

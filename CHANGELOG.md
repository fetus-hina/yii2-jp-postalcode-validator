# 変更履歴

バージョン番号は [Semantic Versioning](https://semver.org/lang/ja/) に従います。

## v6.0.0

- `JpPostalCodeValidator::$hyphen` プロパティに `bool|null` 型を宣言しました。これまで型宣言が無く任意の値を代入できましたが、`bool` および `null` 以外を代入すると `TypeError` となります。
- `init()`, `validateAttribute()`, `validateValue()` の各メソッドにそれぞれ `void`, `void`, `array|null` の戻り値型を追加しました。`JpPostalCodeValidator` を継承してこれらのメソッドをオーバーライドしている場合は、子クラスのシグネチャを揃える必要があります。

## v5.0.0

- PHP の要求バージョンを 8.2 に引き上げました。コード上の非互換はありません。

## v4.0.0

- PHP の要求バージョンを 8.1 に引き上げました。コード上の非互換はありません。

## v3.0.0

- PHP の要求バージョンを 7.2 に引き上げました。コード上の非互換はありません。

## v2.0.0

- PHP の要求バージョンを引き上げました。コード上の非互換はありません。

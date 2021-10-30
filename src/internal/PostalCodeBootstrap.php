<?php

/**
 * @author AIZAWA Hina <hina@fetus.jp>
 * @copyright 2015 by AIZAWA Hina <hina@fetus.jp>
 * @license https://github.com/fetus-hina/yii2-jp-postalcode-validator/blob/master/LICENSE MIT
 */

namespace jp3cki\yii2\jppostalcode\internal;

use Yii;
use yii\base\BootstrapInterface;

final class PostalCodeBootstrap implements BootstrapInterface
{
    /** @inheritdoc */
    public function bootstrap($app)
    {
        Yii::setAlias('@jp3ckiJpPostalCodeMessages', __DIR__ . '/../../messages');
        $i18n = $app->i18n;
        if (!isset($i18n->translations['jp3ckiJpPostalCode'])) {
            $i18n->translations['jp3ckiJpPostalCode'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en-US',
                'basePath' => '@jp3ckiJpPhoneMessages',
            ];
        }
    }
}

<?php
/**
 * @author AIZAWA Hina <hina@bouhime.com>
 * @copyright 2015 by AIZAWA Hina <hina@bouhime.com>
 * @license https://github.com/fetus-hina/yii2-jp-postalcode-validator/blob/master/LICENSE MIT
 * @since 1.0.0
 */

namespace jp3cki\yii2\jppostalcode\internal;

use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
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

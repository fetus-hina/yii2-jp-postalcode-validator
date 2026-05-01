<?php

/**
 * @author AIZAWA Hina <hina@fetus.jp>
 * @copyright 2015-2025 by AIZAWA Hina <hina@fetus.jp>
 * @license https://github.com/fetus-hina/yii2-jp-postalcode-validator/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace jp3cki\yii2\jppostalcode\internal;

use Override;
use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\i18n\I18N;
use yii\i18n\PhpMessageSource;

final class PostalCodeBootstrap implements BootstrapInterface
{
    /**
     * @inheritdoc
     *
     * @param Application $app
     */
    #[Override]
    public function bootstrap($app): void
    {
        Yii::setAlias('@jp3ckiJpPostalCodeMessages', __DIR__ . '/../../messages');
        $i18n = $app->i18n;
        if ($i18n instanceof I18N && !isset($i18n->translations['jp3ckiJpPostalCode'])) {
            $i18n->translations['jp3ckiJpPostalCode'] = [
                'class' => PhpMessageSource::class,
                'sourceLanguage' => 'en-US',
                'basePath' => '@jp3ckiJpPostalCodeMessages',
            ];
        }
    }
}

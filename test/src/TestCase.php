<?php

namespace jp3cki\yii2\jppostalcode\test;

use Yii;
use jp3cki\yii2\jppostalcode\internal\PostalCodeBootstrap;
use yii\base\NotSupportedException;
use yii\console\Application;
use yii\helpers\ArrayHelper;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    public static function setUpBeforeClass(): void
    {
        $vendorDir = __DIR__ . '/../../vendor';
        $vendorAutoload = $vendorDir . '/autoload.php';
        if (file_exists($vendorAutoload)) {
            require_once $vendorAutoload;
        } else {
            throw new NotSupportedException("Vendor autoload file '{$vendorAutoload}' is missing.");
        }
        require_once $vendorDir . '/yiisoft/yii2/Yii.php';
        Yii::setAlias('@vendor', $vendorDir);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->destroyApplication();
        gc_collect_cycles();
    }

    /** @param array<string, mixed> $config */
    protected function mockApplication(
        string $language = 'en-US',
        array $config = [],
        string $appClass = Application::class
    ): void {
        new $appClass(ArrayHelper::merge(
            [
                'id' => 'testapp',
                'basePath' => __DIR__ . '/..',
                'vendorPath' => __DIR__ . '/../../vendor',
                'language' => $language,
                'bootstrap' => [
                    PostalCodeBootstrap::class,
                ],
            ],
            $config
        ));
    }

    protected function destroyApplication()
    {
        Yii::$app = null;
    }
}

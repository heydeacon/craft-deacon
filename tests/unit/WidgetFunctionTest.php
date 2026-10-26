<?php

namespace heydeacon\deacon\tests\unit;

use Craft;
use craft\test\TestCase;
use craft\web\View;
use heydeacon\deacon\models\Settings;
use heydeacon\deacon\Plugin;
use UnitTester;

class WidgetFunctionTest extends TestCase
{
    protected UnitTester $tester;

    protected function _before(): void
    {
        $this->setEnvironmentKey(null);
        Plugin::getInstance()->getSettings()->setAttributes((new Settings())->getAttributes(), false);
    }

    protected function _after(): void
    {
        $this->setEnvironmentKey(null);
    }

    public function testItPrintsTheTagFromInstallWithTheKeyFromTheSettings(): void
    {
        Plugin::getInstance()->getSettings()->key = 'pk_test123';

        $this->assertSame(
            '<script src="https://heydeacon.com/widget/v1/loader.js" data-key="pk_test123" async></script>',
            $this->renderedFunction(),
        );
    }

    public function testItTakesTheKeyFromDeaconKeyWhenTheSettingsHaveNone(): void
    {
        $this->setEnvironmentKey('pk_from_env');

        $this->assertSame(
            '<script src="https://heydeacon.com/widget/v1/loader.js" data-key="pk_from_env" async></script>',
            $this->renderedFunction(),
        );
    }

    private function setEnvironmentKey(?string $key): void
    {
        if ($key === null) {
            putenv('DEACON_KEY');
            unset($_ENV['DEACON_KEY'], $_SERVER['DEACON_KEY']);

            return;
        }

        putenv("DEACON_KEY={$key}");
        $_ENV['DEACON_KEY'] = $key;
        $_SERVER['DEACON_KEY'] = $key;
    }

    /**
     * What a site template's {{ deaconWidget() }} printed.
     */
    private function renderedFunction(): string
    {
        return Craft::$app->getView()->renderTemplate('function', [], View::TEMPLATE_MODE_SITE);
    }
}

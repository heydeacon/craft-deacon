<?php

namespace heydeacon\deacon\tests\unit;

use Craft;
use craft\test\TestCase;
use craft\web\View;
use heydeacon\deacon\Plugin;
use UnitTester;

class WidgetFunctionTest extends TestCase
{
    protected UnitTester $tester;

    public function testItPrintsTheTagFromInstallWithTheKeyFromTheSettings(): void
    {
        Plugin::getInstance()->getSettings()->key = 'pk_test123';

        $this->assertSame(
            '<script src="https://heydeacon.com/widget/v1/loader.js" data-key="pk_test123" async></script>',
            $this->renderedFunction(),
        );
    }

    /**
     * What a site template's {{ deaconWidget() }} printed.
     */
    private function renderedFunction(): string
    {
        return Craft::$app->getView()->renderTemplate('function', [], View::TEMPLATE_MODE_SITE);
    }
}

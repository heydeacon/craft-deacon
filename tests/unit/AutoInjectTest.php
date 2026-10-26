<?php

namespace heydeacon\deacon\tests\unit;

use Craft;
use craft\test\TestCase;
use craft\web\View;
use heydeacon\deacon\models\Settings;
use heydeacon\deacon\Plugin;
use UnitTester;

class AutoInjectTest extends TestCase
{
    protected UnitTester $tester;

    protected function _before(): void
    {
        Plugin::getInstance()->getSettings()->setAttributes((new Settings())->getAttributes(), false);
        Plugin::getInstance()->getSettings()->key = 'pk_test123';
    }

    public function testItAddsTheTagToTheHeadOfEverySitePage(): void
    {
        $this->assertSame(
            '<html><head><title>Page</title>'
            .'<script src="https://heydeacon.com/widget/v1/loader.js" data-key="pk_test123" async></script>'
            .'</head><body></body></html>',
            $this->renderedPage('page'),
        );
    }

    /**
     * A site front-end page, rendered the way Craft renders one for a request.
     */
    private function renderedPage(string $template): string
    {
        return Craft::$app->getView()->renderPageTemplate($template, [], View::TEMPLATE_MODE_SITE);
    }
}

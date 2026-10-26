<?php

namespace heydeacon\deacon\tests\unit;

use Craft;
use craft\events\RegisterTemplateRootsEvent;
use craft\web\View;
use yii\base\Event;

class AutoInjectTest extends \DeaconTestCase
{
    protected function _before(): void
    {
        parent::_before();
        $this->settings()->key = 'pk_test123';
    }

    public function testItAddsTheTagToTheHeadOfEverySitePage(): void
    {
        $this->assertSame(
            '<html><head><title>Page</title>'
            . '<script src="https://heydeacon.com/widget/v1/loader.js" data-key="pk_test123" async></script>'
            . '</head><body></body></html>',
            $this->renderedPage('page'),
        );
    }

    public function testItAddsNothingWhenTheTemplatePrintsTheTagItself(): void
    {
        $this->assertSame(
            '<html><head><title>Page</title>'
            . '<script src="https://heydeacon.com/widget/v1/loader.js" data-key="pk_test123" async></script>'
            . '</head><body></body></html>',
            $this->renderedPage('page-with-function'),
        );
    }

    public function testItLeavesThePageAloneWhenAddingToEveryPageIsTurnedOff(): void
    {
        $this->settings()->addToEveryPage = false;

        $this->assertSame('<html><head><title>Page</title></head><body></body></html>', $this->renderedPage('page'));
    }

    public function testItLeavesATemplateWithNoHeadAlone(): void
    {
        $this->assertSame('{"title": "Page"}', $this->renderedPage('data'));
    }

    public function testItLeavesControlPanelPagesAlone(): void
    {
        Event::on(View::class, View::EVENT_REGISTER_CP_TEMPLATE_ROOTS, function(RegisterTemplateRootsEvent $event): void {
            $event->roots['deacon-test'] = CRAFT_TEMPLATES_PATH;
        });

        $this->assertSame(
            '<html><head><title>Page</title></head><body></body></html>',
            Craft::$app->getView()->renderPageTemplate('deacon-test/page', [], View::TEMPLATE_MODE_CP),
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

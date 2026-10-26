<?php

namespace heydeacon\deacon;

use Craft;
use craft\base\Model;
use craft\base\Plugin as BasePlugin;
use craft\events\TemplateEvent;
use craft\web\View;
use heydeacon\deacon\models\Settings;
use heydeacon\deacon\twig\Extension;
use yii\base\Event;

/**
 * @method Settings getSettings()
 */
class Plugin extends BasePlugin
{
    public function init(): void
    {
        parent::init();

        Craft::$app->getView()->registerTwigExtension(new Extension());

        Event::on(View::class, View::EVENT_AFTER_RENDER_PAGE_TEMPLATE, function (TemplateEvent $event): void {
            if ($this->getSettings()->addToEveryPage) {
                $this->addTagToPage($event);
            }
        });
    }

    protected function createSettingsModel(): ?Model
    {
        return new Settings();
    }

    /**
     * Puts the tag before the page's </head>, unless the template printed it already, with
     * {{ deaconWidget() }} or the snippet from Install.
     */
    private function addTagToPage(TemplateEvent $event): void
    {
        $headEnd = stripos($event->output, '</head>');

        if ($headEnd === false || str_contains($event->output, 'src="'.WidgetTag::LOADER_SRC.'"')) {
            return;
        }

        $event->output = substr_replace($event->output, WidgetTag::html($this->getSettings()), $headEnd, 0);
    }
}

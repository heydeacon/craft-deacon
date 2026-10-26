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
    public bool $hasCpSettings = true;

    public function init(): void
    {
        parent::init();

        Craft::$app->getView()->registerTwigExtension(new Extension());

        Event::on(View::class, View::EVENT_AFTER_RENDER_PAGE_TEMPLATE, function(TemplateEvent $event): void {
            if ($event->templateMode === View::TEMPLATE_MODE_SITE && $this->getSettings()->addToEveryPage) {
                $this->addTagToPage($event);
            }
        });
    }

    protected function createSettingsModel(): ?Model
    {
        return new Settings();
    }

    protected function settingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('deacon/_settings', [
            'settings' => $this->getSettings(),
        ]);
    }

    /**
     * Puts the tag before the page's </head>, unless the template printed it already, with
     * {{ deaconWidget() }} or the snippet from Install.
     */
    private function addTagToPage(TemplateEvent $event): void
    {
        $headEnd = stripos($event->output, '</head>');

        if ($headEnd === false || str_contains($event->output, 'src="' . WidgetTag::LOADER_SRC . '"')) {
            return;
        }

        $event->output = substr_replace($event->output, WidgetTag::html($this->getSettings()), $headEnd, 0);
    }
}

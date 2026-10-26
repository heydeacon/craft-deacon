<?php

namespace heydeacon\deacon;

use Craft;
use craft\base\Model;
use craft\base\Plugin as BasePlugin;
use heydeacon\deacon\models\Settings;
use heydeacon\deacon\twig\Extension;

/**
 * @method Settings getSettings()
 */
class Plugin extends BasePlugin
{
    public function init(): void
    {
        parent::init();

        Craft::$app->getView()->registerTwigExtension(new Extension());
    }

    protected function createSettingsModel(): ?Model
    {
        return new Settings();
    }
}

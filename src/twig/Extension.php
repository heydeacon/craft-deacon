<?php

namespace heydeacon\deacon\twig;

use Craft;
use craft\helpers\App;
use heydeacon\deacon\Plugin;
use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

class Extension extends AbstractExtension
{
    private const LOADER_SRC = 'https://heydeacon.com/widget/v1/loader.js';

    private const MISSING_KEY = 'craft-deacon: the key is missing, so the page has no Deacon widget. Set '
        .'DEACON_KEY in .env, or the key in Settings → Plugins → Deacon. In Deacon, open Install (the '
        .'button at the top of the dashboard) and copy the data-key value from the snippet. It starts with pk_.';

    public function getFunctions(): array
    {
        return [
            new TwigFunction('deaconWidget', $this->deaconWidget(...)),
        ];
    }

    /**
     * The {{ deaconWidget() }} function.
     */
    public function deaconWidget(): Markup
    {
        $key = (string) App::parseEnv(Plugin::getInstance()->getSettings()->key);

        if ($key === '') {
            $key = (string) App::env('DEACON_KEY');
        }

        if (trim($key) === '') {
            Craft::warning(self::MISSING_KEY, 'deacon');

            return new Markup('', 'UTF-8');
        }

        return new Markup('<script src="'.self::LOADER_SRC.'" data-key="'.$key.'" async></script>', 'UTF-8');
    }
}

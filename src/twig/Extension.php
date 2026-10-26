<?php

namespace heydeacon\deacon\twig;

use craft\helpers\App;
use heydeacon\deacon\Plugin;
use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

class Extension extends AbstractExtension
{
    private const LOADER_SRC = 'https://heydeacon.com/widget/v1/loader.js';

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

        return new Markup('<script src="'.self::LOADER_SRC.'" data-key="'.$key.'" async></script>', 'UTF-8');
    }
}

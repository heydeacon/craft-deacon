<?php

namespace heydeacon\deacon\twig;

use heydeacon\deacon\Plugin;
use heydeacon\deacon\WidgetTag;
use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

class Extension extends AbstractExtension
{
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
        return new Markup(WidgetTag::html(Plugin::getInstance()->getSettings()), 'UTF-8');
    }
}

<?php

namespace heydeacon\deacon\twig;

use Craft;
use craft\helpers\App;
use craft\helpers\Html;
use heydeacon\deacon\Plugin;
use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;
use yii\base\InvalidConfigException;

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
        $settings = Plugin::getInstance()->getSettings();
        $key = trim((string) App::parseEnv($settings->key));

        if ($key === '') {
            $key = trim((string) App::env('DEACON_KEY'));
        }

        if ($key === '') {
            Craft::warning(self::MISSING_KEY, 'deacon');

            return new Markup('', 'UTF-8');
        }

        if ($settings->context !== null && ! $this->isContext($settings->context)) {
            throw new InvalidConfigException(
                'craft-deacon: context in config/deacon.php must be an array of names to strings, numbers or booleans.'
            );
        }

        if (! is_bool($settings->pageViews)) {
            throw new InvalidConfigException('craft-deacon: pageViews in config/deacon.php must be true or false.');
        }

        $attributes = ['data-key' => $key];

        if ($settings->context !== null) {
            $attributes['data-context'] = json_encode($settings->context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        if ($settings->pageViews === false) {
            $attributes['data-no-page-views'] = true;
        }

        return new Markup($this->loaderTag($attributes), 'UTF-8');
    }

    /**
     * @phpstan-assert-if-true array<string, string|int|float|bool> $context
     */
    private function isContext(mixed $context): bool
    {
        if (! is_array($context)) {
            return false;
        }

        foreach ($context as $name => $value) {
            if (! is_string($name) || ! (is_string($value) || is_int($value) || is_float($value) || is_bool($value))) {
                return false;
            }
        }

        return true;
    }

    /**
     * The tag Install gives, with any of its other attributes between the key and async. An
     * attribute that is true is written with no value, as async is.
     *
     * @param  array<string, string|true>  $attributes
     */
    private function loaderTag(array $attributes): string
    {
        $rendered = '';

        foreach ($attributes as $name => $value) {
            $rendered .= $value === true ? ' '.$name : ' '.$name.'="'.Html::encode($value).'"';
        }

        return '<script src="'.self::LOADER_SRC.'"'.$rendered.' async></script>';
    }
}

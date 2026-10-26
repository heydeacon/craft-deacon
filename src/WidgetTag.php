<?php

namespace heydeacon\deacon;

use Craft;
use craft\helpers\App;
use craft\helpers\Html;
use heydeacon\deacon\models\Settings;
use yii\base\InvalidConfigException;

/**
 * The tag Install gives, built from the plugin's settings.
 */
final class WidgetTag
{
    private const LOADER_SRC = 'https://heydeacon.com/widget/v1/loader.js';

    private const MISSING_KEY = 'craft-deacon: the key is missing, so the page has no Deacon widget. Set '
        . 'DEACON_KEY in .env, or the key in Settings → Plugins → Deacon. In Deacon, open Install (the '
        . 'button at the top of the dashboard) and copy the data-key value from the snippet. It starts with pk_.';

    /**
     * Whether the HTML already loads Deacon, from this tag or the snippet pasted from Install.
     */
    public static function isIn(string $html): bool
    {
        return str_contains($html, 'src="' . self::LOADER_SRC . '"');
    }

    /**
     * The tag, with the key from the settings, or from DEACON_KEY when they have none. With no
     * key it is empty and a warning is logged, so a missing setting never takes a page down.
     *
     * @throws InvalidConfigException when config/deacon.php gives a context of the wrong type
     */
    public static function html(Settings $settings): string
    {
        $key = trim((string) App::parseEnv($settings->key));

        if ($key === '') {
            $key = trim((string) App::env('DEACON_KEY'));
        }

        if ($key === '') {
            Craft::warning(self::MISSING_KEY, 'deacon');

            return '';
        }

        if ($settings->context !== null && !self::isContext($settings->context)) {
            throw new InvalidConfigException(
                'craft-deacon: context in config/deacon.php must be an array whose values are strings, numbers or booleans.'
            );
        }

        $attributes = ['data-key' => $key];

        if ($settings->context !== null) {
            $attributes['data-context'] = json_encode($settings->context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        if (!$settings->pageViews) {
            $attributes['data-no-page-views'] = true;
        }

        return self::loaderTag($attributes);
    }

    /**
     * @phpstan-assert-if-true array<string, string|int|float|bool> $context
     */
    private static function isContext(mixed $context): bool
    {
        if (!is_array($context)) {
            return false;
        }

        foreach ($context as $name => $value) {
            if (!is_string($name) || !(is_string($value) || is_int($value) || is_float($value) || is_bool($value))) {
                return false;
            }
        }

        return true;
    }

    /**
     * The tag Install gives, with any of its other attributes between the key and async. An
     * attribute that is true is written with no value, as async is.
     *
     * @param array<string, string|true> $attributes
     */
    private static function loaderTag(array $attributes): string
    {
        $rendered = '';

        foreach ($attributes as $name => $value) {
            $rendered .= $value === true ? ' ' . $name : ' ' . $name . '="' . Html::encode($value) . '"';
        }

        return '<script src="' . self::LOADER_SRC . '"' . $rendered . ' async></script>';
    }
}

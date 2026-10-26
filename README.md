# Deacon for Craft CMS

[Deacon](https://heydeacon.com) is an AI support agent that answers questions from your own docs and content, and says when it doesn't know. This plugin adds its chat widget to every page of a Craft CMS site.

You need a Deacon account, and there's a free plan.

## Install

Search for Deacon in the Plugin Store in your control panel and install it. Or use Composer:

```sh
composer require heydeacon/craft-deacon
php craft plugin/install deacon
```

## Set it up

Give it your key, in `.env`:

```sh
DEACON_KEY=pk_your_widget_key
```

Or paste it in Settings → Plugins → Deacon. That field also takes an environment variable, such as `$DEACON_KEY`.

That's all. The plugin puts the widget in the `<head>` of every page of your site. To place it yourself instead, turn off "Add Deacon to every page" in the plugin's settings and put the function in the `<head>` of your layout:

```twig
<head>
    {# ...the rest of your head #}
    {{ deaconWidget() }}
</head>
```

A page that prints the tag itself gets nothing more from the plugin, so the widget never loads twice.

To find your key, open Install from the button at the top of the Deacon dashboard. The snippet there has it in `data-key`, and it starts with `pk_`. It isn't a secret: every visitor's browser reads it. If the key is missing, the plugin adds nothing and logs a warning that says why, so your pages still render. A `context` of the wrong type throws an `InvalidConfigException` that says why.

Deacon only runs on the sites listed on Install. A listed domain covers its subdomains, so `example.com` covers `docs.example.com`. If your site lives somewhere else, such as a staging address, add that address on Install too.

When you run the site on your own machine at a `.test` address or on `localhost`, Install notices the widget there and lets you add it in one click. Chats on a development address don't count towards your plan. A DDEV address, ending in `.ddev.site`, isn't treated as one, so add it on Install yourself.

If your site passes Deacon the details of whoever is logged in, with the `window.deaconUser` script from [Deacon's install guide](https://heydeacon.com/agent/install.md), put that script in the `<head>` of your layout. The widget has to come after it, because it reads it as it starts. The plugin adds the widget at the end of the head, so it does. If you place the widget yourself, put `{{ deaconWidget() }}` below the script.

## Options

| Setting          | Type    | Where                            | Default      | What it does |
| ---------------- | ------- | -------------------------------- | ------------ | ------------ |
| `key`            | string  | Settings, or `config/deacon.php` | `DEACON_KEY` | Your Deacon key. |
| `addToEveryPage` | boolean | Settings, or `config/deacon.php` | `true`       | Puts the widget in the `<head>` of every page of your site. Turn it off to place it yourself with `{{ deaconWidget() }}`. |
| `pageViews`      | boolean | Settings, or `config/deacon.php` | `true`       | The same script counts visits for Deacon's website analytics. Set this to `false` to send no page views or JavaScript errors from your site. Chat still works. |
| `context`        | array   | `config/deacon.php` only         | none         | Details Deacon takes into account when it answers, the same on every page, such as `['product' => 'api', 'docs_version' => '3.2']`. Up to 12 keys, each value a string, number or boolean. Anyone can read them in the page's HTML, so never put a secret here. |

A setting in `config/deacon.php` wins over the same one in the control panel:

```php
<?php

return [
    'context' => ['product' => 'api', 'docs_version' => '3.2'],
    'pageViews' => false,
];
```

## Content Security Policy

Craft doesn't give each request a nonce of its own, so the tag carries none. If your site sends a Content Security Policy, list Deacon's address, `https://heydeacon.com`, in `script-src`, `connect-src` and `frame-src`, and `'unsafe-inline'` in `style-src`, because the widget writes its styles where a nonce can't reach. Keep any nonce out of `style-src`, since a nonce there makes browsers ignore `'unsafe-inline'`:

```text
script-src 'self' https://heydeacon.com;
style-src 'self' 'unsafe-inline';
connect-src 'self' https://heydeacon.com;
frame-src https://heydeacon.com
```

## Opening the chat from your own button

The widget comes with its own button. To use one of yours instead, hide Deacon's in Appearance in the dashboard and call `window.Deacon.open()` from your button or link. `Deacon.close()` and `Deacon.toggle()` work too.

## What it adds

The plugin prints the same tag Install gives you:

```html
<script src="https://heydeacon.com/widget/v1/loader.js" data-key="pk_your_widget_key" async></script>
```

It loads in the background without holding up the page. Works with Craft CMS 5, on PHP 8.2 and later.

## License

MIT

<?php

namespace heydeacon\deacon\tests\unit;

use Craft;
use craft\test\TestCase;
use craft\web\View;
use heydeacon\deacon\Plugin;
use UnitTester;

class SettingsPageTest extends TestCase
{
    protected UnitTester $tester;

    public function testTheSettingsPageShowsEachSettingWithItsValue(): void
    {
        Plugin::getInstance()->setSettings(['key' => '$DEACON_KEY', 'addToEveryPage' => false, 'pageViews' => true]);

        $this->assertSame(
            ['addToEveryPage' => '', 'key' => '$DEACON_KEY', 'pageViews' => '1'],
            $this->fieldValues(),
        );
    }

    /**
     * Each setting on the plugin's settings page with the value it shows. The key's field is
     * built in the browser, so its value is read from the preview Craft prints in its place.
     *
     * @return array<string, string>
     */
    private function fieldValues(): array
    {
        $html = Craft::$app->getView()->renderTemplate(
            'deacon/_settings',
            ['settings' => Plugin::getInstance()->getSettings()],
            View::TEMPLATE_MODE_CP,
        );

        $document = new \DOMDocument();
        $document->loadHTML('<?xml encoding="utf-8"?><div>'.$html.'</div>', LIBXML_NOERROR);

        $values = [];

        foreach ($document->getElementsByTagName('input') as $input) {
            if ($input->getAttribute('id') === 'key-preview') {
                $values['key'] = $input->getAttribute('value');
            } elseif ($input->getAttribute('name') !== '') {
                $values[$input->getAttribute('name')] = $input->getAttribute('value');
            }
        }

        ksort($values);

        return $values;
    }
}

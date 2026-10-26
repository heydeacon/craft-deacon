<?php

namespace heydeacon\deacon\tests\unit;

use Craft;
use craft\test\TestCase;
use craft\web\View;
use heydeacon\deacon\models\Settings;
use heydeacon\deacon\Plugin;
use UnitTester;
use Codeception\Lib\Connector\Yii2\Logger;

class WidgetFunctionTest extends TestCase
{
    protected UnitTester $tester;

    protected function _before(): void
    {
        $this->testLogger()->getAndClearLog();
        $this->setEnvironmentKey(null);
        Plugin::getInstance()->getSettings()->setAttributes((new Settings())->getAttributes(), false);
    }

    protected function _after(): void
    {
        $this->setEnvironmentKey(null);
    }

    public function testItPrintsTheTagFromInstallWithTheKeyFromTheSettings(): void
    {
        Plugin::getInstance()->getSettings()->key = 'pk_test123';

        $this->assertSame(
            '<script src="https://heydeacon.com/widget/v1/loader.js" data-key="pk_test123" async></script>',
            $this->renderedFunction(),
        );
    }

    public function testItTakesTheKeyFromDeaconKeyWhenTheSettingsHaveNone(): void
    {
        $this->setEnvironmentKey('pk_from_env');

        $this->assertSame(
            '<script src="https://heydeacon.com/widget/v1/loader.js" data-key="pk_from_env" async></script>',
            $this->renderedFunction(),
        );
    }

    public function testItReadsAnEnvironmentVariableNamedInTheKeySetting(): void
    {
        $_SERVER['STAGING_DEACON_KEY'] = 'pk_from_named_variable';
        Plugin::getInstance()->getSettings()->key = '$STAGING_DEACON_KEY';

        try {
            $this->assertSame(
                '<script src="https://heydeacon.com/widget/v1/loader.js" data-key="pk_from_named_variable" async></script>',
                $this->renderedFunction(),
            );
        } finally {
            unset($_SERVER['STAGING_DEACON_KEY']);
        }
    }

    public function testItPassesTheContextToDeaconAsTheDataContextAttribute(): void
    {
        $settings = Plugin::getInstance()->getSettings();
        $settings->key = 'pk_test123';
        $settings->context = ['site' => 'docs', 'version' => '3.2', 'beta' => true];

        $this->assertSame(
            '<script src="https://heydeacon.com/widget/v1/loader.js" data-key="pk_test123" '
            .'data-context="{&quot;site&quot;:&quot;docs&quot;,&quot;version&quot;:&quot;3.2&quot;,'
            .'&quot;beta&quot;:true}" async></script>',
            $this->renderedFunction(),
        );
    }

    public function testItTrimsSpacesPastedAroundTheKey(): void
    {
        Plugin::getInstance()->getSettings()->key = "  pk_test123\n";

        $this->assertSame(
            '<script src="https://heydeacon.com/widget/v1/loader.js" data-key="pk_test123" async></script>',
            $this->renderedFunction(),
        );
    }

    private const MISSING_KEY ='craft-deacon: the key is missing, so the page has no Deacon widget. Set '
        .'DEACON_KEY in .env, or the key in Settings → Plugins → Deacon. In Deacon, open Install (the '
        .'button at the top of the dashboard) and copy the data-key value from the snippet. It starts with pk_.';

    /**
     * @return array<string, array{string}>
     */
    public static function missingKeys(): array
    {
        return [
            'empty' => [''],
            'blank' => ['   '],
            'a variable that is not set' => ['$UNSET_DEACON_KEY'],
        ];
    }

    /**
     * @dataProvider missingKeys
     */
    public function testItPrintsNothingAndLogsAWarningWhenTheKeyIsMissing(string $key): void
    {
        Plugin::getInstance()->getSettings()->key = $key;

        $this->assertSame(['', [self::MISSING_KEY]], [$this->renderedFunction(), $this->loggedWarnings()]);
    }

    /**
     * What was logged in the plugin's category while the test ran. Codeception's logger keeps
     * the last few messages as lines like "[deacon] 'the message'".
     *
     * @return list<string>
     */
    private function loggedWarnings(): array
    {
        $messages = [];

        foreach (explode(PHP_EOL, $this->testLogger()->getAndClearLog()) as $line) {
            if (str_starts_with($line, "[deacon] '") && str_ends_with($line, "'")) {
                $quoted = substr($line, strlen("[deacon] '"), -1);
                $messages[] = preg_replace('/\\\\([\'\\\\])/', '$1', $quoted);
            }
        }

        return $messages;
    }

    private function testLogger(): Logger
    {
        $logger = Craft::getLogger();
        $this->assertInstanceOf(Logger::class, $logger);

        return $logger;
    }

    private function setEnvironmentKey(?string $key): void
    {
        if ($key === null) {
            putenv('DEACON_KEY');
            unset($_ENV['DEACON_KEY'], $_SERVER['DEACON_KEY']);

            return;
        }

        putenv("DEACON_KEY={$key}");
        $_ENV['DEACON_KEY'] = $key;
        $_SERVER['DEACON_KEY'] = $key;
    }

    /**
     * What a site template's {{ deaconWidget() }} printed.
     */
    private function renderedFunction(): string
    {
        return Craft::$app->getView()->renderTemplate('function', [], View::TEMPLATE_MODE_SITE);
    }
}

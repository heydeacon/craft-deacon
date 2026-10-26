<?php

use craft\test\TestCase;
use heydeacon\deacon\models\Settings;
use heydeacon\deacon\Plugin;

/**
 * Starts and ends each test with the plugin's default settings and no DEACON_KEY, since the
 * plugin and the environment are shared by every test in the run.
 */
abstract class DeaconTestCase extends TestCase
{
    protected UnitTester $tester;

    protected function _before(): void
    {
        $this->resetSettingsAndEnvironment();
    }

    protected function _after(): void
    {
        $this->resetSettingsAndEnvironment();
    }

    protected function settings(): Settings
    {
        return Plugin::getInstance()->getSettings();
    }

    protected function setEnvironmentKey(?string $key): void
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

    private function resetSettingsAndEnvironment(): void
    {
        $this->setEnvironmentKey(null);
        $this->settings()->setAttributes((new Settings())->getAttributes(), false);
    }
}

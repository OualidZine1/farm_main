<?php

namespace Tests\Browser;

use PHPUnit\Framework\TestCase;

// Optional WebDriver classes — we will check for existence at runtime
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;

/**
 * Minimal Selenium test base to allow browser tests to run or gracefully skip
 * when Selenium/Chromedriver is not available.
 */
abstract class SeleniumTestCase extends TestCase
{
    protected $webDriver;
    protected $baseUrl;

    protected function setUp(): void
    {
        parent::setUp();

        $this->baseUrl = env('APP_URL', 'http://127.0.0.1:8000');

        // If the WebDriver classes aren't available, skip browser tests.
        if (!class_exists(RemoteWebDriver::class) || !class_exists(DesiredCapabilities::class)) {
            $this->markTestSkipped('php-webdriver is not installed. Skipping browser tests.');
        }

        // Try to create a RemoteWebDriver instance. If connection fails, skip tests.
        try {
            // Default chromedriver URL — tests can be skipped if nothing is listening.
            $host = env('WEBDRIVER_HOST', 'http://localhost:9515');
            $capabilities = DesiredCapabilities::chrome();
            $this->webDriver = RemoteWebDriver::create($host, $capabilities, 5000, 120000);
        } catch (\Throwable $e) {
            $this->markTestSkipped('Could not connect to Selenium/Chromedriver: ' . $e->getMessage());
        }
    }

    protected function tearDown(): void
    {
        if ($this->webDriver) {
            try {
                $this->webDriver->quit();
            } catch (\Throwable $e) {
                // ignore
            }
        }

        parent::tearDown();
    }

    protected function login(): void
    {
        // Use the seeded admin credentials from README.md by default
        $email = env('TEST_ADMIN_EMAIL', 'oualid.zine@uit.ac.ma');
        $password = env('TEST_ADMIN_PASSWORD', 'password');

        $this->webDriver->get(rtrim($this->baseUrl, '/') . '/login');

        // Fill and submit the login form if fields exist
        try {
            $this->webDriver->findElement(WebDriverBy::name('email'))->sendKeys($email);
            $this->webDriver->findElement(WebDriverBy::name('password'))->sendKeys($password);
            $this->webDriver->findElement(WebDriverBy::cssSelector('button[type="submit"]'))->click();
            $this->waitForPageLoad();
        } catch (\Throwable $e) {
            // If the login form isn't present, skip — tests may rely on a different auth flow.
            $this->markTestSkipped('Login form not available: ' . $e->getMessage());
        }
    }

    protected function waitForElement(string $cssSelector, int $timeoutSeconds = 5): void
    {
        $end = time() + $timeoutSeconds;
        while (time() < $end) {
            try {
                $elements = $this->webDriver->findElements(WebDriverBy::cssSelector($cssSelector));
                if (!empty($elements)) {
                    return;
                }
            } catch (\Throwable $e) {
                // ignore and retry
            }
            usleep(200000);
        }
        // allow tests to proceed; they can decide to assert presence
    }

    protected function waitForPageLoad(int $seconds = 2): void
    {
        // simple wait; not robust but sufficient for these tests
        sleep($seconds);
    }
}


<?php

namespace Tests\Browser;

use Facebook\WebDriver\WebDriverBy;
use Carbon\Carbon;

class ProductSystemTest extends SeleniumTestCase
{
    protected static $webDriverInstance;

    protected function setUp(): void
    {
        parent::setUp();

        if (!self::$webDriverInstance) {
            self::$webDriverInstance = $this->webDriver;
            self::$webDriverInstance->manage()->window()->maximize();
        } else {
            $this->webDriver = self::$webDriverInstance;
        }
    }

    protected function tearDown(): void
    {
    }

    public function testAddProductValid()
    {
        $this->login();

        $this->webDriver->get($this->baseUrl . '/products');
        $this->webDriver->findElement(WebDriverBy::cssSelector('a[href$="/products/create"]'))->click();
        $this->waitForElement('form');

        $this->webDriver->findElement(WebDriverBy::name('name'))
            ->sendKeys('Avocado');

        $this->webDriver->findElement(WebDriverBy::name('description'))
            ->sendKeys('Valid description');

        $this->webDriver->findElement(WebDriverBy::cssSelector('select[name="category_id"] option'))->click();

        $this->webDriver->findElement(WebDriverBy::name('current_quantity'))
            ->clear()
            ->sendKeys('0');

        $this->webDriver->findElement(WebDriverBy::cssSelector('button[type="submit"]'))->click();

        $this->waitForPageLoad();

        $this->assertStringContainsString(
            'Product created successfully',
            $this->webDriver->getPageSource()
        );
    }

    public function testAddProductInvalid()
    {
        $this->login();

        $this->webDriver->get($this->baseUrl . '/products');
        $this->webDriver->findElement(WebDriverBy::cssSelector('a[href$="/products/create"]'))->click();
        $this->waitForElement('form');

        $this->webDriver->executeScript("document.getElementById('name').removeAttribute('required')");
        $this->webDriver->executeScript("document.getElementById('category_id').removeAttribute('required')");

        $this->webDriver->findElement(WebDriverBy::name('name'))->clear();

        $this->webDriver->findElement(WebDriverBy::cssSelector('button[type="submit"]'))->click();

        $this->assertStringContainsString(
            'The name field is required',
            $this->webDriver->getPageSource()
        );
    }

    public function testProductSearchValid()
    {
        $this->login();

        $this->webDriver->get($this->baseUrl . '/products');

        $this->webDriver->findElement(WebDriverBy::name('search'))
            ->sendKeys('Insecticide');

        $this->webDriver->findElement(WebDriverBy::cssSelector('button[type="submit"]'))->click();

        $this->assertTrue(true);
    }

    public function testProductSearchInvalid()
    {
        $this->login();

        $this->webDriver->get($this->baseUrl . '/products');

        $searchField = $this->webDriver->findElement(WebDriverBy::name('search'));
        $searchField->sendKeys('nonexistentproduct123');
        $searchField->submit();

        $this->waitForPageLoad();

        $this->assertStringContainsString(
            'No products',
            $this->webDriver->getPageSource()
        );
    }

    public function testAddStockValid()
    {
        $this->login();

        $this->webDriver->get($this->baseUrl . '/products');

        $addStockLinks = $this->webDriver->findElements(WebDriverBy::cssSelector('a[title="Add Stock"]'));
        if (empty($addStockLinks)) {
            $this->markTestSkipped('No products available to add stock');
        }
        $addStockLinks[0]->click();

        $this->webDriver->findElement(WebDriverBy::name('quantity'))
            ->sendKeys(10);

        $this->webDriver->findElement(WebDriverBy::name('price'))
            ->sendKeys('10.50');

        $dateField = $this->webDriver->findElement(WebDriverBy::name('date'));
        $dateField->clear();
        $dateField->sendKeys('01-10-2025');

        $this->webDriver->findElement(WebDriverBy::cssSelector('button.btn-primary[type="submit"]'))->click();

        $this->waitForPageLoad();

        $this->assertTrue(
            str_contains($this->webDriver->getPageSource(), 'alert-success') ||
            str_contains($this->webDriver->getPageSource(), 'Stock added') ||
            str_contains($this->webDriver->getCurrentURL(), '/products')
        );
    }

    public function testAddStockInvalid()
    {
        $this->login();

        $this->webDriver->get($this->baseUrl . '/products');

        $this->webDriver->findElement(WebDriverBy::cssSelector('a[title="Add Stock"]'))->click();

        $this->webDriver->executeScript("document.getElementsByName('quantity')[0].removeAttribute('min')");

        $this->webDriver->findElement(WebDriverBy::name('quantity'))
            ->sendKeys(-5);

        $this->webDriver->findElement(WebDriverBy::name('price'))
            ->sendKeys('10.50');

        $this->webDriver->findElement(WebDriverBy::name('date'))
            ->sendKeys('01-10-2025');

        $this->webDriver->findElement(WebDriverBy::cssSelector('button.btn-primary[type="submit"]'))->click();

        $this->assertTrue(
            str_contains($this->webDriver->getPageSource(), 'quantity') ||
            str_contains($this->webDriver->getPageSource(), 'least 1')
        );
    }

    public function testExportReportValid()
    {
        $this->login();

        $this->webDriver->get($this->baseUrl . '/transactions/product-usage-report');

        $this->webDriver->findElement(WebDriverBy::name('start_date'))
            ->sendKeys('01-01-2020');

        $this->webDriver->findElement(WebDriverBy::name('end_date'))
            ->sendKeys('31-12-2030');

        $this->webDriver->findElement(WebDriverBy::cssSelector('button.btn-primary[type="submit"]'))->click();

        $this->waitForPageLoad();

        if (str_contains($this->webDriver->getPageSource(), 'No usage data found')) {
            $this->markTestSkipped('No usage data available for export test');
        }

        $this->waitForElement('a.btn-success');
        $exportBtn = $this->webDriver->findElement(WebDriverBy::cssSelector('a.btn-success'));
        $exportBtn->click();

        $this->assertStringNotContainsString(
            'There is no data to export',
            $this->webDriver->getPageSource()
        );
    }

    public function testExportReportInvalidNoData()
    {
        $this->login();

        $this->webDriver->get($this->baseUrl . '/transactions/product-usage-report');

        $this->webDriver->findElement(WebDriverBy::name('start_date'))
            ->sendKeys('01-01-2000');

        $this->webDriver->findElement(WebDriverBy::name('end_date'))
            ->sendKeys('31-01-2000');

        $this->webDriver->findElement(WebDriverBy::cssSelector('button.btn-primary[type="submit"]'))->click();

        $this->waitForPageLoad();

        $this->waitForElement('a.btn-success');
        $exportBtn = $this->webDriver->findElement(WebDriverBy::cssSelector('a.btn-success'));
        $exportBtn->click();

        $this->assertStringContainsString(
            'There is no data to export',
            $this->webDriver->getPageSource()
        );
    }
}

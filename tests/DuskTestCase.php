<?php

namespace Tests;

use Laravel\Dusk\TestCase as BaseTestCase;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     * @return void
     */

    public static function prepare()
    {
        static::startChromeDriver();
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        $options = (new ChromeOptions)->addArguments([
            '--disable-gpu',
//            '--no-sandbox',
//            '--headless'
        ]);

        return RemoteWebDriver::create(
            'http://localhost:4444/wd/hub', DesiredCapabilities::chrome()
                ->setCapability(ChromeOptions::CAPABILITY,$options)
        );
    }

    public function typeInCKEditor ($selector, $browser, $text)
    {
        $ckIframe = $browser->elements($selector)[0];
        $browser->driver->switchTo()->frame($ckIframe);
        $body = $browser->driver->findElement(WebDriverBy::xpath('//body'));
        $body->clear();
        $body->sendKeys($text);
        $browser->driver->switchTo()->defaultContent();
    }

}

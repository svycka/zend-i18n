<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\I18n\View\Helper;

use DateTime;
use Locale;
use IntlDateFormatter;
use Zend\I18n\View\Helper\DateFormat as DateFormatHelper;

/**
 * Test class for Zend\View\Helper\Currency
 *
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class DateFormatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DateFormatHelper
     */
    public $helper;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        if (! interface_exists('Zend\View\Helper\HelperInterface')) {
            $this->markTestSkipped(
                'Skipping tests that utilize zend-view until that component is '
                . 'forwards-compatible with zend-stdlib and zend-servicemanager v3'
            );
        }

        if (!extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $this->helper = new DateFormatHelper();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->helper);
    }

    public function dateTestsDataProvider()
    {
        if (!extension_loaded('intl')) {
            if (version_compare(\PHPUnit_Runner_Version::id(), '3.8.0-dev') === 1) {
                $this->markTestSkipped('ext/intl not enabled');
            } else {
                return [[]];
            }
        }

        $date = new DateTime('2012-07-02T22:44:03Z');

        return [
            [
                'de_DE',
                'Europe/Berlin',
                IntlDateFormatter::FULL,
                IntlDateFormatter::FULL,
                $date,
            ],
            [
                'de_DE',
                'Europe/Berlin',
                IntlDateFormatter::LONG,
                IntlDateFormatter::LONG,
                $date,
            ],
            [
                'de_DE',
                'Europe/Berlin',
                IntlDateFormatter::MEDIUM,
                IntlDateFormatter::MEDIUM,
                $date,
            ],
            [
                'de_DE',
                'Europe/Berlin',
                IntlDateFormatter::SHORT,
                IntlDateFormatter::SHORT,
                $date,
            ],
            [
                'ru_RU',
                'Europe/Moscow',
                IntlDateFormatter::FULL,
                IntlDateFormatter::FULL,
                $date,
            ],
            [
                'ru_RU',
                'Europe/Moscow',
                IntlDateFormatter::LONG,
                IntlDateFormatter::LONG,
                $date,
            ],
            [
                'ru_RU',
                'Europe/Moscow',
                IntlDateFormatter::MEDIUM,
                IntlDateFormatter::MEDIUM,
                $date,
            ],
            [
                'ru_RU',
                'Europe/Moscow',
                IntlDateFormatter::SHORT,
                IntlDateFormatter::SHORT,
                $date,
            ],
            [
                'en_US',
                'America/New_York',
                IntlDateFormatter::FULL,
                IntlDateFormatter::FULL,
                $date,
            ],
            [
                'en_US',
                'America/New_York',
                IntlDateFormatter::LONG,
                IntlDateFormatter::LONG,
                $date,
            ],
            [
                'en_US',
                'America/New_York',
                IntlDateFormatter::MEDIUM,
                IntlDateFormatter::MEDIUM,
                $date,
            ],
            [
                'en_US',
                'America/New_York',
                IntlDateFormatter::SHORT,
                IntlDateFormatter::SHORT,
                $date,
            ],
        ];
    }

    public function dateTestsDataProviderWithPattern()
    {
        if (!extension_loaded('intl')) {
            if (version_compare(\PHPUnit_Runner_Version::id(), '3.8.0-dev') === 1) {
                $this->markTestSkipped('ext/intl not enabled');
            } else {
                return [[]];
            }
        }

        $date = new DateTime('2012-07-02T22:44:03Z');

        return [
            [
                'de_DE',
                'Europe/Berlin',
                IntlDateFormatter::FULL,
                IntlDateFormatter::FULL,
                'dd-MM',
                $date,
            ],
            [
                'de_DE',
                'Europe/Berlin',
                null,
                null,
                'MMMM',
                $date,
            ],
            [
                'de_DE',
                'Europe/Berlin',
                null,
                null,
                'MMMM.Y',
                $date,
            ],
            [
                'de_DE',
                'Europe/Berlin',
                null,
                null,
                'dd/Y',
                $date,
            ],
        ];
    }

    /**
     * @dataProvider dateTestsDataProvider
     */
    public function testBasic($locale, $timezone, $timeType, $dateType, $date)
    {
        $this->helper
             ->setTimezone($timezone);

        $expected = $this->getIntlDateFormatter($locale, $dateType, $timeType, $timezone)
                         ->format($date->getTimestamp());

        $this->assertMbStringEquals($expected, $this->helper->__invoke(
            $date,
            $dateType,
            $timeType,
            $locale,
            null
        ));
    }

    /**
     * @dataProvider dateTestsDataProvider
     */
    public function testSettersProvideDefaults($locale, $timezone, $timeType, $dateType, $date)
    {
        $this->helper
            ->setTimezone($timezone)
            ->setLocale($locale);

        $expected = $this->getIntlDateFormatter($locale, $dateType, $timeType, $timezone)
                         ->format($date->getTimestamp());

        $this->assertMbStringEquals($expected, $this->helper->__invoke(
            $date,
            $dateType,
            $timeType
        ));
    }

    /**
     * @dataProvider dateTestsDataProviderWithPattern
     */
    public function testUseCustomPattern($locale, $timezone, $timeType, $dateType, $pattern, $date)
    {
        $this->helper
             ->setTimezone($timezone);

        $expected = $this->getIntlDateFormatter($locale, $dateType, $timeType, $timezone, $pattern)
                         ->format($date->getTimestamp());

        $this->assertMbStringEquals($expected, $this->helper->__invoke(
            $date,
            $dateType,
            $timeType,
            $locale,
            $pattern
        ));
    }

    public function testDefaultLocale()
    {
        $this->assertEquals(Locale::getDefault(), $this->helper->getLocale());
    }

    public function testBugTwoPatternOnSameHelperInstance()
    {
        $date = new DateTime('2012-07-02T22:44:03Z');

        $helper = new DateFormatHelper();
        $helper->setTimezone('Europe/Berlin');
        $this->assertEquals('03/2012', $helper->__invoke($date, null, null, 'it_IT', 'dd/Y'));
        $this->assertEquals('03-2012', $helper->__invoke($date, null, null, 'it_IT', 'dd-Y'));
    }

    public function assertMbStringEquals($expected, $test, $message = '')
    {
        $expected = str_replace(["\xC2\xA0", ' '], '', $expected);
        $test     = str_replace(["\xC2\xA0", ' '], '', $test);
        $this->assertEquals($expected, $test, $message);
    }

    public function getIntlDateFormatter($locale, $dateType, $timeType, $timezone, $pattern = null)
    {
        return new IntlDateFormatter($locale, $dateType, $timeType, $timezone, null, $pattern);
    }
}

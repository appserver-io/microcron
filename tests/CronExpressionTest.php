<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * Most of this work is derived from mtdowling/cron-expression which is copyrighted as:
 * Copyright (c) 2011 Michael Dowling <mtdowling@gmail.com> and contributors
 * The licence of this work can be found here: https://github.com/mtdowling/cron-expression/blob/master/LICENSE
 *
 * Some limitations might apply.
 *
 * PHP version 5
 *
 * @category  Library
 * @package   Microcron
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH - <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io/
 */

namespace AppserverIo\Microcron;

/**
 * AppserverIo\Microcron\CronExpressionTest
 *
 * Tests for the AppserverIo\Microcron\CronExpression class
 *
 * @category  Library
 * @package   Microcron
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH - <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io/
 */
class CronExpressionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Tests if the cron expression factory recognizes existing templates
     *
     * @return null
     *
     * @covers AppserverIo\Microcron\CronExpression::factory
     */
    public function testFactoryRecognizesTemplates()
    {
        $this->assertEquals('0 0 0 1 1 *', CronExpression::factory('@annually')->getExpression());
        $this->assertEquals('0 0 0 1 1 *', CronExpression::factory('@yearly')->getExpression());
        $this->assertEquals('0 0 0 * * 0', CronExpression::factory('@weekly')->getExpression());
        $this->assertEquals('0 0 0 1 * *', CronExpression::factory('@monthly')->getExpression());
        $this->assertEquals('0 0 0 * * *', CronExpression::factory('@daily')->getExpression());
        $this->assertEquals('0 0 * * * *', CronExpression::factory('@hourly')->getExpression());
        $this->assertEquals('0 * * * * *', CronExpression::factory('@byMinute')->getExpression());
        $this->assertEquals('* * * * * *', CronExpression::factory('@bySecond')->getExpression());
    }

    /**
     * Tests if the parser can make out the different parts of a cron expressions together with their semantic meaning
     *
     * @return null
     *
     * @covers AppserverIo\Microcron\CronExpression::__construct
     * @covers AppserverIo\Microcron\CronExpression::getExpression
     * @covers AppserverIo\Microcron\CronExpression::__toString
     */
    public function testParsesCronSchedule()
    {
        // '2010-09-10 12:00:00'
        $baseExpression = '14 1 2-4 * 4,5,6 */3';
        $cron = CronExpression::factory($baseExpression);
        $this->assertEquals('14', $cron->getExpression(CronExpression::SECOND));
        $this->assertEquals('1', $cron->getExpression(CronExpression::MINUTE));
        $this->assertEquals('2-4', $cron->getExpression(CronExpression::HOUR));
        $this->assertEquals('*', $cron->getExpression(CronExpression::DAY));
        $this->assertEquals('4,5,6', $cron->getExpression(CronExpression::MONTH));
        $this->assertEquals('*/3', $cron->getExpression(CronExpression::WEEKDAY));
        $this->assertEquals($baseExpression, $cron->getExpression());
        $this->assertEquals($baseExpression, (string) $cron);
        $this->assertNull($cron->getExpression('foo'));

        try {
            $cron = CronExpression::factory('A 1 2 3 4');
            $this->fail('Validation exception not thrown');
        } catch (\InvalidArgumentException $e) {
        }
    }

    /**
     * Tests if the parser can handle different space characters in between semantic parts
     * Uses a data provider
     *
     * @param string $schedule The oddly separated schedule to parse
     * @param array  $expected The expected parts
     *
     * @return null
     *
     * @covers AppserverIo\Microcron\CronExpression::__construct
     * @covers AppserverIo\Microcron\CronExpression::getExpression
     *
     * @dataProvider scheduleWithDifferentSeparatorsProvider
     */
    public function testParsesCronScheduleWithAnySpaceCharsAsSeparators($schedule, array $expected)
    {
        $cron = CronExpression::factory($schedule);
        $this->assertEquals($expected[0], $cron->getExpression(CronExpression::SECOND));
        $this->assertEquals($expected[1], $cron->getExpression(CronExpression::MINUTE));
        $this->assertEquals($expected[2], $cron->getExpression(CronExpression::HOUR));
        $this->assertEquals($expected[3], $cron->getExpression(CronExpression::DAY));
        $this->assertEquals($expected[4], $cron->getExpression(CronExpression::MONTH));
        $this->assertEquals($expected[5], $cron->getExpression(CronExpression::WEEKDAY));
        $this->assertEquals($expected[6], $cron->getExpression(CronExpression::YEAR));
    }

    /**
     * Data provider for testParsesCronScheduleWithAnySpaceCharsAsSeparators
     *
     * @return array
     */
    public static function scheduleWithDifferentSeparatorsProvider()
    {
        return array(
            array("*\t*\t*\t*\t*\t*\t*", array('*', '*', '*', '*', '*', '*', '*')),
            array("*  *  *  *  *  *  *", array('*', '*', '*', '*', '*', '*', '*')),
            array("* \t * \t * \t * \t * \t * \t *", array('*', '*', '*', '*', '*', '*', '*')),
            array("*\t \t*\t \t*\t \t*\t \t*\t \t*\t \t*", array('*', '*', '*', '*', '*', '*', '*')),
        );
    }

    /**
     * Tests if impossible cron expressions will get objected
     *
     * @return null
     *
     * @covers AppserverIo\Microcron\CronExpression::__construct
     * @covers AppserverIo\Microcron\CronExpression::setExpression
     * @covers AppserverIo\Microcron\CronExpression::setPart
     *
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidCronsWillFail()
    {
        // only five digits
        CronExpression::factory('* * * * 1');
        // seven digits
        CronExpression::factory('* * * * * * *');
    }

    /**
     * Tests if the later addition of invalid parts gets objected
     *
     * @return null
     *
     * @covers AppserverIo\Microcron\CronExpression::setPart
     *
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidPartsWillFail()
    {
        $cron = CronExpression::factory('* * * * * *');
        $cron->setPart(1, 'abc');
    }

    /**
     * Data provider for cron schedule
     *
     * @return array
     */
    public function scheduleProvider()
    {
        return array(
            array('0 */2 */2 * * *', '2015-08-10 21:47:27', '2015-08-10 22:00:00', false),
            array('* * * * * *', '2015-08-10 21:50:37', '2015-08-10 21:50:37', true),
            array('* * * * * *', '2015-08-10 21:50:38', '2015-08-10 21:50:38', true),
            array('0 * 20,21,22 * * *', '2015-08-10 21:50:00', '2015-08-10 21:50:00', true),
            // Handles CSV values
            array('0 * 20,22 * * *', '2015-08-10 21:50:00', '2015-08-10 22:00:00', false),
            // CSV values can be complex
            array('0 * 5,21-22 * * *', '2015-08-10 21:50:00', '2015-08-10 21:50:00', true),
            array('0 7-9 * */9 * *', '2015-08-10 22:02:33', '2015-08-18 00:07:00', false),
            // 15th minute, of the second hour, every 15 days, in January, every Friday
            array('0 1 * * * 7', '2015-08-10 21:47:27', '2015-08-16 00:01:00', false),
            // Test with exact times
            array('0 47 21 * * *', strtotime('2015-08-10 21:47:00'), '2015-08-10 21:47:00', true),
            array('0 47 21 * * *', strtotime('2015-08-10 21:47:30'), '2015-08-11 21:47:00', false),
            array('17 47 21 * * *', strtotime('2015-08-10 21:47:30'), '2015-08-11 21:47:17', false),
            array('17 47 21 * * *', strtotime('2015-08-10 21:47:00'), '2015-08-10 21:47:17', false),
            array('17 47 21 * * *', strtotime('2015-08-10 21:47:17'), '2015-08-10 21:47:17', true),
            // Test Day of the week (issue #1)
            // According cron implementation, 0|7 = sunday, 1 => monday, etc
            array('0 * * * * 0', strtotime('2011-06-15 23:09:00'), '2011-06-19 00:00:00', false),
            array('0 * * * * 7', strtotime('2011-06-15 23:09:00'), '2011-06-19 00:00:00', false),
            array('0 * * * * 1', strtotime('2011-06-15 23:09:00'), '2011-06-20 00:00:00', false),
            // Should return the sunday date as 7 equals 0
            array('0 0 0 * * MON,SUN', strtotime('2011-06-15 23:09:00'), '2011-06-19 00:00:00', false),
            array('0 0 0 * * 1,7', strtotime('2011-06-15 23:09:00'), '2011-06-19 00:00:00', false),
            array('0 0 0 * * 0-4', strtotime('2011-06-15 23:09:00'), '2011-06-16 00:00:00', false),
            array('0 0 0 * * 7-4', strtotime('2011-06-15 23:09:00'), '2011-06-16 00:00:00', false),
            array('0 0 0 * * 4-7', strtotime('2011-06-15 23:09:00'), '2011-06-16 00:00:00', false),
            array('0 0 0 * * 7-3', strtotime('2011-06-15 23:09:00'), '2011-06-19 00:00:00', false),
            array('0 0 0 * * 3-7', strtotime('2011-06-15 23:09:00'), '2011-06-16 00:00:00', false),
            array('0 0 0 * * 3-7', strtotime('2011-06-18 23:09:00'), '2011-06-19 00:00:00', false),
            // Test lists of values and ranges (Abhoryo)
            array('0 0 0 * * 2-7', strtotime('2011-06-20 23:09:00'), '2011-06-21 00:00:00', false),
            array('0 0 0 * * 0,2-6', strtotime('2011-06-20 23:09:00'), '2011-06-21 00:00:00', false),
            array('0 0 0 * * 2-7', strtotime('2011-06-18 23:09:00'), '2011-06-19 00:00:00', false),
            array('0 0 0 * * 4-7', strtotime('2011-07-19 00:00:00'), '2011-07-21 00:00:00', false),
            // Test increments of ranges
            array('0 0-12/4 * * * *', strtotime('2011-06-20 12:04:00'), '2011-06-20 12:04:00', true),
            array('0 4-59/2 * * * *', strtotime('2011-06-20 12:04:00'), '2011-06-20 12:04:00', true),
            array('0 4-59/2 * * * *', strtotime('2011-06-20 12:06:00'), '2011-06-20 12:06:00', true),
            array('0 4-59/3 * * * *', strtotime('2011-06-20 12:06:00'), '2011-06-20 12:07:00', false),
            //array('0 0 * * 0,2-6', strtotime('2011-06-20 23:09:00'), '2011-06-21 00:00:00', false),
            // Test Day of the Week and the Day of the Month (issue #1)
            array('0 0 0 1 1 0', strtotime('2011-06-15 23:09:00'), '2012-01-01 00:00:00', false),
            array('0 0 0 1 JAN 0', strtotime('2011-06-15 23:09:00'), '2012-01-01 00:00:00', false),
            array('0 0 0 1 * 0', strtotime('2011-06-15 23:09:00'), '2012-01-01 00:00:00', false),
            array('0 0 0 L * *', strtotime('2011-07-15 00:00:00'), '2011-07-31 00:00:00', false),
            // Test the W day of the week modifier for day of the month field
            array('0 0 0 2W * *', strtotime('2011-07-01 00:00:00'), '2011-07-01 00:00:00', true),
            array('0 0 0 1W * *', strtotime('2011-05-01 00:00:00'), '2011-05-02 00:00:00', false),
            array('0 0 0 1W * *', strtotime('2011-07-01 00:00:00'), '2011-07-01 00:00:00', true),
            array('0 0 0 3W * *', strtotime('2011-07-01 00:00:00'), '2011-07-04 00:00:00', false),
            array('0 0 0 16W * *', strtotime('2011-07-01 00:00:00'), '2011-07-15 00:00:00', false),
            array('0 0 0 28W * *', strtotime('2011-07-01 00:00:00'), '2011-07-28 00:00:00', false),
            array('0 0 0 30W * *', strtotime('2011-07-01 00:00:00'), '2011-07-29 00:00:00', false),
            array('0 0 0 31W * *', strtotime('2011-07-01 00:00:00'), '2011-07-29 00:00:00', false),
            // Test the year field
            array('* * * * * * 2012', strtotime('2011-05-01 00:00:00'), '2012-01-01 00:00:00', false),
            // Test the last weekday of a month
            array('0 * * * * 5L', strtotime('2011-07-01 00:00:00'), '2011-07-29 00:00:00', false),
            array('0 * * * * 6L', strtotime('2011-07-01 00:00:00'), '2011-07-30 00:00:00', false),
            array('0 * * * * 7L', strtotime('2011-07-01 00:00:00'), '2011-07-31 00:00:00', false),
            array('0 * * * * 1L', strtotime('2011-07-24 00:00:00'), '2011-07-25 00:00:00', false),
            array('0 * * * * TUEL', strtotime('2011-07-24 00:00:00'), '2011-07-26 00:00:00', false),
            array('0 * * * 1 5L', strtotime('2011-12-25 00:00:00'), '2012-01-27 00:00:00', false),
            // Test the hash symbol for the nth weekday of a given month
            array('0 * * * * 5#2', strtotime('2011-07-01 00:00:00'), '2011-07-08 00:00:00', false),
            array('0 * * * * 5#1', strtotime('2011-07-01 00:00:00'), '2011-07-01 00:00:00', true),
            array('0 * * * * 3#4', strtotime('2011-07-01 00:00:00'), '2011-07-27 00:00:00', false),
        );
    }

    /**
     * Tests if certain cron expressions will correctly state that they are due (or not) when checked against
     * given dates.
     * Uses a data provider
     *
     * @param string    $schedule     The cron expression to use
     * @param \DateTime $relativeTime The current time assumed during testing
     * @param string    $nextRun      The expected next run time
     * @param boolean   $isDue        If the expression is due based on the given times
     *
     * @return null
     *
     * @covers AppserverIo\Microcron\CronExpression::isDue
     * @covers AppserverIo\Microcron\CronExpression::getNextRunDate
     * @covers Cron\DayOfMonthField
     * @covers Cron\DayOfWeekField
     * @covers Cron\MinutesField
     * @covers Cron\HoursField
     * @covers Cron\MonthField
     * @covers Cron\YearField
     * @covers AppserverIo\Microcron\SecondsField
     * @covers AppserverIo\Microcron\CronExpression::getRunDate
     *
     * @dataProvider scheduleProvider
     */
    public function testDeterminesIfCronIsDue($schedule, $relativeTime, $nextRun, $isDue)
    {
        $relativeTimeString = is_int($relativeTime) ? date('Y-m-d H:i:s', $relativeTime) : $relativeTime;

        // Test next run date
        $cron = CronExpression::factory($schedule);
        if (is_string($relativeTime)) {
            $relativeTime = new \DateTime($relativeTime);
        } elseif (is_int($relativeTime)) {
            $relativeTime = date('Y-m-d H:i:s', $relativeTime);
        }
        $this->assertEquals($isDue, $cron->isDue($relativeTime));
        $next = $cron->getNextRunDate($relativeTime, 0, true);
        $this->assertEquals(new \DateTime($nextRun), $next);
    }

    /**
     * Tests if the isDue() method can handle different input formats
     *
     * @return null
     *
     * @covers AppserverIo\Microcron\CronExpression::isDue
     */
    public function testIsDueHandlesDifferentDates()
    {
        $cron = CronExpression::factory('* * * * * *');
        $this->assertTrue($cron->isDue());
        $this->assertTrue($cron->isDue('now'));
        $this->assertTrue($cron->isDue(new \DateTime('now')));
        $this->assertTrue($cron->isDue(date('Y-m-d H:i:s')));
    }

    /**
     * Tests if it is possible to correctly obtain the previous run date
     *
     * @return null
     *
     * @covers AppserverIo\Microcron\CronExpression::getPreviousRunDate
     */
    public function testCanGetPreviousRunDates()
    {
        $cron = CronExpression::factory('* * * * * *');
        $next = $cron->getNextRunDate('now');
        $two = $cron->getNextRunDate('now', 1);
        $this->assertEquals($next, $cron->getPreviousRunDate($two));

        $cron = CronExpression::factory('* * */2 * * *');
        $next = $cron->getNextRunDate('now');
        $two = $cron->getNextRunDate('now', 1);
        $this->assertEquals($next, $cron->getPreviousRunDate($two));

        $cron = CronExpression::factory('* * * * */2 *');
        $next = $cron->getNextRunDate('now');
        $two = $cron->getNextRunDate('now', 1);
        $this->assertEquals($next, $cron->getPreviousRunDate($two));
    }

    /**
     * Tests if run dates can be projected into the future correctly
     *
     * @return null
     *
     * @covers AppserverIo\Microcron\CronExpression::getMultipleRunDates
     */
    public function testProvidesMultipleRunDates()
    {
        $cron = CronExpression::factory('0 */2 * * * *');
        $this->assertEquals(array(
                new \DateTime('2008-11-09 00:00:00'),
                new \DateTime('2008-11-09 00:02:00'),
                new \DateTime('2008-11-09 00:04:00'),
                new \DateTime('2008-11-09 00:06:00')
            ), $cron->getMultipleRunDates(4, '2008-11-09 00:00:00', false, true));
        $cron = CronExpression::factory('*/2 * * * * *');
        $this->assertEquals(array(
                new \DateTime('2008-11-09 00:00:00'),
                new \DateTime('2008-11-09 00:00:02'),
                new \DateTime('2008-11-09 00:00:04'),
                new \DateTime('2008-11-09 00:00:06')
            ), $cron->getMultipleRunDates(4, '2008-11-09 00:00:00', false, true));
    }

    /**
     * Tests if iterating next run dates works as expected
     *
     * @return null
     *
     * @covers AppserverIo\Microcron\CronExpression
     */
    public function testCanIterateOverNextRuns()
    {
        $cron = CronExpression::factory('@weekly');
        $nextRun = $cron->getNextRunDate("2008-11-09 08:00:00");
        $this->assertEquals($nextRun, new \DateTime("2008-11-16 00:00:00"));

        // true is cast to 1
        $nextRun = $cron->getNextRunDate("2008-11-09 00:00:00", true, true);
        $this->assertEquals($nextRun, new \DateTime("2008-11-16 00:00:00"));

        // You can iterate over them
        $nextRun = $cron->getNextRunDate($cron->getNextRunDate("2008-11-09 00:00:00", 1, true), 1, true);
        $this->assertEquals($nextRun, new \DateTime("2008-11-23 00:00:00"));

        // You can skip more than one
        $nextRun = $cron->getNextRunDate("2008-11-09 00:00:00", 2, true);
        $this->assertEquals($nextRun, new \DateTime("2008-11-23 00:00:00"));
        $nextRun = $cron->getNextRunDate("2008-11-09 00:00:00", 3, true);
        $this->assertEquals($nextRun, new \DateTime("2008-11-30 00:00:00"));
    }

    /**
     * Tests if the current date gets skipped
     *
     * @return null
     *
     * @covers AppserverIo\Microcron\CronExpression::getRunDate
     */
    public function testSkipsCurrentDateByDefault()
    {
        $cron = CronExpression::factory('* * * * * *');
        $current = new \DateTime('now');
        $next = $cron->getNextRunDate($current);
        $this->assertEquals($current, $cron->getPreviousRunDate($next));
    }

    /**
     * Tests if seconds do not get stripped as customary to cron
     *
     * @return null
     *
     * @covers AppserverIo\Microcron\CronExpression::getRunDate
     */
    public function testDoesNotStripForSeconds()
    {
        $cron = CronExpression::factory('* * * * * *');
        $current = new \DateTime('2011-09-27 10:10:54');
        $this->assertEquals('2011-09-27 10:10:55', $cron->getNextRunDate($current)->format('Y-m-d H:i:s'));
    }

    /**
     * Tests for the fix of a certain PHP bug
     *
     * @return null
     *
     * @covers AppserverIo\Microcron\CronExpression::getRunDate
     */
    public function testFixesPhpBugInDateIntervalMonth()
    {
        $cron = CronExpression::factory('0 0 0 27 JAN *');
        $this->assertEquals('2011-01-27 00:00:00', $cron->getPreviousRunDate('2011-08-22 00:00:00')->format('Y-m-d H:i:s'));
    }

    /**
     * Tests for known formatting problems
     *
     * @return null
     */
    public function testIssue29()
    {
        $cron = CronExpression::factory('@weekly');
        $this->assertEquals(
            '2013-03-10 00:00:00',
            $cron->getPreviousRunDate('2013-03-17 00:00:00')->format('Y-m-d H:i:s')
        );
    }
}

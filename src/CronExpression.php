<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category  Library
 * @package   Microcron
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2014 TechDivision GmbH - <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io/
 */

namespace AppserverIo\Microcron;

use Cron\CronExpression as SimpleCron;
use Cron\FieldFactory;

/**
 * AppserverIo\Microcron
 *
 * CRON expression parser that can determine whether or not a CRON expression is
 * due to run, the next run date and previous run date of a CRON expression.
 * This class is designed to be used every second
 *
 * Schedule parts must map to:
 * minute [0-59], hour [0-23], day of month, month [1-12|JAN-DEC], day of week
 * [1-7|MON-SUN], and an optional year.
 *
 * @category  Library
 * @package   Microcron
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2014 TechDivision GmbH - <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io/
 */
class CronExpression extends SimpleCron
{
    const SECOND = 0;
    const MINUTE = 1;
    const HOUR = 2;
    const DAY = 3;
    const MONTH = 4;
    const WEEKDAY = 5;
    const YEAR = 6;

    /**
     * CRON expression parts
     *
     * @var array $cronParts
     */
    protected $cronParts;

    /**
     *  CRON field factory
     *
     * @var FieldFactory $fieldFactory
     */
    protected $fieldFactory;

    /**
     * Order in which to test of cron parts
     *
     * @var array $order
     */
    protected static $order = array(self::YEAR, self::MONTH, self::DAY, self::WEEKDAY, self::HOUR, self::MINUTE, self::SECOND);

    /**
     * Parse a CRON expression
     *
     * @param string       $expression   CRON expression (e.g. '8 * * * *')
     * @param FieldFactory $fieldFactory Factory to create cron fields
     */
    public function __construct($expression, FieldFactory $fieldFactory)
    {
        $this->fieldFactory = $fieldFactory;
        $this->setExpression($expression);
    }

    /**
     * Factory method to create a new CronExpression.
     * There are  several special predefined values which can be used to substitute the
     * CRON expression. You might see them below
     *
     * @param string       $expression   The CRON expression to create
     * @param FieldFactory $fieldFactory (optional) Field factory to use
     *
     *      @yearly, @annually) - Run once a year, midnight, Jan. 1 - 0 0 0 1 1 *
     *      @monthly - Run once a month, midnight, first of month - 0 0 0 1 * *
     *      @weekly - Run once a week, midnight on Sun - 0 0 0 * * 0
     *      @daily - Run once a day, midnight - 0 0 0 * * *
     *      @hourly - Run once an hour, first minute - 0 0 * * * *
     *      @byMinute - Run once a minute, first second - 0 * * * * *
     *      @bySecond - Run once a second - * * * * * *
     *
     * @return CronExpression
     */
    public static function factory($expression, FieldFactory $fieldFactory = null)
    {
        $mappings = array(
            '@yearly' => '0 0 0 1 1 *',
            '@annually' => '0 0 0 1 1 *',
            '@monthly' => '0 0 0 1 * *',
            '@weekly' => '0 0 0 * * 0',
            '@daily' => '0 0 0 * * *',
            '@hourly' => '0 0 * * * *',
            '@byMinute' => '0 * * * * *',
            '@bySecond' => '* * * * * *'
        );

        if (isset($mappings[$expression])) {
            $expression = $mappings[$expression];
        }

        return new static($expression, $fieldFactory ?: new FieldFactory());
    }

    /**
     * Deterime if the cron is due to run based on the current date or a
     * specific date
     *
     * @param string|\DateTime $currentTime (optional) Relative calculation date
     *
     * @return bool Returns TRUE if the cron is due to run or FALSE if not
     */
    public function isDue($currentTime = null)
    {
        if (null === $currentTime || 'now' === $currentTime) {
            $currentDate = date('Y-m-d H:i:s');
            $currentTime = strtotime($currentDate);
        } elseif ($currentTime instanceof \DateTime) {
            $currentDate = $currentTime->format('Y-m-d H:i:s');
            $currentTime = strtotime($currentDate);
        } else {
            $currentTime = new \DateTime($currentTime);
            $currentTime->setTime($currentTime->format('H'), $currentTime->format('i'), $currentTime->format('s'));
            $currentDate = $currentTime->format('Y-m-d H:i:s');
            $currentTime = $currentTime->getTimeStamp();
        }

        return $this->getNextRunDate($currentDate, 0, true)->getTimestamp() == $currentTime;
    }

    /**
     * Get the next or previous run date of the expression relative to a date
     *
     * @param string|\DateTime $currentTime      (optional) Relative calculation date
     * @param int              $nth              (optional) Number of matches to skip before returning
     * @param bool             $invert           (optional) Set to TRUE to go backwards in time
     * @param bool             $allowCurrentDate (optional) Set to TRUE to return the
     *     current date if it matches the cron expression
     *
     * @return \DateTime
     * @throws \RuntimeException on too many iterations
     */
    protected function getRunDate($currentTime = null, $nth = 0, $invert = false, $allowCurrentDate = false)
    {
        if ($currentTime instanceof \DateTime) {
            $currentDate = $currentTime;
        } else {
            $currentDate = new \DateTime($currentTime ?: 'now');
            $currentDate->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        }

        $currentDate->setTime($currentDate->format('H'), $currentDate->format('i'), $currentDate->format('s'));
        $nextRun = clone $currentDate;
        $nth = (int) $nth;

        // Set a hard limit to bail on an impossible date
        for ($i = 0; $i < 1000; $i++) {

            foreach (self::$order as $position) {
                $part = $this->getExpression($position);
                if (null === $part) {
                    continue;
                }

                $satisfied = false;
                // Get the field object used to validate this part
                $field = $this->fieldFactory->getField($position);
                // Check if this is singular or a list
                if (strpos($part, ',') === false) {
                    $satisfied = $field->isSatisfiedBy($nextRun, $part);
                } else {
                    foreach (array_map('trim', explode(',', $part)) as $listPart) {
                        if ($field->isSatisfiedBy($nextRun, $listPart)) {
                            $satisfied = true;
                            break;
                        }
                    }
                }

                // If the field is not satisfied, then start over
                if (!$satisfied) {
                    $field->increment($nextRun, $invert);
                    continue 2;
                }
            }

            // Skip this match if needed
            if ((!$allowCurrentDate && $nextRun == $currentDate) || --$nth > -1) {
                $this->fieldFactory->getField(0)->increment($nextRun, $invert);
                continue;
            }

            return $nextRun;
        }

        // @codeCoverageIgnoreStart
        throw new \RuntimeException('Impossible CRON expression');
        // @codeCoverageIgnoreEnd
    }
}

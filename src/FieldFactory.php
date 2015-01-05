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
 * @author    Michael Dowling <mtdowling@gmail.com>
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2014 TechDivision GmbH - <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io/
 */

namespace AppserverIo\Microcron;

use Cron\FieldFactory as SimpleFieldFactory;
use Cron\MinutesField;
use Cron\HoursField;
use Cron\DayOfWeekField;
use Cron\DayOfMonthField;
use Cron\MonthField;
use Cron\YearField;

/**
 * AppserverIo\Microcron\FieldFactory
 *
 * Flyweight factory which allows for the creation of FieldInterface implementation objects
 *
 * @category  Library
 * @package   Microcron
 * @author    Michael Dowling <mtdowling@gmail.com>
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2014 TechDivision GmbH - <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io/
 */
class FieldFactory extends SimpleFieldFactory
{
    /**
     * @var array Cache of instantiated fields
     */
    private $fields = array();

    /**
     * Get an instance of a field object for a cron expression position
     *
     * @param int $position CRON expression position value to retrieve
     *
     * @return \Cron\FieldInterface
     * @throws \InvalidArgumentException if a position is not valid
     */
    public function getField($position)
    {
        if (!isset($this->fields[$position])) {
            switch ($position) {
                case 0:
                    $this->fields[$position] = new SecondsField();
                    break;
                case 1:
                    $this->fields[$position] = new MinutesField();
                    break;
                case 2:
                    $this->fields[$position] = new HoursField();
                    break;
                case 3:
                    $this->fields[$position] = new DayOfMonthField();
                    break;
                case 4:
                    $this->fields[$position] = new MonthField();
                    break;
                case 5:
                    $this->fields[$position] = new DayOfWeekField();
                    break;
                case 6:
                    $this->fields[$position] = new YearField();
                    break;
                default:
                    throw new \InvalidArgumentException(
                        $position . ' is not a valid position'
                    );
            }
        }

        return $this->fields[$position];
    }
}

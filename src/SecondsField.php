<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * Some of this work is derived from mtdowling/cron-expression which is copyrighted as:
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

use Cron\AbstractField;
use Cron\FieldInterface;
use DateTime;

/**
 * AppserverIo\SecondsField
 *
 * Field class to enable the usage of seconds. Allows: * , / -
 *
 * @category  Library
 * @package   Microcron
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH - <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io/
 */
class SecondsField extends AbstractField
{

    /**
     * Check if the respective value of a DateTime field satisfies a CRON exp
     *
     * @param DateTime $date  DateTime object to check
     * @param string   $value CRON expression to test against
     *
     * @return bool Returns TRUE if satisfied, FALSE otherwise
     */
    public function isSatisfiedBy(DateTime $date, $value)
    {
        return $this->isSatisfied($date->format('s'), $value);
    }

    /**
     * When a CRON expression is not satisfied, this method is used to increment
     * or decrement a DateTime object by the unit of the cron field
     *
     * @param DateTime $date   DateTime object to change
     * @param bool     $invert (optional) Set to TRUE to decrement
     *
     * @return FieldInterface
     */
    public function increment(DateTime $date, $invert = false)
    {
        if ($invert) {
            $date->modify('-1 second');
        } else {
            $date->modify('+1 second');
        }

        return $this;
    }

    /**
     * Validates a CRON expression for a given field
     *
     * @param string $value CRON expression value to validate
     *
     * @return bool Returns TRUE if valid, FALSE otherwise
     */
    public function validate($value)
    {
        return (bool) preg_match('/[\*,\/\-0-9]+/', $value);
    }
}

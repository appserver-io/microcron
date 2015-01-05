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
 * AppserverIo\Microcron\FieldFactoryTest
 *
 * Tests for the AppserverIo\Microcron\FieldFactory class
 *
 * @category  Library
 * @package   Microcron
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH - <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io/
 */
class FieldFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Will test if the field factory reacts correctly on the possible positions
     *
     * @return null
     *
     * @covers AppserverIo\Microcron\FieldFactory::getField
     */
    public function testRetrievesFieldInstances()
    {
        $mappings = array(
            0 => 'AppserverIo\Microcron\SecondsField',
            1 => 'Cron\MinutesField',
            2 => 'Cron\HoursField',
            3 => 'Cron\DayOfMonthField',
            4 => 'Cron\MonthField',
            5 => 'Cron\DayOfWeekField',
            6 => 'Cron\YearField'
        );

        $f = new FieldFactory();

        foreach ($mappings as $position => $class) {
            $this->assertEquals($class, get_class($f->getField($position)));
        }
    }

    /**
     * Will test if the field factory reacts on wrong positions properly
     *
     * @return null
     *
     * @covers AppserverIo\Microcron\FieldFactory::getField
     *
     * @expectedException \InvalidArgumentException
     */
    public function testValidatesFieldPosition()
    {
        $f = new FieldFactory();
        $f->getField(-1);
    }
}

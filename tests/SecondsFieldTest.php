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
 * AppserverIo\Microcron\SecondsFieldTest
 *
 * Tests for the AppserverIo\Microcron\SecondsField class
 *
 * @category  Library
 * @package   Microcron
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH - <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io/
 */
class SecondsFieldTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test for the field validation
     *
     * @return null
     *
     * @covers AppserverIo\Microcron\SecondsField::validate
     */
    public function testValidatesField()
    {
        $f = new SecondsField();
        $this->assertTrue($f->validate('1'));
        $this->assertTrue($f->validate('*'));
        $this->assertTrue($f->validate('*/3,1,1-12'));
    }

    /**
     * Test the in- and decrement functionality of the field
     *
     * @return null
     *
     * @covers AppserverIo\Microcron\SecondsField::increment
     */
    public function testIncrementsDate()
    {
        $d = new \DateTime('2011-03-15 11:15:00');
        $f = new SecondsField();
        $f->increment($d);
        $this->assertEquals('2011-03-15 11:15:01', $d->format('Y-m-d H:i:s'));
        $f->increment($d, true);
        $this->assertEquals('2011-03-15 11:15:00', $d->format('Y-m-d H:i:s'));
        $f->increment($d);
        $f->increment($d);
        $f->increment($d);
        $this->assertEquals('2011-03-15 11:15:03', $d->format('Y-m-d H:i:s'));
    }
}

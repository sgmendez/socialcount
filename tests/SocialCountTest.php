<?php

/**
 * Copyright (c) 2015, Salvador Mendez
 * All rights reserved. 
 * 
 * This software is licensed by BSD 2-Clause License, you may obtain 
 * a copy of the license at the LICENSE file or at:
 * 
 * http://opensource.org/licenses/bsd-license.php
 * 
 * @author Salvador Mendez <salva@sgmendez.com>
 * @license http://opensource.org/licenses/bsd-license.php BSD 2-Clause License
 * @copyright (c) 2015, Salvador Mendez
 * @package sgmendez/socialcount
 * @version 1.0
 * 
 */

namespace Sgmendez\SocialCount\Tests;

require_once __DIR__ . '/../src/SocialCount.php';

use Sgmendez\SocialCount\SocialCount;
use PHPUnit_Framework_TestCase;
use InvalidArgumentException;

class SocialCountTest extends PHPUnit_Framework_TestCase
{
    const URL = 'http://www.google.es';

    protected $socialCount;
    
    protected function setUp()
    {
        $this->socialCount = new SocialCount();
    }
    
    public function testGetCountFacebook()
    {
        $this->checkValidValues('getCountFacebook');
    }
    
    public function testGetCountTwitter()
    {
        $this->checkValidValues('getCountTwitter');
    }
    
    public function testGetCountLinkedin()
    {
        $this->checkValidValues('getCountLinkedin');
    }
    
    public function testGetCountGoogle()
    {
        $this->checkValidValues('getCountGoogle');
    }
    
    public function testGetCountReddit()
    {
        $this->checkValidValues('getCountReddit');
    }
    
    public function testGetCountStumbleUpon()
    {
        $this->checkValidValues('getCountStumbleUpon');
    }
    
    public function testGetCountPinterest()
    {
        $this->checkValidValues('getCountPinterest');
    }
    
    private function checkValidValues($methodSocial)
    {
        $count = $this->socialCount->$methodSocial(self::URL);
        
        $this->assertContainsOnly('int', array($count));
        
        //Check exception for bad url        
        try
        {
            $this->socialCount->$methodSocial('url not valid');
        } 
        catch (InvalidArgumentException $ex) 
        {
            $this->assertEquals(1, $ex->getCode());
            return;
        }
        
        $this->fail('An expected InvalidArgumentException has not been thrown');
    }
}

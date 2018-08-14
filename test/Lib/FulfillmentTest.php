<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace KryuuCommon\CryptoConditionsTest\Lib;

use PHPUnit\Framework\TestCase;
use KryuuCommon\CryptoConditions\Lib\Fulfillment;

/**
 * Description of FulfillmentTest
 *
 * @author spawn
 */
class FulfillmentTest extends TestCase {
    
    /**
     * 
     */
    public function testFromUri_SucessfullyParseTheMinimalFulfillment() {
        Fulfillment::fromUri('oAKAAA');
    }
    
    public function testFromUri_SuccesfullyParsesABasicFulfillment() {
        
    }
    
    public function testFromUri_SuccessfullyParsesAFullfillmentWithBase64Padding() {
        
    }
    
    public function testFromUri_SuccessfullyParsesAFulfillmentWithRegularBase64Characters() {
        
    }
    
    /**
     * @expectedException \Exception
     */
    public function testFromUri_RejectsAFulfillmentWithInvalidCharacters() {
        
        Fulfillment::fromUri('oAKAAA.'); 
    }
    
    
    /**
     * @expectedException \Exception
     */
    public function testFromUri_RejectsAFulfillmentContainingASpace1() {
        
        Fulfillment::fromUri('oAK AAA');
    }
    
    /**
     * @expectedException \Exception
     */
    public function testFromUri_RejectsAFulfillmentContainingASpace2() {
        
        Fulfillment::fromUri('oAKAAA ');
    }
    
    /**
     * @expectedException \Exception
     */
    public function testFromUri_RejectsAFulfillmentContainingASpace3() {
        
        Fulfillment::fromUri(' oAKAAA');
    }
    
}

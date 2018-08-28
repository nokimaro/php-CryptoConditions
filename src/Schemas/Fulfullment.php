<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace KryuuCommon\CryptoConditions\Schemas;

use ASN1\Type\Constructed\Sequence;
use ASN1\Type\Primitive\OctetString;
use ASN1\Type\Primitive\Integer;
use ASN1\Type\Tagged\ExplicitlyTaggedType;
use ASN1\Type\Tagged\ImplicitlyTaggedType;
use ASN1\Type\Constructed\Set;


/**
 * Description of Fulfullment
 *
 * @author spawn
 */
class Fulfullment {
    //put your code here
    
    /**
     * 
     * @return Sequence
     */    
    public function preImageFulfillment() {
        return new Sequence(
            new ImplicitlyTaggedType(0, new OctetString())
        );
    }
    
    /**
     * 
     * @return Sequence
     */
    public function prefixFulfillment() {
        return new Sequence(
            new ImplicitlyTaggedType(0, new OctetString()),
            new ImplicitlyTaggedType(1, new Integer()),
            new ExplicitlyTaggedType(2, $this->preImageFulfillment())
        );
    }
    
    /**
     * 
     * @return Sequence
     */
    public function thresholdFulfillment() {
        return new Sequence(
            new ImplicitlyTaggedType(0, new Set($this->fulfillment())),
            new ImplicitlyTaggedType(1, new Set(Condition))
                
        );
    }
    
    /**
     * 
     * @return Sequence
     */
    public function RsaSha256Fulfillment() {
        return new Sequence(
            new ImplicitlyTaggedType(0, new OctetString),
            new ImplicitlyTaggedType(1, new OctetString)
        );
    }
    
    /**
     * 
     * @return Sequence
     */
    public function Ed25519Sha256Fulfillment() {
        return new Sequence(
            new ImplicitlyTaggedType(0, new OctetString),
            new ImplicitlyTaggedType(1, new OctetString)
        );
    }
    
    public function fullfillment() {
        
    }
    
    private function choice() {
        
    }
}

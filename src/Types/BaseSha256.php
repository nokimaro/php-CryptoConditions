<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace KryuuCommon\CryptoConditions\Types;

/**
 * Description of BaseSha256
 *
 * @author spawn
 */
class BaseSha256 {

    /**
     * Calculate condition hash.
     *
     * This method is called internally by `getCondition`. It calculates the
     * condition hash by hashing the hash payload.
     *
     * @return {Buffer} Result from hashing the hash payload.
     */
    public function generateHash() {
        $hash = $crypto->createHash('sha256');
        $hash->update($this->getFingerprintContents());

        return $hash->digest();
    }

}

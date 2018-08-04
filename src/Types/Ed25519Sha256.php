<?php

/*
 * To change $this license header, choose License Headers in Project Properties.
 * To change $this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace KryuuCommon\CryptoConditions\Types;

use KryuuCommon\Buffer\Buffer;
use Exception;

/**
 * Description of Ed25519Sha256
 *
 * @author spawn
 */
class Ed25519Sha256 extends BaseSha256 {

    public function __construct() {
        parent::__construct();
        $this->publicKey = null;
        $this->signature = null;
    }

    /**
     * Set the public publicKey.
     *
     * This is the Ed25519 public key. It has to be provided as a buffer.
     *
     * @param {Buffer} publicKey Public Ed25519 publicKey
     */
    public function setPublicKey($publicKey) {
        if (!Buffer . isBuffer($publicKey)) {
            throw new TypeException('Public key must be a Buffer, was: ' + $publicKey);
        }

        if (count($publicKey) !== 32) {
            throw new Exception('Public key must be 32 bytes, was: ' + count($publicKey));
        }

        // TODO Validate public key

        $this->publicKey = $publicKey;
    }

    /**
     * Set the signature.
     *
     * Instead of using the private key to sign using the sign() method, we can
     * also generate the signature elsewhere and pass it in.
     *
     * @param {Buffer} signature 64-byte signature.
     */
    public function setSignature($signature) {
        if (!Buffer . isBuffer(signature)) {
            throw new TypeException('Signature must be a Buffer, was: ' + $signature);
        }

        if (signature . length !== 64) {
            throw new Exception('Signature must be 64 bytes, was: ' + count($signature));
        }

        $this->signature = $signature;
    }

    /**
     * Sign a message.
     *
     * This method will take a message and an Ed25519 private key and store a
     * corresponding signature in $this fulfillment.
     *
     * @param {Buffer} message Message to sign.
     * @param {String} privateKey Ed25519 private key.
     */
    public function sign($message, $privateKey) {
        if (!Buffer . isBuffer($message)) {
            throw new MissingDataException('Message must be a Buffer');
        }
        if (!Buffer . isBuffer($privateKey)) {
            throw new TypeException('Private key must be a Buffer, was: ' + $privateKey);
        }
        if (count($privateKey) !== 32) {
            throw new ('Private key must be 32 bytes, was: ' + count($privateKey));
        }

        // This would be the Ed25519ph version:
        // message = crypto.createHash('sha512')
        //   .update(message)
        //   .digest()
        // Use native library if available (~65x faster)
        if ($ed25519) {
            $keyPair = ed25519 . MakeKeypair($privateKey);
            $this->setPublicKey($keyPair->publicKey);
            $this->signature = ed25519 . Sign($message, $keyPair);
        } else {
            $keyPair = nacl . sign . keyPair . fromSeed($privateKey);
            $this->setPublicKey((new Buffer)->from($keyPair->publicKey));
            $this->signature = (new Buffer)->from($nacl->sign->detached($message, $keyPair->secretKey));
        }
    }

    public function parseJson($json) {
        $this->setPublicKey((new Buffer)->from($json->publicKey, 'base64'));
        $this->setSignature((new Buffer)->from($json->signature, 'base64'));
    }

    /**
     * Produce the contents of the condition hash.
     *
     * This function is called internally by the `getCondition` method.
     *
     * @return {Buffer} Encoded contents of fingerprint hash.
     *
     * @private
     */
    private function getFingerprintContents() {
        if (!$this->publicKey) {
            throw new MissingDataException('Requires public key');
        }

        return Asn1Ed25519FingerprintContents . encode([
                    "publicKey" => $this->publicKey
        ]);
    }

    private function getAsn1JsonPayload() {
        return [
            "publicKey" => $this->publicKey,
            "signature" => $this->signature
        ];
    }

    /**
     * Calculate the cost of fulfilling $this condition.
     *
     * The cost of the Ed25519 condition is 2^17 = 131072.
     *
     * @return {Number} Expected maximum cost to fulfill $this condition
     * @private
     */
    private function calculateCost() {
        return Ed25519Sha256 . CONSTANT_COST;
    }

    /**
     * Verify the signature of $this Ed25519 fulfillment.
     *
     * The signature of $this Ed25519 fulfillment is verified against the provided
     * message and public key.
     *
     * @param {Buffer} message Message to validate against.
     * @return {Boolean} Whether $this fulfillment is valid.
     */
    public function validate($message) {
        if (!Buffer . isBuffer(message)) {
            throw new TypeException('Message must be a Buffer');
        }

        // Use native library if available (~60x faster)
        $result = null;
        if (ed25519) {
            $result = ed25519 . Verify(message, $this->signature, $this->publicKey);
        } else {
            $result = nacl . sign . detached . verify(message, $this->signature, $this->publicKey);
        }

        if (result !== true) {
            throw new ValidationException('Invalid ed25519 signature');
        }

        return true;
    }

}

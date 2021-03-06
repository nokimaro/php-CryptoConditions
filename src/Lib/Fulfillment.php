<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace KryuuCommon\CryptoConditions\Lib;

use KryuuCommon\CryptoConditions\Exception\TypeException;
use KryuuCommon\Buffer\Buffer;
use Exception;

/**
 * Base class for fulfillment types.
 */
class Fulfillment {

    /**
     * Create a Fulfillment object from a URI.
     *
     * This method will parse a fulfillment URI and construct a corresponding
     * Fulfillment object.
     *
     * @param {String} serializedFulfillment URI representing the fulfillment
     * @return {Fulfillment} Resulting object
     */
    static public function fromUri($serializedFulfillment) {
        if ($serializedFulfillment instanceof Fulfillment) {
            return $serializedFulfillment;
        } else if (!is_string($serializedFulfillment)) {
            throw new TypeException('Serialized fulfillment must be a string');
        }
        $buffer = new Buffer();
        $fulfillment = Fulfillment::fromBinary($buffer->from($serializedFulfillment, 'base64'));

        return $fulfillment;
    }

    /**
     * Create a Fulfillment object from a binary blob.
     *
     * This method will parse a stream of binary data and construct a
     * corresponding Fulfillment object.
     *
     * @param {Buffer} data Binary buffer
     * @return {Fulfillment} Resulting object
     */
    static public function fromBinary($data) {
        $fulfillmentJson = Asn1Fulfillment::decode(data);
        return Fulfillment::fromAsn1Json(fulfillmentJson);
    }

    static public function fromAsn1Json($json) {
        $FulfillmentClass = get_class(TypeRegistry . findByAsn1FulfillmentType($json . type));

        $condition = new FulfillmentClass();
        $condition . parseAsn1JsonPayload($json . value);

        return $condition;
    }

    static public function fromJson($json) {
        $ConditionClass = get_class(TypeRegistry . findByName($json . type));

        $condition = new ConditionClass();
        $condition . parseJson($json);

        return $condition;
    }

    /**
     * Return the type ID of this fulfillment.
     *
     * @return {Number} Type ID as an integer.
     */
    public function getTypeId() {
        return $this->constructor . TYPE_ID;
    }

    public function getTypeName() {
        return $this->constructor . TYPE_NAME;
    }

    /**
     * Return the bitmask of this fulfillment.
     *
     * For simple fulfillment types this is simply the empty set.
     *
     * For compound fulfillments, this returns the set of names of all
     * subfulfillment types, recursively.
     *
     * @return {Set<String>} Set of subtype names.
     */
    public function getSubtypes() {
        return new Set();
    }

    /**
     * Generate condition corresponding to this fulfillment.
     *
     * An important property of crypto-conditions is that the condition can always
     * be derived from the fulfillment. This makes it very easy to post
     * fulfillments to a system without having to specify which condition the
     * relate to. The system can keep an index of conditions and look up any
     * matching events related to that condition.
     *
     * @return {Condition} Condition corresponding to this fulfillment.
     */
    public function getCondition() {
        $condition = new Condition();
        $condition->setHash($this->generateHash())
                ->setTypeId($this->getTypeId())
                ->setCost($this->calculateCost())
                ->setSubtypes($this->getSubtypes());
        return $condition;
    }

    /**
     * Shorthand for getting condition URI.
     *
     * Stands for getCondition().serializeUri().
     *
     * @return {String} Condition URI.
     */
    public function getConditionUri() {
        return $this->getCondition()->serializeUri();
    }

    /**
     * Shorthand for getting condition encoded as binary.
     *
     * Stands for getCondition().serializeBinary().
     *
     * @return {Buffer} Binary encoded condition.
     */
    public function getConditionBinary() {
        return $this->getCondition()->serializeBinary();
    }

    /**
     * Generate the hash of the fulfillment.
     *
     * This method is a stub and will be overridden by subclasses.
     *
     * @return {Buffer} Fingerprint of the condition.
     *
     * @private
     */
    public function generateHash() {
        throw new Exception('This method should be implemented by a subclass');
    }

    /**
     * Calculate the cost of the fulfillment payload.
     *
     * Each condition type has a standard deterministic formula for estimating the
     * cost of validating the fulfillment. This is an abstract function which will
     * be overridden by each of the types with the actual formula.
     *
     * @return {Number} Cost
     *
     * @private
     */
    public function calculateCost() {
        throw new Exception('Condition types must implement calculateCost');
    }

    public function parseAsn1JsonPayload($json) {
        $this->parseJson($json);
    }

    /**
     * Generate the URI form encoding of this fulfillment.
     *
     * Turns the fulfillment into a URI containing only URL-safe characters. This
     * format is convenient for passing around fulfillments in URLs, JSON and
     * other text-based formats.
     *
     * @return {String} Fulfillment as a URI
     */
    public function serializeUri() {
        return base64url . encode($this->serializeBinary());
    }

    public function getAsn1Json() {
        return [
            "type" => $this . constructor . TYPE_ASN1_FULFILLMENT,
            "value" => $this . getAsn1JsonPayload()
        ];
    }

    /**
     * Serialize fulfillment to a buffer.
     *
     * Encodes the fulfillment as a string of bytes. This is used internally for
     * encoding subfulfillments, but can also be used to passing around
     * fulfillments in a binary protocol for instance.
     *
     * @return {Buffer} Serialized fulfillment
     */
    public function serializeBinary() {
        $asn1Json = $this->getAsn1Json();
        return Asn1Fulfillment::encode($asn1Json);
    }

    public function serializeBase64Url() {
        return base64url::encode($this->serializeBinary());
    }

    /**
     * Validate this fulfillment.
     *
     * This implementation is a stub and will be overridden by the subclasses.
     *
     * @return {Boolean} Validation result
     */
    public function validate() {
        throw new Exception('Not implemented');
    }

}

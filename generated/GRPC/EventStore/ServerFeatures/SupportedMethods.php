<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# NO CHECKED-IN PROTOBUF GENCODE
# source: serverfeatures.proto

namespace GRPC\EventStore\ServerFeatures;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>event_store.client.server_features.SupportedMethods</code>
 */
class SupportedMethods extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string event_store_server_version = 2;</code>
     */
    protected $event_store_server_version = '';
    /**
     * Generated from protobuf field <code>repeated .event_store.client.server_features.SupportedMethod methods = 1;</code>
     */
    private $methods;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type array<\GRPC\EventStore\ServerFeatures\SupportedMethod>|\Google\Protobuf\Internal\RepeatedField $methods
     *     @type string $event_store_server_version
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Serverfeatures::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>repeated .event_store.client.server_features.SupportedMethod methods = 1;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * Generated from protobuf field <code>repeated .event_store.client.server_features.SupportedMethod methods = 1;</code>
     * @param array<\GRPC\EventStore\ServerFeatures\SupportedMethod>|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setMethods($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \GRPC\EventStore\ServerFeatures\SupportedMethod::class);
        $this->methods = $arr;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string event_store_server_version = 2;</code>
     * @return string
     */
    public function getEventStoreServerVersion()
    {
        return $this->event_store_server_version;
    }

    /**
     * Generated from protobuf field <code>string event_store_server_version = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setEventStoreServerVersion($var)
    {
        GPBUtil::checkString($var, True);
        $this->event_store_server_version = $var;

        return $this;
    }

}


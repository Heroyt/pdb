<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# NO CHECKED-IN PROTOBUF GENCODE
# source: ClientMessageDtos.proto

namespace GRPC\EventStore\Client;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>EventStore.Client.Messages.ScavengeDatabaseResponse</code>
 */
class ScavengeDatabaseResponse extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>.EventStore.Client.Messages.ScavengeDatabaseResponse.ScavengeResult result = 1;</code>
     */
    protected $result = 0;
    /**
     * Generated from protobuf field <code>string scavengeId = 2;</code>
     */
    protected $scavengeId = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int $result
     *     @type string $scavengeId
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\ClientMessageDtos::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>.EventStore.Client.Messages.ScavengeDatabaseResponse.ScavengeResult result = 1;</code>
     * @return int
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Generated from protobuf field <code>.EventStore.Client.Messages.ScavengeDatabaseResponse.ScavengeResult result = 1;</code>
     * @param int $var
     * @return $this
     */
    public function setResult($var)
    {
        GPBUtil::checkEnum($var, \GRPC\EventStore\Client\ScavengeDatabaseResponse\ScavengeResult::class);
        $this->result = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string scavengeId = 2;</code>
     * @return string
     */
    public function getScavengeId()
    {
        return $this->scavengeId;
    }

    /**
     * Generated from protobuf field <code>string scavengeId = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setScavengeId($var)
    {
        GPBUtil::checkString($var, True);
        $this->scavengeId = $var;

        return $this;
    }

}


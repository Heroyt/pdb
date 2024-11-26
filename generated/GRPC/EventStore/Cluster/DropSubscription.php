<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# NO CHECKED-IN PROTOBUF GENCODE
# source: cluster.proto

namespace GRPC\EventStore\Cluster;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>event_store.cluster.DropSubscription</code>
 */
class DropSubscription extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>bytes leader_id = 1;</code>
     */
    protected $leader_id = '';
    /**
     * Generated from protobuf field <code>bytes subscription_id = 2;</code>
     */
    protected $subscription_id = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $leader_id
     *     @type string $subscription_id
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Cluster::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>bytes leader_id = 1;</code>
     * @return string
     */
    public function getLeaderId()
    {
        return $this->leader_id;
    }

    /**
     * Generated from protobuf field <code>bytes leader_id = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setLeaderId($var)
    {
        GPBUtil::checkString($var, False);
        $this->leader_id = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>bytes subscription_id = 2;</code>
     * @return string
     */
    public function getSubscriptionId()
    {
        return $this->subscription_id;
    }

    /**
     * Generated from protobuf field <code>bytes subscription_id = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setSubscriptionId($var)
    {
        GPBUtil::checkString($var, False);
        $this->subscription_id = $var;

        return $this;
    }

}


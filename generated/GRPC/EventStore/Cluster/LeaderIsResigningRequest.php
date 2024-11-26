<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# NO CHECKED-IN PROTOBUF GENCODE
# source: cluster.proto

namespace GRPC\EventStore\Cluster;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>event_store.cluster.LeaderIsResigningRequest</code>
 */
class LeaderIsResigningRequest extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>.event_store.client.UUID leader_id = 1;</code>
     */
    protected $leader_id = null;
    /**
     * Generated from protobuf field <code>.event_store.cluster.EndPoint leader_http = 2;</code>
     */
    protected $leader_http = null;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type \GRPC\EventStore\Shared\UUID $leader_id
     *     @type \GRPC\EventStore\Cluster\EndPoint $leader_http
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Cluster::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>.event_store.client.UUID leader_id = 1;</code>
     * @return \GRPC\EventStore\Shared\UUID|null
     */
    public function getLeaderId()
    {
        return $this->leader_id;
    }

    /**
     * Generated from protobuf field <code>.event_store.client.UUID leader_id = 1;</code>
     * @param \GRPC\EventStore\Shared\UUID $var
     * @return $this
     */
    public function setLeaderId($var)
    {
        GPBUtil::checkMessage($var, \GRPC\EventStore\Shared\UUID::class);
        $this->leader_id = $var;

        return $this;
    }

    public function hasLeaderId()
    {
        return isset($this->leader_id);
    }

    public function clearLeaderId()
    {
        unset($this->leader_id);
    }

    /**
     * Generated from protobuf field <code>.event_store.cluster.EndPoint leader_http = 2;</code>
     * @return \GRPC\EventStore\Cluster\EndPoint|null
     */
    public function getLeaderHttp()
    {
        return $this->leader_http;
    }

    /**
     * Generated from protobuf field <code>.event_store.cluster.EndPoint leader_http = 2;</code>
     * @param \GRPC\EventStore\Cluster\EndPoint $var
     * @return $this
     */
    public function setLeaderHttp($var)
    {
        GPBUtil::checkMessage($var, \GRPC\EventStore\Cluster\EndPoint::class);
        $this->leader_http = $var;

        return $this;
    }

    public function hasLeaderHttp()
    {
        return isset($this->leader_http);
    }

    public function clearLeaderHttp()
    {
        unset($this->leader_http);
    }

}


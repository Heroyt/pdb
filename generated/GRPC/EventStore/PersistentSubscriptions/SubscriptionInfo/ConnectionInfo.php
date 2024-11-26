<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# NO CHECKED-IN PROTOBUF GENCODE
# source: persistent.proto

namespace GRPC\EventStore\PersistentSubscriptions\SubscriptionInfo;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>event_store.client.persistent_subscriptions.SubscriptionInfo.ConnectionInfo</code>
 */
class ConnectionInfo extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string from = 1;</code>
     */
    protected $from = '';
    /**
     * Generated from protobuf field <code>string username = 2;</code>
     */
    protected $username = '';
    /**
     * Generated from protobuf field <code>int32 average_items_per_second = 3;</code>
     */
    protected $average_items_per_second = 0;
    /**
     * Generated from protobuf field <code>int64 total_items = 4;</code>
     */
    protected $total_items = 0;
    /**
     * Generated from protobuf field <code>int64 count_since_last_measurement = 5;</code>
     */
    protected $count_since_last_measurement = 0;
    /**
     * Generated from protobuf field <code>int32 available_slots = 7;</code>
     */
    protected $available_slots = 0;
    /**
     * Generated from protobuf field <code>int32 in_flight_messages = 8;</code>
     */
    protected $in_flight_messages = 0;
    /**
     * Generated from protobuf field <code>string connection_name = 9;</code>
     */
    protected $connection_name = '';
    /**
     * Generated from protobuf field <code>repeated .event_store.client.persistent_subscriptions.SubscriptionInfo.Measurement observed_measurements = 6;</code>
     */
    private $observed_measurements;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $from
     *     @type string $username
     *     @type int $average_items_per_second
     *     @type int|string $total_items
     *     @type int|string $count_since_last_measurement
     *     @type array<\GRPC\EventStore\PersistentSubscriptions\SubscriptionInfo\Measurement>|\Google\Protobuf\Internal\RepeatedField $observed_measurements
     *     @type int $available_slots
     *     @type int $in_flight_messages
     *     @type string $connection_name
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Persistent::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string from = 1;</code>
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Generated from protobuf field <code>string from = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setFrom($var)
    {
        GPBUtil::checkString($var, True);
        $this->from = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string username = 2;</code>
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Generated from protobuf field <code>string username = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setUsername($var)
    {
        GPBUtil::checkString($var, True);
        $this->username = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 average_items_per_second = 3;</code>
     * @return int
     */
    public function getAverageItemsPerSecond()
    {
        return $this->average_items_per_second;
    }

    /**
     * Generated from protobuf field <code>int32 average_items_per_second = 3;</code>
     * @param int $var
     * @return $this
     */
    public function setAverageItemsPerSecond($var)
    {
        GPBUtil::checkInt32($var);
        $this->average_items_per_second = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int64 total_items = 4;</code>
     * @return int|string
     */
    public function getTotalItems()
    {
        return $this->total_items;
    }

    /**
     * Generated from protobuf field <code>int64 total_items = 4;</code>
     * @param int|string $var
     * @return $this
     */
    public function setTotalItems($var)
    {
        GPBUtil::checkInt64($var);
        $this->total_items = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int64 count_since_last_measurement = 5;</code>
     * @return int|string
     */
    public function getCountSinceLastMeasurement()
    {
        return $this->count_since_last_measurement;
    }

    /**
     * Generated from protobuf field <code>int64 count_since_last_measurement = 5;</code>
     * @param int|string $var
     * @return $this
     */
    public function setCountSinceLastMeasurement($var)
    {
        GPBUtil::checkInt64($var);
        $this->count_since_last_measurement = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>repeated .event_store.client.persistent_subscriptions.SubscriptionInfo.Measurement observed_measurements = 6;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getObservedMeasurements()
    {
        return $this->observed_measurements;
    }

    /**
     * Generated from protobuf field <code>repeated .event_store.client.persistent_subscriptions.SubscriptionInfo.Measurement observed_measurements = 6;</code>
     * @param array<\GRPC\EventStore\PersistentSubscriptions\SubscriptionInfo\Measurement>|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setObservedMeasurements($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \GRPC\EventStore\PersistentSubscriptions\SubscriptionInfo\Measurement::class);
        $this->observed_measurements = $arr;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 available_slots = 7;</code>
     * @return int
     */
    public function getAvailableSlots()
    {
        return $this->available_slots;
    }

    /**
     * Generated from protobuf field <code>int32 available_slots = 7;</code>
     * @param int $var
     * @return $this
     */
    public function setAvailableSlots($var)
    {
        GPBUtil::checkInt32($var);
        $this->available_slots = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 in_flight_messages = 8;</code>
     * @return int
     */
    public function getInFlightMessages()
    {
        return $this->in_flight_messages;
    }

    /**
     * Generated from protobuf field <code>int32 in_flight_messages = 8;</code>
     * @param int $var
     * @return $this
     */
    public function setInFlightMessages($var)
    {
        GPBUtil::checkInt32($var);
        $this->in_flight_messages = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string connection_name = 9;</code>
     * @return string
     */
    public function getConnectionName()
    {
        return $this->connection_name;
    }

    /**
     * Generated from protobuf field <code>string connection_name = 9;</code>
     * @param string $var
     * @return $this
     */
    public function setConnectionName($var)
    {
        GPBUtil::checkString($var, True);
        $this->connection_name = $var;

        return $this;
    }

}


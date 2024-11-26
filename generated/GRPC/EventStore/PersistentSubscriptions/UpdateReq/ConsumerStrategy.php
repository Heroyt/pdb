<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# NO CHECKED-IN PROTOBUF GENCODE
# source: persistent.proto

namespace GRPC\EventStore\PersistentSubscriptions\UpdateReq;

use UnexpectedValueException;

/**
 * Protobuf type <code>event_store.client.persistent_subscriptions.UpdateReq.ConsumerStrategy</code>
 */
class ConsumerStrategy
{
    /**
     * Generated from protobuf enum <code>DispatchToSingle = 0;</code>
     */
    const DispatchToSingle = 0;
    /**
     * Generated from protobuf enum <code>RoundRobin = 1;</code>
     */
    const RoundRobin = 1;
    /**
     * Generated from protobuf enum <code>Pinned = 2;</code>
     */
    const Pinned = 2;

    private static $valueToName = [
        self::DispatchToSingle => 'DispatchToSingle',
        self::RoundRobin => 'RoundRobin',
        self::Pinned => 'Pinned',
    ];

    public static function name($value)
    {
        if (!isset(self::$valueToName[$value])) {
            throw new UnexpectedValueException(sprintf(
                    'Enum %s has no name defined for value %s', __CLASS__, $value));
        }
        return self::$valueToName[$value];
    }


    public static function value($name)
    {
        $const = __CLASS__ . '::' . strtoupper($name);
        if (!defined($const)) {
            throw new UnexpectedValueException(sprintf(
                    'Enum %s has no value defined for name %s', __CLASS__, $name));
        }
        return constant($const);
    }
}


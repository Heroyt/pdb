<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# NO CHECKED-IN PROTOBUF GENCODE
# source: streams.proto

namespace GRPC\EventStore\Streams\AppendResp;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>event_store.client.streams.AppendResp.WrongExpectedVersion</code>
 */
class WrongExpectedVersion extends \Google\Protobuf\Internal\Message
{
    protected $current_revision_option_20_6_0;
    protected $expected_revision_option_20_6_0;
    protected $current_revision_option;
    protected $expected_revision_option;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int|string $current_revision_20_6_0
     *     @type \GRPC\EventStore\Shared\PBEmpty $no_stream_20_6_0
     *     @type int|string $expected_revision_20_6_0
     *     @type \GRPC\EventStore\Shared\PBEmpty $any_20_6_0
     *     @type \GRPC\EventStore\Shared\PBEmpty $stream_exists_20_6_0
     *     @type int|string $current_revision
     *     @type \GRPC\EventStore\Shared\PBEmpty $current_no_stream
     *     @type int|string $expected_revision
     *     @type \GRPC\EventStore\Shared\PBEmpty $expected_any
     *     @type \GRPC\EventStore\Shared\PBEmpty $expected_stream_exists
     *     @type \GRPC\EventStore\Shared\PBEmpty $expected_no_stream
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Streams::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>uint64 current_revision_20_6_0 = 1;</code>
     * @return int|string
     */
    public function getCurrentRevision2060()
    {
        return $this->readOneof(1);
    }

    public function hasCurrentRevision2060()
    {
        return $this->hasOneof(1);
    }

    /**
     * Generated from protobuf field <code>uint64 current_revision_20_6_0 = 1;</code>
     * @param int|string $var
     * @return $this
     */
    public function setCurrentRevision2060($var)
    {
        GPBUtil::checkUint64($var);
        $this->writeOneof(1, $var);

        return $this;
    }

    /**
     * Generated from protobuf field <code>.event_store.client.Empty no_stream_20_6_0 = 2;</code>
     * @return \GRPC\EventStore\Shared\PBEmpty|null
     */
    public function getNoStream2060()
    {
        return $this->readOneof(2);
    }

    public function hasNoStream2060()
    {
        return $this->hasOneof(2);
    }

    /**
     * Generated from protobuf field <code>.event_store.client.Empty no_stream_20_6_0 = 2;</code>
     * @param \GRPC\EventStore\Shared\PBEmpty $var
     * @return $this
     */
    public function setNoStream2060($var)
    {
        GPBUtil::checkMessage($var, \GRPC\EventStore\Shared\PBEmpty::class);
        $this->writeOneof(2, $var);

        return $this;
    }

    /**
     * Generated from protobuf field <code>uint64 expected_revision_20_6_0 = 3;</code>
     * @return int|string
     */
    public function getExpectedRevision2060()
    {
        return $this->readOneof(3);
    }

    public function hasExpectedRevision2060()
    {
        return $this->hasOneof(3);
    }

    /**
     * Generated from protobuf field <code>uint64 expected_revision_20_6_0 = 3;</code>
     * @param int|string $var
     * @return $this
     */
    public function setExpectedRevision2060($var)
    {
        GPBUtil::checkUint64($var);
        $this->writeOneof(3, $var);

        return $this;
    }

    /**
     * Generated from protobuf field <code>.event_store.client.Empty any_20_6_0 = 4;</code>
     * @return \GRPC\EventStore\Shared\PBEmpty|null
     */
    public function getAny2060()
    {
        return $this->readOneof(4);
    }

    public function hasAny2060()
    {
        return $this->hasOneof(4);
    }

    /**
     * Generated from protobuf field <code>.event_store.client.Empty any_20_6_0 = 4;</code>
     * @param \GRPC\EventStore\Shared\PBEmpty $var
     * @return $this
     */
    public function setAny2060($var)
    {
        GPBUtil::checkMessage($var, \GRPC\EventStore\Shared\PBEmpty::class);
        $this->writeOneof(4, $var);

        return $this;
    }

    /**
     * Generated from protobuf field <code>.event_store.client.Empty stream_exists_20_6_0 = 5;</code>
     * @return \GRPC\EventStore\Shared\PBEmpty|null
     */
    public function getStreamExists2060()
    {
        return $this->readOneof(5);
    }

    public function hasStreamExists2060()
    {
        return $this->hasOneof(5);
    }

    /**
     * Generated from protobuf field <code>.event_store.client.Empty stream_exists_20_6_0 = 5;</code>
     * @param \GRPC\EventStore\Shared\PBEmpty $var
     * @return $this
     */
    public function setStreamExists2060($var)
    {
        GPBUtil::checkMessage($var, \GRPC\EventStore\Shared\PBEmpty::class);
        $this->writeOneof(5, $var);

        return $this;
    }

    /**
     * Generated from protobuf field <code>uint64 current_revision = 6;</code>
     * @return int|string
     */
    public function getCurrentRevision()
    {
        return $this->readOneof(6);
    }

    public function hasCurrentRevision()
    {
        return $this->hasOneof(6);
    }

    /**
     * Generated from protobuf field <code>uint64 current_revision = 6;</code>
     * @param int|string $var
     * @return $this
     */
    public function setCurrentRevision($var)
    {
        GPBUtil::checkUint64($var);
        $this->writeOneof(6, $var);

        return $this;
    }

    /**
     * Generated from protobuf field <code>.event_store.client.Empty current_no_stream = 7;</code>
     * @return \GRPC\EventStore\Shared\PBEmpty|null
     */
    public function getCurrentNoStream()
    {
        return $this->readOneof(7);
    }

    public function hasCurrentNoStream()
    {
        return $this->hasOneof(7);
    }

    /**
     * Generated from protobuf field <code>.event_store.client.Empty current_no_stream = 7;</code>
     * @param \GRPC\EventStore\Shared\PBEmpty $var
     * @return $this
     */
    public function setCurrentNoStream($var)
    {
        GPBUtil::checkMessage($var, \GRPC\EventStore\Shared\PBEmpty::class);
        $this->writeOneof(7, $var);

        return $this;
    }

    /**
     * Generated from protobuf field <code>uint64 expected_revision = 8;</code>
     * @return int|string
     */
    public function getExpectedRevision()
    {
        return $this->readOneof(8);
    }

    public function hasExpectedRevision()
    {
        return $this->hasOneof(8);
    }

    /**
     * Generated from protobuf field <code>uint64 expected_revision = 8;</code>
     * @param int|string $var
     * @return $this
     */
    public function setExpectedRevision($var)
    {
        GPBUtil::checkUint64($var);
        $this->writeOneof(8, $var);

        return $this;
    }

    /**
     * Generated from protobuf field <code>.event_store.client.Empty expected_any = 9;</code>
     * @return \GRPC\EventStore\Shared\PBEmpty|null
     */
    public function getExpectedAny()
    {
        return $this->readOneof(9);
    }

    public function hasExpectedAny()
    {
        return $this->hasOneof(9);
    }

    /**
     * Generated from protobuf field <code>.event_store.client.Empty expected_any = 9;</code>
     * @param \GRPC\EventStore\Shared\PBEmpty $var
     * @return $this
     */
    public function setExpectedAny($var)
    {
        GPBUtil::checkMessage($var, \GRPC\EventStore\Shared\PBEmpty::class);
        $this->writeOneof(9, $var);

        return $this;
    }

    /**
     * Generated from protobuf field <code>.event_store.client.Empty expected_stream_exists = 10;</code>
     * @return \GRPC\EventStore\Shared\PBEmpty|null
     */
    public function getExpectedStreamExists()
    {
        return $this->readOneof(10);
    }

    public function hasExpectedStreamExists()
    {
        return $this->hasOneof(10);
    }

    /**
     * Generated from protobuf field <code>.event_store.client.Empty expected_stream_exists = 10;</code>
     * @param \GRPC\EventStore\Shared\PBEmpty $var
     * @return $this
     */
    public function setExpectedStreamExists($var)
    {
        GPBUtil::checkMessage($var, \GRPC\EventStore\Shared\PBEmpty::class);
        $this->writeOneof(10, $var);

        return $this;
    }

    /**
     * Generated from protobuf field <code>.event_store.client.Empty expected_no_stream = 11;</code>
     * @return \GRPC\EventStore\Shared\PBEmpty|null
     */
    public function getExpectedNoStream()
    {
        return $this->readOneof(11);
    }

    public function hasExpectedNoStream()
    {
        return $this->hasOneof(11);
    }

    /**
     * Generated from protobuf field <code>.event_store.client.Empty expected_no_stream = 11;</code>
     * @param \GRPC\EventStore\Shared\PBEmpty $var
     * @return $this
     */
    public function setExpectedNoStream($var)
    {
        GPBUtil::checkMessage($var, \GRPC\EventStore\Shared\PBEmpty::class);
        $this->writeOneof(11, $var);

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrentRevisionOption2060()
    {
        return $this->whichOneof("current_revision_option_20_6_0");
    }

    /**
     * @return string
     */
    public function getExpectedRevisionOption2060()
    {
        return $this->whichOneof("expected_revision_option_20_6_0");
    }

    /**
     * @return string
     */
    public function getCurrentRevisionOption()
    {
        return $this->whichOneof("current_revision_option");
    }

    /**
     * @return string
     */
    public function getExpectedRevisionOption()
    {
        return $this->whichOneof("expected_revision_option");
    }

}


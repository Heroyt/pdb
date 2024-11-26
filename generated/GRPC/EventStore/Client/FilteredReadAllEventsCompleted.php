<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# NO CHECKED-IN PROTOBUF GENCODE
# source: ClientMessageDtos.proto

namespace GRPC\EventStore\Client;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>EventStore.Client.Messages.FilteredReadAllEventsCompleted</code>
 */
class FilteredReadAllEventsCompleted extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>int64 commit_position = 1;</code>
     */
    protected $commit_position = 0;
    /**
     * Generated from protobuf field <code>int64 prepare_position = 2;</code>
     */
    protected $prepare_position = 0;
    /**
     * Generated from protobuf field <code>int64 next_commit_position = 4;</code>
     */
    protected $next_commit_position = 0;
    /**
     * Generated from protobuf field <code>int64 next_prepare_position = 5;</code>
     */
    protected $next_prepare_position = 0;
    /**
     * Generated from protobuf field <code>bool is_end_of_stream = 6;</code>
     */
    protected $is_end_of_stream = false;
    /**
     * Generated from protobuf field <code>.EventStore.Client.Messages.FilteredReadAllEventsCompleted.FilteredReadAllResult result = 7;</code>
     */
    protected $result = 0;
    /**
     * Generated from protobuf field <code>string error = 8;</code>
     */
    protected $error = '';
    /**
     * Generated from protobuf field <code>repeated .EventStore.Client.Messages.ResolvedEvent events = 3;</code>
     */
    private $events;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int|string $commit_position
     *     @type int|string $prepare_position
     *     @type array<\GRPC\EventStore\Client\ResolvedEvent>|\Google\Protobuf\Internal\RepeatedField $events
     *     @type int|string $next_commit_position
     *     @type int|string $next_prepare_position
     *     @type bool $is_end_of_stream
     *     @type int $result
     *     @type string $error
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\ClientMessageDtos::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>int64 commit_position = 1;</code>
     * @return int|string
     */
    public function getCommitPosition()
    {
        return $this->commit_position;
    }

    /**
     * Generated from protobuf field <code>int64 commit_position = 1;</code>
     * @param int|string $var
     * @return $this
     */
    public function setCommitPosition($var)
    {
        GPBUtil::checkInt64($var);
        $this->commit_position = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int64 prepare_position = 2;</code>
     * @return int|string
     */
    public function getPreparePosition()
    {
        return $this->prepare_position;
    }

    /**
     * Generated from protobuf field <code>int64 prepare_position = 2;</code>
     * @param int|string $var
     * @return $this
     */
    public function setPreparePosition($var)
    {
        GPBUtil::checkInt64($var);
        $this->prepare_position = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>repeated .EventStore.Client.Messages.ResolvedEvent events = 3;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Generated from protobuf field <code>repeated .EventStore.Client.Messages.ResolvedEvent events = 3;</code>
     * @param array<\GRPC\EventStore\Client\ResolvedEvent>|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setEvents($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \GRPC\EventStore\Client\ResolvedEvent::class);
        $this->events = $arr;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int64 next_commit_position = 4;</code>
     * @return int|string
     */
    public function getNextCommitPosition()
    {
        return $this->next_commit_position;
    }

    /**
     * Generated from protobuf field <code>int64 next_commit_position = 4;</code>
     * @param int|string $var
     * @return $this
     */
    public function setNextCommitPosition($var)
    {
        GPBUtil::checkInt64($var);
        $this->next_commit_position = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int64 next_prepare_position = 5;</code>
     * @return int|string
     */
    public function getNextPreparePosition()
    {
        return $this->next_prepare_position;
    }

    /**
     * Generated from protobuf field <code>int64 next_prepare_position = 5;</code>
     * @param int|string $var
     * @return $this
     */
    public function setNextPreparePosition($var)
    {
        GPBUtil::checkInt64($var);
        $this->next_prepare_position = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>bool is_end_of_stream = 6;</code>
     * @return bool
     */
    public function getIsEndOfStream()
    {
        return $this->is_end_of_stream;
    }

    /**
     * Generated from protobuf field <code>bool is_end_of_stream = 6;</code>
     * @param bool $var
     * @return $this
     */
    public function setIsEndOfStream($var)
    {
        GPBUtil::checkBool($var);
        $this->is_end_of_stream = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>.EventStore.Client.Messages.FilteredReadAllEventsCompleted.FilteredReadAllResult result = 7;</code>
     * @return int
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Generated from protobuf field <code>.EventStore.Client.Messages.FilteredReadAllEventsCompleted.FilteredReadAllResult result = 7;</code>
     * @param int $var
     * @return $this
     */
    public function setResult($var)
    {
        GPBUtil::checkEnum($var, \GRPC\EventStore\Client\FilteredReadAllEventsCompleted\FilteredReadAllResult::class);
        $this->result = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string error = 8;</code>
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Generated from protobuf field <code>string error = 8;</code>
     * @param string $var
     * @return $this
     */
    public function setError($var)
    {
        GPBUtil::checkString($var, True);
        $this->error = $var;

        return $this;
    }

}


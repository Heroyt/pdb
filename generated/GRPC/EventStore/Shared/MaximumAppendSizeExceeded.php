<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# NO CHECKED-IN PROTOBUF GENCODE
# source: shared.proto

namespace GRPC\EventStore\Shared;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>event_store.client.MaximumAppendSizeExceeded</code>
 */
class MaximumAppendSizeExceeded extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>uint32 maxAppendSize = 1;</code>
     */
    protected $maxAppendSize = 0;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int $maxAppendSize
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Shared::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>uint32 maxAppendSize = 1;</code>
     * @return int
     */
    public function getMaxAppendSize()
    {
        return $this->maxAppendSize;
    }

    /**
     * Generated from protobuf field <code>uint32 maxAppendSize = 1;</code>
     * @param int $var
     * @return $this
     */
    public function setMaxAppendSize($var)
    {
        GPBUtil::checkUint32($var);
        $this->maxAppendSize = $var;

        return $this;
    }

}


<?php
// GENERATED CODE -- DO NOT EDIT!

namespace GRPC\EventStore\Streams;

/**
 */
class StreamsClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param \GRPC\EventStore\Streams\ReadReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\ServerStreamingCall
     */
    public function Read(\GRPC\EventStore\Streams\ReadReq $argument,
      $metadata = [], $options = []) {
        return $this->_serverStreamRequest('/event_store.client.streams.Streams/Read',
        $argument,
        ['\GRPC\EventStore\Streams\ReadResp', 'decode'],
        $metadata, $options);
    }

    /**
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\ClientStreamingCall
     */
    public function Append($metadata = [], $options = []) {
        return $this->_clientStreamRequest('/event_store.client.streams.Streams/Append',
        ['\GRPC\EventStore\Streams\AppendResp','decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\Streams\DeleteReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Delete(\GRPC\EventStore\Streams\DeleteReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.client.streams.Streams/Delete',
        $argument,
        ['\GRPC\EventStore\Streams\DeleteResp', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\Streams\TombstoneReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Tombstone(\GRPC\EventStore\Streams\TombstoneReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.client.streams.Streams/Tombstone',
        $argument,
        ['\GRPC\EventStore\Streams\TombstoneResp', 'decode'],
        $metadata, $options);
    }

    /**
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\BidiStreamingCall
     */
    public function BatchAppend($metadata = [], $options = []) {
        return $this->_bidiRequest('/event_store.client.streams.Streams/BatchAppend',
        ['\GRPC\EventStore\Streams\BatchAppendResp','decode'],
        $metadata, $options);
    }

}

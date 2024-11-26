<?php
// GENERATED CODE -- DO NOT EDIT!

namespace GRPC\EventStore\Operations;

/**
 */
class OperationsClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param \GRPC\EventStore\Operations\StartScavengeReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function StartScavenge(\GRPC\EventStore\Operations\StartScavengeReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.client.operations.Operations/StartScavenge',
        $argument,
        ['\GRPC\EventStore\Operations\ScavengeResp', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\Operations\StopScavengeReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function StopScavenge(\GRPC\EventStore\Operations\StopScavengeReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.client.operations.Operations/StopScavenge',
        $argument,
        ['\GRPC\EventStore\Operations\ScavengeResp', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\Shared\PBEmpty $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Shutdown(\GRPC\EventStore\Shared\PBEmpty $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.client.operations.Operations/Shutdown',
        $argument,
        ['\GRPC\EventStore\Shared\PBEmpty', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\Shared\PBEmpty $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function MergeIndexes(\GRPC\EventStore\Shared\PBEmpty $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.client.operations.Operations/MergeIndexes',
        $argument,
        ['\GRPC\EventStore\Shared\PBEmpty', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\Shared\PBEmpty $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function ResignNode(\GRPC\EventStore\Shared\PBEmpty $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.client.operations.Operations/ResignNode',
        $argument,
        ['\GRPC\EventStore\Shared\PBEmpty', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\Operations\SetNodePriorityReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function SetNodePriority(\GRPC\EventStore\Operations\SetNodePriorityReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.client.operations.Operations/SetNodePriority',
        $argument,
        ['\GRPC\EventStore\Shared\PBEmpty', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\Shared\PBEmpty $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function RestartPersistentSubscriptions(\GRPC\EventStore\Shared\PBEmpty $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.client.operations.Operations/RestartPersistentSubscriptions',
        $argument,
        ['\GRPC\EventStore\Shared\PBEmpty', 'decode'],
        $metadata, $options);
    }

}

<?php
// GENERATED CODE -- DO NOT EDIT!

namespace GRPC\EventStore\PersistentSubscriptions;

/**
 */
class PersistentSubscriptionsClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param \GRPC\EventStore\PersistentSubscriptions\CreateReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Create(\GRPC\EventStore\PersistentSubscriptions\CreateReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.client.persistent_subscriptions.PersistentSubscriptions/Create',
        $argument,
        ['\GRPC\EventStore\PersistentSubscriptions\CreateResp', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\PersistentSubscriptions\UpdateReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Update(\GRPC\EventStore\PersistentSubscriptions\UpdateReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.client.persistent_subscriptions.PersistentSubscriptions/Update',
        $argument,
        ['\GRPC\EventStore\PersistentSubscriptions\UpdateResp', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\PersistentSubscriptions\DeleteReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Delete(\GRPC\EventStore\PersistentSubscriptions\DeleteReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.client.persistent_subscriptions.PersistentSubscriptions/Delete',
        $argument,
        ['\GRPC\EventStore\PersistentSubscriptions\DeleteResp', 'decode'],
        $metadata, $options);
    }

    /**
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\BidiStreamingCall
     */
    public function Read($metadata = [], $options = []) {
        return $this->_bidiRequest('/event_store.client.persistent_subscriptions.PersistentSubscriptions/Read',
        ['\GRPC\EventStore\PersistentSubscriptions\ReadResp','decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\PersistentSubscriptions\GetInfoReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetInfo(\GRPC\EventStore\PersistentSubscriptions\GetInfoReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.client.persistent_subscriptions.PersistentSubscriptions/GetInfo',
        $argument,
        ['\GRPC\EventStore\PersistentSubscriptions\GetInfoResp', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\PersistentSubscriptions\ReplayParkedReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function ReplayParked(\GRPC\EventStore\PersistentSubscriptions\ReplayParkedReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.client.persistent_subscriptions.PersistentSubscriptions/ReplayParked',
        $argument,
        ['\GRPC\EventStore\PersistentSubscriptions\ReplayParkedResp', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\PersistentSubscriptions\ListReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function List(\GRPC\EventStore\PersistentSubscriptions\ListReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.client.persistent_subscriptions.PersistentSubscriptions/List',
        $argument,
        ['\GRPC\EventStore\PersistentSubscriptions\ListResp', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\Shared\PBEmpty $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function RestartSubsystem(\GRPC\EventStore\Shared\PBEmpty $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.client.persistent_subscriptions.PersistentSubscriptions/RestartSubsystem',
        $argument,
        ['\GRPC\EventStore\Shared\PBEmpty', 'decode'],
        $metadata, $options);
    }

}

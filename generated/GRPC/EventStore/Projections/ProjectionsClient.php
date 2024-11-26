<?php
// GENERATED CODE -- DO NOT EDIT!

namespace GRPC\EventStore\Projections;

/**
 */
class ProjectionsClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param \GRPC\EventStore\Projections\CreateReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Create(\GRPC\EventStore\Projections\CreateReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.client.projections.Projections/Create',
        $argument,
        ['\GRPC\EventStore\Projections\CreateResp', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\Projections\UpdateReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Update(\GRPC\EventStore\Projections\UpdateReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.client.projections.Projections/Update',
        $argument,
        ['\GRPC\EventStore\Projections\UpdateResp', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\Projections\DeleteReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Delete(\GRPC\EventStore\Projections\DeleteReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.client.projections.Projections/Delete',
        $argument,
        ['\GRPC\EventStore\Projections\DeleteResp', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\Projections\StatisticsReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\ServerStreamingCall
     */
    public function Statistics(\GRPC\EventStore\Projections\StatisticsReq $argument,
      $metadata = [], $options = []) {
        return $this->_serverStreamRequest('/event_store.client.projections.Projections/Statistics',
        $argument,
        ['\GRPC\EventStore\Projections\StatisticsResp', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\Projections\DisableReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Disable(\GRPC\EventStore\Projections\DisableReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.client.projections.Projections/Disable',
        $argument,
        ['\GRPC\EventStore\Projections\DisableResp', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\Projections\EnableReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Enable(\GRPC\EventStore\Projections\EnableReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.client.projections.Projections/Enable',
        $argument,
        ['\GRPC\EventStore\Projections\EnableResp', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\Projections\ResetReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Reset(\GRPC\EventStore\Projections\ResetReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.client.projections.Projections/Reset',
        $argument,
        ['\GRPC\EventStore\Projections\ResetResp', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\Projections\StateReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function State(\GRPC\EventStore\Projections\StateReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.client.projections.Projections/State',
        $argument,
        ['\GRPC\EventStore\Projections\StateResp', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\Projections\ResultReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Result(\GRPC\EventStore\Projections\ResultReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.client.projections.Projections/Result',
        $argument,
        ['\GRPC\EventStore\Projections\ResultResp', 'decode'],
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
        return $this->_simpleRequest('/event_store.client.projections.Projections/RestartSubsystem',
        $argument,
        ['\GRPC\EventStore\Shared\PBEmpty', 'decode'],
        $metadata, $options);
    }

}

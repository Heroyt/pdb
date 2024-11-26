<?php
// GENERATED CODE -- DO NOT EDIT!

namespace GRPC\EventStore\Cluster;

/**
 */
class ElectionsClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param \GRPC\EventStore\Cluster\ViewChangeRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function ViewChange(\GRPC\EventStore\Cluster\ViewChangeRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.cluster.Elections/ViewChange',
        $argument,
        ['\GRPC\EventStore\Shared\PBEmpty', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\Cluster\ViewChangeProofRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function ViewChangeProof(\GRPC\EventStore\Cluster\ViewChangeProofRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.cluster.Elections/ViewChangeProof',
        $argument,
        ['\GRPC\EventStore\Shared\PBEmpty', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\Cluster\PrepareRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Prepare(\GRPC\EventStore\Cluster\PrepareRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.cluster.Elections/Prepare',
        $argument,
        ['\GRPC\EventStore\Shared\PBEmpty', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\Cluster\PrepareOkRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function PrepareOk(\GRPC\EventStore\Cluster\PrepareOkRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.cluster.Elections/PrepareOk',
        $argument,
        ['\GRPC\EventStore\Shared\PBEmpty', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\Cluster\ProposalRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Proposal(\GRPC\EventStore\Cluster\ProposalRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.cluster.Elections/Proposal',
        $argument,
        ['\GRPC\EventStore\Shared\PBEmpty', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\Cluster\AcceptRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Accept(\GRPC\EventStore\Cluster\AcceptRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.cluster.Elections/Accept',
        $argument,
        ['\GRPC\EventStore\Shared\PBEmpty', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\Cluster\LeaderIsResigningRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function LeaderIsResigning(\GRPC\EventStore\Cluster\LeaderIsResigningRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.cluster.Elections/LeaderIsResigning',
        $argument,
        ['\GRPC\EventStore\Shared\PBEmpty', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\Cluster\LeaderIsResigningOkRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function LeaderIsResigningOk(\GRPC\EventStore\Cluster\LeaderIsResigningOkRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.cluster.Elections/LeaderIsResigningOk',
        $argument,
        ['\GRPC\EventStore\Shared\PBEmpty', 'decode'],
        $metadata, $options);
    }

}

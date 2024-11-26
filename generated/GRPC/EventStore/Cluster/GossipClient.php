<?php
// GENERATED CODE -- DO NOT EDIT!

namespace GRPC\EventStore\Cluster;

/**
 */
class GossipClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param \GRPC\EventStore\Cluster\GossipRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Update(\GRPC\EventStore\Cluster\GossipRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.cluster.Gossip/Update',
        $argument,
        ['\GRPC\EventStore\Cluster\ClusterInfo', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\Shared\PBEmpty $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Read(\GRPC\EventStore\Shared\PBEmpty $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.cluster.Gossip/Read',
        $argument,
        ['\GRPC\EventStore\Cluster\ClusterInfo', 'decode'],
        $metadata, $options);
    }

}

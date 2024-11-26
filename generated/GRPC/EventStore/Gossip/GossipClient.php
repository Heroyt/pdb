<?php
// GENERATED CODE -- DO NOT EDIT!

namespace GRPC\EventStore\Gossip;

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
     * @param \GRPC\EventStore\Shared\PBEmpty $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Read(\GRPC\EventStore\Shared\PBEmpty $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.client.gossip.Gossip/Read',
        $argument,
        ['\GRPC\EventStore\Gossip\ClusterInfo', 'decode'],
        $metadata, $options);
    }

}

<?php
// GENERATED CODE -- DO NOT EDIT!

namespace GRPC\EventStore\Monitoring;

/**
 */
class MonitoringClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param \GRPC\EventStore\Monitoring\StatsReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\ServerStreamingCall
     */
    public function Stats(\GRPC\EventStore\Monitoring\StatsReq $argument,
      $metadata = [], $options = []) {
        return $this->_serverStreamRequest('/event_store.client.monitoring.Monitoring/Stats',
        $argument,
        ['\GRPC\EventStore\Monitoring\StatsResp', 'decode'],
        $metadata, $options);
    }

}

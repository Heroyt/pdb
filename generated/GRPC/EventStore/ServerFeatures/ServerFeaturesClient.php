<?php
// GENERATED CODE -- DO NOT EDIT!

namespace GRPC\EventStore\ServerFeatures;

/**
 */
class ServerFeaturesClient extends \Grpc\BaseStub {

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
    public function GetSupportedMethods(\GRPC\EventStore\Shared\PBEmpty $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.client.server_features.ServerFeatures/GetSupportedMethods',
        $argument,
        ['\GRPC\EventStore\ServerFeatures\SupportedMethods', 'decode'],
        $metadata, $options);
    }

}

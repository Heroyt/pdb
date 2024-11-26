<?php
// GENERATED CODE -- DO NOT EDIT!

namespace GRPC\EventStore\Users;

/**
 */
class UsersClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param \GRPC\EventStore\Users\CreateReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Create(\GRPC\EventStore\Users\CreateReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.client.users.Users/Create',
        $argument,
        ['\GRPC\EventStore\Users\CreateResp', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\Users\UpdateReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Update(\GRPC\EventStore\Users\UpdateReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.client.users.Users/Update',
        $argument,
        ['\GRPC\EventStore\Users\UpdateResp', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\Users\DeleteReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Delete(\GRPC\EventStore\Users\DeleteReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.client.users.Users/Delete',
        $argument,
        ['\GRPC\EventStore\Users\DeleteResp', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\Users\DisableReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Disable(\GRPC\EventStore\Users\DisableReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.client.users.Users/Disable',
        $argument,
        ['\GRPC\EventStore\Users\DisableResp', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\Users\EnableReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Enable(\GRPC\EventStore\Users\EnableReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.client.users.Users/Enable',
        $argument,
        ['\GRPC\EventStore\Users\EnableResp', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\Users\DetailsReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\ServerStreamingCall
     */
    public function Details(\GRPC\EventStore\Users\DetailsReq $argument,
      $metadata = [], $options = []) {
        return $this->_serverStreamRequest('/event_store.client.users.Users/Details',
        $argument,
        ['\GRPC\EventStore\Users\DetailsResp', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\Users\ChangePasswordReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function ChangePassword(\GRPC\EventStore\Users\ChangePasswordReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.client.users.Users/ChangePassword',
        $argument,
        ['\GRPC\EventStore\Users\ChangePasswordResp', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \GRPC\EventStore\Users\ResetPasswordReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function ResetPassword(\GRPC\EventStore\Users\ResetPasswordReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/event_store.client.users.Users/ResetPassword',
        $argument,
        ['\GRPC\EventStore\Users\ResetPasswordResp', 'decode'],
        $metadata, $options);
    }

}

<?php
// GENERATED CODE -- DO NOT EDIT!

namespace GRPC\EventStore\Redaction;

/**
 * The intended usage is as follows:
 * 1. Initiate the SwitchChunks() call in order to lock the database for chunk switching.
 *    This ensures that chunks / event positions will not change while the lock is acquired.
 * 2. The GetEventPositions() call is then initiated to obtain the required event positions.
 * 3. The relevant chunks are copied to files with a .tmp extension and modified as necessary.
 * 4. SwitchChunk requests are then sent to replace the relevant chunks with the modified chunks.
 * 5. Finally, the lock is released by ending the SwitchChunks() call.
 *
 */
class RedactionClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\BidiStreamingCall
     */
    public function GetEventPositions($metadata = [], $options = []) {
        return $this->_bidiRequest('/event_store.client.redaction.Redaction/GetEventPositions',
        ['\GRPC\EventStore\Redaction\GetEventPositionResp','decode'],
        $metadata, $options);
    }

    /**
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\BidiStreamingCall
     */
    public function SwitchChunks($metadata = [], $options = []) {
        return $this->_bidiRequest('/event_store.client.redaction.Redaction/SwitchChunks',
        ['\GRPC\EventStore\Redaction\SwitchChunkResp','decode'],
        $metadata, $options);
    }

}

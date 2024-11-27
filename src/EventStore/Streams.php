<?php

declare(strict_types=1);

namespace App\EventStore;

use App\EventStore\Streams\AppendResult;
use App\EventStore\Streams\ReadQuery;
use Grpc\ChannelCredentials;
use GRPC\EventStore\Shared\PBEmpty;
use GRPC\EventStore\Shared\StreamIdentifier;
use GRPC\EventStore\Shared\UUID;
use GRPC\EventStore\Streams\AppendReq;
use GRPC\EventStore\Streams\AppendReq\ProposedMessage;
use GRPC\EventStore\Streams\AppendResp;
use GRPC\EventStore\Streams\ReadReq;
use GRPC\EventStore\Streams\StreamsClient;
use Grpc\ServerStreamingCall;
use Symfony\Component\Serializer\Serializer;

final class Streams
{
    public const string DEFAULT_STREAM = 'default';

    public StreamsClient $client {
        get {
    if (!isset($this->client)) {
        $this->client = new StreamsClient(
            $this->host,
            [
            'credentials' => ChannelCredentials::createInsecure(),
            ]
        );
    }
            return $this->client;
        }
    }

    public function __construct(
        public readonly string $host,
        public readonly Serializer $serializer,
    ) {
    }

    public function appendEvent(Event $event, string $stream = self::DEFAULT_STREAM): AppendResult {
        $streamIdentifier = new StreamIdentifier();
        $streamIdentifier->setStreamName($stream);

        [$response, $status, $uuid] = $this->appendOneEvent($event, $streamIdentifier);

        return new AppendResult(
            $uuid,
            $event::class,
            $stream,
            $response,
            $status,
        );
    }

    /**
     * @return array{0:AppendResp|null,1:Status,2:non-empty-string} Response, Status, UUID
     */
    private function appendOneEvent(Event $event, StreamIdentifier $streamIdentifier): array {
        $idString = guidV4();
        $uuid = new UUID();
        $uuid->setString($idString);

        $proposedMessage = new ProposedMessage(
            [
            'id'       => $uuid,
            'metadata' => [
              'type'         => $event::class,
              'content-type' => 'application/json',
            ],
            'data'     => $this->serializer->serialize($event, 'json'),
            ]
        );

        $options = new AppendReq\Options(
            [
            'stream_identifier' => $streamIdentifier,
            'any'               => new PBEmpty(),
            ]
        );

        $call = $this->client->Append();

        $optionsReq = new AppendReq();
        $optionsReq->setOptions($options);
        /** @phpstan-ignore argument.type */
        $call->write($optionsReq);

        $payloadReq = new AppendReq();
        $payloadReq->setProposedMessage($proposedMessage);
        /** @phpstan-ignore argument.type */
        $call->write($payloadReq);

        [$response, $status] = $call->wait();
        assert($response === null || $response instanceof AppendResp);
        assert(is_object($status));
        return [$response, Status::fromObject($status), $idString];
    }

    public function query(): ReadQuery {
        return new ReadQuery($this);
    }

    public function read(ReadReq $request): ServerStreamingCall {
        return $this->client->Read($request);
    }
}

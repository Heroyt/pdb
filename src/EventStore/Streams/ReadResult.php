<?php

declare(strict_types=1);

namespace App\EventStore\Streams;

use App\EventStore\Event;
use App\EventStore\Events\GenericEvent;
use App\EventStore\EventWrapper;
use App\EventStore\Status;
use App\Exceptions\EventSource\ReadErrorException;
use Generator;
use GRPC\EventStore\Streams\ReadResp;
use Grpc\ServerStreamingCall;
use Symfony\Component\Serializer\Serializer;

readonly class ReadResult
{
    public function __construct(
        private ServerStreamingCall $call,
        private Serializer          $serializer,
    ) {
    }

    /**
     * @return Generator<EventWrapper>
     */
    public function get(): Generator {
        foreach ($this->call->responses() as $response) {
            assert($response instanceof ReadResp);
            $eventData = $response->getEvent();
            if ($eventData !== null) {
                $event = $eventData->getEvent();
                if ($event === null) {
                    continue;
                }
                $meta = $event->getMetadata();
                /** @var class-string<Event> $type */
                $type = $meta['type'];
                $data = $event->getData();
                if (class_exists($type)) {
                    $class = $type;
                } else {
                    // TODO: Maybe map specific system events to specific system DTOs
                    $class = match ($type) {
                        default => GenericEvent::class,
                    };
                }
                if (empty($data) || !json_validate($data)) {
                    continue;
                }
                yield new EventWrapper(
                    $class === GenericEvent::class ?
                    GenericEvent::fromData($this->serializer->decode($data, 'json'), $type)
                    : $this->serializer->deserialize($data, $class, 'json'),
                    $event->getId()?->getString(),
                    (int) $event->getStreamRevision(),
                    (int) $event->getCommitPosition(),
                );
            }
        }
        $status = Status::fromObject($this->call->getStatus());
        if ($status->code !== Status::CODE_OK) {
            throw new ReadErrorException($status);
        }
    }
}

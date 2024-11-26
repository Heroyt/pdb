<?php

declare(strict_types=1);

namespace App\EventStore\Streams;

use App\EventStore\Event;
use App\EventStore\Streams;
use App\Exceptions\Logic\CannotCombineFiltersException;
use GRPC\EventStore\Shared\PBEmpty;
use GRPC\EventStore\Shared\StreamIdentifier;
use GRPC\EventStore\Streams\ReadReq;
use GRPC\EventStore\Streams\ReadReq\Options;

class ReadQuery
{
    public const int DEFAULT_LIMIT = 100;
    public const int DEFAULT_WINDOW_SIZE = 30;
    public const int DEFAULT_CHECKPOINT_INTERVAL_MULTIPLIER = 6;

    /** @var class-string<Event>|null */
    private ?string $eventType = null;
    private ?StreamIdentifier $stream = null;
    private bool $backwards = false;
    private ?int $position = null;
    private ?int $revision = null;
    private bool $resolveLinks = false;
    private int $limit = self::DEFAULT_LIMIT;
    private bool $subscribe = false;

    public function __construct(
        private readonly Streams $streams,
    ) {
    }

    /**
     * @param  class-string<Event>  $eventType
     * @warning Cannot combine with ReadQuery::stream() filter
     * @return $this
     */
    public function type(string $eventType): ReadQuery {
        if ($this->stream !== null) {
            throw new CannotCombineFiltersException('Cannot combine type() filter with stream() filter');
        }
        $this->eventType = $eventType;
        return $this;
    }

    /**
     * @warning Cannot combine with ReadQuery::type() filter
     */
    public function stream(string $stream): ReadQuery {
        if ($this->eventType !== null) {
            throw new CannotCombineFiltersException('Cannot combine type() filter with stream() filter');
        }
        $streamIdentifier = new StreamIdentifier();
        $streamIdentifier->setStreamName($stream);
        $this->stream = $streamIdentifier;
        return $this;
    }

    public function backwards(): ReadQuery {
        $this->backwards = true;
        return $this;
    }

    public function forwards(): ReadQuery {
        $this->backwards = false;
        return $this;
    }

    public function position(int $position): ReadQuery {
        $this->position = $position;
        return $this;
    }

    public function revision(int $revision): ReadQuery {
        $this->revision = $revision;
        return $this;
    }

    public function resolveLinks(bool $resolve = true): ReadQuery {
        $this->resolveLinks = $resolve;
        return $this;
    }

    public function limit(int $limit): ReadQuery {
        $this->limit = $limit;
        return $this;
    }

    public function subscribe(): ReadQuery {
        $this->subscribe = true;
        return $this;
    }

    public function send(): ReadResult {
        $request = new ReadReq([
          'options' => $this->buildOptions(),
                               ]);

        $response = $this->streams->read($request);
        return new ReadResult($response, $this->streams->serializer);
    }

    private function buildOptions(): Options {
        $options = new Options([
          'resolve_links' => $this->resolveLinks,
          'read_direction' => $this->backwards ? 1 : 0,
                               ]);

        if ($this->stream !== null) {
            $streamOptions = new Options\StreamOptions(
                [
                'stream_identifier' => $this->stream,
                ]
            );
            if ($this->revision !== null) {
                $streamOptions->setRevision($this->revision);
            } else if ($this->backwards) {
                $streamOptions->setEnd(new PBEmpty());
            } else {
                $streamOptions->setStart(new PBEmpty());
            }
            $options->setStream($streamOptions);
        } else {
            $allOptions = new Options\AllOptions();
            if ($this->position !== null) {
                $allOptions->setPosition(
                    new Options\Position(
                        [
                        'commit_position'  => $this->position,
                        'prepare_position' => $this->position,
                        ]
                    )
                );
            } else if ($this->backwards) {
                $allOptions->setEnd(new PBEmpty());
            } else {
                $allOptions->setStart(new PBEmpty());
            }
            $options->setAll($allOptions);
        }

        if ($this->subscribe) {
            $options->setSubscription(new Options\SubscriptionOptions());
        } else {
            $options->setCount($this->limit);
        }

        if ($this->eventType !== null) {
            $filterOptions = new Options\FilterOptions(
                [
                'max'                          => self::DEFAULT_WINDOW_SIZE,
                'checkpointIntervalMultiplier' => self::DEFAULT_CHECKPOINT_INTERVAL_MULTIPLIER,
                ]
            );

            $filterOptions->setEventType(
                new Options\FilterOptions\Expression(
                    [
                    'regex' => '^' . str_replace('\\', '\\\\', $this->eventType) . '$',
                    'prefix' => [],
                    ]
                )
            );

            $options->setFilter($filterOptions);
        } else {
            $options->setNoFilter(new PBEmpty());
        }

        $options->setUuidOption(
            new Options\UUIDOption(
                [
                'string' => new PBEmpty(),
                ]
            )
        );

        return $options;
    }
}

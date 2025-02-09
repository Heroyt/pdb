<?php

use App\Core\Info;
use App\Tasks\TaskDispatcherInterface;
use Lsr\Core\App;
use Lsr\Core\Exceptions\ModelNotFoundException;
use Lsr\Core\Models\ModelRepository;
use Lsr\Core\Requests\Dto\ErrorResponse;
use Lsr\Core\Requests\Enums\ErrorType;
use Lsr\Core\Requests\Exceptions\RouteNotFoundException;
use Lsr\Core\Requests\RequestFactory;
use Lsr\Core\Routing\Exceptions\MethodNotAllowedException;
use Lsr\Core\Routing\Exceptions\ModelNotFoundException as RoutingModelNotFoundException;
use Lsr\Logging\Logger;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Spiral\RoadRunner\Environment;
use Spiral\RoadRunner\Environment\Mode;
use Spiral\RoadRunner\Http\PSR7Worker;
use Spiral\RoadRunner\Jobs\Consumer;
use Spiral\RoadRunner\Worker;
use Tracy\Debugger;
use Tracy\Helpers;
use Tracy\ILogger;

const ROOT = __DIR__ . '/';

error_reporting(E_ALL ^ E_DEPRECATED);
ini_set('display_errors', 'stderr');
ini_set('display_startup_errors', '1');

require_once ROOT . "include/load.php";

$app = App::getInstance();
$env = Environment::fromGlobals();

Debugger::$logDirectory = LOG_DIR . 'tracy';

if (
    !file_exists(Debugger::$logDirectory) &&
    !mkdir(Debugger::$logDirectory, 0777, true) &&
    !is_dir(Debugger::$logDirectory)
) {
    Debugger::$logDirectory = LOG_DIR;
}

switch ($env->getMode()) {
    case Mode::MODE_JOBS:
        $consumer = new Consumer();
        /** @var Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface $task */
        while ($task = $consumer->waitTask()) {
            // Clear static cache
            Info::clearStaticCache();
            ModelRepository::clearInstances();

            try {
                $name = $task->getName();

                /** @var TaskDispatcherInterface $dispatcher */
                $dispatcher = $app::getService($name);
                $dispatcher->process($task);

                if (!$task->isCompleted()) {
                    $task->ack();
                }
            } catch (Throwable $e) {
                $task->nack($e);
            }
            $app->translations->updateTranslations();
        }
        break;
    default:
        $worker = Worker::create();

        $logger = new Logger(LOG_DIR, 'worker');
        $factory = new Psr17Factory();
        $psr7 = new PSR7Worker($worker, $factory, $factory, $factory);

        while (true) {
            // Clear static cache
            Info::clearStaticCache();
            ModelRepository::clearInstances();
            if (isset($request)) {
                unset($request);
            }

            try {
                try {
                    $request = $psr7->waitRequest();
                    if ($request === null) {
                        break;
                    }
                } catch (Throwable $e) {
                    // Although the PSR-17 specification clearly states that there can be
                    // no exceptions when creating a request, however, some implementations
                    // may violate this rule. Therefore, it is recommended to process the
                    // incoming request for errors.
                    //
                    // Send "Bad Request" response.
                    $psr7->respond(new Response(400));
                    continue;
                }

                $request = RequestFactory::fromPsrRequest($request);
                $app->setRequest($request);
                assert($request === $app->getRequest(), 'Request set does not match');

                try {
                    $psr7->respond(
                        $app->run()
                          ->withAddedHeader('Content-Language', $app->translations->getLang())
                    );
                } catch (RouteNotFoundException $e) { // 404 error
                    if (in_array('application/json', getAcceptTypes($request))) {
                        $psr7->respond(
                            new Response(
                                404,
                                ['Content-Type' => 'application/json'],
                                json_encode(
                                    new ErrorResponse(
                                        'Route not found',
                                        type     : ErrorType::NOT_FOUND,
                                        detail   : $e->getMessage(),
                                        exception: $e
                                    ),
                                    JSON_THROW_ON_ERROR
                                )
                            )
                        );
                    } else {
                        $psr7->respond(new Response(404, [], $e->getMessage()));
                    }
                    continue;
                } catch (MethodNotAllowedException $e) {
                    if (in_array('application/json', getAcceptTypes($request))) {
                        $psr7->respond(
                            new Response(
                                405,
                                ['Content-Type' => 'application/json'],
                                json_encode(
                                    new ErrorResponse(
                                        $e->getMessage(),
                                        type: ErrorType::NOT_FOUND,
                                    ),
                                    JSON_THROW_ON_ERROR
                                )
                            )
                        );
                    } else {
                        $psr7->respond(new Response(405, [], $e->getMessage()));
                    }
                    continue;
                } catch (RoutingModelNotFoundException | ModelNotFoundException $e) {
                    if (in_array('application/json', getAcceptTypes($request))) {
                        $psr7->respond(
                            new Response(
                                404,
                                ['Content-Type' => 'application/json'],
                                json_encode(
                                    new ErrorResponse(
                                        'Model not found',
                                        type     : ErrorType::NOT_FOUND,
                                        detail   : $e->getMessage(),
                                        exception: $e
                                    ),
                                    JSON_THROW_ON_ERROR
                                )
                            )
                        );
                    } else {
                        $psr7->respond(new Response(404, [], $e->getMessage()));
                    }
                    continue;
                } catch (Throwable $e) {
                    $logger->exception($e);
                    Helpers::improveException($e);
                    Debugger::log($e, ILogger::EXCEPTION);

                    try {
                        if (in_array('application/json', getAcceptTypes($request))) {
                            $psr7->respond(
                              new Response(
                                500,
                                ['Content-Type' => 'application/json'],
                                json_encode(
                                  new ErrorResponse(
                                               'Something Went wrong!',
                                    detail   : $e->getMessage(),
                                    exception: $e,
                                    values   : [$e::class]
                                  ),
                                  JSON_THROW_ON_ERROR
                                )
                              )
                            );
                            continue;
                        }
                    } catch (JsonException) {
                        
                    }

                    if (!$app->isProduction()) {
                        ob_start(); // double buffer prevents sending HTTP headers in some PHP
                        ob_start();
                        Debugger::getBlueScreen()->render($e);
                        /** @var string $blueScreen */
                        $blueScreen = ob_get_clean();
                        ob_end_clean();

                        $psr7->respond(
                            new Response(
                                500,
                                [
                                'Content-Type' => 'text/html',
                                ],
                                $blueScreen
                            )
                        );
                        file_put_contents('php://stderr', (string) $e);
                        continue;
                    }

                    $psr7->respond(new Response(500, ['Content-Type' => 'text/plain'], $e->getMessage()));

                    file_put_contents('php://stderr', (string) $e);
                }
            } catch (Throwable $e) { // Last line of defence if any error occurs
                // Log exception
                $logger->exception($e);
                Helpers::improveException($e);
                Debugger::log($e, ILogger::EXCEPTION);

                // Inform worker that an unexpected error occured
                $psr7->respond(new Response(500, [], $e->getMessage()));
                file_put_contents('php://stderr', (string) $e);
            }
        }
        break;
}

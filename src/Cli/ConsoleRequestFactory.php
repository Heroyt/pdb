<?php

declare(strict_types=1);

namespace App\Cli;

use App\Exceptions\Logic\InvalidArgument;
use App\Exceptions\Logic\InvalidState;
use Nette\Http\Request;
use Nette\Http\RequestFactory;
use Nette\Http\UrlScript;
use Nette\Utils\Validators;
use Symfony\Component\Console\Input\ArgvInput;

use function strtolower;

/**
 * @internal
 */
final class ConsoleRequestFactory extends RequestFactory
{
    private ?string $url;

    private string $argvOptionName;

    private string $configOptionName;

    /** @var array<string, string> */
    private array $headers = [];

    public function __construct(?string $url, string $argvOptionName, string $configOptionName) {
        $this->url = $url;
        $this->argvOptionName = $argvOptionName;
        $this->configOptionName = $configOptionName;
    }

    public function fromGlobals(): Request {
        return new Request(
            new UrlScript($this->getUrl()),
            [],
            [],
            [],
            $this->headers,
        );
    }

    private function getUrl(): string {
        $argv = new ArgvInput();
        if ($argv->hasParameterOption($this->argvOptionName)) {
            $url = $argv->getParameterOption($this->argvOptionName, null);

            if ($url !== null) {
                if (!Validators::isUrl($url)) {
                    throw new InvalidArgument("Command option '$this->argvOptionName' has to be valid URL, '$url' given.");
                }

                return $url;
            }
        }

        if ($this->url !== null) {
            return $this->url;
        }

        throw new InvalidState("Request factory for console mode is used and no URL was provided. Trying to create HTTP request. Specify URL either via '$this->configOptionName' extension option or via '$this->argvOptionName' command option.");
    }

    public function addHeader(string $name, string $value): void {
        $this->headers[strtolower($name)] = $value;
    }
}

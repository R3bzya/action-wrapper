<?php

namespace R3bzya\ActionWrapper\Support\Log;

use Illuminate\Support\Facades\Log as LogFacade;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;
use Stringable;

class Logger implements LoggerInterface
{
    public function emergency(Stringable|string $message, array $context = []): void
    {
        $this->write('emergency', $message, $context);
    }

    public function alert(Stringable|string $message, array $context = []): void
    {
        $this->write('alert', $message, $context);
    }

    public function critical(Stringable|string $message, array $context = []): void
    {
        $this->write('critical', $message, $context);
    }

    public function error(Stringable|string $message, array $context = []): void
    {
        $this->write('error', $message, $context);
    }

    public function warning(Stringable|string $message, array $context = []): void
    {
        $this->write('warning', $message, $context);
    }

    public function notice(Stringable|string $message, array $context = []): void
    {
        $this->write('notice', $message, $context);
    }

    public function info(Stringable|string $message, array $context = []): void
    {
        $this->write('info', $message, $context);
    }

    public function debug(Stringable|string $message, array $context = []): void
    {
        $this->write('debug', $message, $context);
    }

    public function log($level, Stringable|string $message, array $context = []): void
    {
        $this->write('log', $message, $context);
    }

    private function write(string $level, mixed $message, mixed $context): void
    {
        [$file, $message] = $this->parseMessage($message);

        $this->channel($file)->$level($message, $context);
    }

    private function parseMessage(string $message): array
    {
        if (str_contains($message, ':')) {
            return explode(':', $message, 2);
        }

        return [null, $message];
    }

    private function channel(string|null $file): LoggerInterface
    {
        $config = config('action-wrapper.logging.config', []);

        $config['path'] = $this->preparePath(
            $config['path'] ?? storage_path('logs'), $file
        );

        return LogFacade::build($config);
    }

    private function preparePath(string $path, string|null $file): string
    {
        if (Str::endsWith($path, '.log') && is_null($file)) {
            return $path;
        }

        $file = ltrim(str_replace('.', '/', $file ?: 'actions'), '\/');

        $path = Str::replaceMatches('/\/(\w+).log$/', '', $path);

        return sprintf('%s/%s.log', $path, $file);
    }
}
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

    protected function write(string $level, mixed $message, mixed $context): void
    {
        // Get channels and the log message
        [$channels, $message] = $this->parseMessage($message);

        LogFacade::stack($this->getStack($channels))->$level($message, $context);
    }

    protected function getStack(string|null $channels): array
    {
        if ($channels && $channels = $this->prepareChannels($channels)) {
            return $channels;
        }

        return [$this->channel(null)];
    }

    protected function parseMessage(string $message): array
    {
        if (! str_contains($message, ':')) {
            return [null, $message];
        }

        return explode(':', $message, 2);
    }

    protected function prepareChannels(string $channels): array
    {
        // Find an on-demand channel and other channels
        $channels = $this->replacePlaceholders(
            $this->parseChannels(
                $this->setPlaceholders($channels),
            )
        );

        return collect(array_filter_recursive($channels))
            ->flatMap(function (string|array $value) {
                return is_array($value) ? $value : [$this->channel($value)];
            })
            ->unique()
            ->values()
            ->all();
    }

    protected function parseChannels(string $channels): array
    {
        $channels = $this->trimChannels($channels);

        if (! str_contains($channels, ',')) {
            return [$channels, []];
        }

        $channels = explode(',', $channels);

        return [array_shift($channels), $channels];
    }

    protected function trimChannels(string $channels): string
    {
        return rtrim(trim($channels), ',');
    }

    protected function channel(string|null $file): LoggerInterface
    {
        $config = config('action-wrapper.logging.config', []);

        // If we want to save the logs to a specific file, we need to create a file path.
        $config['path'] = $this->preparePath(
            $config['path'] ?? storage_path('logs'), $file,
        );

        return LogFacade::build($config);
    }

    /**
     * Prepares and returns a complete path for a log file based on the given path and file name.
     */
    protected function preparePath(string $path, string|null $file): string
    {
        // If the file name is null and the path ends with ".log",
        // the path is a complete path and should be returned.
        if (Str::endsWith($path, '.log') && is_null($file)) {
            return $path;
        }

        // If the path ends with ".log", we need to remove that part before concatenating.
        $path = Str::replaceMatches('/\/(\w+).log$/', '', $path);

        $file = is_string($file) ? trim($file, '\\/') : null;

        return sprintf('%s/%s.log', $path, $file ?: 'actions');
    }

    protected function setPlaceholders(string $value): string
    {
        return str_replace(['\\,'], ['__comma__'], $value);
    }

    protected function replacePlaceholders(array $data): array
    {
        return array_map(function (array|string $datum) {
            return is_array($datum)
                ? $this->replacePlaceholders($datum)
                : $this->replacePlaceholdersInString($datum);
        }, $data);
    }

    protected function replacePlaceholdersInString(string $value): string
    {
        return str_replace(['__comma__'], [','], $value);
    }
}
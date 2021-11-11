<?php

declare(strict_types=1);

namespace Code;

use Exception;
use function explode;
use function file_exists;
use function file_get_contents;
use function json_decode;

class ComposerLoader
{
    /** @var string */
    protected const COMPOSER_FILE = '/composer.json';

    protected array $config = [];

    /**
     * @throws Exception
     */
    public function __construct(protected ?string $composerDir = null, protected string $extraKey = 'code')
    {
        $this->composerDir ??= getcwd();

        if (!file_exists($this->composerDir . self::COMPOSER_FILE)) {
            throw new Exception(self::COMPOSER_FILE . ' file not found');
        }

        $content      = file_get_contents($this->composerDir . self::COMPOSER_FILE);
        $content      = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        $this->config = $content['extra'][$this->extraKey] ?? [];
    }

    /**
     * @return array|mixed|null
     */
    public function get(string $key, mixed $default = null)
    {
        $keys  = explode('.', $key);
        $array = $this->config;
        foreach ($keys as $key) {
            if (\is_array($array) && isset($array[$key])) {
                $array = $array[$key];
            } else {
                return $default;
            }
        }

        return $array;
    }

    public function getAbsolutePaths(string $key, array $default = []): array
    {
        $paths = $this->get($key, $default);
        $root  = $this->composerDir;

        return array_map(static fn ($path): string => $root . $path, (array)$paths);
    }
}

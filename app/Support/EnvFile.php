<?php

namespace App\Support;

/**
 * Reads and changes KEY=value lines in a .env file, leaving every other line,
 * comment and blank line as it was.
 */
class EnvFile
{
    public function __construct(private string $path) {}

    public function exists(): bool
    {
        return is_file($this->path);
    }

    /** @return array<string, string> */
    public function read(): array
    {
        if (! $this->exists()) {
            return [];
        }

        $values = [];

        foreach (file($this->path, FILE_IGNORE_NEW_LINES) as $line) {
            if (preg_match('/^\s*([A-Z0-9_]+)\s*=\s*(.*)$/', $line, $match)) {
                $values[$match[1]] = self::unquote(trim($match[2]));
            }
        }

        return $values;
    }

    public function get(string $key): ?string
    {
        $value = $this->read()[$key] ?? null;

        return $value === '' ? null : $value;
    }

    /** @param array<string, string|int|bool|null> $values */
    public function set(array $values): void
    {
        $lines = $this->exists() ? file($this->path, FILE_IGNORE_NEW_LINES) : [];

        foreach ($values as $key => $value) {
            $line = $key.'='.self::quote($value);
            $found = false;

            foreach ($lines as $i => $existing) {
                if (preg_match('/^\s*'.preg_quote($key, '/').'\s*=/', $existing)) {
                    $lines[$i] = $line;
                    $found = true;
                }
            }

            if (! $found) {
                $lines[] = $line;
            }
        }

        file_put_contents($this->path, implode("\n", $lines)."\n");
        @chmod($this->path, 0600);
    }

    private static function quote(string|int|bool|null $value): string
    {
        $value = match (true) {
            $value === null => '',
            is_bool($value) => $value ? 'true' : 'false',
            default => (string) $value,
        };

        if ($value === '' || preg_match('/^[A-Za-z0-9_.:\/@+=,-]+$/', $value)) {
            return $value;
        }

        return '"'.str_replace(['\\', '"', '$'], ['\\\\', '\\"', '\\$'], $value).'"';
    }

    private static function unquote(string $value): string
    {
        if (strlen($value) >= 2 && $value[0] === '"' && str_ends_with($value, '"')) {
            return str_replace(['\\"', '\\$', '\\\\'], ['"', '$', '\\'], substr($value, 1, -1));
        }

        if (strlen($value) >= 2 && $value[0] === "'" && str_ends_with($value, "'")) {
            return substr($value, 1, -1);
        }

        return preg_replace('/\s+#.*$/', '', $value);
    }
}

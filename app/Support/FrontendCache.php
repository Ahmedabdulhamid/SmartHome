<?php

namespace App\Support;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class FrontendCache
{
    private const VERSION_KEY = 'frontend_cache_version';

    public static function remember(string $key, array $context, int $ttl, Closure $callback): mixed
    {
        return Cache::remember(
            self::key($key, $context),
            now()->addSeconds($ttl),
            $callback
        );
    }

    public static function flush(): void
    {
        Cache::forever(self::VERSION_KEY, Str::uuid()->toString());
    }

    private static function key(string $key, array $context): string
    {
        $version = Cache::get(self::VERSION_KEY, 'v1');
        $hash = md5(json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return "frontend_cache:{$version}:{$key}:{$hash}";
    }
}

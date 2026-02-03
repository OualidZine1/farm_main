<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    protected $cachePrefix = 'farm_';

    public function get(string $key, mixed $default = null): mixed
    {
        $key = $this->buildKey($key);

        return Cache::get($key, $default);
    }

    public function put(string $key, mixed $value, int $minutes = 60): bool
    {
        $key = $this->buildKey($key);

        return Cache::put($key, $value, $minutes);
    }

    public function remember(string $key, int $minutes, \Closure $callback)
    {
        $key = $this->buildKey($key);

        return Cache::remember($key, $minutes, $callback);
    }

    public function forget(string $key): bool
    {
        $key = $this->buildKey($key);

        return Cache::forget($key);
    }

    public function flush(): void
    {
        Cache::flush();
    }

    protected function buildKey(string $key): string
    {
        return $this->cachePrefix.$key;
    }

    public function cacheCategories()
    {
        $key = 'categories_list';

        return $this->remember($key, 24 * 60, function () {
            return \App\Models\Category::orderBy('name')->get();
        });
    }

    public function cacheProductSearch(string $search)
    {
        $key = 'products_search_'.md5($search);

        return $this->remember($key, 60, function () use ($search) {
            return \App\Models\Product::with('category')
                ->when($search, function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhereHas('category', function ($q) use ($search) {
                            $q->where('name', 'like', '%'.$search.'%');
                        });
                })
                ->orderBy('name')
                ->paginate(10);
        });
    }
}

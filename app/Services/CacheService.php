<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    protected $cachePrefix = 'farm_';

    public function get($key, $default = null)
    {
        $key = $this->buildKey($key);
        return Cache::get($key, $default);
    }

    public function put($key, $value, $minutes = 60)
    {
        $key = $this->buildKey($key);
        return Cache::put($key, $value, $minutes);
    }

    public function remember($key, $minutes, \Closure $callback)
    {
        $key = $this->buildKey($key);
        return Cache::remember($key, $minutes, $callback);
    }

    public function forget($key)
    {
        $key = $this->buildKey($key);
        return Cache::forget($key);
    }

    public function flush()
    {
        Cache::flush();
    }

    protected function buildKey($key)
    {
        return $this->cachePrefix . $key;
    }

    public function cacheCategories()
    {
        $key = 'categories_list';
        return $this->remember($key, 24 * 60, function () {
            return \App\Models\Category::orderBy('name')->get();
        });
    }

    public function cacheProductSearch($search)
    {
        $key = 'products_search_' . md5($search);
        return $this->remember($key, 60, function () use ($search) {
            return \App\Models\Product::with('category')
                ->when($search, function($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhereHas('category', function($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%');
                        });
                })
                ->orderBy('name')
                ->paginate(10);
        });
    }
}

<?php

namespace TypiCMS\Modules\Core\Services\Cache;

use Illuminate\Cache\CacheManager;
use stdClass;

class LaravelCache implements CacheInterface
{
    protected $cache;
    protected $tags;
    protected $minutes;

    /**
     * @param CacheManager $cache
     * @param array|string $tags
     * @param int          $minutes
     */
    public function __construct(CacheManager $cache, $tags, $minutes = null)
    {
        $this->cache = $cache;
        $this->tags = is_array($tags) ? $tags : [$tags];
        $this->minutes = $minutes;
    }

    /**
     * Retrieve data from cache.
     *
     * @param string $key Cache item key
     *
     * @return mixed
     */
    public function get($key)
    {
        return $this->cache->tags($this->tags)->get($key);
    }

    /**
     * Add data to the cache.
     *
     * @param string $key     Cache item key
     * @param mixed  $value   The data to store
     * @param int    $minutes The number of minutes to store the item
     *
     * @return mixed
     */
    public function put($key, $value, $minutes = null)
    {
        if (is_null($minutes)) {
            $minutes = $this->minutes;
        }

        return $this->cache->tags($this->tags)->put($key, $value, $minutes);
    }

    /**
     * Add data to the cache
     * taking pagination data into account.
     *
     * @param int    $currentPage Page of the cached items
     * @param int    $perPage     Number of results per page
     * @param int    $totalItems  Total number of possible items
     * @param mixed  $items       The actual items for this page
     * @param string $key         Cache item key
     * @param int    $minutes     The number of minutes to store the item
     *
     * @return stdClass $items variable returned for convenience
     */
    public function putPaginated($currentPage, $perPage, $totalItems, $items, $key, $minutes = null)
    {
        $cached = new stdClass();

        $cached->currentPage = $currentPage;
        $cached->items = $items;
        $cached->totalItems = $totalItems;
        $cached->perPage = $perPage;

        $this->put($key, $cached, $minutes);

        return $cached;
    }

    /**
     * Test if item exists in cache
     * Only returns true if exists && is not expired.
     *
     * @param string $key Cache item key
     *
     * @return bool If cache item exists
     */
    public function has($key)
    {
        return $this->cache->tags($this->tags)->has($key);
    }

    /**
     * Set tags.
     *
     * @param array $tags tags
     *
     * @return false|null If cache item exists
     */
    public function addTags($tags = null)
    {
        if (!$tags) {
            return false;
        }
        $tags = is_array($tags) ? $tags : func_get_args();
        $this->tags = array_merge($this->tags, $tags);
    }

    /**
     * Flush cache for tags.
     *
     * @param string $tags Cache tags
     *
     * @return bool If cache is flushed
     */
    public function flush($tags = null)
    {
        if ($tags !== null) {
            $tags = is_array($tags) ? $tags : func_get_args();
        } else {
            $tags = $this->tags;
        }

        return $this->cache->tags($tags)->flush();
    }
}

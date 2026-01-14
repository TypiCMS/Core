<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * @property int $id
 * @property string $group_name
 * @property string $key_name
 * @property string|null $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Setting extends Model
{
    protected $fillable = [
        'group_name',
        'key_name',
        'value',
    ];

    /** @return array<string, string|array<string>> */
    public function allToArray(): array
    {
        $config = [];

        try {
            foreach (self::query()->get() as $object) {
                $key = $object->key_name;
                if ($object->group_name !== 'config') {
                    $config[$object->group_name][$key] = $object->value;
                } else {
                    $config[$key] = $object->value;
                }
            }
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
        }

        return $config;
    }
}

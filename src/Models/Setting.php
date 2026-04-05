<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * @property int $id
 * @property string $group_name
 * @property string $key_name
 * @property string|null $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable('group_name', 'key_name', 'value')]
class Setting extends Model
{
    /** @return array<string, string|null|array<string, string|null>> */
    public function allToArray(): array
    {
        /** @var array<string, string|null|array<string, string|null>> $config */
        $config = [];

        try {
            foreach (self::query()->get() as $object) {
                $key = $object->key_name;
                if ($object->group_name !== 'config') {
                    if (! isset($config[$object->group_name]) || ! is_array($config[$object->group_name])) {
                        $config[$object->group_name] = [];
                    }

                    $config[$object->group_name][$key] = $object->value;
                } else {
                    $config[$key] = $object->value;
                }
            }
        } catch (Throwable $throwable) {
            Log::error($throwable->getMessage());
        }

        return $config;
    }
}

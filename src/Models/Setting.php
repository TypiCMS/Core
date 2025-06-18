<?php

namespace TypiCMS\Modules\Core\Models;

use Eloquent;
use Exception;
use Illuminate\Support\Facades\Log;

class Setting extends Eloquent
{
    protected $fillable = [
        'group_name',
        'key_name',
        'value',
    ];

    public function allToArray(): array
    {
        $config = [];

        try {
            foreach ($this->get() as $object) {
                $key = $object->key_name;
                if ($object->group_name != 'config') {
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

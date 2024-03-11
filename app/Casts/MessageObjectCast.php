<?php

namespace App\Casts;

use App\Objects\MessageMailObject;
use App\Objects\MessageObject;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class MessageObjectCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?MessageObject
    {
        if (empty($value)) {
            return null;
        }

        return MessageMailObject::fromString($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value instanceof MessageObject) {
            return $value->toString();
        }

        return $value;
    }
}

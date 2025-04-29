<?php

declare(strict_types=1);

namespace App\Traits;

trait EnumOption
{
    public static function options()
    {
        $cases = static::cases();

        $data = collect();
        foreach ($cases as $case) {
            $data->push((object) [
                'name' => $case->name,
                'value' => $case->value,
            ]);
        }

        return $data;
    }

    public static function optionsInArray()
    {
        $options = static::options();

        return $options->pluck('value')->filter()->toArray();
    }
}

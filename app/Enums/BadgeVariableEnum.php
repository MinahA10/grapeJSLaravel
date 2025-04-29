<?php

namespace App\Enums;

use App\Traits\EnumOption;

enum BadgeVariableEnum: string
{
    use EnumOption;
    
    case FULLNAME = 'name';
    case QRCODE = 'qrcode';
    case FIRSTNAME = 'firstname';
    case LASTNAME = 'lastname';
    case LOGO = 'logo';

    public static function options()
    {
        $cases = self::cases();

        $data = collect();
        foreach ($cases as $case) {
            $data->push((object) [
                'name' => $case->name,
                'value' => $case->value,
                'en' => $case->toLocaleEn(),
                'fr' => $case->toLocaleFr(),
            ]);
        }

        return $data;
    }

    public static function optionsInArray()
    {
        $options = self::options();

        return $options->pluck('value')->filter()->toArray();
    }

    public function toLocaleFr(): string
    {
        return match ($this) {
            self::FULLNAME => 'nom',
            self::QRCODE => 'qrcode',
            self::FIRSTNAME => 'prenom',
            self::LASTNAME => 'nom',
            self::LOGO => 'logo',
          
        };
    }

    public function toLocaleEn(): string
    {
        return match ($this) {
            self::FULLNAME => 'name',
            self::QRCODE => 'qrcode',
            self::FIRSTNAME => 'firstname',
            self::LASTNAME => 'lastname',
            self::LOGO => 'logo',    
        };
    }
}
<?php

namespace App\Models;

use Dotlogics\Grapesjs\App\Contracts\Editable;
use Dotlogics\Grapesjs\App\Traits\EditableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Template extends Model implements Editable
{
    use EditableTrait, HasFactory;
    public $incrementing = false;

    protected $guarded = [];

    protected $keyType = 'string';

    public static function boot()
    {
        parent::boot();
        static::creating(function (self $template) {
            $template->id = (string) Str::uuid();
        });
    }
}

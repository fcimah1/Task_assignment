<?php
namespace App\DTO;

use Dotenv\Util\Str;
use Spatie\LaravelData\Data;
Class CategoryDTO extends Data
{
    public function __construct( public string $name)
    {
    }
}

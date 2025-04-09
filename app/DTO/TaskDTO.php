<?php
namespace App\DTO;
use Spatie\LaravelData\Data;
Class TaskDTO extends Data
{
    public function __construct(public string $title, public string $status, public int $user_id, public int $category_id)
    {
    }
}

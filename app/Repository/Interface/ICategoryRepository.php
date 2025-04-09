<?php
namespace App\Repository\Interface;

use App\DTO\CategoryDTO;

interface ICategoryRepository 
{
    public function getAllCategorys();
    public function createCategory(CategoryDTO $categoryDTO): object;
    public function updateCategory(CategoryDTO $categoryDTO, string $id): bool;
    public function deleteCategory(string $id): bool;
}

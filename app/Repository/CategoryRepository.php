<?php
namespace App\Repository;

use App\DTO\CategoryDTO;
use App\Models\Category;
use App\Repository\Interface\ICategoryRepository;
class CategoryRepository implements ICategoryRepository
{
    public function getAllCategorys()
    {
        return Category::orderBy('created_at', 'desc')->paginate(10);
    }

    public function createCategory(CategoryDTO $categoryDTO): object
    {
        $category = Category::create($categoryDTO->toArray());
        return $category ? $category : "Error";
    }

    public function updateCategory(CategoryDTO $categoryDTO, string $id): bool
    {
        $category = Category::find($id);
        $updatedCategory = $category->update($categoryDTO->toArray());
        return $updatedCategory ? $updatedCategory : "Error";
    }

    public function deleteCategory(string $id): bool
    {
        $category = Category::find($id);
        return $category->delete() ? true : false;
    }

}

<?php

namespace App\Http\Controllers;

use App\DTO\CategoryDTO;
use App\Http\Requests\CategoryRequest;
use App\Repository\Interface\ICategoryRepository;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $categoryRepository;

    public function __construct(ICategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = $this->categoryRepository->getAllCategorys();
        return response()->json([
            'status' => 'true',
            'categories' => $categories
            ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        $categortDTO = CategoryDTO::from($request->all());
        $category = $this->categoryRepository->createCategory($categortDTO);
        return response()->json([
            'status' => 'true',
            'category' => $category
            ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, string $id)
    {
        $categortDTO = CategoryDTO::from($request->all());
        $category = $this->categoryRepository->updateCategory($categortDTO, $id);
        return response()->json([
            'status' => 'true',
            'category' => $category
            ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->categoryRepository->deleteCategory($id);
        return response()->json([
            'status' => 'true',
            'message' => 'task delete successfully'
            ],200);
    }

}

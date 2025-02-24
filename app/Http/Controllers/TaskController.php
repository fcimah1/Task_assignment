<?php

namespace App\Http\Controllers;

use App\DTO\TaskDTO;
use App\Http\Requests\TaskRequest;
use App\Repository\Interface\ITaskRepository;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    protected $taskRepository;

    public function __construct(ITaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = $this->taskRepository->getAllTasks();
        return response()->json([
            'status' => true,
            'tasks' => $tasks,
            'message' => 'tasks returned successfully'
        ]);
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
    public function store(TaskRequest $request)
    {
        $taskDTO = TaskDTO::from($request->all());
        $task = $this->taskRepository->createTask($taskDTO);
        return response()->json([
            'status' => true,
            'task' => $task,
            'message' => 'task add successfully'
        ]);
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
    public function update(TaskRequest $request, string $id)
    {
        $taskDTO = TaskDTO::from($request->all());
        $task = $this->taskRepository->updateTask($taskDTO, $id);
        return response()->json([
            'status' => true,
            'message' => 'task update successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->taskRepository->deleteTask($id);
        return response()->json([
            'status' => true,
            'message' => 'task delete successfully'
        ]);
    }
}

<?php
namespace App\Repository;

use App\DTO\TaskDTO;
use App\Models\Task;
use App\Repository\Interface\ITaskRepository;
class TaskRepository implements ITaskRepository
{
    public function getAllTasks()
    {
        return Task::all();
    }

    public function createTask(TaskDTO $taskDTO): object
    {
        $task = Task::create($taskDTO->toArray());
        return $task ?  $task : "Error";
    }

    public function updateTask(TaskDTO $taskDTO, string $id): bool
    {
        $task = Task::findOrFail($id);
        $updatedTask = $task->update($taskDTO->toArray());
        return $updatedTask ?  $updatedTask : "Error";
    }
    
    public function deleteTask(string $id): bool
    {
        $task = Task::findOrFail($id);
        return $task->delete() ? true : false;
    }
}


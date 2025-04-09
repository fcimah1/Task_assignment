<?php
namespace App\Repository;

use App\DTO\TaskDTO;
use App\Models\Task;
use App\Repository\Interface\ITaskRepository;
use Illuminate\Http\Request;

class TaskRepository implements ITaskRepository
{
    public function getTasks(Request $request)
    {
        $query = Task::where('user_id', auth()->id);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Optional: filter by category too
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        
        return $query->orderBy('due_date', 'asc')->paginate(10);
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

    // Restore a soft-deleted task.
    public function restoreTask(string $id): bool
    {
        $task = Task::withTrashed()->findOrFail($id);
        return $task->restore() ? true : false;
    }

}


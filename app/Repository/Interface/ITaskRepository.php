<?php
namespace App\Repository\Interface;

use App\DTO\TaskDTO;
use Illuminate\Http\Request;

interface ITaskRepository 
{
    public function getTasks(Request $request);
    public function createTask(TaskDTO $taskDTO): object;
    public function updateTask(TaskDTO $taskDTO, string $id): bool;
    public function deleteTask(string $id): bool;

    public function restoreTask(string $id): bool;

}
?>
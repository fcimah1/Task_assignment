<?php
namespace App\Repository\Interface;

use App\DTO\TaskDTO;

interface ITaskRepository 
{
    public function getAllTasks();
    public function createTask(TaskDTO $taskDTO): object;
    public function updateTask(TaskDTO $taskDTO, string $id): bool;
    public function deleteTask(string $id): bool;
}
?>
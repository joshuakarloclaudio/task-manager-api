<?php

namespace App\Repositories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TaskRepository
{
    public function __construct(
        private Task $model
    ) { }

    public function getList(int $userId, array $pagination, string|null $term): LengthAwarePaginator|Collection
    {
        $query = $this->model->newModelQuery();

        $query->where('user_id', $userId);
        $query->orderBy($pagination['order_column'], $pagination['order_type']);

        if ($term) {
            $query->where(function (Builder $query) use ($term) {
                $query
                    ->where('title', 'like', '%' . $term . '%')
                    ->orWhere('description', 'like', '%' . $term . '%');
            });
        }

        if ($pagination['page']) {
            return $query->paginate(perPage: $pagination['per_page'], page: $pagination['page']);
        }

        return $query->get();
    }

    public function create(array $data): Task
    {
        return $this->model->create($data);
    }

    public function update(Task $task, array $data): bool
    {
        return $task->update($data);
    }

    public function delete(Task $task): ?bool
    {
        return $task->delete();
    }

    public function updateCompletionStatus(Task $task, bool $complete): bool
    {
        return $task->update(['completed' => $complete]);
    }
}

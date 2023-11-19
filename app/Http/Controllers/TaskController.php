<?php

namespace App\Http\Controllers;

use App\Helpers\RequestHelper;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Repositories\TaskRepository;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskController extends Controller
{
    public function __construct(
        private TaskRepository $repository
    ) { }

    public function index(Request $request): AnonymousResourceCollection
    {
        $pagination = RequestHelper::formatPagination($request);
        $term = $request->query('term');

        $tasks = $this->repository->getList($request->user()->id, $pagination, $term);

        return TaskResource::collection($tasks);
    }

    public function store(StoreTaskRequest $request): JsonResource
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['due_date'] = $data['due_date'] ? Carbon::parse($data['due_date'])->format('Y-m-d H:i:s') : null;

        return new TaskResource($this->repository->create($data));
    }

    public function show(Task $task): JsonResource
    {
        return new TaskResource($task);
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        if ($task->user_id !== $request->user()->id) {
            abort(JsonResponse::HTTP_FORBIDDEN, 'You are not allowed to update a task that you do not own.');
        }

        $data = $request->validated();
        $data['due_date'] = $data['due_date'] ? Carbon::parse($data['due_date'])->format('Y-m-d H:i:s') : null;

        if ($this->repository->update($task, $data)) {
            return response()->json(['message' => 'Task successfully updated.']);
        }

        abort(JsonResponse::HTTP_BAD_REQUEST, "We can't update your task right now. Please try again.");
    }

    public function destroy(Request $request, Task $task): JsonResponse
    {
        if ($task->user_id !== $request->user()->id) {
            abort(JsonResponse::HTTP_FORBIDDEN, 'You are not allowed to delete a task that you do not own.');
        }

        if ($this->repository->delete($task)) {
            return response()->json(['message' => 'Task successfully deleted.']);
        }

        abort(JsonResponse::HTTP_BAD_REQUEST, "We can't delete your task right now. Please try again.");
    }

    public function updateCompletionStatus(Request $request, Task $task): JsonResponse
    {
        if ($task->user_id !== $request->user()->id) {
            abort(JsonResponse::HTTP_FORBIDDEN, 'You are not allowed to update the completion status of a task that you do not own.');
        }

        $complete = $request->boolean('complete');

        if ($this->repository->updateCompletionStatus($task, $complete)) {
            $markedAs = $complete ? 'complete' : 'incomplete';

            return response()->json(['message' => "Task successfully marked as {$markedAs}"]);
        }

        abort(JsonResponse::HTTP_BAD_REQUEST, "We can't update your task's completion status right now. Please try again.");
    }
}

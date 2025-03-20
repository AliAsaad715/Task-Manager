<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $tasks = Task::where('user_id', Auth::id())->orderByDesc('created_at')->get();
        return response([
            'tasks' => $tasks,
            'message' => 'Tasks retrieved successfully.'],
        200);
    }

    public function store(CreateTaskRequest $request)
    {
        $task = Task::create([
            'user_id' => Auth::id(),
            'title' => $request['title'],
            'description' => $request['description'],
            'status' => $request['status'],
            'deadline' => $request['deadline'],
        ]);

        return response([
            'task' => $task,
            'message' => 'Task Created Successfully'],
        201);
    }

    public function update(UpdateTaskRequest $request, $id)
    {
        $task = Task::where('user_id', Auth::id())->findOrFail($id);
        $task->update($request->validated());
        return response([
            'task' => $task,
            'message' => 'Task Updated Successfully'],
            200);
    }
    public function show($id)
    {
        $task = Task::where('user_id', Auth::id())->find($id);
        if(!$task)
            return response(['message' => 'Task Not Found!'], 404);

        return response(['task' => $task], 200);
    }

    public function destroy($id)
    {
        $task = Task::where('user_id', Auth::id())->find($id);
        if(!$task)
            return response(['message' => 'Task Not Found!'], 404);

        $task->delete();
        return response(['message' => 'Task Deleted Successfully!'], 200);
    }

    public function search(Request $request)
    {
        $tasks = Task::where('user_id', Auth::id())
            ->when($request->title, fn($query) => $query->where('title', 'like', "%{$request->title}%"))
            ->when($request->status, fn($query) => $query->where('status', 'like', "%{$request->status}%"))
            ->orderByDesc('created_at')
            ->get();
        return response(['tasks' => $tasks], 200);
    }

}

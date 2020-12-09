<?php

namespace App\Http\Controllers;

use App\Task;
use App\Column;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        return Column::with('tasks')->get();
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => ['required', 'string', 'max:56'],
            'description' => ['nullable', 'string'],
            'column_id' => ['required', 'exists:columns,id']
        ]);

        return Task::create($request->only('title', 'description', 'column_id'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => ['required', 'string', 'max:56'],
            'description' => ['nullable', 'string'],
        ]);
        $task = Task::findOrFail($id);
        $task->update([
            'title' => $request->input('title'),
            'description' => $request->input('description')
        ]);

        return Column::with('tasks')->get();
    }

    public function sync(Request $request)
    {
        $this->validate(request(), [
            'columns' => ['required', 'array']
        ]);

        foreach ($request->columns as $column) {
            foreach ($column['tasks'] as $i => $task) {
                $order = $i + 1;
                if ($task['column_id'] !== $column['id'] || $task['order'] !== $order) {
                    Task::find($task['id'])
                        ->update(['column_id' => $column['id'], 'order' => $order]);
                }
            }
        }
        return Column::with('tasks')->get();
    }
    
}

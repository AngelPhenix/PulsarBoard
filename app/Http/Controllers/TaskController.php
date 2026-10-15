<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Task;
use App\Models\Historic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function show(Board $board)
    {
        $tasks = $board->tasks()->orderBy('position', 'asc')->get();

        $labels = Auth::user()->labels;

        return view('task.show', [
            'board' => $board,
            'tasks' => $tasks,
            'labels' => $labels,
            'boardList' => Auth::user()->boards,
            'friends' => Auth::user()->friends
        ]);
    }

    public function update(Request $request, Task $task)
    {
        $action = '';
        $task->is_completed = $request->has('is_completed');
        
        if($task->is_completed){
            $action = "done";
        }else{
            $action = "undone";
        }

        $task->save();

        Historic::create([
            'task_name' => $task->name,
            'board_id' => $task->board_id,
            'modified_by' => Auth::user()->username,
            'modified_at' => now()->toDateTimeString(),
            'action' => $action,
        ]);

        return back();
    }

    public function store(Request $request, Board $board)
    {
        $attributes = $request->validate([
            'name' => ['required'],
        ]);

        $attributes['board_id'] = $board->id;
        Task::create($attributes);
        
        // Auth::user()->tasks()->create($attributes);

        return redirect('/board/' . $board->id);
    }

    public function updateOrder(Request $request)
    {
        $tasksData = $request->input('tasks', []);

        foreach ($tasksData as $taskData) {
            // Met à jour la position de chaque tâche de manière séquentielle
            Task::where('id', $taskData['id'])->update([
                'position' => $taskData['position']
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function destroy(Task $task)
    {
        Historic::create([
            'task_name' => $task->name,
            'board_id' => $task->board_id,
            'modified_by' => Auth::user()->username,
            'modified_at' => now()->toDateTimeString(),
            'action' => "deleted"
        ]);

        $task->delete();
        return back();
    }
}

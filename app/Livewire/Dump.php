<?php

namespace App\Livewire;

use App\Models\Todo;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Usernotnull\Toast\Concerns\WireToast;

class Dump extends Component
{
    use WireToast;

    public $todos;

    #[Validate('required|string|min:3|max:255')]
    public $title;

    public $editingTodoId = null;

    public $editingTitle = '';

    public $editingDescription = '';

    public function getListeners()
    {
        $userId = auth()->user()->id;

        return [
            "echo-private:user.{$userId},TodoUpdated" => 'refreshTodos',
        ];
    }

    public function mount()
    {
        $this->refreshTodos();
    }

    public function refreshTodos()
    {
        $this->todos = Todo::getAllTodosExceptToday()->where('status', '!=', 'completed');
    }

    public function addTodo()
    {
        try {
            $this->validate();
            Todo::create([
                'title' => $this->title,
                'worked_at' => null,
            ]);
            $this->refreshTodos();
            $this->title = '';
        } catch (\Exception $e) {
            // toast()->danger($e->getMessage())->push();
        }
    }

    public function addToToday($id)
    {
        try {
            $todo = Todo::getAllTodos()->where('id', $id)->first();
            $todo->worked_at = now();
            $todo->save();
            $this->refreshTodos();
        } catch (\Exception $e) {
            toast()->danger($e->getMessage())->push();
        }
    }

    public function deleteTodo($id)
    {
        try {
            $todo = Todo::getAllTodosExceptToday()->where('id', $id)->first();
            if (! $todo) {
                return;
            }

            $todo->delete();
            $this->refreshTodos();
        } catch (\Exception $e) {
            toast()->danger('Todo not found')->push();
        }
    }

    public function updateTodo()
    {
        try {
            Todo::updateTodo($this->editingTodoId, [
                'title' => $this->editingTitle,
                'description' => $this->editingDescription,
                'status' => $this->todos->where('id', $this->editingTodoId)->first()->status,
            ]);

            $this->editingTodoId = null;
            $this->editingTitle = '';
            $this->editingDescription = '';

            $this->refreshTodos();
        } catch (\Exception $e) {
            toast()->danger($e->getMessage())->push();
        }
    }

    public function render()
    {
        return view('livewire.dump');
    }
}

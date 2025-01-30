<?php

namespace App\Livewire;

use App\Models\Todo;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Usernotnull\Toast\Concerns\WireToast;

class Dump extends Component
{
    use WireToast;

    #[Title('Dump')]
    public $todos;

    #[Validate('required|string|min:3|max:255')]
    public $title = '';

    public $editingTodoId = null;

    public $editingTitle = '';

    public $editingDescription = '';

    public function getListeners()
    {
        $userId = Auth::user()->id;

        return [
            "echo-private:user.{$userId},TodoUpdated" => 'refreshTodos',
            'todos-updated' => 'refreshTodos',
        ];
    }

    public function updateTitle($title)
    {
        $this->title = $title;
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
            $todo = Todo::create([
                'title' => Todo::cleanTitle($this->title),
                'worked_at' => null,
            ]);
            $todo->syncHashtags($this->title);
            $this->title = '';
        } catch (\Exception $e) {
            toast()->danger($e->getMessage())->push();
        }
    }

    public function addToToday($id)
    {
        try {
            $todo = Todo::getOwnTodo($id);
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
            $todo = Todo::getOwnTodo($id);
            $todo->delete();
            $this->refreshTodos();
            $this->dispatch('todo-saved');
        } catch (\Exception $e) {
            toast()->danger($e->getMessage())->push();
        }
    }

    public function render()
    {
        return view('livewire.dump');
    }
}

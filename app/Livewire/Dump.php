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
            $todo = Todo::create([
                'title' => $this->title,
                'user_id' => auth()->user()->id,
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

    public function render()
    {
        return view('livewire.dump');
    }
}

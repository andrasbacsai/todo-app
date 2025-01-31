<?php

namespace App\Livewire\Forms;

use App\Events\TodoUpdated;
use App\Models\Todo;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Component;

class TodoInput extends Component
{
    #[Validate('required|string|max:255')]
    public $title = '';

    public $placeholder = 'Enter task name';

    #[Locked]
    public $isDump = false;

    #[Locked]
    public $mode = 'create';

    #[Locked]
    public $todoId = null;

    #[Locked]
    public $target = null;

    #[Locked]
    public $autoSaveEnabled = false;

    public function mount($title = '', $isDump = false, $mode = 'create', $todoId = null, $target = null, $autoSaveEnabled = false)
    {
        $this->title = $title;
        $this->isDump = $isDump;
        $this->mode = $mode;
        $this->todoId = $todoId;
        $this->target = $target;
        $this->autoSaveEnabled = $autoSaveEnabled;
    }

    public function getListeners()
    {
        return [
            'todo-saved' => 'resetInput',
        ];
    }

    public function resetInput()
    {
        if ($this->mode === 'create') {
            $this->title = '';
        }
    }

    public function handleSubmit()
    {
        try {
            $this->validate();
            if ($this->mode === 'edit' && $this->todoId) {
                $todo = Todo::getOwnTodo($this->todoId);
                $todo->title = $this->title;
                $todo->save();
            } else {
                Todo::create([
                    'title' => $this->title,
                    'worked_at' => $this->isDump ? null : now(),
                ]);
            }
            $this->dispatch('hashtags-updated');
            $this->dispatch('todos-updated');
            $this->resetInput();
        } catch (\Exception $e) {
            toast()->danger($e->getMessage())->push();
        }
    }

    public function render()
    {
        return view('livewire.forms.todo-input');
    }
}


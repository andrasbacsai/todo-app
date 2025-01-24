<?php

namespace App\Livewire\Forms;

use App\Events\TodoUpdated;
use App\Models\Hashtag;
use App\Models\Todo;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class TodoInput extends Component
{
    #[Validate('required|string|max:255')]
    public $title = '';

    public $placeholder = 'Enter task name';

    public $isDump = false;

    public $mode = 'create';

    public $todoId = null;

    public $allHashtags = [];

    public function mount($title = '', $isDump = false, $mode = 'create', $todoId = null)
    {
        $this->title = $title;
        $this->isDump = $isDump;
        $this->mode = $mode;
        $this->todoId = $todoId;
        $this->loadHashtags();
    }

    public function loadHashtags()
    {
        $this->allHashtags = Hashtag::where('user_id', Auth::id())->pluck('name')->toArray();
    }

    public function getListeners()
    {
        return [
            'todo-saved' => 'resetInput',
            'hashtags-updated' => 'loadHashtags',
        ];
    }

    public function refreshHashtags()
    {
        $this->loadHashtags();
        $this->dispatch('$refresh');
    }

    public function resetInput()
    {
        if ($this->mode === 'create') {
            $this->title = '';
        }
        $this->loadHashtags();
    }

    public function handleSubmit($title)
    {
        $this->title = $title;
        $this->validate();

        try {
            if ($this->mode === 'edit' && $this->todoId) {
                $todo = Todo::find($this->todoId);
                if (! $todo) {
                    throw new \Exception('Todo not found');
                }

                $todo->update([
                    'title' => Todo::cleanTitle($title),
                ]);
                $todo->syncHashtags($title);
                $this->loadHashtags();
                $this->dispatch('hashtags-updated');
            } else {
                $todo = Todo::create([
                    'title' => Todo::cleanTitle($title),
                    'worked_at' => $this->isDump ? null : now(),
                ]);
                $todo->syncHashtags($title);
                $this->loadHashtags();
                $this->dispatch('hashtags-updated');
            }

            broadcast(new TodoUpdated(Auth::id()))->toOthers();
            $this->dispatch('todos-updated');
            $this->resetInput();
        } catch (\Exception $e) {
            toast()->danger($e->getMessage())->push();
        }
    }

    public function render()
    {
        return view('livewire.forms.todo-input', [
            'allHashtags' => $this->allHashtags,
        ]);
    }
}

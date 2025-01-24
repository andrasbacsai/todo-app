<?php

namespace App\Livewire;

use App\Events\TodoUpdated;
use App\Models\Hashtag;
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

    public $hashtagSuggestions = [];

    public $showHashtagSuggestions = false;

    public $editingTodoId = null;

    public $editingTitle = '';

    public $editingDescription = '';

    public function getListeners()
    {
        $userId = Auth::user()->id;

        return [
            "echo-private:user.{$userId},TodoUpdated" => 'refreshTodos',
            'todos-updated' => 'refreshTodos',
            'title-updated' => 'updateTitle',
            'todo-input-submit' => 'addTodo',
            'hashtags-updated' => 'refreshTodos',
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

    public function updatedTitle($value)
    {
        $this->updateHashtagSuggestions($value);
    }

    protected function updateHashtagSuggestions($value)
    {
        // Find the hashtag being typed
        $position = strrpos($value, '#');
        if ($position === false) {
            $this->hashtagSuggestions = [];
            $this->showHashtagSuggestions = false;

            return;
        }

        $query = substr($value, $position + 1);
        if (empty($query)) {
            $this->hashtagSuggestions = [];
            $this->showHashtagSuggestions = false;

            return;
        }

        // Only show suggestions if we're actually typing a hashtag
        if (preg_match('/^[\w\-]+$/', $query)) {
            $this->hashtagSuggestions = Hashtag::search($query);
            $this->showHashtagSuggestions = ! empty($this->hashtagSuggestions);
        } else {
            $this->hashtagSuggestions = [];
            $this->showHashtagSuggestions = false;
        }
    }

    public function selectHashtag($hashtag)
    {
        // Replace the current hashtag being typed with the selected one
        $position = strrpos($this->title, '#');
        if ($position !== false) {
            $this->title = substr($this->title, 0, $position).'#'.$hashtag.' ';
        }
        $this->showHashtagSuggestions = false;
    }

    public function addTodo($title = null)
    {
        try {
            $this->validate();
            $todo = Todo::create([
                'title' => Todo::cleanTitle($title ?? $this->title),
                'worked_at' => null,
            ]);
            $todo->syncHashtags($title ?? $this->title);
            $this->title = '';
            $this->dispatch('todo-saved');
            $this->refreshTodos();
        } catch (\Exception $e) {
            toast()->danger($e->getMessage())->push();
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
            $todo = Todo::getOwnTodo($id);
            $todo->delete();
            $this->refreshTodos();
            $this->dispatch('todo-saved');
        } catch (\Exception $e) {
            toast()->danger($e->getMessage())->push();
        }
    }

    public function updateTodo()
    {
        try {
            $todo = Todo::find($this->editingTodoId);
            if (! $todo) {
                throw new \Exception('Todo not found');
            }

            $todo->update([
                'title' => Todo::cleanTitle($this->editingTitle),
                'description' => $this->editingDescription,
                'status' => $this->todos->where('id', $this->editingTodoId)->first()->status,
            ]);

            $todo->syncHashtags($this->editingTitle);

            $this->editingTodoId = null;
            $this->editingTitle = '';
            $this->editingDescription = '';

            $this->refreshTodos();
            $this->dispatch('todo-saved');
            $this->dispatch('hashtags-updated');
        } catch (\Exception $e) {
            toast()->danger($e->getMessage())->push();
        }
    }

    public function dehydrate()
    {
        $this->title = '';
    }

    public function render()
    {
        return view('livewire.dump');
    }
}

<?php

namespace App\Livewire;

use App\Events\TodoUpdated;
use App\Models\Hashtag;
use App\Models\Todo;
use App\Settings\InstanceSettings;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Usernotnull\Toast\Concerns\WireToast;

class Dashboard extends Component
{
    use WireToast;

    #[Title('Todos')]
    public $isPaymentEnabled;

    #[Validate('required|string|min:3|max:255')]
    public $title = '';

    #[Locked]
    public $todos;

    #[Locked]
    public $backlogTodos;

    #[Locked]
    public $completedTodos;

    #[Locked]
    public $previousUndoneTodos;

    public $editingTodoId = null;

    public $editingTitle = '';

    public $editingDescription = '';

    public $hashtagSuggestions = [];

    public $showHashtagSuggestions = false;

    public function getListeners()
    {
        $userId = Auth::user()->id;

        return [
            "echo-private:user.{$userId},TodoUpdated" => 'refreshTodos',
            'title-updated' => 'updateTitle',
            'todo-input-submit' => 'addTodo',
            'hashtags-updated' => 'refreshTodos',
            'todos-updated' => 'refreshTodos',
        ];
    }

    public function updateTitle($title)
    {
        $this->title = $title;
    }

    public function mount(InstanceSettings $instanceSettings)
    {
        $this->isPaymentEnabled = $instanceSettings->is_payment_enabled;
        $this->refreshTodos();
    }

    public function refreshTodos()
    {
        $this->todos = Todo::getTodayTodos();
        $this->backlogTodos = $this->todos->where('status', '!=', 'completed');
        $this->completedTodos = $this->todos->where('status', 'completed');
        $this->previousUndoneTodos = Todo::getYesterdayUndoneTodos();
    }

    public function transferYesterdayTodos($id)
    {
        try {
            $todo = Todo::getYesterdayUndoneTodos()->where('id', $id)->first();
            if (! $todo) {
                return;
            }
            $todo->worked_at = now();
            $todo->save();
            $this->refreshTodos();
        } catch (\Exception $e) {
            toast()->danger($e->getMessage())->push();
        }
    }

    public function addTodo($title = null)
    {
        try {
            $this->validate();
            $todo = Todo::create([
                'title' => Todo::cleanTitle($title ?? $this->title),
                'worked_at' => now(),
            ]);
            $todo->syncHashtags($title ?? $this->title);
            $this->title = '';
            $this->dispatch('todo-saved');
            $this->refreshTodos();
        } catch (\Exception $e) {
            toast()->danger($e->getMessage())->push();
        }
    }

    public function addToDump($id)
    {
        try {
            $todo = $this->todos->where('id', $id)->first();
            $todo->worked_at = null;
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

    public function switchTodoStatus($id)
    {
        try {
            $todo = Todo::find($id);
            if (! $todo) {
                throw new \Exception('Todo not found');
            }

            Todo::updateTodo($id, [
                'title' => $todo->title,
                'description' => $todo->description,
                'status' => $todo->status === 'completed' ? 'backlog' : 'completed',
            ]);

            $this->refreshTodos();
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

    public function logout()
    {
        Auth::logout();

        return redirect()->route('login');
    }

    public function render()
    {

        return view('livewire.dashboard');
    }

    #[Computed]
    public function completedCount()
    {
        return $this->completedTodos->count();
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

    public function transferAllYesterdayTodos()
    {
        try {
            Todo::transferUndoneYesterdayTodos();
            $this->refreshTodos();
            toast()->success('All yesterday\'s todos transferred to today')->push();
        } catch (\Exception $e) {
            toast()->danger($e->getMessage())->push();
        }
    }

    public function dehydrate()
    {
        $this->title = '';
    }
}

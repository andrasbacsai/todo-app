<?php

namespace App\Livewire;

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

    #[Title('Dashboard')]
    public $isPaymentEnabled;

    #[Validate('required|string|min:3|max:255')]
    public $title;

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

    public function getListeners()
    {
        $userId = Auth::user()->id;

        return [
            "echo-private:user.{$userId},TodoUpdated" => 'refreshTodos',
        ];
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

    public function addTodo()
    {
        try {
            $this->validate();
            Todo::create([
                'title' => $this->title,
                'worked_at' => now(),
            ]);
            $this->refreshTodos();
            $this->title = '';
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
            $todo = Todo::getTodayTodos()->where('id', $id)->first();
            if (! $todo) {
                return;
            }

            $todo->delete();
            $this->refreshTodos();
        } catch (\Exception $e) {
            toast()->danger('Todo not found')->push();
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
}

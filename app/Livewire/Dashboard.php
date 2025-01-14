<?php

namespace App\Livewire;

use App\Models\Todo;
use App\Settings\InstanceSettings;
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

    public array $updatedTodos = [];

    public function mount(InstanceSettings $instanceSettings)
    {
        $this->isPaymentEnabled = $instanceSettings->is_payment_enabled;

        $this->refreshTodos();

    }

    private function getUpdatedTitle()
    {
        return collect($this->todos)->mapWithKeys(function ($todo) {
            return [$todo['id'] => [
                'title' => $todo['title'],
                'description' => $todo['description'],
                'status' => $todo['status'],
            ]];
        })->toArray();
    }

    public function transferYesterdayTodos()
    {
        try {
            Todo::transferYesterdayTodos();
            $this->refreshTodos();
        } catch (\Exception $e) {
            toast()->danger($e->getMessage())->push();
        }
    }

    public function refreshTodos()
    {
        $this->todos = Todo::getTodayTodos();
        $this->backlogTodos = $this->todos->where('status', '!=', 'completed');
        $this->completedTodos = $this->todos->where('status', 'completed');
        $this->updatedTodos = $this->getUpdatedTitle();
    }

    public function addTodo()
    {
        try {
            $this->validate();
            Todo::create([
                'title' => $this->title,
                'user_id' => auth()->user()->id,
            ]);
            $this->refreshTodos();
            $this->title = '';
        } catch (\Exception $e) {
            // toast()->danger($e->getMessage())->push();
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
            $todo->delete();
            $this->refreshTodos();
        } catch (\Exception $e) {
            toast()->danger('Todo not found')->push();
        }
    }

    public function switchTodoStatus($id)
    {
        try {
            $updateTodo = $this->updatedTodos[$id];
            $updateTodo['status'] = $updateTodo['status'] === 'completed' ? 'backlog' : 'completed';
            Todo::updateTodo($id, $updateTodo);
            $this->refreshTodos();
        } catch (\Exception $e) {
            toast()->danger($e->getMessage())->push();
        }
    }

    public function updateTodo($id)
    {
        try {
            $updateTodo = $this->updatedTodos[$id];
            Todo::updateTodo($id, $updateTodo);
            $this->refreshTodos();
        } catch (\Exception $e) {
            toast()->danger($e->getMessage())->push();
        }
    }

    public function logout()
    {
        auth()->logout();

        return redirect()->route('login');
    }

    public function render()
    {

        return view('livewire.dashboard');
    }
}

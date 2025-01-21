<?php

namespace App\Livewire;

use App\Models\Todo as ModelTodo;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Usernotnull\Toast\Concerns\WireToast;

class Todo extends Component
{
    use WireToast;

    #[Title('Todo')]
    public ModelTodo $todo;

    #[Validate('required')]
    public $title;

    #[Validate('max:10000')]
    public $description;

    public $showPreview = false;

    public $taskStates = [];

    public function updatedDescription($value)
    {
        $this->todo->update(['description' => $value]);
        $this->dispatch('description-updated');
    }

    protected function renderMarkdown($text)
    {
        if (! $text) {
            return '';
        }

        // First pass: Convert task list items to a special format
        $lines = explode("\n", $text);
        foreach ($lines as $i => $line) {
            if (preg_match('/^- \[([ x])\]( .*)?$/', $line, $matches)) {
                $checked = $matches[1] === 'x';
                $content = isset($matches[2]) ? trim($matches[2]) : '';
                $lines[$i] = '@@TASK_START@@'.($checked ? '1' : '0').'@@'.$content.'@@TASK_END@@';
            }
        }
        $text = implode("\n", $lines);

        // Convert markdown to HTML
        $html = Str::markdown($text, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        // Remove wrapping paragraph tags and empty paragraphs
        $html = preg_replace(['/^<p>/', '/<\/p>$/', '/<p>\s*<\/p>/'], '', $html);

        // Second pass: Replace our special format with proper checkbox HTML
        return preg_replace_callback(
            '/@@TASK_START@@(0|1)@@(.*)@@TASK_END@@/',
            function ($matches) {
                static $lineNumber = 0;
                $lineNumber++;

                $checked = $matches[1] === '1' ? 'checked' : '';
                $content = $matches[2];
                $taskId = md5($content.'_line_'.$lineNumber);

                return "<div class=\"task-list-item-wrapper\"><div class=\"task-list-item\">
                    <input type=\"checkbox\" wire:model.live=\"taskStates.{$taskId}\" {$checked}>
                    <span>{$content}</span>
                </div></div>";
            },
            $html
        );
    }

    public function updatedTaskStates($value, $taskId)
    {
        if (! $this->description) {
            return;
        }

        $lines = explode("\n", $this->description);
        $lineNumber = 0;
        foreach ($lines as $i => $line) {
            if (preg_match('/^- \[([ x])\]( .*)?$/', $line, $matches)) {
                $lineNumber++;
                if (md5((isset($matches[2]) ? trim($matches[2]) : '').'_line_'.$lineNumber) === $taskId) {
                    $lines[$i] = '- ['.($value ? 'x' : ' ').']'.(isset($matches[2]) ? $matches[2] : '');
                    break;
                }
            }
        }

        $this->description = implode("\n", $lines);
        $this->todo->update(['description' => $this->description]);
    }

    public function mount($id)
    {
        try {
            $this->todo = ModelTodo::getOwnTodo($id);
            $this->title = $this->todo->title;
            $this->description = $this->todo->description;
            $this->taskStates = [];

            // Initialize task states
            if ($this->description) {
                $lineNumber = 0;
                foreach (explode("\n", $this->description) as $line) {
                    if (preg_match('/^- \[([ x])\]( .*)?$/', $line, $matches)) {
                        $lineNumber++;
                        $taskId = md5((isset($matches[2]) ? trim($matches[2]) : '').'_line_'.$lineNumber);
                        $this->taskStates[$taskId] = $matches[1] === 'x';
                    }
                }
            }
        } catch (\Exception $e) {
            return redirect()->route('dashboard');
        }
    }

    public function updateTodo()
    {
        try {
            $this->validate();
            $this->todo->update([
                'title' => $this->title,
                'description' => $this->description,
            ]);
            toast()->success('Todo updated')->push();
        } catch (\Exception $e) {
            toast()->danger($e->getMessage())->push();
        }
    }

    public function render()
    {
        return view('livewire.todo', [
            'renderedMarkdown' => $this->description ? $this->renderMarkdown($this->description) : '',
        ]);
    }
}

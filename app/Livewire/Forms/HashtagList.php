<?php

namespace App\Livewire\Forms;

use App\Models\Todo;
use Livewire\Component;

class HashtagList extends Component
{
    public Todo $todo;

    public bool $clickable = true;

    public function getListeners()
    {
        return [
            'hashtags-updated' => '$refresh',
        ];
    }

    public function removeHashtag($hashtagId)
    {
        if (! $this->clickable) {
            return;
        }

        // Get the hashtag before detaching
        $hashtag = $this->todo->hashtags()->find($hashtagId);
        if (!$hashtag) {
            return;
        }

        // Detach the hashtag from the todo
        $this->todo->hashtags()->detach($hashtagId);
        
        // If this was the last todo using this hashtag, delete it
        if ($hashtag->todos()->count() === 0) {
            $hashtag->delete();
        }

        $this->todo->refresh();

        // Dispatch event to refresh parent components
        $this->dispatch('hashtags-updated');
    }

    public function render()
    {
        return view('livewire.forms.hashtag-list');
    }
}

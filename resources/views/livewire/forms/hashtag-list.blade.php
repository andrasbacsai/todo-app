<div class="flex flex-wrap gap-1 px-1">
    @foreach ($todo->hashtags as $hashtag)
        <button type="button" wire:confirm="Are you sure you want to remove #{{ $hashtag->name }}?"
            @if ($clickable) wire:click="removeHashtag('{{ $hashtag->id }}')"
                class="text-primary text-sm hover:text-destructive transition-colors duration-200"
            @else
                class="text-primary text-sm" @endif>
            #{{ $hashtag->name }}
        </button>
    @endforeach
</div>

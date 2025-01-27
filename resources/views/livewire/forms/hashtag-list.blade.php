<div class="flex flex-wrap gap-1 px-1">
    @foreach ($todo->hashtags as $hashtag)
        @if ($clickable)
            <button type="button" wire:confirm="Are you sure you want to remove #{{ $hashtag->name }}?"
                wire:key="hashtag-{{ $hashtag->id }}" wire:click="removeHashtag('{{ $hashtag->id }}')"
                class="text-muted-foreground text-sm hover:text-destructive">
                #{{ $hashtag->name }}
            </button>
        @else
            <span class="text-muted-foreground text-sm">
                #{{ $hashtag->name }}
            </span>
        @endif
    @endforeach
</div>

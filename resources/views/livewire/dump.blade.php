<div class="flex flex-col justify-center items-center">
    <div class="flex-1 w-full max-w-full lg:max-w-2xl">
        <x-form wire:submit="addTodo" :class="$todos->count() > 0 ? 'pb-10' : ''">
            <livewire:forms.todo-input :isDump="true" :title="$title" />
            <x-button type="submit" class="hidden">Add todo</x-button>
        </x-form>
        <div class="flex flex-col space-y-4">
            @forelse ($todos as $todo)
                <div class="flex justify-between items-center px-2 group transition-all duration-150 space-x-2"
                    wire:key="todo-{{ $todo->id }}">
                    <a href="{{ route('todo', $todo->id) }}" wire:navigate
                        class="w-full cursor-pointer select-none px-2 hover:border-primary hover:border-l-2 border-l-2 border-transparent">
                        <p>{{ Str::limit($todo->title, 50) }}</p>
                        @if (filled($todo->description))
                            <p class="text-muted-foreground text-xs min-h-4">
                                {{ Str::limit($todo->description, 30) }}
                            </p>
                        @endif
                        <livewire:forms.hashtag-list :todo="$todo" :wire:key="'hashtags-'.$todo->id"
                            :clickable="false" />
                    </a>
                    <div>
                        <x-button variant="link" size="sm"
                            class="p-1 px-2 text-muted-foreground hover:text-foreground group-hover:opacity-100 opacity-70"
                            wire:click="addToToday('{{ $todo->id }}')"><x-lucide-alarm-clock-plus
                                class="size-4" /></x-button>
                    </div>
                    <div>
                        <x-button size="sm" variant="link"
                            wire:confirm="Are you sure you want to delete this todo?"
                            class="text-muted-foreground hover:text-destructive p-1 px-2 group-hover:opacity-100 opacity-70"
                            wire:click="deleteTodo('{{ $todo->id }}')"><x-lucide-trash class="size-4" /></x-button>
                    </div>
                </div>
            @empty
                <p class="text-muted-foreground text-xs p-2">No tasks. Nice work!</p>
            @endforelse
        </div>
    </div>
</div>

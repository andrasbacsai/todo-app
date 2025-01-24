<div>
    <div class="flex flex-col justify-center items-center">
        <div class="flex-1 w-full max-w-full lg:max-w-2xl">
            <x-form wire:submit="addTodo" :class="$todos->count() > 0 ? 'pb-10' : ''">
                <livewire:forms.todo-input :isDump="true" />
                <x-button type="submit" class="hidden">Add todo</x-button>
            </x-form>
            <div class="flex flex-col space-y-2">
                @forelse ($todos as $todo)
                    <div class="flex justify-between items-center px-2 group transition-all duration-150 space-x-4"
                        wire:key="todo-{{ $todo->id }}">
                        <a href="{{ route('todo', $todo->id) }}" wire:navigate
                            class="w-full cursor-pointer select-none px-2 hover:bg-muted/50">
                            <p>{{ $todo->title }}</p>
                            <livewire:forms.hashtag-list :todo="$todo" :wire:key="'hashtags-'.$todo->id"
                                :clickable="false" />
                            @if (filled($todo->description))
                                <p class="text-muted-foreground text-xs min-h-4">
                                    {{ Str::limit($todo->description, 30) }}
                                </p>
                            @endif
                        </a>

                        <x-tooltip>
                            <x-tooltip.trigger>
                                <x-button variant="link" size="sm"
                                    class="p-1 px-2 text-muted-foreground hover:text-foreground group-hover:opacity-100 opacity-70"
                                    wire:click="addToToday('{{ $todo->id }}')"><x-lucide-alarm-clock-plus
                                        class="h-4 w-4" /></x-button>
                            </x-tooltip.trigger>
                            <x-tooltip.content>
                                <p>Add to today</p>
                            </x-tooltip.content>
                        </x-tooltip>
                        <x-tooltip>
                            <x-tooltip.trigger>
                                <x-button size="sm" variant="link"
                                    wire:confirm="Are you sure you want to delete this todo?"
                                    class="text-muted-foreground hover:text-destructive p-1 px-2 group-hover:opacity-100 opacity-70"
                                    wire:click="deleteTodo('{{ $todo->id }}')"><x-lucide-trash
                                        class="size-4" /></x-button>
                            </x-tooltip.trigger>
                            <x-tooltip.content>
                                <p>Delete</p>
                            </x-tooltip.content>
                        </x-tooltip>
                    </div>
                @empty
                    <p class="text-muted-foreground text-xs p-2">No tasks. Nice work!</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

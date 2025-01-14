<div>
    <div class="flex flex-col justify-center items-center">
        <div class="flex-1 w-full max-w-full lg:max-w-2xl">
            <x-form wire:submit="addTodo" class="pb-2">
                <x-form.input name="title" class="w-full" wire:model="title" placeholder="Enter task name" type="text"
                    copy="false" label="" />
                <x-button type="submit" class="hidden">Add todo</x-button>
            </x-form>
            <div class="flex flex-col space-y-2">
                @forelse ($todos as $todo)
                    <div class="flex justify-between items-center px-2 hover:bg-muted/50 transition-all duration-150"
                        wire:key="todo-{{ $todo->id }}">
                        <div class="flex flex-col">
                            <p>{{ $todo->title }}</p>
                            <p class="text-muted-foreground text-xs min-h-4">{{ Str::limit($todo->description, 30) }}
                            </p>
                        </div>
                        <x-button variant="link" size="sm" class="p-1 px-2"
                            wire:click="addToToday('{{ $todo->id }}')"><x-lucide-alarm-clock-plus
                                class="h-4 w-4 text-muted-foreground hover:text-foreground" /></x-button>
                    </div>
                @empty
                    <p class="text-muted-foreground text-xs p-2">No tasks. Nice work!</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

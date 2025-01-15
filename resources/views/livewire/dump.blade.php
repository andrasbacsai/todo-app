<div>
    <div class="flex flex-col justify-center items-center">
        <div class="flex-1 w-full max-w-full lg:max-w-2xl">
            <x-form wire:submit="addTodo" :class="$todos->count() > 0 ? 'pb-10' : ''">
                <x-form.input name="title" class="w-full" wire:model="title" placeholder="Enter task name" type="text"
                    copy="false" label="" />
                <x-button type="submit" class="hidden">Add todo</x-button>
            </x-form>
            <div class="flex flex-col space-y-2">
                @forelse ($todos as $todo)
                    <div class="flex justify-between items-center px-2 hover:bg-muted/50 transition-all duration-150"
                        wire:key="todo-{{ $todo->id }}">
                        <div class="w-full cursor-pointer select-none px-2">
                            <p>{{ $todo->title }}</p>
                            @if (filled($todo->description))
                                <p class="text-muted-foreground text-xs min-h-4">
                                    {{ Str::limit($todo->description, 30) }}
                                </p>
                            @endif
                        </div>
                        <x-dialog class="w-full text-right">
                            <x-dialog.trigger variant="link" size="sm" class="p-1 px-2"
                                @click="
                                    $wire.editingTodoId = '{{ $todo->id }}';
                                    $wire.editingTitle = `{{ addslashes($todo->title) }}`;
                                    $wire.editingDescription = `{{ addslashes($todo->description) }}`;
                                    $dispatch('open-modal', 'edit-todo-{{ $todo->id }}');
                                ">
                                <x-lucide-pencil class="size-4 text-muted-foreground hover:text-foreground" />
                            </x-dialog.trigger>
                            <x-dialog.content class="sm:max-w-[425px]" x-dialog:name="edit-todo-{{ $todo->id }}">
                                <x-form wire:submit="updateTodo" class="flex flex-col space-y-2 text-foreground">
                                    <x-dialog.header>
                                        <x-dialog.title>Edit Todo</x-dialog.title>
                                    </x-dialog.header>
                                    <div class="flex flex-col space-y-2 py-2">
                                        <x-form.input copy="false" wire:model="editingTitle" type="text"
                                            label="" />
                                        <x-textarea copy="false" wire:model="editingDescription" type="text"
                                            label="" />
                                    </div>
                                    <x-dialog.footer>
                                        <x-button x-on:click="__dialogOpen = false" type="submit"
                                            variant="default">Save</x-button>
                                        <x-dialog.close>Cancel</x-dialog.close>
                                    </x-dialog.footer>
                                </x-form>
                            </x-dialog.content>
                        </x-dialog>
                        <x-button variant="link" size="sm" class="p-1 px-2"
                            wire:click="addToToday('{{ $todo->id }}')"><x-lucide-alarm-clock-plus
                                class="h-4 w-4 text-muted-foreground hover:text-foreground" /></x-button>
                        <x-tooltip>
                            <x-tooltip.trigger>
                                <x-button size="sm" variant="link"
                                    class="text-muted-foreground hover:text-destructive p-1 px-2"
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

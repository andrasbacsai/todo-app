<div>
    <div class="flex flex-col justify-center items-center">
        <div class="flex-1 w-full max-w-full lg:max-w-2xl">
            <x-form wire:submit="addTodo" class="pb-2">
                <x-form.input name="title" class="w-full" wire:model="title" placeholder="Enter task name" type="text"
                    copy="false" label="" />
                <x-button type="submit" class="hidden">Add todo</x-button>
            </x-form>

            @forelse ($backlogTodos as $todo)
                <div class="flex items-center hover:bg-muted transition-all duration-150"
                    wire:key="todo-{{ $todo->id }}">
                    <div wire:click="switchTodoStatus('{{ $todo->id }}')"
                        class="w-full cursor-pointer select-none px-2">
                        {{ $todo->title }}
                    </div>
                    <div class="flex items-center space-x-0 gap-0">
                        <x-dialog class="w-full text-right">
                            <x-dialog.trigger variant="link" size="sm" class="p-1 px-2">
                                <x-lucide-pencil class="h-4 w-4 text-muted-foreground hover:text-foreground" />
                            </x-dialog.trigger>
                            <x-dialog.content class="sm:max-w-[425px]">
                                <x-form wire:submit="updateTodo('{{ $todo->id }}')"
                                    class="flex flex-col space-y-2 text-foreground">
                                    <x-dialog.header>
                                        <x-dialog.title>
                                            Edit Todo
                                        </x-dialog.title>
                                    </x-dialog.header>
                                    <div class="flex flex-col space-y-2 py-2">
                                        <x-form.input copy="false" wire:model="updatedTodos.{{ $todo->id }}.title"
                                            type="text" label="" />
                                        <x-textarea copy="false"
                                            wire:model="updatedTodos.{{ $todo->id }}.description" type="text"
                                            label="" />
                                    </div>
                                    <x-dialog.footer>
                                        <x-button x-on:click="__dialogOpen = false" type="submit"
                                            variant="default">Save
                                        </x-button>
                                        <x-dialog.close>Cancel</x-dialog.close>
                                    </x-dialog.footer>
                                </x-form>
                            </x-dialog.content>
                        </x-dialog>
                        <x-button size="sm" variant="link"
                            class="text-muted-foreground hover:text-destructive p-1 px-2"
                            wire:click="addToDump('{{ $todo->id }}')"><x-lucide-alarm-clock-minus
                                class="h-4 w-4 text-muted-foreground hover:text-foreground" /></x-button>
                        <x-button size="sm" variant="link"
                            class=" text-muted-foreground hover:text-destructive p-1 px-2"
                            wire:click="deleteTodo('{{ $todo->id }}')"><x-lucide-trash class="h-4 w-4" /></x-button>
                    </div>
                </div>
            @empty
                <p class="text-muted-foreground text-xs p-2">No todos for today</p>
            @endforelse
            <x-accordion type="single" collapsible wire:key="completed-todos">
                @if ($this->completedCount > 0)
                    <x-accordion.item value="todos" class="border-b-0">
                        <x-accordion.trigger class="hover:no-underline">Completed
                            ({{ $this->completedCount }})</x-accordion.trigger>
                        <x-accordion.content class="text-md">
                            <div class="flex flex-col">
                                @foreach ($completedTodos as $todo)
                                    <div class="flex justify-between items-center px-2 py-1 cursor-pointer line-through hover:bg-muted/50 transition-all duration-150"
                                        wire:key="completed-todo-{{ $todo->id }}"
                                        wire:click="switchTodoStatus('{{ $todo->id }}')">
                                        {{ $todo->title }}
                                    </div>
                                @endforeach
                            </div>
                        </x-accordion.content>
                    </x-accordion.item>
                @endif
            </x-accordion>
            @if ($previousUndoneTodos->count() > 0)
                <x-button size="sm" variant="outline" wire:click="transferYesterdayTodos">Transfer yesterday's
                    todos</x-button>
            @endif
        </div>
    </div>
</div>

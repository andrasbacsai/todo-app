<div>
    <div class="flex flex-col space-y-8 lg:flex-row lg:space-x-12 lg:space-y-0">
        <x-aside />
        <div class="flex-1 lg:max-w-2xl">
            <x-form wire:submit="addTodo">
                <x-form.input name="title" class="w-full" wire:model="title" placeholder="New todo for today"
                    type="text" copy="false" label="" />
                <x-button type="submit" class="hidden">Add todo</x-button>
            </x-form>

            @forelse ($backlogTodos as $todo)
                <div class="flex justify-between items-center space-y-2  space-x-2" wire:key="todo-{{ $todo->id }}">
                    <div wire:click="switchTodoStatus('{{ $todo->id }}')"
                        class=" w-full cursor-pointer select-none {{ $todo->status === 'completed' ? 'line-through' : '' }}">
                        {{ $todo->title }}
                    </div>
                    <x-dialog class="w-full text-right">
                        <x-dialog.trigger size="sm" variant="outline">
                            Edit
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
                                    <x-textarea copy="false" wire:model="updatedTodos.{{ $todo->id }}.description"
                                        type="text" label="" />
                                </div>
                                <x-dialog.footer>
                                    <x-button type="submit" variant="default">Save
                                    </x-button>
                                    <x-dialog.close>Cancel</x-dialog.close>
                                </x-dialog.footer>
                            </x-form>
                        </x-dialog.content>
                    </x-dialog>
                    <x-button size="sm" variant="outline" wire:click="addToDump('{{ $todo->id }}')">Not
                        today</x-button>
                    <x-button size="sm" variant="destructive"
                        wire:click="deleteTodo('{{ $todo->id }}')">Delete</x-button>
                </div>
            @empty
                <p>No todos for today</p>
            @endforelse
            <x-accordion type="single" collapsible>
                <x-accordion.item value="item-1" class="border-b-0">
                    <x-accordion.trigger>Completed ({{ $completedTodos->count() }})</x-accordion.trigger>
                    <x-accordion.content class="text-md">
                        <div class="flex flex-col space-y-2">
                            @foreach ($completedTodos as $todo)
                                <div class="flex justify-between items-center space-y-2 px-2 space-x-2 cursor-pointer line-through"
                                    wire:key="todo-{{ $todo->id }}"
                                    wire:click="switchTodoStatus('{{ $todo->id }}')">
                                    <div>{{ $todo->title }}</div>
                                </div>
                            @endforeach
                        </div>
                    </x-accordion.content>
                </x-accordion.item>
            </x-accordion>
            <x-button size="sm" variant="outline" wire:click="transferYesterdayTodos">Transfer yesterday's
                todos</x-button>
        </div>
    </div>
</div>

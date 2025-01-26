<div class="flex flex-col justify-center items-center">
    <div class="flex-1 w-full max-w-full lg:max-w-2xl">
        <x-form wire:submit="addTodo" :class="$backlogTodos->count() > 0 ? 'pb-10' : ''">
            <livewire:forms.todo-input :title="$title" />
            <x-button type="submit" class="hidden">Add todo</x-button>
        </x-form>
        <div class="h-full">
            <div class="flex flex-col space-y-2">
                @forelse ($backlogTodos as $todo)
                    <div class="flex justify-between items-center px-2 group transition-all duration-150"
                        wire:key="todo-{{ $todo->id }}">
                        <a href="{{ route('todo', $todo->id) }}" wire:navigate
                            class="w-full cursor-pointer select-none px-2  hover:bg-muted/50">
                            <p>{{ $todo->title }}</p>
                            @if (filled($todo->description))
                                <p class="text-muted-foreground text-xs min-h-4">
                                    {{ Str::limit($todo->description, 30) }}
                                </p>
                            @endif
                            <livewire:forms.hashtag-list :todo="$todo" :wire:key="'hashtags-'.$todo->id"
                                :clickable="false" />
                        </a>

                        <div class="flex items-center space-x-4 gap-0">
                            <x-tooltip>
                                <x-tooltip.trigger>
                                    <x-button size="sm" variant="link"
                                        class=" p-1 px-2 group-hover:opacity-100 opacity-70 text-muted-foreground hover:text-green-500"
                                        wire:click="switchTodoStatus('{{ $todo->id }}')"><x-lucide-check-circle
                                            class="size-4" /></x-button>
                                </x-tooltip.trigger>
                                <x-tooltip.content>
                                    <p>Mark as done</p>
                                </x-tooltip.content>
                            </x-tooltip>
                            <x-tooltip>
                                <x-tooltip.trigger>
                                    <x-button size="sm" variant="link"
                                        class="text-muted-foreground hover:text-foreground p-1 px-2 group-hover:opacity-100 opacity-70"
                                        wire:click="addToDump('{{ $todo->id }}')"><x-lucide-alarm-clock-minus
                                            class="size-4" /></x-button>
                                </x-tooltip.trigger>
                                <x-tooltip.content>
                                    <p>Dump it</p>
                                </x-tooltip.content>
                            </x-tooltip>
                            <x-tooltip>
                                <x-tooltip.trigger>
                                    <x-button size="sm" variant="link"
                                        class="text-muted-foreground hover:text-destructive p-1 px-2 group-hover:opacity-100 opacity-70"
                                        wire:confirm="Are you sure you want to delete this todo?"
                                        wire:click="deleteTodo('{{ $todo->id }}')"><x-lucide-trash
                                            class="size-4" /></x-button>
                                </x-tooltip.trigger>
                                <x-tooltip.content>
                                    <p>Delete</p>
                                </x-tooltip.content>
                            </x-tooltip>

                        </div>
                    </div>
                @empty
                    <p class="text-muted-foreground text-xs p-2">No tasks for today. Relax!</p>
                @endforelse
            </div>
            <x-accordion type="single" collapsible wire:key="completed-todos">
                @if ($this->completedCount > 0)
                    <x-accordion.item value="todos" class="border-b-0">
                        <x-accordion.trigger
                            class="hover:no-underline text-xs text-muted-foreground font-normal px-2">Show
                            {{ $this->completedCount }} completed tasks</x-accordion.trigger>
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
            <x-accordion type="single" collapsible wire:key="yesterday-todos">
                @if ($this->previousUndoneTodos->count() > 0)
                    <x-accordion.item value="todos" class="border-b-0">
                        <x-accordion.trigger
                            class="hover:no-underline text-xs text-muted-foreground font-normal px-2">Show
                            {{ $this->previousUndoneTodos->count() }} undone tasks from
                            yesterday</x-accordion.trigger>
                        <x-accordion.content class="text-md">
                            <div class="flex flex-col">
                                @foreach ($previousUndoneTodos as $todo)
                                    <div class="flex justify-between items-center px-2 py-1 cursor-pointer hover:bg-muted/50 transition-all duration-150"
                                        wire:key="yesterday-todo-{{ $todo->id }}"
                                        wire:click="transferYesterdayTodos('{{ $todo->id }}')">
                                        {{ $todo->title }}
                                    </div>
                                @endforeach
                                <div class="flex justify-end px-2 py-2">
                                    <x-button size="sm" variant="outline" wire:click="transferAllYesterdayTodos">
                                        <x-lucide-alarm-clock-plus class="size-4 mr-2" />
                                        Add all to today
                                    </x-button>
                                </div>
                            </div>
                        </x-accordion.content>
                    </x-accordion.item>
                @endif
            </x-accordion>
        </div>
    </div>
</div>

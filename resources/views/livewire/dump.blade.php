<div>
    <div class="flex flex-col space-y-8 lg:flex-row lg:space-x-12 lg:space-y-0">
        <x-aside />
        <div class="flex-1 lg:max-w-2xl">
            <x-form wire:submit="addTodo">
                <x-form.input name="title" class="w-full" wire:model="title" placeholder="New dump todo" type="text"
                    copy="false" label="" />
                <x-button type="submit" class="hidden">Add todo</x-button>
            </x-form>
            @foreach ($todos as $todo)
                <div class="flex justify-between items-center space-y-2  space-x-2" wire:key="todo-{{ $todo->id }}">
                    <div class="flex flex-col">
                        <p>{{ $todo->title }}</p>
                        <p>{{ $todo->description }}</p>
                    </div>
                    <x-button size="sm" variant="outline" wire:click="addToToday('{{ $todo->id }}')">Do it
                        Today</x-button>
                </div>
            @endforeach
        </div>
    </div>
</div>

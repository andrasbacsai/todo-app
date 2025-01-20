<form class="flex flex-col space-y-2 py-2" wire:submit="updateTodo">
    <x-form.input copy="false" wire:model="title" type="text" label="" />
    <div class="flex items-center justify-end space-x-2 text-sm">
        <div class="flex items-center space-x-4">
            <div class="text-muted-foreground" wire:loading.remove wire:target="description">
                <x-lucide-check class="w-4 h-4" />
            </div>
            <div class="text-muted-foreground animate-pulse" wire:loading wire:target="description">
                <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
            </div>
            {{-- <div class="flex items-center space-x-2">
                <x-switch wire:click="$toggle('showPreview')" id="showPreview"
                    class="dark:has-[:checked]:bg-warning rounded-lg" wire:model="showPreview" />
                <x-label htmlFor="showPreview"
                    class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                    Show Preview
                </x-label>
            </div> --}}
        </div>
    </div>

    <div class="grid grid-cols-2 gap-2">
        <div class="h-full">
            <x-textarea copy="false" wire:model.live.debounce.500ms="description" rows="15" type="text"
                label="" @keydown.ctrl.enter="$wire.$refs.saveButton.click()"
                x-on:keydown.enter="
                    const textarea = $el;
                    const start = textarea.selectionStart;
                    const end = textarea.selectionEnd;
                    const value = textarea.value;
                    const lines = value.substring(0, start).split('\n');
                    const currentLine = lines[lines.length - 1];

                    if (currentLine.match(/^- \[[ x]\]( .*)?$/)) {
                        event.preventDefault();
                        const newValue = value.substring(0, start) + '\n- [ ] ' + value.substring(end);
                        $wire.set('description', newValue);

                        // Set cursor position after the checkbox
                        setTimeout(() => {
                            const newPosition = start + '\n- [ ] '.length;
                            textarea.setSelectionRange(newPosition, newPosition);
                        }, 0);
                    }
                "
                class="h-full min-h-[400px]" placeholder="Write your markdown here..." />
        </div>
        @if ($showPreview)
            <div class="markdown-body bg-muted/30 rounded-lg p-4 h-full min-h-[400px] overflow-y-auto">
                {!! $renderedMarkdown !!}
            </div>
        @endif
    </div>
    <x-button ref="saveButton" type="submit">Save</x-button>
</form>

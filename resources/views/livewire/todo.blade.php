<div>
    <div class="flex flex-col justify-center items-center">
        <div class="flex-1 w-full max-w-full lg:max-w-2xl">
            <livewire:forms.todo-input target="handleSubmit" :title="$todo->title" mode="edit" :todoId="$todo->id" />
            <form class="flex flex-col space-y-2 py-2" wire:submit="updateTodo">
                <div class="flex flex-col space-y-1">
                    <livewire:forms.hashtag-list :todo="$todo" />
                </div>
                <div class="grid gap-2 {{ $showPreview ? 'md:grid-cols-2' : 'grid-cols-1' }}">
                    @if ($showPreview)
                        <div class="markdown-body rounded-lg p-1 px-4 h-full min-h-[200px] overflow-y-auto">
                            {!! $renderedMarkdown !!}
                        </div>
                    @endif
                    <div class="h-full">
                        <x-textarea target="description" copy="false" wire:model.live.debounce.500ms="description"
                            type="text" label="" @keydown.ctrl.enter="$wire.$refs.saveButton.click()"
                            x-init="$el.style.overflow = 'hidden';
                            const adjustHeight = () => {
                                requestAnimationFrame(() => {
                                    $el.style.height = 'auto';
                                    $el.style.height = $el.scrollHeight + 'px';
                                });
                            };
                            adjustHeight();
                            $el.addEventListener('input', adjustHeight);
                            
                            // Handle all Livewire updates
                            document.addEventListener('livewire:initialized', () => {
                                Livewire.on('description-updated', adjustHeight);
                                $wire.$watch('description', adjustHeight);
                            });
                            
                            // Additional hooks for other Livewire events
                            document.addEventListener('livewire:update', adjustHeight);
                            document.addEventListener('livewire:load', adjustHeight);" style="min-height: 200px; resize: none;"
                            placeholder="Write your markdown here..."
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
                            x-on:keydown.ctrl.space="
                                event.preventDefault();
                                const textarea = $el;
                                const value = textarea.value;
                                const start = textarea.selectionStart;

                                // Find the start of the current line
                                const beforeCursor = value.substring(0, start);
                                const lineStart = beforeCursor.lastIndexOf('\n') + 1;

                                // Find the end of the current line
                                const afterCursor = value.substring(start);
                                const lineEnd = afterCursor.indexOf('\n');
                                const currentLineEnd = lineEnd === -1 ? value.length : start + lineEnd;

                                // Get the current line content
                                const currentLine = value.substring(lineStart, currentLineEnd);

                                // Check if line has a checkbox
                                const checkboxMatch = currentLine.match(/^(- \[)([x ])\](.*)/);
                                if (checkboxMatch) {
                                    // Toggle the checkbox
                                    const newCheckboxState = checkboxMatch[2] === ' ' ? 'x' : ' ';
                                    const newLine = `${checkboxMatch[1]}${newCheckboxState}]${checkboxMatch[3]}`;
                                    const newValue = value.substring(0, lineStart) + newLine + value.substring(currentLineEnd);
                                    $wire.set('description', newValue);
                                    $wire.$refresh();

                                    // Maintain cursor position
                                    setTimeout(() => {
                                        textarea.setSelectionRange(start, start);
                                    }, 0);
                                }
                            "
                            class="h-full min-h-[200px]" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div x-data="{
    title: $wire.entangle('title').defer,
    initialTitle: @js($title),
    timeout: null,
    isTypingHashtag: false,

    init() {
        if (this.initialTitle) {
            this.title = this.initialTitle;
        }
    },

    cleanupHashtags(title) {
        return title.replace(/#[\w\-]+\s*/g, '');
    },

    handleSubmit() {
        const currentTitle = this.title;
        if ($wire.mode === 'edit') {
            this.title = this.cleanupHashtags(currentTitle);
        }
        $wire.set('title', currentTitle);
        $wire.handleSubmit(currentTitle);
        if ($wire.mode === 'create') {
            this.title = '';
        }
        if ($wire.mode === 'edit') {
            $wire.dispatch('hashtags-updated');
        }
    },

    autoSave() {
        const lastChar = this.title.slice(-1);

        if (this.title.match(/#[\w\-]*$/)) {
            this.isTypingHashtag = true;
            return;
        }

        if (this.isTypingHashtag && lastChar === ' ') {
            this.isTypingHashtag = false;
        }

        if (!this.isTypingHashtag) {
            clearTimeout(this.timeout);
            this.timeout = setTimeout(() => {
                if (this.title !== this.initialTitle) {
                    this.handleSubmit();
                }
            }, 500);
        }
    }
}" class="relative">
    <x-form.input :target="$target" x-ref="input" name="title" class="w-full" x-model="title" :placeholder="$placeholder"
        type="text" copy="false" label="" x-on:keydown.enter.prevent="handleSubmit()" x-on:input="autoSave()"
        x-init="$el.focus();" />
</div>

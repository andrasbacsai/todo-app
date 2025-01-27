<div x-data="{
    title: $wire.entangle('title').defer,
    initialTitle: @js($title),
    timeout: null,
    isTypingHashtag: false,
    regexHashtags: @js($regexHashtags),
    autoSaveEnabled: @js($autoSaveEnabled),
    errorMessage: '',

    init() {
        if (this.initialTitle) {
            this.title = this.initialTitle;
        }
    },

    cleanupHashtags(title) {
        return title.replace(this.regexHashtags, ' ').trim();
    },

    handleSubmit() {
        const cleanTitle = this.cleanupHashtags(this.title).trim();
        // Don't submit if there's no actual content (only hashtags)
        if (!cleanTitle) {
            this.errorMessage = 'Title cannot be empty';
            return;
        }
        this.errorMessage = '';

        if ($wire.mode === 'edit') {
            this.title = this.title;
        }
        $wire.set('title', this.title);
        $wire.handleSubmit();
        if ($wire.mode === 'create') {
            this.title = '';
        }
        if ($wire.mode === 'edit') {
            $wire.dispatch('hashtags-updated');
        }
    },

    autoSave() {
        if (!this.autoSaveEnabled) {
            return;
        }

        const lastChar = this.title.slice(-1);

        if (this.title.match(/#[a-zA-Z0-9][a-zA-Z0-9\-_]*$/)) {
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
    <div x-show="errorMessage" x-text="errorMessage" class="text-red-500 text-sm mt-1"></div>
</div>

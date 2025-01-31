<div x-data="{
    title: @js($title),
    autoSaveEnabled: @js($autoSaveEnabled),
    regexHashtags: /#[a-zA-Z0-9][a-zA-Z0-9\-_]*/g,
    timeout: null,
    isTypingHashtag: false,
    errorMessage: '',

    cleanupHashtags(title) {
        return title.replace(this.regexHashtags, ' ');
    },

    handleSubmit() {
        const cleanTitle = this.cleanupHashtags(this.title);

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
            this.title = cleanTitle;
        }
    },

    autoSave() {
        if (!this.autoSaveEnabled) {
            return;
        }

        const lastChar = this.title.slice(-1);

        if (this.title.match(/#[a-zA-Z0-9][a-zA-Z0-9\-_]*$/)) {
            this.isTypingHashtag = true;
            clearTimeout(this.timeout);
            return;
        }

        if (this.isTypingHashtag && lastChar === ' ') {
            this.isTypingHashtag = false;
        }

        if (!this.isTypingHashtag) {
            clearTimeout(this.timeout);
            this.timeout = setTimeout(() => {
                this.handleSubmit();
            }, 1000);
        }
    }
}" class="relative">
    <x-form.input :target="$target" x-ref="input" name="title" class="w-full" x-model="title" :placeholder="$placeholder"
        type="text" copy="false" label="" x-on:keydown.enter.prevent="handleSubmit()" x-on:input="autoSave()"
        x-init="$el.focus();" />
    <template x-if="errorMessage">
        <span x-text="errorMessage" class="text-red-500 text-sm mt-1">
        </span>
    </template>
</div>

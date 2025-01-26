<div x-data="{
    title: $wire.entangle('title').defer,
    initialTitle: @js($title),

    init() {
        if (this.initialTitle) {
            this.title = this.initialTitle;
        }
    },

    cleanupHashtags(title) {
        return title.replace(/#[\w\-]+\s*/g, '').trim();
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
    }
}" class="relative">
    <x-form.input x-ref="input" name="title" class="w-full" x-model="title" :placeholder="$placeholder" type="text" copy="false"
        label="" x-on:keydown.enter.prevent="handleSubmit()" />
</div>

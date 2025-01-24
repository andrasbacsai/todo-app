<div x-data="{
    title: $wire.entangle('title').defer,
    initialTitle: @js($title),
    hashtags: $wire.entangle('allHashtags'),
    suggestions: [],
    showSuggestions: false,
    selectedIndex: 0,

    init() {
        if (this.initialTitle) {
            this.title = this.initialTitle;
        }

        this.$watch('title', (value) => {
            this.updateHashtagSuggestions(value);
            this.selectedIndex = 0;
        });

        this.$watch('hashtags', () => {
            if (this.title && this.title.includes('#')) {
                this.updateHashtagSuggestions(this.title);
            }
        });
    },

    updateHashtagSuggestions(value) {
        const position = value.lastIndexOf('#');
        if (position === -1) {
            this.suggestions = [];
            this.showSuggestions = false;
            return;
        }

        const query = value.substring(position + 1);
        if (!query) {
            this.suggestions = [];
            this.showSuggestions = false;
            return;
        }

        if (/^[\w\-]+$/.test(query)) {
            const currentHashtags = Array.isArray(this.hashtags) ? this.hashtags : [];
            const matchingTags = currentHashtags.filter(tag =>
                tag.toLowerCase().includes(query.toLowerCase())
            );

            if (!matchingTags.some(tag => tag.toLowerCase() === query.toLowerCase())) {
                matchingTags.unshift(query);
            }

            this.suggestions = matchingTags.slice(0, 5);
            this.showSuggestions = this.suggestions.length > 0;
        } else {
            this.suggestions = [];
            this.showSuggestions = false;
        }
    },

    selectHashtag(tag) {
        const position = this.title.lastIndexOf('#');
        if (position !== -1) {
            this.title = this.title.substring(0, position) + '#' + tag + ' ';
            this.showSuggestions = false;
            this.$refs.input.focus();
        }
    },

    handleKeyDown(event) {
        if (!this.showSuggestions) return;

        switch (event.key) {
            case 'ArrowDown':
                event.preventDefault();
                this.selectedIndex = (this.selectedIndex + 1) % this.suggestions.length;
                break;
            case 'ArrowUp':
                event.preventDefault();
                this.selectedIndex = (this.selectedIndex - 1 + this.suggestions.length) % this.suggestions.length;
                break;
            case 'Tab':
            case 'Enter':
                if (this.showSuggestions && this.suggestions.length > 0) {
                    event.preventDefault();
                    this.selectHashtag(this.suggestions[this.selectedIndex]);
                }
                break;
        }
    },

    cleanupHashtags(title) {
        return title.replace(/#[\w\-]+\s*/g, '').trim();
    },

    handleSubmit() {
        if (this.showSuggestions) {
            return;
        }

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
        label="" x-on:keydown.enter.prevent="handleSubmit()" x-on:keydown="handleKeyDown($event)" />

    <template x-if="showSuggestions">
        <div class="absolute z-10 w-full mt-1 bg-popover border rounded-lg shadow-lg">
            <template x-for="(tag, index) in suggestions" :key="tag">
                <button type="button" x-on:click="selectHashtag(tag)"
                    :class="{ 'bg-muted/50': selectedIndex === index }"
                    class="w-full px-4 py-2 text-left hover:bg-muted/50 first:rounded-t-lg last:rounded-b-lg"
                    x-text="'#' + tag">
                </button>
            </template>
        </div>
    </template>
</div>

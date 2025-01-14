<div x-data="{
    ...ToastComponent($wire),
    expanded: false,
    toastsHovered: false,
    filteredToasts() {
        return this.toasts.filter((a) => a)
    }
}" class="fixed bottom-0 right-0 z-50 p-4 w-full max-w-sm pointer-events-auto sm:p-6"
    style="z-index:999;">
    <div class="relative w-full h-full" @mouseleave="scheduleRemovalWithOlder(); toastsHovered = false"
        @mouseenter="toastsHovered = true">
        <template x-for="(toast, index) in filteredToasts()" :key="toast.index">
            <div @click="remove(toast.index)" x-show="toast.show===1" x-transition:enter="ease-out duration-300 transition"
                x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-10"
                x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" x-init="$nextTick(() => { toast.show = 1 })"
                class="absolute bottom-0 right-0 w-full transform transition-all duration-300 pointer-events-auto"
                :style="toastsHovered
                    ?
                    `transform: translateY(-${(filteredToasts().length - index - 1) * 80}px);
                                       padding-bottom: 20px;
                                       margin-bottom: -20px;
                                       width: 100%;` :
                    `transform: translateY(-${(filteredToasts().length - index) * 8}px);
                                       right: ${index === 0 ? 3 : index === 1 ? 2 : index === 2 ? 0 : 0}%;
                                       width: ${index === 0 ? 90 : index === 1 ? 95 : index === 2 ? 100 : 100}%;`">
                @include('tall-toasts::includes.content')
            </div>
        </template>
    </div>
</div>

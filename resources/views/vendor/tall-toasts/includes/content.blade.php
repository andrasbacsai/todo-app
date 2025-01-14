<div class="overflow-hidden px-3 py-2 bg-card border border-border cursor-pointer pointer-events-auto select-none"
    :class="{
        'border-l-info': toast.type === 'info',
        'border-l-success': toast.type === 'success',
        'border-l-warning': toast.type === 'warning',
        'border-l-destructive': toast.type === 'danger',
        'border-l-primary': toast.type === 'debug'
    }">
    <div class="flex items-center gap-2">

        <div class="flex-1">
            <div class="text-md text-foreground font-medium" x-show="toast.title !== undefined" x-html="toast.title">
            </div>
            <div class="text-sm text-muted-foreground" x-show="toast.message !== undefined" x-html="toast.message">
            </div>
        </div>

        {{-- <div class="text-foreground">
            <template x-if="toast.type === 'success'">
                <x-lucide-check class="size-5" />
            </template>
            <template x-if="toast.type === 'info'">
                <x-lucide-info class="size-5" />
            </template>
            <template x-if="toast.type === 'warning'">
                <x-lucide-alert-triangle class="size-5" />
            </template>
            <template x-if="toast.type === 'danger'">
                <x-lucide-alert-triangle class="size-5" />
            </template>
            <template x-if="toast.type === 'debug'">
                <x-lucide-bug class="size-5" />
            </template>
        </div> --}}
    </div>
</div>

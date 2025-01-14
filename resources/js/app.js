// import './bootstrap';

import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import ToastComponent from '../../vendor/usernotnull/tall-toasts/resources/js/tall-toasts'
import collapse from "@alpinejs/collapse";
import anchor from "@alpinejs/anchor";

document.addEventListener(
    "alpine:init",
    () => {
        const modules = import.meta.glob("./plugins/**/*.js", { eager: true });
        for (const path in modules) {
            window.Alpine.plugin(modules[path].default);
        }
        window.Alpine.plugin(collapse);
        window.Alpine.plugin(anchor);
        window.Alpine.plugin(ToastComponent);
    },
    { once: true },
);
Alpine.directive('clipboard', (el) => {
    let text = el.textContent

    el.addEventListener('click', () => {
        navigator.clipboard.writeText(text)
    })
})

Livewire.start()

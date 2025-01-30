
<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <main>
    <header>
      <a href="/">Jata (livewire)</a>
      <Link href="/i">Jata (inertia)</Link>
    </header>
    <slot />
  </main>
</template>

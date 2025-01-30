<script setup>
import Layout from './Layout.vue'
import { Head, router,useForm, Deferred } from '@inertiajs/vue3'

const props = defineProps({ user: Object, todos: Array })

Echo.private('user.' + props.user.id).listen('TodoUpdated', (e) => { router.reload() })

const form = useForm({
  title: ''
})

</script>

<template>
  <Layout>
    <Head title="Welcome" />
    <form @submit.prevent="form.post('/i')">
      <label for="title">Title</label>
      <input v-model="form.title" class="w-full border border-gray-300 rounded-md p-2" type="text" />
      <div v-if="form.errors.title">{{ form.errors.title }}</div>
      <button type="submit" :disabled="form.processing">Add Todo</button>
    </form>
    <h1> Todo List </h1>
    <Deferred data="todos">
        <template #fallback>
            <div>Loading...</div>
        </template>

        <div v-for="todo in todos">
            {{ todo.title }}
        </div>
    </Deferred>
  </Layout>
</template>

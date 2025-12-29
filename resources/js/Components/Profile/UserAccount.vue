<script setup>
import { useForm } from '@inertiajs/vue3'
import useAuth from '../../Composables/useAuth'
import useNotifications from '@/Composables/useNotifications'
import PrimaryButton from '@/Components/Button/PrimaryButton.vue'
import HorizontalGroup from '@/Components/Layout/HorizontalGroup.vue'
import Input from '../Form/Input.vue'
import Error from '../Form/Error.vue'

const { user } = useAuth()
const { notify } = useNotifications()

const form = useForm({
  name: user.value.name,
  email: user.value.email
})
const save = () => {
  form.put(route('mixpost.profile.updateUser'), {
    preserveScroll: true,
    onSuccess() {
      notify('success', 'Account info have been updated')
    }
  })
}
</script>
<template>
  <form @submit.prevent="save">
    <Error
      v-for="(error, errorKey) in form.errors"
      :key="errorKey"
      :message="error"
      class="mb-xs"
    />

    <HorizontalGroup>
      <template #title>
        <label for="name">Name</label>
      </template>

      <Input id="name" v-model="form.name" type="text" :error="form.errors.name" />
    </HorizontalGroup>

    <HorizontalGroup class="mt-lg">
      <template #title>
        <label for="email">Email</label>
      </template>

      <Input id="email" v-model="form.email" type="email" :error="form.errors.email" />
    </HorizontalGroup>

    <PrimaryButton type="submit" class="mt-lg">Save</PrimaryButton>
  </form>
</template>

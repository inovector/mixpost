<script setup>
import { useForm } from '@inertiajs/vue3'
import useNotifications from '@/Composables/useNotifications'
import PrimaryButton from '@/Components/Button/PrimaryButton.vue'
import HorizontalGroup from '@/Components/Layout/HorizontalGroup.vue'
import Input from '../Form/Input.vue'
import Error from '../Form/Error.vue'

const { notify } = useNotifications()

const form = useForm({
  current_password: '',
  password: '',
  password_confirmation: ''
})
const save = () => {
  form.put(route('mixpost.profile.updatePassword'), {
    preserveScroll: true,
    onSuccess() {
      form.reset()
      notify('success', 'Password have been changed')
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

    <HorizontalGroup class="mt-lg">
      <template #title>
        <label for="current_password">Current password</label>
      </template>

      <Input
        id="current_password"
        v-model="form.current_password"
        type="password"
        :error="form.errors.current_password"
      />
    </HorizontalGroup>

    <HorizontalGroup class="mt-md">
      <template #title>
        <label for="password">New password</label>
      </template>

      <Input
        id="password"
        v-model="form.password"
        :error="form.errors.password"
        type="password"
        class="w-full"
        autocomplete="new-password"
      />
    </HorizontalGroup>

    <HorizontalGroup class="mt-md">
      <template #title>
        <label for="password_confirmation">Confirm new password</label>
      </template>

      <Input
        id="password_confirmation"
        v-model="form.password_confirmation"
        :error="form.errors.password_confirmation"
        type="password"
        class="w-full"
        required
        autocomplete="new-password"
      />
    </HorizontalGroup>

    <PrimaryButton type="submit" class="mt-lg">Save</PrimaryButton>
  </form>
</template>

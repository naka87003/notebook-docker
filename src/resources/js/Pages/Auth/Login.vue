<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const props = defineProps<{
  canResetPassword: boolean,
  status: string
}>();
const form = useForm({
  email: '',
  password: '',
  remember: false,
});
const visible = ref(false);
const alert = ref(false);

onMounted(() => {
  if (props.status) {
    alert.value = true;
  }
});

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  });
};
</script>

<template>
  <GuestLayout>

    <Head title="Log in" />
    <v-card class="mx-auto pa-12 pb-8" elevation="8" max-width="448" rounded="lg">
      <v-alert v-model="alert" :text="status" type="success" variant="tonal" closable />
      <form @submit.prevent="submit">
        <div class="text-subtitle-1 text-medium-emphasis">Email</div>
        <v-text-field v-model="form.email" type="email" density="compact" placeholder="Email address"
          prepend-inner-icon="mdi-email-outline" variant="outlined" :error="Boolean(form.errors.email)"
          :error-messages="form.errors.email" required autofocus autocomplete="username"
          @input="form.errors.email = null" />
        <div class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between">
          Password
          <a v-if="canResetPassword" :href="route('password.request')"
            class="text-caption text-decoration-none text-blue" rel="noopener noreferrer">
            Forgot login password?</a>
        </div>
        <v-text-field :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'" :type="visible ? 'text' : 'password'"
          density="compact" placeholder="Enter your password" prepend-inner-icon="mdi-lock-outline" variant="outlined"
          v-model="form.password" :error="Boolean(form.errors.password)" :error-messages="form.errors.password"
          @click:append-inner="visible = !visible" autocomplete="current-password" required
          @input="form.errors.password = null" />
        <v-btn color="blue" size="large" variant="tonal" :class="{ 'text-disabled': form.processing }"
          :disabled="form.processing" block @click="submit">
          Log In
        </v-btn>
        <v-checkbox color="info" name="remember" label="Remember me" v-model:checked="form.remember"></v-checkbox>
        <v-card-text class="text-center">
          <a class="text-blue text-decoration-none" :href="route('register')" rel="noopener noreferrer">
            Sign up now <v-icon icon="mdi-chevron-right" />
          </a>
        </v-card-text>
      </form>
    </v-card>
  </GuestLayout>
</template>

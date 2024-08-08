<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
  password: '',
});

const visible = ref(false);

const deleteUser = () => {
  form.delete(route('profile.destroy'), {
    preserveScroll: true,
    onSuccess: () => closeModal(),
    onError: () => passwordInput.value.focus(),
    onFinish: () => form.reset(),
  });
};

const closeModal = () => {
  confirmingUserDeletion.value = false;
  form.reset();
};
</script>

<template>
  <section class="space-y-6">
    <v-card title="Delete Account" class="mx-auto pa-6" elevation="8" rounded="lg">
      <v-card class="mb-6" color="surface-variant" variant="tonal">
        <v-card-text class="text-medium-emphasis text-caption">
          <p>Once your account is deleted, all of its resources and data will be permanently deleted.</p>
          <p>Before deleting your account, please download any data or information that you wish to retain.</p>
        </v-card-text>
      </v-card>
      <v-dialog max-width="1000">
        <template v-slot:activator="{ props: activatorProps }">
          <v-btn v-bind="activatorProps" color="error" size="large" variant="tonal"
            :class="{ 'text-disabled': form.processing }" :disabled="form.processing">
            Delete Account
          </v-btn>
        </template>
        <template v-slot:default="{ isActive }">
          <v-card title="Are you sure you want to delete your account?" class="mx-auto pa-6" elevation="8" rounded="lg">
            <v-card class="mb-6" color="surface-variant" variant="tonal">
              <v-card-text class="text-medium-emphasis text-caption">
                <p>Once your account is deleted, all of its resources and data will be permanently deleted.</p>
                <p>please enter your password to confirm you would like to permanently delete your account.</p>
              </v-card-text>
            </v-card>
            <div class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between">
              Password
            </div>
            <v-text-field :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'" :type="visible ? 'text' : 'password'"
              density="compact" placeholder="Enter your password" prepend-inner-icon="mdi-lock-outline"
              variant="outlined" v-model="form.password" :error="Boolean(form.errors.password)"
              :error-messages="form.errors.password" @click:append-inner="visible = !visible"
              autocomplete="new-password" required max-width="600" @input="form.errors.password = null" />
            <v-card-actions>
              <v-btn text="Cancel" color="secondary" size="large" variant="tonal"
                @click="isActive.value = false"></v-btn>
              <v-spacer></v-spacer>
              <v-btn color="error" size="large" variant="tonal" :class="{ 'text-disabled': form.processing }"
                :disabled="form.processing" @click="deleteUser">
                Delete Account
              </v-btn>
            </v-card-actions>
          </v-card>
        </template>
      </v-dialog>
    </v-card>
  </section>
</template>

<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import { ref, inject } from 'vue';
import type { User } from '@/interfaces';

const updateAvatarImage = inject<(url: string) => void>('updateAvatarImage');

const user = usePage().props.auth.user as User;

const form = useForm({
  image: null,
});

const preview = ref();

const avatarImagePath = ref('storage/' + user.image_path);

const previewImage = () => {
  if (form.image) {
    avatarImagePath.value = URL.createObjectURL(preview.value.files[0]);
  } else {
    const user = usePage().props.auth.user as User;
    avatarImagePath.value = 'storage/' + user.image_path;
  }
};

const uploadImage = () => {
  form.post(route('profile.upload'), {
    onSuccess: () => {
      updateAvatarImage(avatarImagePath.value);
      form.image = null;
    }
  });
}
</script>

<template>
  <section>
    <v-card title="Avatar Image">
      <v-divider />
      <v-card-text>
        <v-card class="mb-6" color="surface-variant" variant="tonal">
          <v-card-text class="text-medium-emphasis text-caption">
            <p>Upload your image to use as your avatar.</p>
          </v-card-text>
        </v-card>
        <v-list-item class="mb-5">
          <template v-slot:prepend>
            <v-avatar color="surface-light">
              <v-img :src="avatarImagePath">
                <template v-slot:error>
                  <v-avatar>
                    <v-icon icon="mdi-account"></v-icon>
                  </v-avatar>
                </template>
              </v-img>
            </v-avatar>
          </template>
          <v-list-item-title>{{ $page.props.auth.user.name }}</v-list-item-title>
        </v-list-item>
        <form @submit.prevent="form.patch(route('profile.update'))" enctype="multipart/form-data">
          <div class="text-subtitle-1 text-medium-emphasis">Image Upload</div>
          <v-file-input ref="preview" v-model="form.image" density="compact" label="New image file input"
            variant="outlined" :error="Boolean(form.errors.image)" :error-messages="form.errors.image" required
            autofocus max-width="600" accept="image/png, image/jpeg" @input="form.errors.image = null"
            @update:modelValue="previewImage" />
        </form>
        <Transition>
          <div v-if="form.recentlySuccessful">
            <v-alert text="Uploaded." type="success" variant="tonal" closable />
          </div>
        </Transition>
      </v-card-text>
      <v-divider />
      <template v-slot:actions>
        <v-spacer />
        <v-btn color="primary" size="large" variant="tonal" :class="{ 'text-disabled': form.processing }"
          :disabled="form.processing" @click="uploadImage">
          Upload
        </v-btn>
      </template>
    </v-card>
  </section>
</template>
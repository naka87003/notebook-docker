<script setup lang="ts">
import dayjs from 'dayjs';
import type { Note } from '@/interfaces';
import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps<{ note: Note }>();

const likeCount = ref(props.note.likes.length);

const dialog = ref({
  enlargedImage: false
});

const isLiked = ref(props.note.likes.some((like) => like.user_id === usePage().props.auth.user.id));

const previewImagePath = computed(() => {
  return props.note.image_path ? 'storage/' + props.note.image_path : null;
});

const simplifyDateTime = (str: string): string => dayjs(str).format('YYYY/MM/DD HH:mm');
const splitByNewline = (text: string): string[] => text.split(/\r?\n/);

const like = async () => {

  if (isLiked.value === false) {
    await axios.post(route('timeline.like'), {
      note_id: props.note.id
    })
      .then(function (response) {
        likeCount.value++
        isLiked.value = true;
      })
      .catch(function (error) {
        console.log(error);
      });

  } else {
    await axios.post(route('timeline.unlike'), {
      note_id: props.note.id
    })
      .then(function (response) {
        likeCount.value--;
        isLiked.value = false;
      })
      .catch(function (error) {
        console.log(error);
      });
  }
};
</script>

<template>
  <v-card :color="note.category.vuetify_theme_color_name" variant="tonal" class="mx-auto"
    :prepend-icon="note.category.mdi_name" :title="note.title" rounded="0">
    <template v-slot:prepend>
      <v-icon size="large"></v-icon>
    </template>
    <template v-slot:append>
      <p class="text-caption">
        {{ simplifyDateTime(note.updated_at) }}
      </p>
    </template>
    <v-divider class="border-opacity-25 mx-1" />
    <v-card-text class="text-h6 py-2">
      <v-alert v-if="note.category.id === 3" type="info" variant="tonal" class="mb-3">
        <p class="text-body-2">from {{ simplifyDateTime(note.starts_at) }}</p>
        <p class="text-body-2">to {{ simplifyDateTime(note.ends_at) }}</p>
      </v-alert>
      <p v-for="paragraph in splitByNewline(note.content ?? '')" class="note-paragraph">{{ paragraph }}</p>
      <v-img v-if="previewImagePath" :src="previewImagePath" width="300" class="mt-3 cursor-pointer"
        @click="dialog.enlargedImage = true" />
    </v-card-text>
    <v-card-actions>
      <v-list-item class="w-100">
        <template v-slot:prepend>
          <v-avatar color="grey-darken-3">
            <v-img v-if="note.user.image_path" :src="'storage/' + note.user.image_path" />
            <v-icon v-else icon="mdi-account" />
          </v-avatar>
        </template>
        <v-list-item-title>{{ note.user.name }}</v-list-item-title>
        <v-list-item-subtitle v-if="note.tag" class="text-caption">{{ note.tag?.name }}</v-list-item-subtitle>
        <template v-slot:append="note">
          <div class="justify-self-end">
            <v-btn :prepend-icon="isLiked ? 'mdi-heart' : 'mdi-heart-outline'" :class="{ 'text-pink': isLiked }"
              @click="like">{{ likeCount }}</v-btn>
          </div>
        </template>
      </v-list-item>
    </v-card-actions>
  </v-card>
  <v-dialog v-model="dialog.enlargedImage" close-on-content-click maxWidth="1000px">
    <v-img :src="previewImagePath" height="90vh" />
  </v-dialog>
</template>
<script setup lang="ts">
import dayjs from 'dayjs';
import type { Note } from '@/interfaces';
import { computed, ref } from 'vue';

const props = defineProps<{ note: Note }>();

const dialog = ref({
  enlargedImage: false
})

const previewImagePath = computed(() => {
  return props.note.image_path ? 'storage/' + props.note.image_path : null;
});

const simplifyDateTime = (str: string): string => dayjs(str).format('YYYY/MM/DD HH:mm');
const splitByNewline = (text: string): string[] => text.split(/\r?\n/);
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
          <template v-if="note.tag">
            <v-icon :color="note.tag?.hex_color" size="small" class="me-1 hidden-xs" icon="mdi-tag" />
            <span class="me-5 text-caption hidden-xs">{{ note.tag?.name }}</span>
            <v-tooltip :text="note.tag?.name" location="top">
              <template v-slot:activator="{ props }">
                <v-icon icon="mdi-tag" size="small" class="me-5 hidden-sm-and-up" :color="note.tag?.hex_color"
                  v-bind="props" open-on-click />
              </template>
            </v-tooltip>
          </template>
          <v-icon v-if="note.status.name === 'archived'" size="small" class="me-5" icon="mdi-archive-outline" />
          <v-icon v-if="note.public === false" size="small" class="me-5" icon="mdi-lock-outline"></v-icon>
          <v-btn v-else-if="note.likes.length" class="ms-n4" prepend-icon="mdi-heart">{{ note.likes.length }}</v-btn>
        </template>
        <template v-slot:append>
          <slot name="actions" />
        </template>
      </v-list-item>
    </v-card-actions>
  </v-card>
  <v-dialog v-model="dialog.enlargedImage" close-on-content-click maxWidth="1000px">
    <v-img :src="previewImagePath" height="90vh" />
  </v-dialog>
</template>
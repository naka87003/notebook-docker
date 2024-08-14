<script setup lang="ts">
import dayjs from 'dayjs';
import type { Note } from '@/interfaces';

defineProps<{ note: Note }>();

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
      <p v-for="paragraph in splitByNewline(note.description)" class="note-paragraph">{{ paragraph }}</p>
    </v-card-text>
    <v-card-actions>
      <v-list-item class="w-100">
        <template v-slot:prepend>
          <div class="justify-self-end">
            <template v-if="note.tag">
              <v-icon :color="note.tag?.hex_color" size="small" class="me-1" icon="mdi-tag"></v-icon>
              <span class="me-5 text-caption">{{ note.tag?.name }}</span>
            </template>
            <v-icon v-if="note.status.name === 'archived'" size="small" class="me-5" icon="mdi-archive-outline" />
            <v-icon v-if="note.public === false" size="small" class="me-5" icon="mdi-lock-outline"></v-icon>
          </div>
        </template>
        <template v-slot:append>
          <div class="justify-self-end">
            <slot name="actions" />
          </div>
        </template>
      </v-list-item>
    </v-card-actions>
  </v-card>
</template>
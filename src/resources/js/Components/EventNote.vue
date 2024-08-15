<script setup lang="ts">
import dayjs from 'dayjs';
import type { Note } from '@/interfaces';

defineProps<{ targetNote: Note }>();

const simplifyDateTime = (str: string): string => dayjs(str).format('YYYY/MM/DD HH:mm');
const splitByNewline = (text: string): string[] => text.split(/\r?\n/);
</script>

<template>
  <v-card :prepend-icon="targetNote.category.mdi_name" :title="targetNote.title" rounded="0">
    <template v-slot:prepend>
      <v-icon size="large"></v-icon>
    </template>
    <template v-slot:append>
      <p class="text-caption">
        {{ simplifyDateTime(targetNote.updated_at) }}
      </p>
    </template>
    <v-divider class="border-opacity-25 mx-1" />
    <v-card-text class="text-h6 py-2">
      <v-alert v-if="targetNote.category.id === 3" type="info" variant="tonal" class="mb-3">
        <p class="text-body-2">from {{ simplifyDateTime(targetNote.starts_at) }}</p>
        <p class="text-body-2">to {{ simplifyDateTime(targetNote.ends_at) }}</p>
      </v-alert>
      <p v-for="paragraph in splitByNewline(targetNote.content)" class="note-paragraph">{{ paragraph }}</p>
    </v-card-text>
    <v-card-actions>
      <v-list-item class="w-100">
        <template v-slot:prepend>
          <div class="justify-self-end">
            <template v-if="targetNote.tag">
              <v-icon :color="targetNote.tag?.hex_color" size="small" class="me-1" icon="mdi-tag"></v-icon>
              <span class="me-5 text-caption">{{ targetNote.tag?.name }}</span>
            </template>
            <v-icon v-if="targetNote.status.name === 'archived'" size="small" class="me-5" icon="mdi-archive-outline" />
            <v-icon v-if="targetNote.public === false" size="small" class="me-5" icon="mdi-lock-outline"></v-icon>
          </div>
        </template>
        <template v-slot:append>
          <div class="justify-self-end">
            <slot name="actions" :targetNote/>
          </div>
        </template>
      </v-list-item>
    </v-card-actions>
  </v-card>
</template>
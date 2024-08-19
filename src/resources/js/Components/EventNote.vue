<script setup lang="ts">
import dayjs from 'dayjs';
import type { Note } from '@/interfaces';
import { computed, ref } from 'vue';

const props = defineProps<{ targetNote: Note }>();

const emit = defineEmits<{
  close: []
}>();

const dialog = ref({
  enlargedImage: false
})

const previewImagePath = computed(() => {
  return props.targetNote.image_path ? 'storage/' + props.targetNote.image_path : null;
});


const simplifyDateTime = (str: string): string => dayjs(str).format('YYYY/MM/DD HH:mm');
const splitByNewline = (text: string): string[] => text.split(/\r?\n/);
</script>

<template>
  <v-card rounded="0">
    <v-toolbar density="comfortable" color="transparent" :title="targetNote.title">
      <template v-slot:prepend>
        <v-icon :icon="targetNote.category.mdi_name" size="large" class="ms-3"></v-icon>
      </template>
      <template v-slot:append>
        <v-btn icon="mdi-close" @click="$emit('close')"></v-btn>
      </template>
    </v-toolbar>
    <v-divider class="border-opacity-25 mx-1" />
    <v-card-text class="text-h6 py-2">
      <v-alert v-if="targetNote.category.id === 3" type="info" variant="tonal" class="mb-3">
        <p class="text-body-2">from {{ simplifyDateTime(targetNote.starts_at) }}</p>
        <p class="text-body-2">to {{ simplifyDateTime(targetNote.ends_at) }}</p>
      </v-alert>
      <p v-for="paragraph in splitByNewline(targetNote.content ?? '')" class="note-paragraph">{{ paragraph }}</p>
      <v-img v-if="previewImagePath" :src="previewImagePath" width="300" class="mt-3 cursor-pointer"
        @click="dialog.enlargedImage = true" />
    </v-card-text>
    <v-card-actions>
      <v-list-item class="w-100">
        <template v-slot:prepend>
          <template v-if="targetNote.tag">
            <v-icon :color="targetNote.tag?.hex_color" size="small" class="me-1 hidden-xs" icon="mdi-tag" />
            <span class="me-5 text-caption hidden-xs">{{ targetNote.tag?.name }}</span>
            <v-tooltip :text="targetNote.tag?.name" location="top">
              <template v-slot:activator="{ props }">
                <v-icon icon="mdi-tag" size="small" class="me-5 hidden-sm-and-up" :color="targetNote.tag?.hex_color"
                  v-bind="props" open-on-click />
              </template>
            </v-tooltip>
          </template>
          <v-icon v-if="targetNote.status.name === 'archived'" size="small" class="me-5" icon="mdi-archive-outline" />
          <v-icon v-if="targetNote.public === false" size="small" class="me-5" icon="mdi-lock-outline"></v-icon>
        </template>
        <template v-slot:append>
          <slot name="actions" :targetNote />
        </template>
      </v-list-item>
    </v-card-actions>
  </v-card>
  <v-dialog v-model="dialog.enlargedImage" close-on-content-click maxWidth="1000px">
    <v-img :src="previewImagePath" height="90vh" />
  </v-dialog>
</template>

<style>
.note-paragraph {
  background-image: linear-gradient(180deg, rgba(204, 204, 204, 0) 0%, rgba(204, 204, 204, 0) 98.5%, rgba(100, 100, 100, 100) 100%);
  background-repeat: repeat-y;
  background-size: 100% 1.7em;
  line-height: 1.7;
}
</style>
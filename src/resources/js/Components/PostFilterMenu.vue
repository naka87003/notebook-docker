<script setup lang="ts">
import { ref, onMounted, type Ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import type { PostsFilter, Tag } from '@/interfaces';
import { getTagSelectItems } from '@/common';

const emit = defineEmits<{
  close: [];
  apply: [newFilter: PostsFilter];
}>();

const page = usePage();

const props = defineProps<{ filter: PostsFilter }>();

const items = ref({
  status: [
    { id: 1, name: 'Normal', mdi_name: 'mdi-book-open-outline' },
    { id: 2, name: 'Archive', mdi_name: 'mdi-archive-outline' },
  ],
  tag: [] as Tag[]
});

const newFilter: Ref<PostsFilter> = ref({
  onlyMyLiked: props.filter.onlyMyLiked,
  users: []
});

onMounted(async () => {
  items.value.tag = await getTagSelectItems();
});

const resetFilter = () => {
  newFilter.value.onlyMyLiked = false;
  newFilter.value.users = [];
  emit('apply', newFilter.value);
};
</script>
<template>
  <v-card>
    <v-toolbar density="comfortable" color="transparent">
      <v-toolbar-title class="text-h6" text="Filter Menu"></v-toolbar-title>
      <template v-slot:prepend>
        <v-icon class="ms-3" icon="mdi-filter-menu-outline" />
      </template>
      <template v-slot:append>
        <v-btn icon="mdi-close" @click="$emit('close')"></v-btn>
      </template>
    </v-toolbar>
    <v-divider />
    <v-card-text>
      <v-row>
        <v-col cols="12">
          <div class="text-subtitle-1 text-medium-emphasis">Like</div>
          <v-checkbox v-model="newFilter.onlyMyLiked" class="mb-n10">
            <template #label>
              <v-icon icon="mdi-heart" class="mx-3"></v-icon>
              Show only my liked posts
            </template>
          </v-checkbox>
        </v-col>
      </v-row>
    </v-card-text>
    <v-divider />
    <template v-slot:actions>
      <v-btn variant="plain" @click="resetFilter">Reset</v-btn>
      <v-spacer></v-spacer>
      <v-btn variant="plain" @click="$emit('close')">Close</v-btn>
      <v-btn color="primary" variant="tonal" @click="$emit('apply', newFilter)">Apply</v-btn>
    </template>
  </v-card>
</template>
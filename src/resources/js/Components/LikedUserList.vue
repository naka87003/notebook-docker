<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { Like } from '@/interfaces';
import { relativeDateTime } from '@/common';

defineProps<{
  targetLikes: Like[];
}>();

defineEmits<{
  close: []
}>();

const headers = [
  { title: 'User', align: 'start', key: 'user_id' },
  { title: 'Date', align: 'end', key: 'updated_at' },
] as any;

const showSelectedUserPosts = (userId: number) => {
  router.get(route('timeline'), {
    user: userId
  });
};
</script>

<template>
  <v-card class="mx-auto w-100">
    <v-toolbar>
      <v-btn icon="mdi-heart" variant="text"></v-btn>
      <v-toolbar-title>Liked User List</v-toolbar-title>
      <template v-slot:append>
        <v-btn icon="mdi-close" @click="$emit('close')"></v-btn>
      </template>
    </v-toolbar>
    <v-data-table-virtual :headers="headers" :items="targetLikes" item-value="id" hide-default-header height="400">
      <template v-slot:item.user_id="{ item }">
        <v-list-item class="w-100" @click="showSelectedUserPosts(item.user.id)">
          <template v-slot:prepend>
            <v-avatar>
              <v-img v-if="item.user.image_path" :src="'storage/' + item.user.image_path" />
              <v-icon v-else icon="mdi-account" />
            </v-avatar>
          </template>
          <v-list-item-title>{{ item.user.name }}</v-list-item-title>
        </v-list-item>
      </template>
      <template v-slot:item.updated_at="{ item }">
        {{ relativeDateTime(item.updated_at) }}
      </template>
    </v-data-table-virtual>
  </v-card>
</template>
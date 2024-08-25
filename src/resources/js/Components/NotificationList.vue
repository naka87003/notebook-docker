<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import type { Notification } from '@/interfaces';
import { relativeDateTime } from '@/common';
import { onMounted, Ref, ref } from 'vue';
import axios from 'axios';

defineProps<{
  unreadNotificationCount: number;
}>();

const emit = defineEmits<{
  close: [];
  markAllAsRead: []
}>();

const items: Ref<Notification[]> = ref([]);

onMounted(async () => {
  const result = await loadItems();
  items.value.push(...result);
});

const loadItems = async (): Promise<Notification[]> => {
  const response = await axios.get(route('notifications'), {
    params: {
      offset: items.value.length
    }
  })
  return response.data;
};

const load = async ({ done }): Promise<void> => {
  const result = await loadItems();
  if (result.length > 0) {
    items.value.push(...result);
    done('ok');
  } else {
    done('empty');
  }
};

const selectItem = async (item: Notification) => {
  await markAsRead(item.id);
  let data = {};
  switch (item.data.type) {
    case 'follow':
      data = { user: item.user.id };
      break;
    case 'comment':
      data = { note: item.data.comment.note_id };
      break;
  }
  router.get(route('timeline'), data);
};

const markAsRead = async (notificationId: string) => {
  await axios.put(route('notifications.markAsRead', notificationId));
};

const markAllAsRead = async () => {
  await axios.put(route('notifications.markAllAsRead'))
    .then(async () => {
      emit('markAllAsRead');
    })
    .catch(error => {
      console.log(error);
    });
};

const titleMsg = (item: Notification) => {
  switch (item.data.type) {
    case 'follow':
      return `${item.user.name} followed you`;
    case 'comment':
      return `${item.user.name} commented on your note`;
  }
};
</script>

<template>
  <v-card class="mx-auto w-100">
    <v-toolbar>
      <v-toolbar-title>Notifications</v-toolbar-title>
      <template v-slot:prepend>
        <v-icon class="ms-3" icon="mdi-bell-outline" />
      </template>
      <template v-slot:append>
        <v-btn icon="mdi-close" @click="$emit('close')"></v-btn>
      </template>
    </v-toolbar>
    <v-divider />
    <v-card-text class="py-0">
      <v-alert v-if="items.length === 0" variant="text" class="text-center" text="No data available" />
      <v-list v-if="items.length > 0">
        <v-infinite-scroll :onLoad="load" class="w-100 overflow-hidden" empty-text="">
          <template v-for="item in items" :key="item.id">
            <v-list-item :base-color="item.read_at ? '' : 'info'" @click="selectItem(item)">
              <v-list-item-title>{{ titleMsg(item) }}</v-list-item-title>
              <template v-slot:prepend>
                <v-avatar color="surface-light">
                  <v-img v-if="item.user.image_path" :src="'/storage/' + item.user.image_path" />
                  <v-icon v-else icon="mdi-account" />
                </v-avatar>
              </template>
              <template v-slot:append>
                <span class="text-caption">{{ relativeDateTime(item.created_at) }}</span>
              </template>
            </v-list-item>
            <v-divider />
          </template>
        </v-infinite-scroll>
      </v-list>
    </v-card-text>
    <template v-slot:actions>
      <v-btn variant="plain" @click="$emit('close')">Close</v-btn>
      <v-spacer></v-spacer>
      <v-btn color="secondary" variant="tonal" :disabled="unreadNotificationCount === 0" @click="markAllAsRead">Mark All
        as
        Read</v-btn>
    </template>
  </v-card>
</template>
<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { User } from '@/interfaces';
import { relativeDateTime } from '@/common';
import { onMounted, Ref, ref } from 'vue';
import axios from 'axios';

const props = defineProps<{
  selectedUser: User;
}>();

defineEmits<{
  close: []
}>();

const items: Ref<User[]> = ref([]);

onMounted(async () => {
  const result = await loadItems();
  items.value.push(...result);
});

const loadItems = async (): Promise<User[]> => {
  const response = await axios.get(route('users.followees', props.selectedUser.id), {
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

const showSelectedUserPosts = (userId: number) => {
  router.get(route('timeline'), {
    user: userId
  });
};
</script>

<template>
  <v-card class="mx-auto w-100">
    <v-toolbar>
      <v-toolbar-title>Following</v-toolbar-title>
      <template v-slot:append>
        <v-btn icon="mdi-close" @click="$emit('close')"></v-btn>
      </template>
    </v-toolbar>
    <v-divider />
    <v-card-text class="py-0">
      <v-list>
        <v-infinite-scroll v-if="items.length > 0" :onLoad="load" class="w-100 overflow-hidden" empty-text="">
          <template v-for="item in items" :key="item.id">
            <v-list-item @click="showSelectedUserPosts(item.id)">
              <template v-slot:prepend>
                <v-avatar color="surface-light">
                  <v-img v-if="item.image_path" :src="'storage/' + item.image_path" />
                  <v-icon v-else icon="mdi-account" />
                </v-avatar>
              </template>
              <v-list-item-title>{{ item.name }}</v-list-item-title>
              <template v-slot:append>
                <span class="text-caption">{{ relativeDateTime(item.updated_at) }}</span>
              </template>
            </v-list-item>
            <v-divider />
          </template>
        </v-infinite-scroll>
      </v-list>
    </v-card-text>
  </v-card>
</template>
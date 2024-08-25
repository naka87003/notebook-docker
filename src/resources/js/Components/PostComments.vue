<script setup lang="ts">
import { router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted, inject } from 'vue';
import axios from 'axios';
import Post from '@/Components/Post.vue';
import type { Comment, Note, User } from '@/interfaces';
import { relativeDateTime } from '@/common';

const props = defineProps<{
  targetNote: Note;
}>();

defineEmits<{
  close: [];
}>();

const updatePosts: (id: number) => Promise<void> = inject('updatePosts');

const form = useForm({
  comment: '',
});

const items = ref([]);

const userImagePath = computed((): string | null => {
  const user = usePage().props.auth.user as User;
  return user.image_path;
});

const avatarImagePath = computed(() => userImagePath.value ? '/storage/' + userImagePath.value : null);

onMounted(async () => {
  const result = await loadItems();
  items.value.push(...result);
});

const loadItems = async (fresh: boolean = false): Promise<Comment[]> => {
  const params = {};
  if (fresh === false) {
    params['offset'] = items.value.length;
  }
  const response = await axios.get(route('comments', props.targetNote.id), { params })
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

const addComment = async () => {
  form.post(route('comments.store', props.targetNote.id), {
    preserveScroll: true,
    onSuccess: async () => {
      form.reset('comment');
      const result = await loadItems(true);
      items.value = result;
      updatePosts(props.targetNote.id);
    }
  });
};

const showSelectedUserPosts = (userId: number) => {
  router.get(route('timeline'), {
    user: userId
  });
};
</script>

<template>
  <v-card>
    <v-toolbar density="compact" color="transparent">
      <v-toolbar-title class="text-h6" text="Comments"></v-toolbar-title>
      <template v-slot:prepend>
        <v-btn icon="mdi-arrow-left" @click="$emit('close')"></v-btn>
      </template>
    </v-toolbar>
    <v-divider />
    <v-card-text>
      <v-container>
        <v-row>
          <v-col cols="12" class="px-0">
            <Post :note="targetNote" />
          </v-col>
          <v-col cols="12" class="px-0">
            <v-card density="compact" variant="text">
              <v-card-text>
                <form @submit.prevent="addComment">
                  <v-textarea v-model="form.comment" density="compact" placeholder="Add a comment" hide-details
                    clearable auto-grow rows="1" maxLength="300" :error="Boolean(form.errors.comment)">
                    <template v-slot:prepend>
                      <v-avatar color="surface-light">
                        <v-img v-if="avatarImagePath" :src="avatarImagePath" />
                        <v-icon v-else icon="mdi-account" />
                      </v-avatar>
                    </template>
                  </v-textarea>
                  <v-card-actions v-show="form.comment" class="mb-n4 pa-0">
                    <v-spacer />
                    <v-btn size="small" variant="tonal" color="secondary" :class="{ 'text-disabled': form.processing }"
                      :disabled="form.processing" @click="addComment">Comment</v-btn>
                  </v-card-actions>
                </form>
              </v-card-text>
            </v-card>
            <v-infinite-scroll v-if="items.length > 0" :onLoad="load" class="w-100 overflow-hidden" empty-text="">
              <template v-for="item in items" :key="item.id">
                <v-alert density="compact" variant="text">
                  <template v-slot:prepend>
                    <v-avatar color="grey-darken-3 cursor-pointer" style="z-index: 1;" @click="showSelectedUserPosts(item.user_id)">
                      <v-img v-if="item.user.image_path" :src="'/storage/' + item.user.image_path" />
                      <v-icon v-else icon="mdi-account" />
                    </v-avatar>
                  </template>
                  <template #title>
                    <span class="text-caption text-truncate">{{ item.user.name }}</span>
                    <v-spacer />
                    <span class="text-caption text-no-wrap">{{ relativeDateTime(item.created_at) }}</span>
                  </template>
                  {{ item.content }}
                  <div>
                    <v-icon flat size="x-small" icon="mdi-reply-outline" @click="console.log('click')" />
                  </div>
                </v-alert>
              </template>
            </v-infinite-scroll>
          </v-col>
        </v-row>
      </v-container>
    </v-card-text>
  </v-card>
</template>
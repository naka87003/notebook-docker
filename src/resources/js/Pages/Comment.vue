<script setup lang="ts">
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Post from '@/Components/Post.vue';
import type { Comment, Note } from '@/interfaces';
import { relativeDateTime } from '@/common';

const props = defineProps<{
  note: Note;
}>();

const dialog = ref({
  filterUserMenu: false,
  enlargedImage: false
});

const form = useForm({
  comment: '',
});

const previewImagePath = ref('');

const snackbar = ref({
  display: false,
  message: ''
});

const items = ref([]);

const userImagePath = computed((): string | null => usePage().props.auth.user.image_path);

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
  const response = await axios.get(route('comments.comments', props.note.id), { params })
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

const showEnlargedImage = (src: string) => {
  dialog.value.enlargedImage = true;
  previewImagePath.value = src;
};

const addComment = async () => {
  form.post(route('comments.store', props.note.id), {
    preserveScroll: true,
    onSuccess: async () => {
      form.reset('comment');
      const result = await loadItems(true);
      items.value = result;
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

  <Head title="Comment" />
  <v-snackbar v-model="snackbar.display" location="top right" color="success" timeout="3000">
    <v-icon class="me-3" style="margin-bottom: 2px;">mdi-check-circle</v-icon>{{ snackbar.message }}
  </v-snackbar>
  <AuthenticatedLayout>
    <v-container>
      <v-row>
        <v-col cols="12">
          <Post :note @showEnlargedImage="showEnlargedImage" />
        </v-col>
        <v-col cols="12">
          <v-card density="compact" variant="text">
            <v-card-text>
              <form @submit.prevent="addComment">
                <v-textarea v-model="form.comment" density="compact" placeholder="Add a comment" hide-details clearable
                  auto-grow rows="1" maxLength="300" :error="Boolean(form.errors.comment)">
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
                  <v-avatar color="grey-darken-3" style="z-index: 1;" @click="showSelectedUserPosts(item.user_id)">
                    <v-img v-if="item.user.image_path" :src="'/storage/' + item.user.image_path" />
                    <v-icon v-else icon="mdi-account" />
                  </v-avatar>
                </template>
                <template #title>
                  <span class="text-caption">{{ item.user.name }}</span>
                  <v-spacer />
                  <span class="text-caption">{{ relativeDateTime(item.created_at) }}</span>
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
    <v-dialog v-model="dialog.enlargedImage" close-on-content-click maxWidth="1000px">
      <v-img :src="previewImagePath" height="90vh" />
    </v-dialog>
  </AuthenticatedLayout>
</template>
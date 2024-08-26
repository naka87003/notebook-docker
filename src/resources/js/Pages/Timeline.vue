<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, computed, onMounted, provide, type Ref } from 'vue';
import { watchDebounced } from '@vueuse/core'
import type { Note, PostsFilter, User } from '@/interfaces';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Post from '@/Components/Post.vue';
import FilterUserMenu from '@/Components/FilterUserMenu.vue';
import SelectedUserMenu from '@/Components/SelectedUserMenu.vue';
import Comments from '@/Components/Comments.vue';

const props = defineProps<{
  user?: number;
  userItem?: User;
  note?: Note;
}>();

const dialog = ref({
  filterUserMenu: false,
  enlargedImage: false,
  postComments: false
});

const search = ref('');

const previewImagePath = ref('');

const targetNoteId: Ref<number> = ref(null);

const notes = ref(new Map<number, Note>());

const snackbar = ref({
  display: false,
  message: ''
});

const filter: Ref<PostsFilter> = ref({
  onlyMyLiked: false,
  user: props.user ?? null,
  following: false
});

const userItems: Ref<User[]> = ref(props.userItem ? [props.userItem] : []);

const selectedUser = computed((): User | undefined => userItems.value.find((item) => item.id === filter.value.user));

const searchEntered = computed((): boolean => Boolean(search.value));

const targetNote = computed(() => notes.value.get(targetNoteId.value));

watchDebounced(search,
  () => refreshDisplay(),
  { debounce: 500, maxWait: 1000 },
);

onMounted(async () => {
  const result = await loadNotes();
  for (const note of result) {
    notes.value.set(note.id, note);
  }

  if (props.note) {
    notes.value.set(props.note.id, props.note);
    showComments(props.note.id);
  }
});

const loadNotes = async (): Promise<(Note & { likes_count: number })[]> => {
  const response = await axios.get(route('notes.posts'), {
    params: {
      offset: notes.value.size,
      search: search.value,
      ...filter.value
    }
  })
  return response.data;
};

const load = async ({ done }): Promise<void> => {
  const result = await loadNotes();
  if (result.length > 0) {
    for (const note of result) {
      notes.value.set(note.id, note);
    }
    done('ok');
  } else {
    done('empty');
  }
};

const refreshDisplay = async (): Promise<void> => {
  notes.value.clear();
  const result = await loadNotes();
  for (const note of result) {
      notes.value.set(note.id, note);
    }
};

const showEnlargedImage = (src: string) => {
  dialog.value.enlargedImage = true;
  previewImagePath.value = src;
};

const filterUser = async (user: number): Promise<void> => {
  filter.value.following = false;
  filter.value.onlyMyLiked = false;
  dialog.value.filterUserMenu = false;
  filter.value.user = user;
  await refreshDisplay();
};

const filterOnlyMyLiked = async () => {
  filter.value.onlyMyLiked = !filter.value.onlyMyLiked;
  await refreshDisplay();
};

const filterFollowing = async () => {
  filter.value.onlyMyLiked = false;
  filter.value.user = null;
  filter.value.following = !filter.value.following;
  await refreshDisplay();
};

const showComments = (id: number) => {
  targetNoteId.value = id;
  dialog.value.postComments = true;
};

const closeComments = async () => {
  await updatePosts(targetNoteId.value);
  dialog.value.postComments = false;
};

const updatePosts = async (id: number) => {
  const response = await axios.get(route('notes.note', id));
  notes.value.set(id,response.data);
};

provide('showEnlargedImage', showEnlargedImage);
provide('updatePosts', updatePosts);
</script>

<template>

  <Head title="Timeline" />
  <v-snackbar v-model="snackbar.display" location="top right" color="success" timeout="3000">
    <v-icon class="me-3" style="margin-bottom: 2px;">mdi-check-circle</v-icon>{{ snackbar.message }}
  </v-snackbar>
  <AuthenticatedLayout>
    <template #action>
      <v-text-field v-model="search" density="compact" label="Search" variant="solo-filled" flat hide-details
        single-line clearable>
        <template #prepend-inner>
          <v-icon icon="mdi-magnify" :class="{ 'text-red': searchEntered }" />
        </template>
      </v-text-field>
      <v-spacer></v-spacer>
      <v-btn icon="mdi-account-multiple-outline" :class="{ 'text-red': filter.following }" @click="filterFollowing" />
      <v-btn icon="mdi-heart-outline" :class="{ 'text-red': filter.onlyMyLiked }" @click="filterOnlyMyLiked" />
      <v-btn icon="mdi-account-filter-outline" :class="{ 'text-red': filter.user }"
        @click="dialog.filterUserMenu = true" />
    </template>
    <v-container>
      <v-row v-if="selectedUser">
        <v-col cols="12">
          <SelectedUserMenu :selectedUser />
        </v-col>
      </v-row>
      <v-alert v-if="notes.size === 0" variant="text" class="text-center" text="No data available" />
      <v-infinite-scroll v-else :onLoad="load" class="w-100 overflow-hidden" empty-text="">
        <v-row>
          <template v-for="note in notes.values()" :key="note.id">
            <v-col cols="12">
              <Post :note @showEnlargedImage="showEnlargedImage" @showComments="showComments(note.id)"
                @update="updatePosts(note.id)" />
            </v-col>
          </template>
        </v-row>
      </v-infinite-scroll>
    </v-container>
    <v-dialog v-model="dialog.filterUserMenu" max-width="600" scrollable>
      <FilterUserMenu v-model:userItems="userItems" :filter @close="dialog.filterUserMenu = false"
        @apply="filterUser" />
    </v-dialog>
    <v-dialog v-model="dialog.enlargedImage" close-on-content-click maxWidth="1000px">
      <v-img :src="previewImagePath" height="90vh" />
    </v-dialog>
    <v-dialog v-model="dialog.postComments" fullscreen scrollable transition="scroll-x-transition">
      <Comments :targetNote @close="closeComments" >
        <Post :note="targetNote" commentLinkDisabled/>
      </Comments>
    </v-dialog>
  </AuthenticatedLayout>
</template>
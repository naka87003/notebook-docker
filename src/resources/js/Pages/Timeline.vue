<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, computed, onMounted, type Ref } from 'vue';
import { watchDebounced } from '@vueuse/core'
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Post from '@/Components/Post.vue';
import type { Note as NoteType, PostsFilter, User } from '@/interfaces';
import FilterUserMenu from '@/Components/FilterUserMenu.vue';
import SelectedUserMenu from '@/Components/SelectedUserMenu.vue';

const props = defineProps<{
  user?: number;
  userItem?: User;
}>();

const dialog = ref({
  filterUserMenu: false,
  enlargedImage: false
});

const search = ref('');

const previewImagePath = ref('');

const notes: Ref<NoteType[]> = ref([]);

const snackbar = ref({
  display: false,
  message: ''
});

const filter: Ref<PostsFilter> = ref({
  onlyMyLiked: false,
  user: props.user ?? null,
  following: false
});

const userItems: Ref<User[]> = ref([]);

if (props.userItem) {
  userItems.value.push(props.userItem);
}

const selectedUser = computed((): User | undefined => userItems.value.find((item) => item.id === filter.value.user))

const searchEntered = computed((): boolean => Boolean(search.value));

watchDebounced(search,
  () => refreshDisplay(),
  { debounce: 500, maxWait: 1000 },
);

onMounted(async () => {
  const result = await loadNotes();
  notes.value.push(...result);
});

const loadNotes = async (): Promise<(NoteType & { likes_count: number })[]> => {
  const response = await axios.get(route('notes.posts'), {
    params: {
      offset: notes.value.length,
      search: search.value,
      ...filter.value
    }
  })
  return response.data;
};

const load = async ({ done }): Promise<void> => {
  const result = await loadNotes();
  if (result.length > 0) {
    notes.value.push(...result);
    done('ok');
  } else {
    done('empty');
  }
};

const refreshDisplay = async (): Promise<void> => {
  notes.value.splice(0);
  const result = await loadNotes();
  notes.value.push(...result);
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
      <v-btn icon="mdi-account-multiple-outline" :class="{ 'text-red': filter.following }"
        @click="filterFollowing" />
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
      <v-alert v-if="notes.length === 0" variant="text" class="text-center" text="No data available" />
      <v-infinite-scroll v-else :onLoad="load" class="w-100 overflow-hidden" empty-text="">
        <v-row>
          <template v-for="note in notes" :key="note.id">
            <v-col cols="12">
              <Post :note @showEnlargedImage="showEnlargedImage" />
            </v-col>
          </template>
        </v-row>
      </v-infinite-scroll>
    </v-container>
    <v-dialog v-model="dialog.filterUserMenu" max-width="600" scrollable>
      <FilterUserMenu v-model:userItems="userItems" :filter @close="dialog.filterUserMenu = false" @apply="filterUser" />
    </v-dialog>
    <v-dialog v-model="dialog.enlargedImage" close-on-content-click maxWidth="1000px">
      <v-img :src="previewImagePath" height="90vh" />
    </v-dialog>
  </AuthenticatedLayout>
</template>
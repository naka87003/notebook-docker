<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted, type Ref } from 'vue';
import { watchDebounced } from '@vueuse/core'
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Post from '@/Components/Post.vue';
import type { Note as NoteType, PostsFilter, User } from '@/interfaces';
import PostFilterMenu from '@/Components/PostFilterMenu.vue';

const dialog = ref({
  filterMenu: false,
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
  user: null
});

const userItems: Ref<User[]> = ref([]);

const bottomElement: Ref<HTMLElement | null> = ref();

let observer: IntersectionObserver | null = null;

const searchEntered = computed((): boolean => Boolean(search.value));

const filterChanged = computed((): boolean => (filter.value.onlyMyLiked === true || filter.value.user !== null));

watchDebounced(search,
  () => refreshDisplay(),
  { debounce: 500, maxWait: 1000 },
);

onMounted(async () => {
  // 最下部までスクロールしたらさらに読み込むイベントを登録
  if (bottomElement.value) {
    observer = new IntersectionObserver(
      async (entries: IntersectionObserverEntry[]) => {
        const [entry] = entries;
        if (entry.isIntersecting) {
          await loadNotes();
        }
      },
      { root: null, threshold: 0 });
    observer.observe(bottomElement.value);
  }
});

onUnmounted(() => {
  observer?.disconnect();
});

const loadNotes = async (): Promise<void> => {
  await axios.get(route('timeline.posts'), {
    params: {
      offset: notes.value.length,
      search: search.value,
      ...filter.value
    }
  })
    .then(response => {
      notes.value.push(...response.data);
    })
    .catch(error => {
      console.log(error);
    });
};

const refreshDisplay = async (): Promise<void> => {
  observer?.disconnect();
  notes.value.splice(0);
  await loadNotes();
  observer.observe(bottomElement.value);
};

const filterApply = async (newFilter: PostsFilter): Promise<void> => {
  dialog.value.filterMenu = false;
  filter.value.onlyMyLiked = newFilter.onlyMyLiked;
  filter.value.user = newFilter.user;
  await refreshDisplay();
};

const showEnlargedImage = (src: string) => {
  dialog.value.enlargedImage = true;
  previewImagePath.value = src;
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
      <v-btn icon="mdi-filter-menu-outline" :class="{ 'text-red': filterChanged }" @click="dialog.filterMenu = true" />
    </template>
    <v-container>
      <v-alert v-if="notes.length === 0" variant="text" class="text-center" text="No data available" />
      <v-row>
        <v-col v-for="note in notes" cols="12">
          <Post :note @showEnlargedImage="showEnlargedImage" />
        </v-col>
      </v-row>
    </v-container>
    <v-dialog v-model="dialog.filterMenu" max-width="600" scrollable>
      <PostFilterMenu v-model:userItems="userItems" :filter @close="dialog.filterMenu = false" @apply="filterApply" />
    </v-dialog>
    <v-dialog v-model="dialog.enlargedImage" close-on-content-click maxWidth="1000px">
      <v-img :src="previewImagePath" height="90vh" />
    </v-dialog>
  </AuthenticatedLayout>
  <div ref="bottomElement"></div>
</template>

<style>
.note-paragraph {
  background-image: linear-gradient(180deg, rgba(204, 204, 204, 0) 0%, rgba(204, 204, 204, 0) 98.5%, rgba(100, 100, 100, 100) 100%);
  background-repeat: repeat-y;
  background-size: 100% 1.7em;
  line-height: 1.7;
}
</style>
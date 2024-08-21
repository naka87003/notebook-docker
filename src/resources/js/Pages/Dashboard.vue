<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, computed, onMounted, type Ref } from 'vue';
import { watchDebounced } from '@vueuse/core'
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Note from '@/Components/Note.vue';
import NoteCreateForm from '@/Components/NoteCreateForm.vue';
import NoteEditForm from '@/Components/NoteEditForm.vue';
import ConfirmCard from '@/Components/ConfirmCard.vue';
import NoteSortMenu from '@/Components/NoteSortMenu.vue';
import NoteFilterMenu from '@/Components/NoteFilterMenu.vue';
import type { Note as NoteType, Sort, NotesFilter } from '@/interfaces';
import LikedUserList from '@/Components/LikedUserList.vue';

const props = defineProps<{
  tag?: number;
  status?: number;
}>();

const search = ref('');
const notes: Ref<(NoteType & { likes_count: number })[]> = ref([]);
const dialog = ref({
  create: false,
  edit: false,
  archiveConfirm: false,
  retrieveConfirm: false,
  deleteConfirm: false,
  sortMenu: false,
  filterMenu: false,
  enlargedImage: false,
  likedUserList: false
});
const snackbar = ref({
  display: false,
  message: ''
});

const sort: Ref<Sort> = ref({
  key: 'updated_at',
  order: 'desc'
});

const filter: Ref<NotesFilter> = ref({
  category: [1, 2, 3],
  tag: [],
  status: props.status ?? 1,
  onlyLiked: false
});

const targetNote = ref();

const previewImagePath = ref('');

const searchEntered = computed((): boolean => Boolean(search.value));

const sortChanged = computed((): boolean => (sort.value.key !== 'updated_at' || sort.value.order !== 'desc'));

const sortIcon = computed((): string => {
  if (sort.value.key === 'starts_at') {
    return sort.value.order === 'asc' ? 'mdi-sort-calendar-ascending' : 'mdi-sort-calendar-descending';
  } else {
    return sort.value.order === 'asc' ? 'mdi-sort-clock-ascending-outline' : 'mdi-sort-clock-descending-outline';
  }
});

const filterChanged = computed((): boolean => (
  filter.value.category.length !== 3 ||
  filter.value.status !== 1 ||
  filter.value.tag.length !== 0 ||
  filter.value.onlyLiked === true
));

watchDebounced(search,
  () => refreshDisplay(),
  { debounce: 500, maxWait: 1000 },
);

onMounted(async () => {
  if (props.tag !== undefined) {
    filter.value.tag.push(props.tag);
  }
  const result = await loadNotes();
  notes.value.push(...result);
});

const loadNotes = async (): Promise<(NoteType & { likes_count: number })[]> => {
  const response = await axios.get(route('notes.index'), {
    params: {
      offset: notes.value.length,
      search: search.value,
      ...sort.value,
      ...filter.value
    }
  });
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

const noteCreated = async () => {
  dialog.value.create = false;
  await refreshDisplay();
  showSnackBar('Created Successfully.');
};

const noteUpdated = async () => {
  dialog.value.edit = false;
  await refreshDisplay();
  showSnackBar('Updated Successfully.');
};

const showEditDialog = (item: NoteType): void => {
  targetNote.value = item;
  dialog.value.edit = true;
};

const showArchiveConfirmDialog = (item: NoteType): void => {
  targetNote.value = item;
  dialog.value.archiveConfirm = true;
};

const showRetrieveConfirmDialog = (item: NoteType): void => {
  targetNote.value = item;
  dialog.value.retrieveConfirm = true;
};

const showDeleteConfirmDialog = (item: NoteType): void => {
  targetNote.value = item;
  dialog.value.deleteConfirm = true;
};

const archiveNote = async (): Promise<void> => {
  dialog.value.archiveConfirm = false;
  await axios.put(route('notes.archive', targetNote.value.id))
    .then(async () => {
      await refreshDisplay();
      showSnackBar('Archived Successfully.');
    })
    .catch(error => {
      console.log(error);
    });
};

const retrieveNote = async (): Promise<void> => {
  dialog.value.retrieveConfirm = false;
  await axios.put(route('notes.retrieve', targetNote.value.id))
    .then(async () => {
      await refreshDisplay();
      showSnackBar('Retrieved Successfully.');
    })
    .catch(error => {
      console.log(error);
    });
};

const deleteNote = async (): Promise<void> => {
  dialog.value.deleteConfirm = false;
  await axios.delete(route('notes.destroy', targetNote.value.id))
    .then(async () => {
      await refreshDisplay();
      showSnackBar('Deleted Successfully.');
    })
    .catch(error => {
      console.log(error);
    });
};

const showSnackBar = (msg: string): void => {
  snackbar.value.message = msg;
  snackbar.value.display = true;
};

const refreshDisplay = async (): Promise<void> => {
  notes.value.splice(0);
  const result = await loadNotes();
  notes.value.push(...result);
};

const sortApply = async (newSort: Sort): Promise<void> => {
  dialog.value.sortMenu = false;
  sort.value.key = newSort.key;
  sort.value.order = newSort.order;
  await refreshDisplay();
};

const filterApply = async (newFilter: NotesFilter): Promise<void> => {
  dialog.value.filterMenu = false;
  filter.value.category = newFilter.category;
  filter.value.status = newFilter.status;
  filter.value.tag = newFilter.tag;
  filter.value.onlyLiked = newFilter.onlyLiked;
  await refreshDisplay();
};

const showEnlargedImage = (src: string) => {
  dialog.value.enlargedImage = true;
  previewImagePath.value = src;
};

const showLikedUserList = (note: NoteType) => {
  dialog.value.likedUserList = true;
  targetNote.value = note;
}
</script>

<template>

  <Head title="Dashboard" />
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
      <v-btn icon="mdi-plus" @click="dialog.create = true" />
      <v-btn :icon="sortIcon" :class="{ 'text-red': sortChanged }" @click="dialog.sortMenu = true" />
      <v-btn icon="mdi-filter-menu-outline" :class="{ 'text-red': filterChanged }" @click="dialog.filterMenu = true" />
    </template>
    <v-container>
      <v-alert v-if="notes.length === 0" variant="text" class="text-center" text="No data available" />
      <v-infinite-scroll v-else :items="notes" :onLoad="load" class="w-100 overflow-hidden" empty-text="">
          <v-row>
          <template v-for="(item, index) in notes" :key="item">
            <v-col cols="12">
              <Note :note="item" @showEnlargedImage="showEnlargedImage" @showLikedUserList="showLikedUserList(item)">
                <template #actions>
                  <v-icon size="small" class="ms-5" icon="mdi-pencil-outline" @click="showEditDialog(item)" />
                  <v-icon v-if="item.status.name === 'archived'" size="small" class="ms-5" icon="mdi-keyboard-return"
                    @click="showRetrieveConfirmDialog(item)" />
                  <v-icon v-else size="small" class="ms-5" icon="mdi-archive-plus-outline"
                    @click="showArchiveConfirmDialog(item)" />
                  <v-icon size="small" class="ms-5" icon="mdi-delete-outline" @click="showDeleteConfirmDialog(item)" />
                </template>
              </Note>
            </v-col>
          </template>
        </v-row>
        </v-infinite-scroll>
    </v-container>
  </AuthenticatedLayout>
  <v-dialog v-model="dialog.create" fullscreen scrollable>
    <NoteCreateForm @noteCreated="noteCreated" @close="dialog.create = false" />
  </v-dialog>
  <v-dialog v-model="dialog.edit" fullscreen scrollable>
    <NoteEditForm :targetNote @noteUpdated="noteUpdated" @close="dialog.edit = false" />
  </v-dialog>
  <v-dialog v-model="dialog.archiveConfirm" max-width="600">
    <ConfirmCard title="Archive Note" message="Are you sure you want to archive this note?"
      icon="mdi-archive-plus-outline" confirmBtnName="Archive" @confirmed="archiveNote"
      @close="dialog.archiveConfirm = false" />
  </v-dialog>
  <v-dialog v-model="dialog.retrieveConfirm" max-width="600">
    <ConfirmCard title="Retrieve" message="Are you sure you want to retrieve this note from the archive?"
      icon="mdi-keyboard-return" confirmBtnName="Retrieve" @confirmed="retrieveNote"
      @close="dialog.retrieveConfirm = false" />
  </v-dialog>
  <v-dialog v-model="dialog.deleteConfirm" max-width="600">
    <ConfirmCard icon="mdi-delete-outline" title="Delete Note" message="Are you sure you want to delete this note?"
      description="Once the note is deleted, it will be permanently deleted." confirmBtnName="Delete"
      confirmBtnColor="error" @confirmed="deleteNote" @close="dialog.deleteConfirm = false" />
  </v-dialog>
  <v-dialog v-model="dialog.sortMenu" max-width="600" scrollable>
    <NoteSortMenu :sort @close="dialog.sortMenu = false" @apply="sortApply" />
  </v-dialog>
  <v-dialog v-model="dialog.filterMenu" max-width="600" scrollable>
    <NoteFilterMenu :filter @close="dialog.filterMenu = false" @apply="filterApply" />
  </v-dialog>
  <v-dialog v-model="dialog.enlargedImage" close-on-content-click maxWidth="1000px">
    <v-img :src="previewImagePath" height="90vh" />
  </v-dialog>
  <v-dialog v-model="dialog.likedUserList" maxWidth="600px">
    <LikedUserList :targetNote @close="dialog.likedUserList = false" />
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
<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, type Ref } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Note from '@/Components/Note.vue';
import NoteCreateForm from '@/Components/NoteCreateForm.vue';
import NoteEditForm from '@/Components/NoteEditForm.vue';
import ConfirmCard from '@/Components/ConfirmCard.vue';

const search = ref('');
const notes = ref([]);
const dialog = ref({
  create: false,
  edit: false,
  archiveConfirm: false,
  deleteConfirm: false
});
const snackbar = ref({
  display: false,
  message: ''
});
const targetId = ref(0);

const bottomElement: Ref<HTMLElement | null> = ref();

let observer: IntersectionObserver | null = null;

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
  await axios.get(route('notes.index'), {
    params: {
      notesLength: notes.value.length
    }
  })
    .then(response => {
      notes.value.push(...response.data);
    })
    .catch(error => {
      console.log(error);
    });
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

const showEditDialog = (id: number): void => {
  targetId.value = id;
  dialog.value.edit = true;
};

const showArchiveConfirmDialog = (id: number): void => {
  targetId.value = id;
  dialog.value.archiveConfirm = true;
};

const showDeleteConfirmDialog = (id: number): void => {
  targetId.value = id;
  dialog.value.deleteConfirm = true;
};

const archiveNote = async () => {
  dialog.value.archiveConfirm = false;
  await axios.put(route('notes.archive', targetId.value))
    .then(async () => {
      await refreshDisplay();
      showSnackBar('Archived Successfully.');
    })
    .catch(error => {
      console.log(error);
    });
};

const deleteNote = async () => {
  dialog.value.deleteConfirm = false;
  await axios.delete(route('notes.destroy', targetId.value))
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

const refreshDisplay = async () => {
  observer?.disconnect();
  targetId.value = 0;
  notes.value.splice(0);
  await loadNotes();
  observer.observe(bottomElement.value);
};
</script>

<template>

  <Head title="Dashboard" />
  <v-snackbar v-model="snackbar.display" location="top right" color="success" timeout="3000">
    <v-icon class="me-3" style="margin-bottom: 2px;">mdi-check-circle</v-icon>{{ snackbar.message }}
  </v-snackbar>
  <AuthenticatedLayout>
    <template #action>
      <v-text-field v-model="search" density="compact" label="Search" prepend-inner-icon="mdi-magnify"
        variant="solo-filled" flat hide-details single-line></v-text-field>
      <v-spacer></v-spacer>
      <v-btn icon="mdi-plus" variant="flat" @click="dialog.create = true"></v-btn>
      <v-btn icon="mdi-sort" variant="flat"></v-btn>
      <v-btn icon="mdi-filter-menu-outline" variant="flat"></v-btn>
    </template>
    <v-container>
      <v-row>
        <v-col v-for="note in notes" cols="12">
          <Note :note>
            <template #actions>
              <v-icon size="small" class="ms-5" icon="mdi-pencil-outline" @click="showEditDialog(note.id)" />
              <v-icon size="small" class="ms-5" icon="mdi-archive-outline" @click="showArchiveConfirmDialog(note.id)" />
              <v-icon size="small" class="ms-5" icon="mdi-delete-outline" @click="showDeleteConfirmDialog(note.id)" />
            </template>
          </Note>
        </v-col>
      </v-row>
    </v-container>
    <v-dialog v-model="dialog.create" fullscreen>
      <NoteCreateForm @noteCreated="noteCreated" @close="dialog.create = false" />
    </v-dialog>
    <v-dialog v-model="dialog.edit" fullscreen>
      <NoteEditForm :targetId @noteUpdated="noteUpdated" @close="dialog.edit = false" />
    </v-dialog>
    <v-dialog v-model="dialog.archiveConfirm" max-width="600">
      <ConfirmCard title="Archive Note" message="Are you sure you want to archive this note?" icon="mdi-archive-outline"
        confirmBtnName="Archive" @confirmed="archiveNote" />
    </v-dialog>
    <v-dialog v-model="dialog.deleteConfirm" max-width="600">
      <ConfirmCard icon="mdi-delete-outline" title="Delete Note" message="Are you sure you want to delete this note?"
        description="Once the note is deleted, it will be permanently deleted." confirmBtnName="Delete"
        confirmBtnColor="error" @confirmed="deleteNote" />
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
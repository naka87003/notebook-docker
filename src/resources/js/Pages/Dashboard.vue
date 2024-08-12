<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { useGoTo } from 'vuetify'
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import NoteCreateDialog from '@/Components/NoteCreateDialog.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import Note from '@/Components/Note.vue';

const goTo = useGoTo();
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

onMounted((): void => {
  loadNotes();
});

const loadNotes = async (): Promise<void> => {
  axios.get(route('notes.index'))
    .then(response => {
      notes.value = response.data;
    })
    .catch(error => {
      console.log(error);
    });
};

const noteCreated = async () => {
  dialog.value.create = false;
  await loadNotes();
  await goTo(0);
  showSnackBar('Created Successfully.');
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
    .then(response => {
      targetId.value = 0;
      showSnackBar('Archived Successfully.');
      loadNotes();
    })
    .catch(error => {
      console.log(error);
    });
};

const deleteNote = async () => {
  dialog.value.deleteConfirm = false;
  await axios.delete(route('notes.destroy', targetId.value))
    .then(response => {
      targetId.value = 0;
      showSnackBar('Deleted Successfully.');
      loadNotes();
    })
    .catch(error => {
      console.log(error);
    });
};

const showSnackBar = (msg: string): void => {
  snackbar.value.message = msg;
  snackbar.value.display = true;
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
      <v-btn icon="mdi-filter-menu" variant="flat"></v-btn>
    </template>
    <v-container>
      <v-row>
        <v-col v-for="note in notes" cols="12">
          <Note :note>
            <template #actions>
              <v-icon size="small" class="ms-5" icon="mdi-pencil-outline"></v-icon>
              <v-icon size="small" class="ms-5" icon="mdi-archive-outline" @click="showArchiveConfirmDialog(note.id)" />
              <v-icon size="small" class="ms-5" icon="mdi-delete-outline" @click="showDeleteConfirmDialog(note.id)" />
            </template>
          </Note>
        </v-col>
      </v-row>
    </v-container>
    <NoteCreateDialog v-model="dialog.create" @noteCreated="noteCreated" />
    <ConfirmDialog v-model="dialog.archiveConfirm" title="Archive Note"
      message="Are you sure you want to archive this note?" icon="mdi-archive-outline" confirmBtnName="Archive"
      max-width="600" @confirmed="archiveNote" />
    <ConfirmDialog v-model="dialog.deleteConfirm" icon="mdi-delete-outline" title="Delete Note"
      message="Are you sure you want to delete this note?"
      description="Once the note is deleted, it will be permanently deleted." confirmBtnName="Delete"
      confirmBtnColor="error" max-width="600" @confirmed="deleteNote" />
  </AuthenticatedLayout>
</template>

<style>
.note-paragraph {
  background-image: linear-gradient(180deg, rgba(204, 204, 204, 0) 0%, rgba(204, 204, 204, 0) 98.5%, rgba(100, 100, 100, 100) 100%);
  background-repeat: repeat-y;
  background-size: 100% 1.7em;
  line-height: 1.7;
}
</style>
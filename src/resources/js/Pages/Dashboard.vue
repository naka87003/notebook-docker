<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import dayjs from 'dayjs';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import NoteCreateDialog from '@/Components/NoteCreateDialog.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';

const search = ref('');
const notes = ref([]);
const dialog = ref({
  create: false,
  edit: false,
  archiveConfirm: false,
  deleteConfirm: false
});
const snackbar = ref(false);
const targetId = ref(0);

onMounted((): void => {
  loadNotes();
});

const loadNotes = (): void => {
  axios.get(route('notes.index'))
    .then(response => {
      notes.value = response.data;
    })
    .catch(error => {
      console.log(error);
    });
};
const simplifyDateTime = (str: string): string => dayjs(str).format('YYYY/MM/DD HH:mm');
const splitByNewline = (text: string): string[] => text.split(/\r?\n/);

const noteCreated = () => {
  dialog.value.create = false;
  loadNotes();
  snackbar.value = true;
};

const showArchiveConfirmDialog = (id: number): void => {
  targetId.value = id;
  dialog.value.archiveConfirm = true;
};

const showDeleteConfirmDialog = (id: number): void => {
  targetId.value = id;
  dialog.value.deleteConfirm = true;
};

const deleteNote = () => {
  dialog.value.deleteConfirm = false;
  console.log(targetId.value);
  targetId.value = 0;
};
</script>

<template>

  <Head title="Dashboard" />
  <v-snackbar v-model="snackbar" location="top right" color="success" timeout="3000">
    <v-icon class="me-3" style="margin-bottom: 2px;">mdi-check-circle</v-icon>New Note Created Successfully.
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
          <v-card :color="note.category.vuetify_theme_color_name" variant="tonal" class="mx-auto"
            :prepend-icon="note.category.mdi_name" :title="note.title" rounded="0">
            <template v-slot:prepend>
              <v-icon size="large"></v-icon>
            </template>
            <template v-slot:append>
              <p class="text-caption">
                {{ simplifyDateTime(note.updated_at) }}
              </p>
            </template>
            <v-divider class="border-opacity-25 mx-1" />
            <v-card-text class="text-h6 py-2">
              <v-alert v-if="note.category.id === 3" type="info" variant="tonal" class="mb-3">
                <p class="text-body-2">from {{ simplifyDateTime(note.starts_at) }}</p>
                <p class="text-body-2">to {{ simplifyDateTime(note.ends_at) }}</p>
              </v-alert>
              <p v-for="paragraph in splitByNewline(note.description)" class="note-paragraph">{{ paragraph }}</p>
            </v-card-text>
            <v-card-actions>
              <v-list-item class="w-100">
                <template v-slot:prepend>
                  <div class="justify-self-end">
                    <template v-if="note.tag">
                      <v-icon :color="note.tag?.hex_color" size="small" class="me-1" icon="mdi-tag"></v-icon>
                      <span class="me-5 text-caption">{{ note.tag?.name }}</span>
                    </template>
                    <v-icon v-if="note.public === false" size="small" class="me-5" icon="mdi-lock-outline"></v-icon>
                  </div>
                </template>
                <template v-slot:append>
                  <div class="justify-self-end">
                    <v-icon size="small" class="ms-5" icon="mdi-pencil-outline"></v-icon>
                    <v-icon size="small" class="ms-5" icon="mdi-archive-outline"
                      @click="showArchiveConfirmDialog(note.id)" />
                    <v-icon size="small" class="ms-5" icon="mdi-delete-outline" @click="showDeleteConfirmDialog(note.id)" />
                  </div>
                </template>
              </v-list-item>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
    <NoteCreateDialog v-model="dialog.create" @noteCreated="noteCreated" />
    <ConfirmDialog v-model="dialog.archiveConfirm" title="Archive Note"
      message="Are you sure you want to archive this note?" icon="mdi-archive-outline" confirmBtnName="Archive" max-width="600"
      @confirmed="deleteNote" />
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
<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { watchDebounced } from '@vueuse/core'
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TagCreateForm from '@/Components/TagCreateForm.vue';
import TagEditForm from '@/Components/TagEditForm.vue';

const searchText = ref('');

const dialog = ref({
  create: false,
  edit: false
});

const snackbar = ref({
  display: false,
  message: ''
});

const search = ref('');
const itemsPerPage = ref(10);
const headers = ref([
  { title: 'Tag', key: 'name' },
  { title: 'Normal', key: 'normal_count' },
  { title: 'Archived', key: 'archived_count' },
  { title: 'Actions', key: 'actions', sortable: false },
]) as any;

const items = ref([]);
const loading = ref(true);
const totalItems = ref(0);

const targetTag = ref();

watchDebounced(
  searchText,
  () => search.value = String(Date.now()),
  { debounce: 500, maxWait: 1000 },
)

const loadItems = async ({ page, itemsPerPage, sortBy }) => {
  loading.value = true
  await axios.get(route('tags.items.datatable'), {
    params: { page, itemsPerPage, sortBy, search: searchText.value, }
  })
    .then(response => {
      items.value = response.data.items;
      totalItems.value = response.data.count;
    })
    .catch(error => {
      console.log(error);
    });
  loading.value = false
};

const editItem = (item) => {
  targetTag.value = item;
  dialog.value.edit = true;
};

const deleteItem = (item) => {

};

const tagCreated = async () => {
  search.value = String(Date.now());
  dialog.value.create = false;
  showSnackBar('Created Successfully.');
};
const tagUpdated = async () => {
  search.value = String(Date.now());
  dialog.value.edit = false;
  showSnackBar('Updated Successfully.');
};

const showSnackBar = (msg: string): void => {
  snackbar.value.message = msg;
  snackbar.value.display = true;
};
</script>
<template>

  <Head title="Tag" />
  <v-snackbar v-model="snackbar.display" location="top right" color="success" timeout="3000">
    <v-icon class="me-3" style="margin-bottom: 2px;">mdi-check-circle</v-icon>{{ snackbar.message }}
  </v-snackbar>
  <AuthenticatedLayout>
    <template #action>
      <v-text-field v-model="searchText" density="compact" label="Search" prepend-inner-icon="mdi-magnify"
        variant="solo-filled" flat hide-details single-line></v-text-field>
      <v-spacer></v-spacer>
      <v-btn icon="mdi-tag-plus" variant="flat" @click="dialog.create = true" />
    </template>
    <v-container>
      <v-data-table-server v-model:items-per-page="itemsPerPage" :headers :items :items-length="totalItems"
        :loading="loading" :search item-value="name" @update:options="loadItems">
        <template v-slot:item.name="{ item }">
          <v-icon class="me-3" size="small" :color="item.hex_color" icon="mdi-tag" />
          {{ item.name }}
        </template>
        <template v-slot:item.normal_count="{ item }">
          <v-btn v-if="item.normal_count !== null" variant="plain" color="primary">{{ item.normal_count }}</v-btn>
          <v-btn v-else variant="plain" disabled>0</v-btn>
        </template>
        <template v-slot:item.archived_count="{ item }">
          <v-btn v-if="item.archived_count !== null" variant="plain" color="primary">{{ item.archived_count }}</v-btn>
          <v-btn v-else variant="plain" disabled>0</v-btn>
        </template>
        <template v-slot:item.actions="{ item }">
          <v-icon class="me-3" size="small" @click="editItem(item)">
            mdi-pencil
          </v-icon>
          <v-icon size="small" @click="deleteItem(item)">
            mdi-delete
          </v-icon>
        </template>
      </v-data-table-server>
    </v-container>
    <v-dialog v-model="dialog.create" max-width="600">
      <TagCreateForm @close="dialog.create = false" @tagCreated="tagCreated" />
    </v-dialog>
    <v-dialog v-model="dialog.edit" max-width="600">
      <TagEditForm :targetTag @close="dialog.edit = false" @tagUpdated="tagUpdated" />
    </v-dialog>
  </AuthenticatedLayout>
</template>
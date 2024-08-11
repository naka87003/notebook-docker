<script setup lang="ts">
import { ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';

const emit = defineEmits<{
  close: [],
  noteCreated: []
}>();

const page = usePage();
const form = useForm({
  title: '',
  description: '',
  category: 1,
  public: true
});

const items = ref({
  category: page.props.categoryItems as { 'title': string, 'value': number }[]
});


const submit = () => {
  form.post(route('notes.store'), {
    onSuccess: () => emit('noteCreated'),
  });
};
</script>
<template>
  <v-card>
    <v-toolbar density="comfortable" color="transparent">
      <v-toolbar-title class="text-h6" text="New Note"></v-toolbar-title>
      <template v-slot:append>
        <v-btn icon="mdi-close" @click="$emit('close')"></v-btn>
      </template>
    </v-toolbar>
    <v-divider />
    <v-card-text>
      <form @submit.prevent="submit">
        <v-row>
          <v-col cols="12" lg="6">
            <div class="text-subtitle-1 text-medium-emphasis">Description</div>
            <v-textarea v-model="form.description" hide-details="auto" type="text" density="compact"
              placeholder="Enter Desctiption" variant="outlined" :error="Boolean(form.errors.description)"
              :error-messages="form.errors.description" required autocomplete="username"
              @input="form.errors.description = null" />
          </v-col>
          <v-col cols="12" lg="6">
            <div class="text-subtitle-1 text-medium-emphasis">Title</div>
            <v-text-field v-model="form.title" hide-details="auto" type="text" density="compact"
              placeholder="Enter Title" variant="outlined" :error="Boolean(form.errors.title)"
              :error-messages="form.errors.title" required autofocus autocomplete="username" maxLength="20"
              @input="form.errors.title = null" />
          </v-col>
          <v-col cols="12" lg="6">
            <div class="text-subtitle-1 text-medium-emphasis">Category</div>
            <v-select v-model="form.category" hide-details="auto" :items="items.category" density="compact"
              placeholder="Enter Desctiption" variant="outlined" :error="Boolean(form.errors.category)"
              :error-messages="form.errors.category" required autocomplete="username"
              @input="form.errors.category = null" />
          </v-col>
          <v-col cols="12" lg="6">
            <div class="text-subtitle-1 text-medium-emphasis">Visibility</div>
            <v-radio-group v-model="form.public" column :error-messages="form.errors.public"
              :error="Boolean(form.errors.public)" hide-details="auto">
              <v-radio label="Public" :value="true" />
              <v-radio label="Private" :value="false" />
            </v-radio-group>
          </v-col>
        </v-row>
      </form>
    </v-card-text>
    <v-divider />
    <template v-slot:actions>
      <v-spacer></v-spacer>
      <v-btn variant="plain" @click="$emit('close')">Close</v-btn>
      <v-btn color="primary" variant="tonal" @click="submit">Save</v-btn>
    </template>
  </v-card>
</template>
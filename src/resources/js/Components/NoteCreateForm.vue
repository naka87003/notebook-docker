<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import TagCreateForm from './TagCreateForm.vue';
import { getTagSelectItems } from '@/common';
import type { Category, Tag } from '@/interfaces';

const emit = defineEmits<{
  close: []
  noteCreated: []
}>();

const page = usePage();
const form = useForm({
  title: '',
  description: '',
  category: 1,
  public: true,
  tag: null,
  starts: dayjs().add(1, 'hour').format('YYYY-MM-DDTHH:00'),
  ends: dayjs().add(2, 'hour').format('YYYY-MM-DDTHH:00')
});

const dialog = ref({
  tagCreate: false
});

const allDay = ref(false);

const items = ref({
  category: page.props.categoryItems as Category[],
  tag: [] as Tag[],
});

const startsDate = computed({
  get() {
    return dayjs(form.starts).format('YYYY-MM-DD');
  },
  set(newValue) {
    form.starts = dayjs(newValue).format('YYYY-MM-DD 00:00');
  }
});

const endsDate = computed({
  get() {
    return dayjs(form.ends).format('YYYY-MM-DD');
  },
  set(newValue) {
    form.ends = dayjs(newValue).format('YYYY-MM-DD 23:59');
  }
});

onMounted(async () => {
  items.value.tag = await getTagSelectItems();
});

const toAllDayRange = () => {
  form.starts = dayjs(form.starts).format('YYYY-MM-DD 00:00');
  form.ends = dayjs(form.ends).format('YYYY-MM-DD 23:59');
};

const tagCreated = async () => {
  items.value.tag = await getTagSelectItems();
  form.tag = items.value.tag[0].id;
  dialog.value.tagCreate = false;
};

const submit = () => {
  form.post(route('notes.store'), {
    onSuccess: () => {
      emit('noteCreated')
    },
  });
};
</script>
<template>
  <v-card>
    <v-toolbar density="comfortable" color="transparent">
      <v-toolbar-title class="text-h6" text="Create New Note"></v-toolbar-title>
      <template v-slot:prepend>
        <v-icon class="ms-3" icon="mdi-plus" />
      </template>
      <template v-slot:append>
        <v-btn icon="mdi-close" @click="$emit('close')"></v-btn>
      </template>
    </v-toolbar>
    <v-divider />
    <v-card-text>
      <form @submit.prevent="submit">
        <v-row>
          <v-col cols="12" md="6">
            <v-row>
              <v-col cols="12">
                <div class="text-subtitle-1 text-medium-emphasis">Description</div>
                <v-textarea v-model="form.description" hide-details="auto" type="text" density="compact"
                  placeholder="Enter Description" variant="outlined" :error="Boolean(form.errors.description)"
                  :error-messages="form.errors.description" required autofocus auto-grow
                  @input="form.errors.description = null" />
              </v-col>
            </v-row>
          </v-col>
          <v-col cols="12" md="6">
            <v-row>
              <v-col cols="12">
                <div class="text-subtitle-1 text-medium-emphasis">Title</div>
                <v-text-field v-model="form.title" hide-details="auto" type="text" density="compact"
                  placeholder="Enter Title" variant="outlined" :error="Boolean(form.errors.title)"
                  :error-messages="form.errors.title" required maxLength="20" @input="form.errors.title = null" />
              </v-col>
              <v-col cols="12">
                <div class="text-subtitle-1 text-medium-emphasis">Category</div>
                <v-autocomplete v-model="form.category" hide-details="auto" :items="items.category" density="compact"
                  placeholder="Select Category" variant="outlined" :error="Boolean(form.errors.category)"
                  :error-messages="form.errors.category" required item-title="name" item-value="id"
                  @input="form.errors.category = null">
                  <template v-slot:item="{ props, item }">
                    <v-list-item v-bind="props" prepend-icon="mdi-tag" :title="item.raw.name">
                      <template v-slot:prepend>
                        <v-icon :icon="item.raw.mdi_name" />
                      </template>
                    </v-list-item>
                  </template>
                  <template v-slot:selection="{ item }">
                    <v-list-item prepend-icon="mdi-tag" :title="item.raw.name">
                      <template v-slot:prepend>
                        <v-icon :icon="item.raw.mdi_name" />
                      </template>
                    </v-list-item>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col v-if="form.category === 3" cols="12" lg="6">
                <div class="text-subtitle-1 text-medium-emphasis">DateTime</div>
                <v-switch v-model="allDay" color="primary" density="compact" label="All-day" hide-details inset
                  @update:modelValue="toAllDayRange"></v-switch>
                <template v-if="allDay === false">
                  <v-text-field v-model="form.starts" class="mt-3" label="Starts" hide-details="auto"
                    type="datetime-local" density="compact" variant="outlined" :error="Boolean(form.errors.starts)"
                    :error-messages="form.errors.starts" required @input="form.errors.starts = null" />
                  <v-text-field v-model="form.ends" class="mt-3" label="Ends" hide-details="auto" type="datetime-local"
                    density="compact" variant="outlined" :error="Boolean(form.errors.ends)"
                    :error-messages="form.errors.ends" required @input="form.errors.ends = null" />
                </template>
                <template v-else>
                  <v-text-field v-model="startsDate" class="mt-3" label="Starts" hide-details="auto" type="date"
                    density="compact" variant="outlined" :error="Boolean(form.errors.starts)"
                    :error-messages="form.errors.starts" required @input="form.errors.starts = null" />
                  <v-text-field v-model="endsDate" class="mt-3" label="Ends" hide-details="auto" type="date"
                    density="compact" variant="outlined" :error="Boolean(form.errors.ends)"
                    :error-messages="form.errors.ends" required @input="form.errors.ends = null" />
                </template>
              </v-col>
              <v-col cols="12">
                <div class="text-subtitle-1 text-medium-emphasis">Tag</div>
                <v-autocomplete v-model="form.tag" hide-details="auto" :items="items.tag" density="compact"
                  placeholder="Select Tag" variant="outlined" :error="Boolean(form.errors.tag)"
                  append-icon="mdi-tag-plus" :error-messages="form.errors.tag" required item-title="name"
                  item-value="id" clearable @input="form.errors.tag = null" @click:append="dialog.tagCreate = true">
                  <template v-slot:item="{ props, item }">
                    <v-list-item v-bind="props" prepend-icon="mdi-tag" :title="item.raw.name">
                      <template v-slot:prepend>
                        <v-icon icon="mdi-tag" :color="item.raw.hex_color" />
                      </template>
                    </v-list-item>
                  </template>
                  <template v-slot:selection="{ item }">
                    <v-list-item prepend-icon="mdi-tag" :title="item.raw.name">
                      <template v-slot:prepend>
                        <v-icon icon="mdi-tag" :color="item.raw.hex_color" />
                      </template>
                    </v-list-item>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col cols="12">
                <div class="text-subtitle-1 text-medium-emphasis">Visibility</div>
                <v-radio-group v-model="form.public" column :error-messages="form.errors.public"
                  :error="Boolean(form.errors.public)" hide-details="auto">
                  <v-radio label="Public" :value="true" />
                  <v-radio label="Private" :value="false" />
                </v-radio-group>
              </v-col>
            </v-row>
          </v-col>
        </v-row>
      </form>
    </v-card-text>
    <v-divider />
    <template v-slot:actions>
      <v-spacer></v-spacer>
      <v-btn variant="plain" @click="$emit('close')">Close</v-btn>
      <v-btn color="primary" variant="tonal" :disabled="form.processing" @click="submit">Save</v-btn>
    </template>
  </v-card>
  <v-dialog v-model="dialog.tagCreate" max-width="600">
    <TagCreateForm @close="dialog.tagCreate = false" @tagCreated="tagCreated" />
  </v-dialog>
</template>
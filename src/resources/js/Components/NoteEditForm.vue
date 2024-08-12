<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import dayjs from 'dayjs';

const props = defineProps<{
  targetId: number;
}>();

const emit = defineEmits<{
  close: []
  noteUpdated: []
}>();

const page = usePage();
const form = useForm({
  id: props.targetId,
  title: '',
  description: '',
  public: true,
  category: 1,
  tag: null,
  starts: dayjs().add(1, 'hour').format('YYYY-MM-DDTHH:00'),
  ends: dayjs().add(2, 'hour').format('YYYY-MM-DDTHH:00')
});

const allDay = ref(false);

const items = ref({
  category: page.props.categoryItems as { 'id': string, 'name': number, 'mdi_name': string }[],
  tag: [] as { 'id': string, 'name': number, 'hex_color': string }[],
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
  await axios.get(route('notes.edit', props.targetId))
    .then(response => {
      form.title = response.data.title;
      form.description = response.data.description;
      form.public = response.data.public;
      form.category = response.data.category_id;
      form.tag = response.data.tag_id;
      if (response.data.starts_at !== null && response.data.ends_at !== null) {
        form.starts = dayjs(response.data.starts_at).format('YYYY-MM-DDTHH:mm');
        form.ends = dayjs(response.data.ends_at).format('YYYY-MM-DDTHH:mm');
      }
    })
    .catch(error => {
      console.log(error);
    });
  await getTagSelectItems();
});

const getTagSelectItems = async (): Promise<void> => {
  await axios.get(route('tags.selectItems'))
    .then(response => {
      items.value.tag = response.data;
    })
    .catch(error => {
      console.log(error);
    });
};

const toAllDayRange = () => {
  form.starts = dayjs(form.starts).format('YYYY-MM-DD 00:00');
  form.ends = dayjs(form.ends).format('YYYY-MM-DD 23:59');
};

const submit = () => {
  form.put(route('notes.update', props.targetId), {
    onSuccess: () => {
      emit('noteUpdated')
    },
  });
};
</script>
<template>
  <v-card>
    <v-toolbar density="comfortable" color="transparent">
      <v-toolbar-title class="text-h6" text="Edit Note"></v-toolbar-title>
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
              :error-messages="form.errors.description" required autofocus autocomplete="username"
              @input="form.errors.description = null" />
          </v-col>
          <v-col cols="12" lg="6">
            <div class="text-subtitle-1 text-medium-emphasis">Title</div>
            <v-text-field v-model="form.title" hide-details="auto" type="text" density="compact"
              placeholder="Enter Title" variant="outlined" :error="Boolean(form.errors.title)"
              :error-messages="form.errors.title" required autocomplete="username" maxLength="20"
              @input="form.errors.title = null" />
          </v-col>
          <v-col cols="12" lg="6">
            <div class="text-subtitle-1 text-medium-emphasis">Category</div>
            <v-autocomplete v-model="form.category" hide-details="auto" :items="items.category" density="compact"
              placeholder="Select Category" variant="outlined" :error="Boolean(form.errors.category)"
              :error-messages="form.errors.category" required autocomplete="username" item-title="name" item-value="id"
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
              <v-text-field v-model="form.starts" class="mt-3" label="Starts" hide-details="auto" type="datetime-local"
                density="compact" placeholder="Enter Desctiption" variant="outlined"
                :error="Boolean(form.errors.starts)" :error-messages="form.errors.starts" required
                autocomplete="username" @input="form.errors.starts = null" />
              <v-text-field v-model="form.ends" class="mt-3" label="Ends" hide-details="auto" type="datetime-local"
                density="compact" placeholder="Enter Desctiption" variant="outlined" :error="Boolean(form.errors.ends)"
                :error-messages="form.errors.ends" required autocomplete="username" @input="form.errors.ends = null" />
            </template>
            <template v-else>
              <v-text-field v-model="startsDate" class="mt-3" label="Starts" hide-details="auto" type="date"
                density="compact" placeholder="Enter Desctiption" variant="outlined"
                :error="Boolean(form.errors.starts)" :error-messages="form.errors.starts" required
                autocomplete="username" @input="form.errors.starts = null" />
              <v-text-field v-model="endsDate" class="mt-3" label="Ends" hide-details="auto" type="date"
                density="compact" placeholder="Enter Desctiption" variant="outlined" :error="Boolean(form.errors.ends)"
                :error-messages="form.errors.ends" required autocomplete="username" @input="form.errors.ends = null" />
            </template>
          </v-col>
          <v-col cols="12" lg="6">
            <div class="text-subtitle-1 text-medium-emphasis">Tag</div>
            <v-autocomplete v-model="form.tag" hide-details="auto" :items="items.tag" density="compact"
              placeholder="Select Tag" variant="outlined" :error="Boolean(form.errors.tag)"
              :error-messages="form.errors.tag" required autocomplete="username" item-title="name" item-value="id"
              clearable @input="form.errors.tag = null">
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
      <v-btn color="primary" variant="tonal" :disabled="form.processing" @click="submit">Save</v-btn>
    </template>
  </v-card>
</template>
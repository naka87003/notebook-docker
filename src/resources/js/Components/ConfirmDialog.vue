<script setup lang="ts">
withDefaults(defineProps<{
  icon?: string;
  title?: string;
  message?: string;
  description?: string;
  confirmBtnName?: string;
  confirmBtnColor?: string;
  variant?: string;
}>(), { title: 'Title', message: 'Message', confirmBtnName: 'Confirm' });

defineEmits<{
  close: [],
  confirmed: []
}>();

const dialogShow = defineModel<boolean>();
</script>
<template>
  <v-dialog v-model="dialogShow" persistent>
    <v-card>
      <v-toolbar density="comfortable" color="transparent">
        <v-toolbar-title class="text-h6" :text="title"></v-toolbar-title>
        <template v-if="icon" v-slot:prepend>
          <v-icon class="ms-3" :icon />
        </template>
        <template v-slot:append>
          <v-btn icon="mdi-close" @click="dialogShow = false"></v-btn>
        </template>
      </v-toolbar>
      <v-divider />
      <v-card-text>
        {{ message }}
        <v-card v-if="description" class="ma-2" color="surface-variant" variant="tonal">
          <v-card-text class="text-medium-emphasis text-caption">
            <p>{{ description }}</p>
          </v-card-text>
        </v-card>
      </v-card-text>
      <v-divider />
      <template v-slot:actions>
        <v-spacer></v-spacer>
        <v-btn variant="plain" @click="dialogShow = false">Cancel</v-btn>
        <v-btn :color="confirmBtnColor" variant="tonal" @click="$emit('confirmed')">{{ confirmBtnName }}</v-btn>
      </template>
    </v-card>
  </v-dialog>
</template>
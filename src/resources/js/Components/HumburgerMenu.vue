<script setup lang="ts">
defineProps<{
  currentPageName: string;
}>();

defineEmits<{
  logout: [];
  close: [];
}>();

const isDark = defineModel('isDark');
</script>

<template>
  <v-card>
    <v-toolbar density="comfortable" color="transparent">
      <v-toolbar-title class="text-h6" text="Menu"></v-toolbar-title>
      <template v-slot:append>
        <v-btn icon="mdi-close" @click="$emit('close')"></v-btn>
      </template>
    </v-toolbar>
    <v-divider />
    <v-card-text  class="pa-0">
      <v-list lines="two">
        <v-list-subheader>Page</v-list-subheader>
        <v-list-item title="Note" density="compact" :active="currentPageName === 'dashboard'"
          :href="route('dashboard')">
          <template v-slot:prepend>
            <v-avatar color="info" density="compact">
              <v-icon>mdi-book-open-outline</v-icon>
            </v-avatar>
          </template>
        </v-list-item>
        <v-list-item title="Calendar" density="compact" :active="currentPageName === 'calendar'"
          :href="route('calendar')">
          <template v-slot:prepend>
            <v-avatar color="warning" density="compact">
              <v-icon>mdi-calendar-multiselect-outline</v-icon>
            </v-avatar>
          </template>
        </v-list-item><v-divider></v-divider>
        <v-list-subheader>Theme</v-list-subheader>
        <v-list-item density="compact" class="mb-n3">
          <v-radio-group v-model="isDark">
            <v-radio :value="false">
              <template #label>
                <v-icon icon="mdi-weather-sunny mr-2"></v-icon>
                Light
              </template>
            </v-radio>
            <v-radio :value="true">
              <template #label>
                <v-icon icon="mdi-weather-night mr-2"></v-icon>
                Dark
              </template>
            </v-radio>
          </v-radio-group>
        </v-list-item>
        <v-divider></v-divider>
        <v-list-subheader>Account</v-list-subheader>
        <v-list-item :title="$page.props.auth.user.name" :subtitle="$page.props.auth.user.email" density="compact"
          :active="currentPageName === 'profile.edit'" :href="route('profile.edit')">
          <template v-slot:prepend>
            <v-avatar color="surface-light" density="compact">
              <v-icon>mdi-account</v-icon>
            </v-avatar>
          </template>
        </v-list-item>
        <v-list-item title="Logout" @click="$emit('logout')">
          <template v-slot:prepend>
            <v-avatar color="secondary" density="compact">
              <v-icon>mdi-logout</v-icon>
            </v-avatar>
          </template>
        </v-list-item>
      </v-list>
    </v-card-text>
    <v-divider />
    <template v-slot:actions>
      <v-spacer></v-spacer>
      <v-btn variant="plain" @click="$emit('close')">Close</v-btn>
    </template>
  </v-card>
</template>
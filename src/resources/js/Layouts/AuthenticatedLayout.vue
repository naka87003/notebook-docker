<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { useTheme } from 'vuetify'
import { useDark } from '@vueuse/core';

const theme = useTheme();
const isDark = useDark();

const xsMenu = ref(false);

watch(isDark, (value) => {
  theme.global.name.value = value ? 'dark' : 'light';
}, { immediate: true });

const currentPageName = computed({
  get() {
    return route().current();
  },
  set() { }
});

const logout = () => {
  router.post('logout');
};
</script>

<template>
  <v-layout>
    <v-app-bar :extended="Boolean($slots.action)">
      <template v-if="$slots.action" #extension>
        <v-container class="mx-auto d-flex align-center justify-center">
        <slot name="action" />
        </v-container>
      </template>
      <v-container class="mx-auto d-flex align-center justify-center">
        <v-img class="mx-auto my-6" max-width="60"
          src="https://cdn.vuetifyjs.com/docs/images/brand-kit/v-logo.svg"></v-img>
        <v-tabs v-model="currentPageName" class="hidden-xs">
          <v-tab value="dashboard" :href="route('dashboard')">Notebook</v-tab>
          <v-tab value="calendar" :href="route('calendar')">Calendar</v-tab>
        </v-tabs>
        <v-spacer />
        <v-divider vertical class="hidden-sm-and-down" />
        <v-btn :icon="isDark ? 'mdi-weather-night' : 'mdi-weather-sunny'" class="hidden-sm-and-down mx-3"
          @click="isDark = !isDark" />
        <v-divider vertical class="hidden-xs" />
        <v-menu class="hidden-sm-and-down">
          <template v-slot:activator="{ props }">
            <v-list-item v-bind="props" :title="$page.props.auth.user.name" class="hidden-sm-and-down">
              <template v-slot:prepend>
                <v-avatar color="surface-light"> <v-icon>mdi-account</v-icon></v-avatar>
              </template>
              <template v-slot:append>
                <v-icon>mdi-chevron-down</v-icon>
              </template>
            </v-list-item>
          </template>
          <v-list>
            <v-list-item :href="route('profile.edit')" :active="route().current() === 'profile.edit'">
              <v-list-item-title>Profile</v-list-item-title>
            </v-list-item>
            <v-list-item @click="logout">
              <v-list-item-title>Logout</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
        <v-btn icon="mdi-menu" size="x-large" class="hidden-md-and-up" @click="xsMenu = !xsMenu" />
      </v-container>
    </v-app-bar>
    <v-main>
      <v-container class="hidden-md-and-up">
        <v-list v-if="xsMenu" lines="two" density="compact">
          <v-list-subheader>Menu</v-list-subheader>
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
          <v-list-item title="Logout" @click="logout">
            <template v-slot:prepend>
              <v-avatar color="secondary" density="compact">
                <v-icon>mdi-logout</v-icon>
              </v-avatar>
            </template>
          </v-list-item>
        </v-list>
      </v-container>
      <slot />
    </v-main>
  </v-layout>
</template>

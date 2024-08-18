<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { useTheme } from 'vuetify'
import { useDark } from '@vueuse/core';
import HumburgerMenu from '@/Components/HumburgerMenu.vue';

const theme = useTheme();
const isDark = useDark();

const dialog = ref({
  humburgerMenu: false
});

watch(isDark, (value) => {
  theme.global.name.value = value ? 'dark' : 'light';
}, { immediate: true });

const currentPageName = computed({
  get() {
    return route().current();
  },
  set() { }
});

const userImagePath = computed((): string | null => usePage().props.auth.user.image_path);

const avatarImagePath = computed(() => userImagePath.value ? 'storage/' + userImagePath.value : null);

const logout = () => {
  router.post('logout');
};

const pageTransition = (name: string) => {
  router.visit(route(name));
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
        <v-tabs v-model="currentPageName" class="hidden-xs">
          <v-tab value="dashboard" @click="pageTransition('dashboard')">Notebook</v-tab>
          <v-tab value="calendar" @click="pageTransition('calendar')">Calendar</v-tab>
        </v-tabs>
        <v-spacer />
        <v-btn icon="mdi-tag-multiple-outline" class="hidden-sm-and-down" :active="currentPageName === 'tags.index'"
          @click="pageTransition('tags.index')" />
        <v-btn :icon="isDark ? 'mdi-weather-night' : 'mdi-weather-sunny'" class="hidden-sm-and-down ms-3"
          @click="isDark = !isDark" />
        <v-divider vertical class="hidden-xs ms-3" />
        <v-menu class="hidden-sm-and-down">
          <template v-slot:activator="{ props }">
            <v-list-item v-bind="props" :title="$page.props.auth.user.name" class="hidden-sm-and-down">
              <template v-slot:prepend>
                <v-avatar color="surface-light">
                  <v-img v-if="avatarImagePath" :src="avatarImagePath" />
                  <v-icon v-else icon="mdi-account" />
                </v-avatar>
              </template>
              <template v-slot:append>
                <v-icon>mdi-chevron-down</v-icon>
              </template>
            </v-list-item>
          </template>
          <v-list>
            <v-list-item @click="pageTransition('profile.edit')" :active="route().current() === 'profile.edit'">
              <v-list-item-title>Profile</v-list-item-title>
            </v-list-item>
            <v-list-item @click="logout">
              <v-list-item-title>Logout</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
        <v-btn icon="mdi-menu" size="x-large" class="hidden-md-and-up"
          @click="dialog.humburgerMenu = !dialog.humburgerMenu" />
      </v-container>
    </v-app-bar>
    <v-main>
      <v-container class="hidden-md-and-up">
        <v-dialog v-model="dialog.humburgerMenu" scrollable>
          <HumburgerMenu :currentPageName v-model:isDark="isDark" @logout="logout"
            @close="dialog.humburgerMenu = false" />
        </v-dialog>
      </v-container>
      <slot />
    </v-main>
  </v-layout>
</template>
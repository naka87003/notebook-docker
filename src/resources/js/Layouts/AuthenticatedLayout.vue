<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

const xsMenu = ref(false);

const currentPageName = computed((): string => route().current());

const logout = () => {
  router.post('logout');
};
</script>

<template>
  <v-layout>
    <v-app-bar>
      <v-container class="mx-auto d-flex align-center justify-center">
        <v-img class="mx-auto my-6" max-width="60"
          src="https://cdn.vuetifyjs.com/docs/images/brand-kit/v-logo.svg"></v-img>
        <v-tabs v-model="currentPageName" class="hidden-xs">
          <v-tab value="dashboard" :href="route('dashboard')">Note</v-tab>
          <v-tab value="calendar" :href="route('calendar')">Calendar</v-tab>
        </v-tabs>
        <v-spacer></v-spacer>
        <v-menu class="hidden-xs">
          <template v-slot:activator="{ props }">
            <v-list-item v-bind="props" :title="$page.props.auth.user.name" class="hidden-xs">
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
        <v-btn icon="mdi-menu" size="x-large" class="hidden-sm-and-up" @click="xsMenu = !xsMenu" />
      </v-container>
    </v-app-bar>
    <v-main>
      <v-list v-if="xsMenu" lines="two">
        <v-list-subheader>Menu</v-list-subheader>
        <v-list-item title="Note" :active="currentPageName === 'dashboard'" :href="route('dashboard')">
          <template v-slot:prepend>
            <v-avatar color="info">
              <v-icon>mdi-book-open-outline</v-icon>
            </v-avatar>
          </template>
        </v-list-item>
        <v-list-item title="Calendar" :active="currentPageName === 'calendar'" :href="route('calendar')">
          <template v-slot:prepend>
            <v-avatar color="warning">
              <v-icon>mdi-calendar-multiselect-outline</v-icon>
            </v-avatar>
          </template>
        </v-list-item>
        <v-divider></v-divider>
        <v-list-subheader>Account</v-list-subheader>
        <v-list-item :title="$page.props.auth.user.name" :subtitle="$page.props.auth.user.email"
          :active="currentPageName === 'profile.edit'" :href="route('profile.edit')">
          <template v-slot:prepend>
            <v-avatar color="surface-light">
              <v-icon>mdi-account</v-icon>
            </v-avatar>
          </template>
        </v-list-item>
        <v-list-item title="Logout" @click="logout">
          <template v-slot:prepend>
            <v-avatar color="secondary">
              <v-icon>mdi-logout</v-icon>
            </v-avatar>
          </template>
        </v-list-item>
      </v-list>
      <slot />
    </v-main>
  </v-layout>
</template>

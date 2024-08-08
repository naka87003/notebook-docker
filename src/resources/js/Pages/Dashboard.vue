<script setup>
import { Head } from '@inertiajs/vue3';
import Note from '@/Components/Note.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
</script>

<template>

  <Head title="Dashboard" />
  <AuthenticatedLayout>
    <v-container>
      <Note />
    </v-container>
  </AuthenticatedLayout>
</template>

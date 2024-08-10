<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const search = ref('');
</script>

<template>

  <Head title="Dashboard" />
  <AuthenticatedLayout>
    <template #action>
      <v-text-field v-model="search" density="compact" label="Search" prepend-inner-icon="mdi-magnify"
        variant="solo-filled" flat hide-details single-line></v-text-field>
      <v-spacer></v-spacer>
      <v-btn icon="mdi-plus" variant="flat"></v-btn>
      <v-btn icon="mdi-sort" variant="flat"></v-btn>
      <v-btn icon="mdi-filter-menu" variant="flat"></v-btn>
    </template>
    <v-container>
      <v-card class="mx-auto" prepend-icon="mdi-note-text-outline" title="toast">
        <template v-slot:prepend>
          <v-icon size="x-large"></v-icon>
        </template>
        <template v-slot:append>
          <p>
            2024/08/10 08:10
          </p>
        </template>
        <v-card-text class="text-h5 py-2">
          不可算名詞<br />
          How may slices of toast do you have for breakfast?
        </v-card-text>
        <v-card-actions>
          <v-list-item class="w-100">
            <template v-slot:prepend>
              <div class="justify-self-end">
                <v-icon class="me-1" icon="mdi-tag-outline"></v-icon>
                <span class="subheading me-5">English</span>
                <v-icon class="me-1" icon="mdi-lock-outline"></v-icon>
              </div>
            </template>
            <template v-slot:append>
              <div class="justify-self-end">
                <v-icon class="me-5" icon="mdi-pencil"></v-icon>
                <v-icon class="me-1" icon="mdi-archive"></v-icon>
              </div>
            </template>
          </v-list-item>
        </v-card-actions>
      </v-card>
      <v-divider class="mt-1" />
      <v-card color="primary" variant="tonal" class="mx-auto" prepend-icon="mdi-format-list-checks" title="買い物">
        <template v-slot:prepend>
          <v-icon size="x-large"></v-icon>
        </template>
        <template v-slot:append>
          <p >
            2024/08/09 20:47
          </p>
        </template>
        <v-card-text class="text-h5 py-2">
          豆腐<br />
          パン<br />
          ヨーグルト<br />
        </v-card-text>
        <v-card-actions>
          <v-list-item class="w-100">
            <template v-slot:prepend>
              <div class="justify-self-end">
                <v-icon class="me-1" icon="mdi-lock-outline"></v-icon>
              </div>
            </template>
            <template v-slot:append>
              <div class="justify-self-end">
                <v-icon class="me-5" icon="mdi-pencil"></v-icon>
                <v-icon class="me-1" icon="mdi-archive"></v-icon>
              </div>
            </template>
          </v-list-item>
        </v-card-actions>
      </v-card>
      <v-divider class="mt-1" />
      <v-card class="mx-auto" prepend-icon="mdi-note-text-outline" title="">
        <template v-slot:prepend>
          <v-icon size="x-large"></v-icon>
        </template>
        <template v-slot:append>
          <p>
            2024/08/07 19:33
          </p>
        </template>
        <v-card-text class="text-h5 py-2">
          "Turns out semicolon-less style is easier and safer in TS because most gotcha edge cases are type invalid as
          well."
        </v-card-text>
        <v-card-actions>
          <v-list-item class="w-100">
            <template v-slot:prepend>
              <div class="justify-self-end">
                <v-icon class="me-1" icon="mdi-tag-outline"></v-icon>
                <span class="subheading me-5">Vue.js</span>
                <v-icon class="me-1" icon="mdi-heart"></v-icon>
                <span class="subheading me-5">256</span>
              </div>
            </template>
            <template v-slot:append>
              <div class="justify-self-end">
                <v-icon class="me-5" icon="mdi-pencil"></v-icon>
                <v-icon class="me-1" icon="mdi-archive"></v-icon>
              </div>
            </template>
          </v-list-item>
        </v-card-actions>
      </v-card>
      <v-divider class="mt-1" />
      <v-card color="secondary" variant="tonal" class="mx-auto" prepend-icon="mdi-calendar-clock" title="サウナ">
        <template v-slot:prepend>
          <v-icon size="x-large"></v-icon>
        </template>
        <template v-slot:append>
          <p>
            2024/08/06 09:02
          </p>
        </template>
        <v-card-text class="text-h5 py-2">
          <v-alert type="info" variant="tonal" class="mb-3">
            <p class="text-body-1">from 2024/08/11 Sun 08:15</p>
            <p class="text-body-1">to 2024/08/11 Sun 14:00</p>
          </v-alert>
          8:15 集合<br />
          8:30 白玉温泉<br />
          11:00 肉五郎（焼肉ランチ）<br />
          14:00 解散
        </v-card-text>
        <v-card-actions>
          <v-list-item class="w-100">
            <template v-slot:prepend>
              <div class="justify-self-end">
                <v-icon class="me-1" icon="mdi-heart"></v-icon>
                <span class="subheading me-5">2</span>
              </div>
            </template>
            <template v-slot:append>
              <div class="justify-self-end">
                <v-icon class="me-5" icon="mdi-pencil"></v-icon>
                <v-icon class="me-1" icon="mdi-archive"></v-icon>
              </div>
            </template>
          </v-list-item>
        </v-card-actions>
      </v-card>
    </v-container>
  </AuthenticatedLayout>
</template>
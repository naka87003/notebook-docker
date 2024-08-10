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
      <v-row>
        <v-col cols="12">
          <v-card class="mx-auto" prepend-icon="mdi-note-text-outline" title="toast" rounded="0">
            <template v-slot:prepend>
              <v-icon size="large"></v-icon>
            </template>
            <template v-slot:append>
              <p class="text-caption">
                2024/08/10 08:10
              </p>
            </template>
            <v-divider class="border-opacity-25 mx-1" />
            <v-card-text class="text-h6 py-2">
              <p class="note-paragraph">不可算名詞</p>
              <p class="note-paragraph">How may slices of toast do you have for breakfast?</p>
            </v-card-text>
            <v-card-actions>
              <v-list-item class="w-100">
                <template v-slot:prepend>
                  <div class="justify-self-end">
                    <v-icon color="#ffd700" size="small" class="me-1" icon="mdi-tag"></v-icon>
                    <span class="me-5 text-caption">English</span>
                    <v-icon size="small" class="me-1" icon="mdi-lock-outline"></v-icon>
                  </div>
                </template>
                <template v-slot:append>
                  <div class="justify-self-end">
                    <v-icon size="small" class="me-5" icon="mdi-pencil-outline"></v-icon>
                    <v-icon size="small" class="me-1" icon="mdi-archive-outline"></v-icon>
                  </div>
                </template>
              </v-list-item>
            </v-card-actions>
          </v-card>
        </v-col>
        <v-col cols="12">
          <v-card color="primary" variant="tonal" class="mx-auto" prepend-icon="mdi-format-list-checks" title="買い物"
            rounded="0">
            <template v-slot:prepend>
              <v-icon size="large"></v-icon>
            </template>
            <template v-slot:append>
              <p class="text-caption">
                2024/08/09 20:47
              </p>
            </template>
            <v-divider class="border-opacity-25 mx-1" />
            <v-card-text class="text-h6 py-2">
              <p class="note-paragraph">豆腐</p>
              <p class="note-paragraph">パン</p>
              <p class="note-paragraph">ヨーグルト</p>
            </v-card-text>
            <v-card-actions>
              <v-list-item class="w-100">
                <template v-slot:prepend>
                  <div class="justify-self-end">
                    <v-icon size="small" class="me-1" icon="mdi-lock-outline"></v-icon>
                  </div>
                </template>
                <template v-slot:append>
                  <div class="justify-self-end">
                    <v-icon size="small" class="me-5" icon="mdi-pencil-outline"></v-icon>
                    <v-icon size="small" class="me-1" icon="mdi-archive-outline"></v-icon>
                  </div>
                </template>
              </v-list-item>
            </v-card-actions>
          </v-card>
        </v-col>
        <v-col cols="12">
          <v-card class="mx-auto" prepend-icon="mdi-note-text-outline" title="" rounded="0">
            <template v-slot:prepend>
              <v-icon size="large"></v-icon>
            </template>
            <template v-slot:append>
              <p class="text-caption">
                2024/08/07 19:33
              </p>
            </template>
            <v-divider class="border-opacity-25 mx-1" />
            <v-card-text class="text-h6 py-2">
              <p class="note-paragraph">
                "Turns out semicolon-less style is easier and safer in TS because most gotcha edge cases are type
                invalid as
                well."
              </p>
            </v-card-text>
            <v-card-actions>
              <v-list-item class="w-100">
                <template v-slot:prepend>
                  <div class="justify-self-end">
                    <v-icon color="#42B883" size="small" class="me-1" icon="mdi-tag"></v-icon>
                    <span class="me-5 text-caption">Vue.js</span>
                    <v-icon size="small" class="me-1" icon="mdi-heart-outline"></v-icon>
                    <span class="me-5 text-caption">256</span>
                  </div>
                </template>
                <template v-slot:append>
                  <div class="justify-self-end">
                    <v-icon size="small" class="me-5" icon="mdi-pencil-outline"></v-icon>
                    <v-icon size="small" class="me-1" icon="mdi-archive-outline"></v-icon>
                  </div>
                </template>
              </v-list-item>
            </v-card-actions>
          </v-card>
        </v-col>
        <v-col cols="12">
          <v-card color="secondary" variant="tonal" class="mx-auto" prepend-icon="mdi-calendar-clock" title="サウナ"
            rounded="0">
            <template v-slot:prepend>
              <v-icon size="large"></v-icon>
            </template>
            <template v-slot:append>
              <p class="text-caption">
                2024/08/06 09:02
              </p>
            </template>
            <v-divider class="border-opacity-25 mx-1" />
            <v-card-text class="text-h6 py-2">
              <v-alert type="info" variant="tonal" class="mb-3">
                <p class="text-body-2">from 2024/08/11 Sun 08:15</p>
                <p class="text-body-2">to 2024/08/11 Sun 14:00</p>
              </v-alert>
              <p class="note-paragraph">8:15 集合</p>
              <p class="note-paragraph">8:30 白玉温泉</p>
              <p class="note-paragraph">11:00 肉五郎（焼肉ランチ）</p>
              <p class="note-paragraph">14:00 解散</p>
            </v-card-text>
            <v-card-actions>
              <v-list-item class="w-100">
                <template v-slot:prepend>
                  <div class="justify-self-end">
                    <v-icon size="small" class="me-1" icon="mdi-heart-outline"></v-icon>
                    <span class="me-5 text-caption">2</span>
                  </div>
                </template>
                <template v-slot:append>
                  <div class="justify-self-end">
                    <v-icon size="small" class="me-5" icon="mdi-pencil-outline"></v-icon>
                    <v-icon size="small" class="me-1" icon="mdi-archive-outline"></v-icon>
                  </div>
                </template>
              </v-list-item>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </AuthenticatedLayout>
</template>

<style>
.note-paragraph {
  background-image: linear-gradient(180deg, rgba(204, 204, 204, 0) 0%, rgba(204, 204, 204, 0) 98.5%, rgba(100, 100, 100, 100) 100%);
  background-repeat: repeat-y;
  background-size: 100% 1.7em;
  line-height: 1.7;
}
</style>
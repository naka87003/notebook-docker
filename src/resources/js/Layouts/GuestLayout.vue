<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { useTheme } from 'vuetify'
import { useDark } from '@vueuse/core';
import { watch } from 'vue';

const theme = useTheme();
const isDark = useDark();

watch(isDark, (value) => {
  theme.global.name.value = value ? 'dark' : 'light';
}, { immediate: true });
</script>

<template>
  <div class="bg">
    <ApplicationLogo />
    <slot />
  </div>
</template>

<style scoped>
.bg {
  width: 100%;
  height: 100%;
  position: absolute;
  top: 0;
  left: 0;
  background: url("notebook.jpeg")
    no-repeat center center;
  background-size: cover;
}
</style>

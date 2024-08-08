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
  <div>
    <ApplicationLogo />
    <slot />
  </div>
</template>

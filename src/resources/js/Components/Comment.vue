<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { relativeDateTime, splitByNewline } from '@/common';
import { Comment } from '@/interfaces';
import { computed, ref } from 'vue';

const props = defineProps<{
  comment: Comment;
}>();

const truncate = ref(true);

const arrCommentLines = computed(() => splitByNewline(props.comment.content ?? ''));

const paragraphs = computed(() => {
  let lines = arrCommentLines.value;
  if (truncate.value && lines.length > 10) {
    lines = lines.slice(0, 9);
    lines[lines.length - 1] += '...';
  }
  return lines;
});

const isTruncated = computed(() => truncate.value && arrCommentLines.value.length > 10);

const showSelectedUserPosts = (userId: number) => {
  router.get(route('timeline'), {
    user: userId
  });
};
</script>

<template>
  <v-alert density="compact" variant="text">
    <template v-slot:prepend>
      <v-avatar color="grey-darken-3 cursor-pointer" style="z-index: 1;"
        @click="showSelectedUserPosts(comment.user_id)">
        <v-img v-if="comment.user.image_path" :src="'/storage/' + comment.user.image_path" />
        <v-icon v-else icon="mdi-account" />
      </v-avatar>
    </template>
    <template #title>
      <span class="text-caption text-truncate">{{ comment.user.name }}</span>
      <v-spacer />
      <span class="text-caption text-no-wrap">{{ relativeDateTime(comment.created_at) }}</span>
    </template>
    <p class="text-body-2" v-for="paragraph in paragraphs">{{ paragraph }}</p>
    <v-btn v-if="isTruncated" class="text-capitalize ps-0" color="primary" variant="text" density="compact"
      @click="truncate = false">Show more</v-btn>
    <div>
      <v-icon flat size="x-small" icon="mdi-reply-outline" @click="console.log('click')" />
    </div>
  </v-alert>
</template>
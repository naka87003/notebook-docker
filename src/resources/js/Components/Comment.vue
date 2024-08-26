<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { relativeDateTime, splitByNewline } from '@/common';
import { Comment, User } from '@/interfaces';
import { computed, ref } from 'vue';

const props = defineProps<{
  comment: Comment;
}>();

defineEmits<{
  delete: [];
}>();

const truncate = ref(true);

const isMyComment = computed((): boolean => {
  const user = usePage().props.auth.user as User;
  return props.comment.user_id === user.id;
});

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
      <span class="text-caption text-no-wrap text-disabled ms-2">{{ relativeDateTime(comment.created_at) }}</span>
      <v-spacer />
      <v-menu v-if="isMyComment">
        <template v-slot:activator="{ props }">
          <v-btn v-bind="props" icon="mdi-dots-horizontal" variant="plain" size="small" />
        </template>
        <v-list>
          <v-list-item density="compact" prepend-icon="mdi-delete-outline" @click="$emit('delete')">
            <v-list-item-title>Delete</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
    </template>
    <p class="text-body-2" v-for="paragraph in paragraphs">{{ paragraph }}</p>
    <v-btn v-if="isTruncated" class="text-capitalize ps-0" color="primary" variant="text" density="compact"
      @click="truncate = false">Show more</v-btn>
    <div>
      <v-icon flat size="x-small" icon="mdi-reply-outline" @click="console.log('click')" />
    </div>
  </v-alert>
</template>
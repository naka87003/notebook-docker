<script setup lang="ts">
import { router, useForm, usePage } from '@inertiajs/vue3';
import { relativeDateTime, splitByNewline } from '@/common';
import { Comment, User } from '@/interfaces';
import { computed, ref } from 'vue';
import axios from 'axios';
import Reply from './Reply.vue';

const props = defineProps<{
  comment: Comment;
}>();

const emit = defineEmits<{
  delete: [];
  updateComment: [];
}>();

const form = useForm({
  reply: '',
});

const items = ref([]);

const truncate = ref(true);

const display = ref({
  replyForm: false,
  replies: false
});

const userImagePath = computed((): string | null => {
  const user = usePage().props.auth.user as User;
  return user.image_path;
});

const avatarImagePath = computed(() => userImagePath.value ? '/storage/' + userImagePath.value : null);

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

const loadItems = async (fresh: boolean = false): Promise<Comment[]> => {
  const params = {};
  if (fresh === false) {
    params['offset'] = items.value.length;
  }
  const response = await axios.get(route('replies', props.comment.id), { params })
  return response.data;
};

const load = async ({ done }): Promise<void> => {
  const result = await loadItems();
  if (result.length > 0) {
    items.value.push(...result);
    done('ok');
  } else {
    done('empty');
  }
};

const addReply = async () => {
  form.post(route('replies.store', props.comment.id), {
    preserveScroll: true,
    onSuccess: async () => {
      form.reset();
      const result = await loadItems(true);
      items.value = result;
      emit('updateComment');
    }
  });
};

const replyAdded = async () => {
  const result = await loadItems(true);
  items.value = result;
  emit('updateComment');
}
</script>

<template>
  <v-alert density="compact" variant="text" class="pa-0">
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
        <v-list class="pa-0">
          <v-list-item density="compact" prepend-icon="mdi-delete-outline" slim @click="$emit('delete')">
            <v-list-item-title>Delete</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
    </template>
    <p class="text-body-2" v-for="paragraph in paragraphs">{{ paragraph }}</p>
    <v-btn v-if="isTruncated" class="text-capitalize ps-0" color="primary" variant="text" density="compact"
      @click="truncate = false">Show more</v-btn>
    <div>
      <v-icon flat size="x-small" icon="mdi-reply-outline" @click="display.replyForm = !display.replyForm" />
    </div>
    <v-card v-show="display.replyForm" density="compact" variant="text">
      <v-card-text class="pa-0">
        <form @submit.prevent="addReply">
          <v-textarea v-model="form.reply" density="compact" variant="underlined" placeholder="Add a reply" hide-details
            clearable auto-grow rows="1" counter="140" maxLength="140">
            <template v-slot:prepend>
              <v-avatar color="surface-light" size="small">
                <v-img v-if="avatarImagePath" :src="avatarImagePath" />
                <v-icon v-else icon="mdi-account" />
              </v-avatar>
            </template>
          </v-textarea>
        </form>
        <v-card-actions v-show="form.reply" class="mb-n4 pa-0">
          <v-spacer />
          <v-btn size="small" variant="tonal" color="secondary" class="text-capitalize"
            :class="{ 'text-disabled': form.processing }" :disabled="form.processing" @click="addReply">Reply</v-btn>
        </v-card-actions>
      </v-card-text>
    </v-card>
    <v-btn v-if="comment.replies_count" variant="text"
      :prepend-icon="display.replies ? 'mdi-chevron-up' : 'mdi-chevron-down'" class="text-lowercase px-0"
      color="primary" @click="display.replies = !display.replies">{{ comment.replies_count }} replies</v-btn>
    <v-infinite-scroll v-if="display.replies" :onLoad="load" class="w-100 overflow-hidden" empty-text="">
      <template v-for="reply in items" :key="reply.id">
        <Reply :reply @replyAdded="replyAdded" />
      </template>
    </v-infinite-scroll>
  </v-alert>
</template>
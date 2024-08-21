<script setup lang="ts">
import { User } from '@/interfaces';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, watch, computed } from 'vue';
import FollowButton from './FollowButton.vue';

const props = defineProps<{
  selectedUser: User;
}>();

const isFollowing = ref(false);
const followees = ref([]);
const followers = ref([]);
const followeesCount = ref(0);
const followersCount = ref(0);

const isMyself = computed(() => props.selectedUser.id === usePage().props.auth.user.id);

watch(() => props.selectedUser, async () => {
  await axios.get(route('timeline.user'), {
    params: {
      user_id: props.selectedUser.id
    }
  })
    .then(response => {
      followees.value = response.data.followees;
      followers.value = response.data.followers;
      followeesCount.value = response.data.followees.length;
      followersCount.value = response.data.followers.length;
      isFollowing.value = followers.value.some((follower) => follower.id === usePage().props.auth.user.id);
    })
    .catch(error => {
      console.log(error);
    });
}, { immediate: true });

const follow = async () => {
  if (isMyself.value === false) {
    await axios.post(route('timeline.follow'), {
      user_id: props.selectedUser.id
    })
      .then(function () {
        isFollowing.value = true;
        followersCount.value++;
      })
      .catch(function (error) {
        console.log(error);
      });
  }
}
const unfollow = async () => {
  await axios.post(route('timeline.unfollow'), {
    user_id: props.selectedUser.id
  })
    .then(function () {
      isFollowing.value = false;
      followersCount.value--;
    })
    .catch(function (error) {
      console.log(error);
    });
};
</script>

<template>
  <v-card class="mx-auto pa-1" :title="selectedUser.name" border="sm" elevation="5" rounded="xl">
    <template v-slot:prepend>
      <v-avatar color="surface-light">
        <v-img v-if="selectedUser.image_path" :src="'storage/' + selectedUser.image_path" />
        <v-icon v-else icon="mdi-account" />
      </v-avatar>
    </template>
    <v-card-text v-if="selectedUser.comment" class="ms-1 mt-2">
      {{ selectedUser.comment }}
    </v-card-text>
    <v-divider class="mb-1" />
    <v-card-actions>
      <v-btn class="text-capitalize ms-1">{{ followeesCount }} Following</v-btn>
      <v-btn class="text-capitalize">{{ followersCount }} Followers</v-btn>
      <v-spacer></v-spacer>
      <FollowButton v-if="isMyself === false" :isFollowing @follow="follow" @unfollow="unfollow" />
    </v-card-actions>
  </v-card>
</template>
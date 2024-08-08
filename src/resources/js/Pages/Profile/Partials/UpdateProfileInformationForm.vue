<script setup lang="ts">
import { useForm, usePage, router } from '@inertiajs/vue3';

defineProps({
	mustVerifyEmail: {
		type: Boolean,
	},
	status: {
		type: String,
	},
});

const user = usePage().props.auth.user;

const form = useForm({
	name: user.name,
	email: user.email,
});

const verificationSend = () => {
	router.post('email/verification-notification');
};
</script>

<template>
	<section>
		<v-card title="Profile Information" class="mx-auto pa-6" elevation="8" rounded="lg">
			<v-card class="mb-6" color="surface-variant" variant="tonal">
				<v-card-text class="text-medium-emphasis text-caption">
					<p>Update your account's profile information and email address.</p>
				</v-card-text>
			</v-card>
			<form @submit.prevent="form.patch(route('profile.update'))" class="mt-6 space-y-6">
				<div class="text-subtitle-1 text-medium-emphasis">Name</div>
				<v-text-field v-model="form.name" type="text" density="compact" placeholder="Enter your new name"
					prepend-inner-icon="mdi-account-outline" variant="outlined" :error="Boolean(form.errors.name)"
					:error-messages="form.errors.name" required autofocus autocomplete="name" max-width="600"
					@input="form.errors.name = null" />
				<div class="text-subtitle-1 text-medium-emphasis">Email</div>
				<v-text-field v-model="form.email" type="email" density="compact" placeholder="Enter your new email address"
					prepend-inner-icon="mdi-email-outline" variant="outlined" :error="Boolean(form.errors.email)"
					:error-messages="form.errors.email" required autofocus autocomplete="username" max-width="600"
					@input="form.errors.email = null" />

				<div v-if="mustVerifyEmail && user.email_verified_at === null">
					<v-card class="mb-6" color="surface-variant" variant="tonal">
						<v-card-text class="text-medium-emphasis text-caption">
							<p>Your email address is unverified.</p>
							<p><a class="text-primary" href="#" @click.prevent="verificationSend">Click here</a> to
								re-send the verification email.</p>
						</v-card-text>
					</v-card>
					<v-alert v-show="status === 'verification-link-sent'"
						text="A new verification link has been sent to your email address." class="mb-6" type="success"
						variant="tonal" closable />
				</div>
				<Transition>
					<div v-if="form.recentlySuccessful">
						<v-alert text="Saved." class="mt-6 mb-6 " type="success" variant="tonal" closable />
					</div>
				</Transition>
				<v-btn color="primary" size="large" variant="tonal" :class="{ 'text-disabled': form.processing }"
					:disabled="form.processing" @click="form.patch(route('profile.update'))">
					Save
				</v-btn>
			</form>
		</v-card>
	</section>
</template>

<style scoped>
.v-leave-to {
	opacity: 0.0
}

.v-leave-active {
	transition: opacity 5s;
}
</style>

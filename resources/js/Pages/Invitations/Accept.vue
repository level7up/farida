<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Button from '@/Components/ui/Button.vue';
import Badge from '@/Components/ui/Badge.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    invitation: Object,
    user: Object,
});

const form = useForm({});

const accept = () => {
    form.post(route('invitations.accept', props.invitation.token));
};
</script>

<template>
    <Head title="Accept Invitation" />

    <GuestLayout>
        <div class="text-center mb-8">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-100 mb-6">
                <svg class="h-8 w-8 text-brand-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 mb-2">You've been invited!</h2>
            <p class="text-slate-500">
                You have been invited to join
                <span class="font-semibold text-slate-900">{{ invitation.organization.name }}</span>
                as
                <Badge>{{ invitation.role }}</Badge>
            </p>
        </div>

        <div v-if="!user" class="space-y-4">
            <p class="text-center text-sm text-slate-500">
                Please log in or register to accept this invitation.
            </p>
            <div class="flex gap-3 justify-center">
                <Link :href="route('login')">
                    <Button variant="secondary">Log in</Button>
                </Link>
                <Link :href="route('register')">
                    <Button>Register</Button>
                </Link>
            </div>
        </div>

        <div v-else-if="user.email !== invitation.email" class="rounded-lg bg-red-50 p-4 text-sm text-red-700">
            <p>
                This invitation was sent to <strong>{{ invitation.email }}</strong>,
                but you are logged in as <strong>{{ user.email }}</strong>.
            </p>
            <p class="mt-2">Please log in with the correct account to accept this invitation.</p>
        </div>

        <div v-else>
            <form @submit.prevent="accept" class="text-center">
                <Button type="submit" class="w-full" :processing="form.processing" :disabled="form.processing">
                    Accept Invitation
                </Button>
            </form>
        </div>
    </GuestLayout>
</template>

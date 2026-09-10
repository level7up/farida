<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Button from '@/Components/ui/Button.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: String,
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head title="Email Verification" />

        <h2 class="text-2xl font-bold text-slate-900 mb-1">Verify your email</h2>
        <p class="text-sm text-slate-500 mb-8">
            Thanks for signing up! Before getting started, could you verify your
            email address by clicking on the link we just emailed to you?
        </p>

        <div
            v-if="verificationLinkSent"
            class="mb-6 rounded-lg bg-emerald-50 p-4 text-sm text-emerald-700"
        >
            A new verification link has been sent to the email address you provided during registration.
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <Button
                type="submit"
                class="w-full"
                :processing="form.processing"
                :disabled="form.processing"
            >
                Resend verification email
            </Button>
        </form>

        <div class="mt-6 text-center">
            <Link
                :href="route('logout')"
                method="post"
                as="button"
                class="text-sm font-medium text-slate-500 hover:text-slate-700"
            >
                Log out
            </Link>
        </div>
    </GuestLayout>
</template>

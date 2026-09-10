<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Button from '@/Components/ui/Button.vue';
import Input from '@/Components/ui/Input.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Reset Password" />

        <h2 class="text-2xl font-bold text-slate-900 mb-1">Reset your password</h2>
        <p class="text-sm text-slate-500 mb-8">
            Enter your new password below.
        </p>

        <form @submit.prevent="submit" class="space-y-5">
            <Input
                v-model="form.email"
                label="Email address"
                type="email"
                required
                autofocus
                autocomplete="username"
                :error="form.errors.email"
            />

            <Input
                v-model="form.password"
                label="New password"
                type="password"
                required
                autocomplete="new-password"
                placeholder="Create a new password"
                :error="form.errors.password"
            />

            <Input
                v-model="form.password_confirmation"
                label="Confirm password"
                type="password"
                required
                autocomplete="new-password"
                placeholder="Confirm your new password"
                :error="form.errors.password_confirmation"
            />

            <Button
                type="submit"
                class="w-full"
                :processing="form.processing"
                :disabled="form.processing"
            >
                Reset password
            </Button>
        </form>
    </GuestLayout>
</template>

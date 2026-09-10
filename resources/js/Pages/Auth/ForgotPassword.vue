<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Button from '@/Components/ui/Button.vue';
import Input from '@/Components/ui/Input.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password" />

        <h2 class="text-2xl font-bold text-slate-900 mb-1">Forgot your password?</h2>
        <p class="text-sm text-slate-500 mb-8">
            No worries. Enter your email and we'll send you a reset link.
        </p>

        <div v-if="status" class="mb-6 rounded-lg bg-emerald-50 p-4 text-sm text-emerald-700">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <Input
                v-model="form.email"
                label="Email address"
                type="email"
                required
                autofocus
                autocomplete="username"
                placeholder="you@example.com"
                :error="form.errors.email"
            />

            <Button
                type="submit"
                class="w-full"
                :processing="form.processing"
                :disabled="form.processing"
            >
                Send reset link
            </Button>
        </form>
    </GuestLayout>
</template>

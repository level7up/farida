<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Button from '@/Components/ui/Button.vue';
import Input from '@/Components/ui/Input.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <h2 class="text-2xl font-bold text-slate-900 mb-1">Create your account</h2>
        <p class="text-sm text-slate-500 mb-8">Get started with Farida today</p>

        <form @submit.prevent="submit" class="space-y-5">
            <Input
                v-model="form.name"
                label="Full name"
                type="text"
                required
                autofocus
                autocomplete="name"
                placeholder="John Doe"
                :error="form.errors.name"
            />

            <Input
                v-model="form.email"
                label="Email address"
                type="email"
                required
                autocomplete="username"
                placeholder="you@example.com"
                :error="form.errors.email"
            />

            <Input
                v-model="form.password"
                label="Password"
                type="password"
                required
                autocomplete="new-password"
                placeholder="Create a password"
                :error="form.errors.password"
            />

            <Input
                v-model="form.password_confirmation"
                label="Confirm password"
                type="password"
                required
                autocomplete="new-password"
                placeholder="Confirm your password"
                :error="form.errors.password_confirmation"
            />

            <Button
                type="submit"
                class="w-full"
                :processing="form.processing"
                :disabled="form.processing"
            >
                Create account
            </Button>
        </form>

        <p class="mt-8 text-center text-sm text-slate-500">
            Already have an account?
            <Link :href="route('login')" class="font-medium text-brand-600 hover:text-brand-700">
                Sign in
            </Link>
        </p>
    </GuestLayout>
</template>

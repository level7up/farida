<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Button from '@/Components/ui/Button.vue';
import Input from '@/Components/ui/Input.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-6 rounded-lg bg-emerald-50 p-4 text-sm text-emerald-700">
            {{ status }}
        </div>

        <h2 class="text-2xl font-bold text-slate-900 mb-1">Welcome back</h2>
        <p class="text-sm text-slate-500 mb-8">Sign in to your account to continue</p>

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

            <Input
                v-model="form.password"
                label="Password"
                type="password"
                required
                autocomplete="current-password"
                placeholder="Enter your password"
                :error="form.errors.password"
            />

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2">
                    <input
                        type="checkbox"
                        v-model="form.remember"
                        class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500"
                    />
                    <span class="text-sm text-slate-600">Remember me</span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm font-medium text-brand-600 hover:text-brand-700"
                >
                    Forgot password?
                </Link>
            </div>

            <Button
                type="submit"
                class="w-full"
                :processing="form.processing"
                :disabled="form.processing"
            >
                Sign in
            </Button>
        </form>

        <p class="mt-8 text-center text-sm text-slate-500">
            Don't have an account?
            <Link :href="route('register')" class="font-medium text-brand-600 hover:text-brand-700">
                Create one
            </Link>
        </p>
    </GuestLayout>
</template>

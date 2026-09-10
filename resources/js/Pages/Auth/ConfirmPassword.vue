<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Button from '@/Components/ui/Button.vue';
import Input from '@/Components/ui/Input.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Confirm Password" />

        <h2 class="text-2xl font-bold text-slate-900 mb-1">Confirm your password</h2>
        <p class="text-sm text-slate-500 mb-8">
            This is a secure area of the application. Please confirm your password before continuing.
        </p>

        <form @submit.prevent="submit" class="space-y-5">
            <Input
                v-model="form.password"
                label="Password"
                type="password"
                required
                autocomplete="current-password"
                autofocus
                placeholder="Enter your password"
                :error="form.errors.password"
            />

            <Button
                type="submit"
                class="w-full"
                :processing="form.processing"
                :disabled="form.processing"
            >
                Confirm
            </Button>
        </form>
    </GuestLayout>
</template>

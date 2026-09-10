<script setup>
import Input from '@/Components/ui/Input.vue';
import Button from '@/Components/ui/Button.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
        <header class="mb-6">
            <h2 class="text-lg font-semibold text-slate-900">Profile Information</h2>
            <p class="mt-1 text-sm text-slate-500">
                Update your account's profile information and email address.
            </p>
        </header>

        <form @submit.prevent="form.patch(route('profile.update'))" class="space-y-5">
            <Input
                v-model="form.name"
                label="Name"
                type="text"
                required
                autofocus
                autocomplete="name"
                :error="form.errors.name"
            />

            <Input
                v-model="form.email"
                label="Email"
                type="email"
                required
                autocomplete="username"
                :error="form.errors.email"
            />

            <div v-if="mustVerifyEmail && user.email_verified_at === null" class="rounded-lg bg-amber-50 p-4">
                <p class="text-sm text-amber-700">
                    Your email address is unverified.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="font-medium text-amber-800 underline hover:text-amber-900"
                    >
                        Click here to re-send the verification email.
                    </Link>
                </p>

                <div v-show="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-emerald-600">
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <Button type="submit" :processing="form.processing" :disabled="form.processing">
                    Save
                </Button>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-slate-500">Saved.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>

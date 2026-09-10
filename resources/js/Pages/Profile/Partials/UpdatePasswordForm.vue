<script setup>
import Input from '@/Components/ui/Input.vue';
import Button from '@/Components/ui/Button.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header class="mb-6">
            <h2 class="text-lg font-semibold text-slate-900">Update Password</h2>
            <p class="mt-1 text-sm text-slate-500">
                Ensure your account is using a long, random password to stay secure.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="space-y-5">
            <Input
                v-model="form.current_password"
                label="Current Password"
                type="password"
                autocomplete="current-password"
                :error="form.errors.current_password"
            />

            <Input
                v-model="form.password"
                label="New Password"
                type="password"
                autocomplete="new-password"
                :error="form.errors.password"
            />

            <Input
                v-model="form.password_confirmation"
                label="Confirm Password"
                type="password"
                autocomplete="new-password"
                :error="form.errors.password_confirmation"
            />

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

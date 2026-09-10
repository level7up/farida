<script setup>
import Button from '@/Components/ui/Button.vue';
import Input from '@/Components/ui/Input.vue';
import Modal from '@/Components/ui/Modal.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section>
        <header class="mb-6">
            <h2 class="text-lg font-semibold text-slate-900">Delete Account</h2>
            <p class="mt-1 text-sm text-slate-500">
                Once your account is deleted, all of its resources and data will be permanently deleted.
                Before deleting your account, please download any data or information that you wish to retain.
            </p>
        </header>

        <Button variant="danger" @click="confirmUserDeletion">
            Delete Account
        </Button>

        <Modal :show="confirmingUserDeletion" @close="closeModal" max-width="md">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-slate-900">Are you sure you want to delete your account?</h3>

                <p class="mt-2 text-sm text-slate-500">
                    Once your account is deleted, all of its resources and data will be permanently deleted.
                    Please enter your password to confirm you would like to permanently delete your account.
                </p>

                <div class="mt-6">
                    <Input
                        v-model="form.password"
                        ref="passwordInput"
                        type="password"
                        placeholder="Password"
                        autocomplete="current-password"
                        :error="form.errors.password"
                        @keyup.enter="deleteUser"
                    />
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <Button variant="ghost" @click="closeModal">
                        Cancel
                    </Button>
                    <Button
                        variant="danger"
                        :processing="form.processing"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        Delete Account
                    </Button>
                </div>
            </div>
        </Modal>
    </section>
</template>

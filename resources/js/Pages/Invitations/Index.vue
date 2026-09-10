<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from '@/Components/ui/Card.vue';
import Button from '@/Components/ui/Button.vue';
import Input from '@/Components/ui/Input.vue';
import Select from '@/Components/ui/Select.vue';
import Badge from '@/Components/ui/Badge.vue';
import Modal from '@/Components/ui/Modal.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    organization: Object,
    invitations: Array,
});

const showInviteModal = ref(false);

const form = useForm({
    email: '',
    role: 'Staff',
});

const submit = () => {
    form.post(route('invitations.store', props.organization.slug), {
        onSuccess: () => {
            form.reset();
            showInviteModal.value = false;
        },
    });
};

const roles = ['Owner', 'Admin', 'Manager', 'Staff', 'Accountant', 'Cleaning'];

const roleBadgeVariant = (role) => {
    const map = {
        Owner: 'danger',
        Admin: 'warning',
        Manager: 'info',
        Staff: 'default',
        Accountant: 'success',
        Cleaning: 'default',
    };
    return map[role] || 'default';
};
</script>

<template>
    <Head title="Team Invitations" />

    <AuthenticatedLayout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Team Invitations</h1>
                <p class="text-slate-500 mt-1">Manage invitations for your organization.</p>
            </div>
            <Button @click="showInviteModal = true">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Invite Member
            </Button>
        </div>

        <Card>
            <div v-if="invitations.length === 0" class="text-center py-12">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 mb-4">
                    <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                </div>
                <p class="text-slate-500">No pending invitations</p>
                <p class="text-sm text-slate-400 mt-1">Invite team members to get started.</p>
            </div>

            <div v-else class="overflow-hidden">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Expires</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <tr v-for="invitation in invitations" :key="invitation.id" class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">{{ invitation.email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <Badge :variant="roleBadgeVariant(invitation.role)">{{ invitation.role }}</Badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ invitation.expires_at }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>

        <Modal :show="showInviteModal" @close="showInviteModal = false" max-width="md">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-slate-900 mb-4">Invite Team Member</h3>
                <form @submit.prevent="submit" class="space-y-4">
                    <Input
                        v-model="form.email"
                        label="Email"
                        type="email"
                        required
                        placeholder="colleague@example.com"
                        :error="form.errors.email"
                    />

                    <Select
                        v-model="form.role"
                        label="Role"
                        :options="roles"
                        :error="form.errors.role"
                    />

                    <div class="flex justify-end gap-3 pt-2">
                        <Button variant="ghost" @click="showInviteModal = false">
                            Cancel
                        </Button>
                        <Button type="submit" :processing="form.processing" :disabled="form.processing">
                            Send Invitation
                        </Button>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from '@/Components/ui/Card.vue';
import Button from '@/Components/ui/Button.vue';
import Input from '@/Components/ui/Input.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    slug: '',
});

const generateSlug = () => {
    form.slug = form.name
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)/g, '');
};

const submit = () => {
    form.post(route('organizations.store'));
};
</script>

<template>
    <Head title="Create Organization" />

    <AuthenticatedLayout>
        <div class="max-w-2xl mx-auto">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-slate-900">Create Organization</h1>
                <p class="text-slate-500 mt-1">Set up a new organization to get started.</p>
            </div>

            <Card>
                <form @submit.prevent="submit" class="space-y-5">
                    <Input
                        v-model="form.name"
                        label="Organization Name"
                        type="text"
                        required
                        autofocus
                        placeholder="My Organization"
                        :error="form.errors.name"
                        @input="generateSlug"
                    />

                    <Input
                        v-model="form.slug"
                        label="Slug"
                        type="text"
                        required
                        placeholder="my-organization"
                        :error="form.errors.slug"
                    />
                    <p class="text-xs text-slate-400 -mt-3">URL-friendly identifier for your organization</p>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <Button variant="ghost" @click="$inertia.visit(route('dashboard'))">
                            Cancel
                        </Button>
                        <Button
                            type="submit"
                            :processing="form.processing"
                            :disabled="form.processing"
                        >
                            Create Organization
                        </Button>
                    </div>
                </form>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import Dropdown from '@/Components/ui/Dropdown.vue';
import { Link, router } from '@inertiajs/vue3';

defineProps({
    organizations: {
        type: Array,
        required: true,
    },
    currentOrganization: {
        type: Object,
        default: null,
    },
});

const switchOrganization = (organizationId) => {
    router.post(route('organizations.switch', organizationId));
};
</script>

<template>
    <Dropdown align="left" width="64">
        <template #trigger>
            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 transition-colors"
            >
                <div class="flex h-6 w-6 items-center justify-center rounded-md bg-brand-100 text-xs font-bold text-brand-700">
                    {{ currentOrganization?.name?.charAt(0)?.toUpperCase() }}
                </div>
                <span class="hidden sm:inline">{{ currentOrganization?.name || 'Select Organization' }}</span>
                <svg class="h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </template>

        <template #content>
            <div class="px-4 py-2 border-b border-slate-100">
                <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Switch Organization</p>
            </div>

            <div class="py-1">
                <button
                    v-for="organization in organizations"
                    :key="organization.id"
                    @click="switchOrganization(organization.id)"
                    class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors"
                >
                    <div class="flex h-7 w-7 items-center justify-center rounded-md bg-brand-100 text-xs font-bold text-brand-700">
                        {{ organization.name.charAt(0).toUpperCase() }}
                    </div>
                    <span class="flex-1 text-left">{{ organization.name }}</span>
                    <svg
                        v-if="organization.id === currentOrganization?.id"
                        class="h-4 w-4 text-brand-600"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                </button>
            </div>

            <div class="border-t border-slate-100 py-1">
                <Link
                    :href="route('organizations.create')"
                    class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors"
                >
                    <div class="flex h-7 w-7 items-center justify-center rounded-md border-2 border-dashed border-slate-300">
                        <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </div>
                    <span>Create New Organization</span>
                </Link>
            </div>
        </template>
    </Dropdown>
</template>

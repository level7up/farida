<script setup>
import { inject, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    icon: String,
    active: Boolean,
    collapsed: Boolean,
});

const page = usePage();
const isActive = computed(() => props.active || page.url === props.href);
</script>

<template>
    <Link
        :href="href"
        :class="[
            'group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-150',
            active
                ? 'bg-brand-50 text-brand-700'
                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
            collapsed && 'justify-center px-2',
        ]"
        :title="collapsed ? $slots.default?.()[0]?.children : undefined"
    >
        <div
            :class="[
                'flex h-5 w-5 shrink-0 items-center justify-center',
                active ? 'text-brand-600' : 'text-slate-400 group-hover:text-slate-600',
            ]"
            v-html="icon"
        />
        <span v-if="!collapsed" class="truncate">
            <slot />
        </span>
    </Link>
</template>

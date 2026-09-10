<script setup>
import { computed } from 'vue';

const props = defineProps({
    name: {
        type: String,
        default: '',
    },
    src: String,
    size: {
        type: String,
        default: 'md',
        validator: (v) => ['sm', 'md', 'lg'].includes(v),
    },
});

const initials = computed(() => {
    if (!props.name) return '?';
    return props.name
        .split(' ')
        .map((w) => w[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
});

const sizeClasses = {
    sm: 'h-8 w-8 text-xs',
    md: 'h-10 w-10 text-sm',
    lg: 'h-12 w-12 text-base',
};
</script>

<template>
    <div
        :class="[
            sizeClasses[size],
            'relative inline-flex items-center justify-center rounded-full bg-brand-100 font-medium text-brand-700 overflow-hidden shrink-0',
        ]"
    >
        <img v-if="src" :src="src" :alt="name" class="h-full w-full object-cover" />
        <span v-else>{{ initials }}</span>
    </div>
</template>

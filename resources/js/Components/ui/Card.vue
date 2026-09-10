<script setup>
defineProps({
    padding: {
        type: Boolean,
        default: true,
    },
});
</script>

<template>
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div v-if="$slots.header" class="px-6 py-4 border-b border-slate-200">
            <slot name="header" />
        </div>
        <div :class="padding ? 'p-6' : ''">
            <slot />
        </div>
        <div v-if="$slots.footer" class="px-6 py-4 border-t border-slate-200 bg-slate-50 rounded-b-xl">
            <slot name="footer" />
        </div>
    </div>
</template>

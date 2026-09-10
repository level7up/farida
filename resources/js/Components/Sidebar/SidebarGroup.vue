<script setup>
defineProps({
    label: String,
    collapsed: Boolean,
});
</script>

<template>
    <div class="mb-1">
        <p
            v-if="label && !collapsed"
            class="mb-1 px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400"
        >
            {{ label }}
        </p>
        <div v-else-if="collapsed" class="mx-3 my-2 border-t border-slate-200" />
        <div class="space-y-0.5">
            <slot />
        </div>
    </div>
</template>

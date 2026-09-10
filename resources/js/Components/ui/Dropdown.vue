<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';

const props = defineProps({
    align: {
        type: String,
        default: 'right',
    },
    width: {
        type: String,
        default: '48',
    },
    contentClasses: {
        type: Array,
        default: () => ['py-1', 'bg-white'],
    },
});

const open = ref(false);
const dropdownRef = ref(null);

const widthClass = computed(() => {
    return { 48: 'w-48', 64: 'w-64' }[props.width];
});

const alignmentClasses = computed(() => {
    if (props.align === 'left') return 'left-0 origin-top-left';
    if (props.align === 'right') return 'right-0 origin-top-right';
    return 'left-1/2 -translate-x-1/2 origin-top';
});

const closeOnEscape = (e) => {
    if (e.key === 'Escape' && open.value) {
        open.value = false;
    }
};

const closeOnClickOutside = (e) => {
    if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
        open.value = false;
    }
};

onMounted(() => {
    document.addEventListener('keydown', closeOnEscape);
    document.addEventListener('click', closeOnClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape);
    document.removeEventListener('click', closeOnClickOutside);
});
</script>

<template>
    <div class="relative" ref="dropdownRef">
        <div @click="open = !open">
            <slot name="trigger" />
        </div>

        <Transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-show="open"
                :class="[
                    widthClass,
                    alignmentClasses,
                    contentClasses,
                    'absolute z-50 mt-2 rounded-xl border border-slate-200 shadow-lg',
                ]"
            >
                <slot name="content" />
            </div>
        </Transition>
    </div>
</template>

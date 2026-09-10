<script setup>
defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
    label: String,
    error: String,
    options: {
        type: Array,
        required: true,
    },
    placeholder: {
        type: String,
        default: 'Select an option',
    },
    disabled: Boolean,
    required: Boolean,
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div>
        <label v-if="label" class="block text-sm font-medium text-slate-700 mb-1.5">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        <select
            :value="modelValue"
            @change="$emit('update:modelValue', $event.target.value)"
            :disabled="disabled"
            :required="required"
            :class="[
                'block w-full rounded-lg border px-3.5 py-2.5 text-sm text-slate-900 shadow-sm transition-colors duration-150 appearance-none bg-no-repeat bg-[right_0.75rem_center] bg-[length:1.25rem]',
                error
                    ? 'border-red-300 focus:border-red-500 focus:ring-red-500'
                    : 'border-slate-300 focus:border-brand-500 focus:ring-brand-500',
                disabled ? 'bg-slate-50 cursor-not-allowed' : 'bg-white',
                'focus:outline-none focus:ring-2 focus:ring-offset-0',
            ]"
            style="background-image: url(&quot;data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e&quot;);"
        >
            <option value="" disabled>{{ placeholder }}</option>
            <option
                v-for="option in options"
                :key="typeof option === 'object' ? option.value : option"
                :value="typeof option === 'object' ? option.value : option"
            >
                {{ typeof option === 'object' ? option.label : option }}
            </option>
        </select>
        <p v-if="error" class="mt-1.5 text-sm text-red-600">{{ error }}</p>
    </div>
</template>

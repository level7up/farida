<script setup>
defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
    label: String,
    error: String,
    type: {
        type: String,
        default: 'text',
    },
    placeholder: String,
    disabled: Boolean,
    required: Boolean,
    autofocus: Boolean,
    autocomplete: String,
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div>
        <label v-if="label" class="block text-sm font-medium text-slate-700 mb-1.5">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        <input
            :type="type"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :placeholder="placeholder"
            :disabled="disabled"
            :required="required"
            :autofocus="autofocus"
            :autocomplete="autocomplete"
            :class="[
                'block w-full rounded-lg border px-3.5 py-2.5 text-sm text-slate-900 shadow-sm transition-colors duration-150 placeholder:text-slate-400',
                error
                    ? 'border-red-300 focus:border-red-500 focus:ring-red-500'
                    : 'border-slate-300 focus:border-brand-500 focus:ring-brand-500',
                disabled ? 'bg-slate-50 cursor-not-allowed' : 'bg-white',
                'focus:outline-none focus:ring-2 focus:ring-offset-0',
            ]"
        />
        <p v-if="error" class="mt-1.5 text-sm text-red-600">{{ error }}</p>
    </div>
</template>

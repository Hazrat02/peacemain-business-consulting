<script setup>
import { computed, ref } from 'vue';
import feather from 'feather-icons';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    label: {
        type: String,
        required: true,
    },
    error: {
        type: String,
        default: '',
    },
    inputClass: {
        type: String,
        default: 'form-control',
    },
    required: {
        type: Boolean,
        default: false,
    },
    placeholder: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue']);
const visible = ref(false);

const inputType = computed(() => (visible.value ? 'text' : 'password'));
const iconSvg = computed(() =>
    visible.value
        ? feather.icons['eye-off'].toSvg({ width: 16, height: 16 })
        : feather.icons.eye.toSvg({ width: 16, height: 16 })
);

const updateValue = (event) => {
    emit('update:modelValue', event.target.value);
};
</script>

<template>
    <div class="mb-3">
        <label class="form-label" :class="{ required }">{{ label }}</label>
        <div class="input-group">
            <input
                :value="modelValue"
                :class="inputClass"
                :type="inputType"
                :required="required"
                :placeholder="placeholder"
                @input="updateValue"
            />
            <button class="btn btn-outline-secondary" type="button" @click="visible = !visible">
                <span v-html="iconSvg"></span>
            </button>
        </div>
        <div v-if="error" class="text-danger mt-1">{{ error }}</div>
    </div>
</template>

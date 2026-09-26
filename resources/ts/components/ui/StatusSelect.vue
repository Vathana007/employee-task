<template>
  <select :value="modelValue" :disabled="disabled" @change="onChange"
    :class="[styles[modelValue], 'text-xs font-semibold pl-2.5 pr-1 py-1 rounded-full cursor-pointer border-0 focus:outline-none focus:ring-2 focus:ring-sky-500 disabled:opacity-60']">
    <option v-for="o in STATUS_OPTIONS" :key="o.value" :value="o.value">{{ o.label }}</option>
  </select>
</template>

<script setup lang="ts">
import { STATUS_OPTIONS } from '../../utils/status.js';
import type { TaskStatus } from '../../types/index.js';

defineProps<{ modelValue: TaskStatus; disabled?: boolean }>();
const emit = defineEmits<{ (e: 'change', value: TaskStatus): void }>();

const styles: Record<TaskStatus, string> = {
  pending: 'bg-amber-50 text-amber-600',
  in_progress: 'bg-sky-50 text-sky-600',
  completed: 'bg-emerald-50 text-emerald-600',
};

const onChange = (e: Event) => emit('change', (e.target as HTMLSelectElement).value as TaskStatus);
</script>

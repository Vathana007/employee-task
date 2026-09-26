<template>
  <Teleport to="body">
    <div v-if="modelValue" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="emit('update:modelValue', false)"></div>
      <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-6 border-b border-slate-100">
          <h3 class="text-base font-bold text-slate-800">{{ title }}</h3>
          <button @click="emit('update:modelValue', false)"
            class="p-1.5 rounded-lg hover:bg-slate-100 transition text-slate-400 cursor-pointer">
            <X class="w-4 h-4" />
          </button>
        </div>
        <div class="p-6">
          <slot />
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { X } from 'lucide-vue-next';

defineProps<{ modelValue: boolean; title: string }>();
const emit = defineEmits<{ 'update:modelValue': [value: boolean] }>();
</script>

<template>
  <div class="form-control flex flex-col">
    <label class="label">
      <span class="label-text">{{ label }}</span>
    </label>
    <div class="items-end">
      <button @click="$emit('reset')" class="btn btn-outline" :class="buttonClass">
        <PhX :size="20" color="currentColor" weight="regular" />
        {{ text }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { PhX } from '@phosphor-icons/vue'

defineProps({
  label: {
    type: String,
    default: 'Aksi'
  },
  text: {
    type: String,
    default: 'Bersihkan Pencarian'
  },
  buttonClass: {
    type: String,
    default: ''
  }
})

defineEmits(['reset'])
</script>

<script setup lang="ts">
import { SelectRoot } from 'reka-ui'
import { cn } from '@/lib/utils'

interface Props {
  modelValue?: string
  disabled?: boolean
  name?: string
  required?: boolean
}

const props = defineProps<Props>()

const emit = defineEmits<{
  'update:modelValue': [value: string]
}>()
</script>

<template>
  <SelectRoot v-bind="props" :model-value="modelValue" @update:model-value="emit('update:modelValue', $event)">
    <slot />
  </SelectRoot>
</template>
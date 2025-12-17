<script setup lang="ts">
import { SelectValue } from 'reka-ui'

interface Props {
  placeholder?: string
}

const props = defineProps<Props>()
</script>

<template>
  <SelectValue v-bind="props">
    <slot />
  </SelectValue>
</template>
<template>
  <select
    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
    v-bind="$attrs"
  >
    <slot />
  </select>
</template>

<script setup>
defineOptions({
  inheritAttrs: false
});
</script> 
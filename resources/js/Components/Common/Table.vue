<script setup>
defineProps({
  data: {
    type: Array,
    default() {
      return [];
    },
  },
  headings: {
    type: Array,
    default() {
      return [];
    },
  },
});
</script>

<template>
  <div class="overflow-x-auto">
    <table class="w-full text-sm text-left text-gray-500">
      <thead class="text-gray-700 uppercase bg-gray-50">
        <tr>
          <th 
            v-for="(heading, headingIndex) in headings" 
            :key="`heading_${headingIndex}`"
            class="px-4 py-3"
          >
            {{ heading }}
          </th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(row, rowIndex) in data"
          :key="`row_${rowIndex}`"
          class="border-b odd:bg-white even:bg-gray-50"
        >
          <slot name="row" :item="row" />
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style scoped>
</style>

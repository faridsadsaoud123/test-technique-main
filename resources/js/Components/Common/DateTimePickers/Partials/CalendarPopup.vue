<script setup>
import { ref, watch, computed } from "vue";
import moment from "moment";

const emit = defineEmits(["change"]);

const props = defineProps({
  show: Boolean,
  value: {
    type: Object, // moment()
    default: () => moment(),
  },
  withDate: {
    type: Boolean,
    default: true,
  },
  withTime: {
    type: Boolean,
    default: true,
  },
});

// Représente la valeur locale sélectionnée
const localValue = ref(props.value?.clone() ?? moment());

watch(() => props.value, (newVal) => {
  if (newVal) {
    localValue.value = newVal.clone();
  }
});

// Envoie la nouvelle valeur au parent
const updateValue = () => {
  emit("change", localValue.value.clone());
};
</script>

<template>
  <div v-if="show" class="absolute z-50 bg-white border border-gray-300 p-4 rounded-md shadow-md space-y-2">
    <!-- Sélecteur de date -->
    <div v-if="withDate">
      <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
      <input
        type="date"
        class="border px-2 py-1 rounded w-full"
        :value="localValue.format('YYYY-MM-DD')"
        @input="localValue.date(moment($event.target.value).date()).month(moment($event.target.value).month()).year(moment($event.target.value).year()); updateValue()"
      />
    </div>

    <!-- Sélecteur d'heure -->  
    <div v-if="withTime">
      <label class="block text-sm font-medium text-gray-700 mb-1">Time</label>
      <input
        type="time"
        class="border px-2 py-1 rounded w-full"
        :value="localValue.format('HH:mm')"
        @input="localValue.hour(moment($event.target.value, 'HH:mm').hour()).minute(moment($event.target.value, 'HH:mm').minute()); updateValue()"
      />
    </div>
  </div>
</template>

<style scoped>
  input {
    border-color: #d1d5db; /* border-gray-300 */
    outline: none;
  }
  input:focus {
    border-color: #a5b4fc; /* border-indigo-300 */
    box-shadow: 0 0 0 3px rgba(199, 210, 254, 0.5); /* focus:ring-indigo-200 focus:ring-opacity-50 */
  }
</style>

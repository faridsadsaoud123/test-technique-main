<script setup>
import { ref, watch } from "vue";
import moment from "moment";

const emit = defineEmits(["update:modelValue"]);

const props = defineProps({
    modelValue: {
        type: [Object, null],
        default: null
    },
    label: {
        type: String,
        required: true
    },
    utcMode: {
        type: Boolean,
        default: false
    }
});

const formatForInput = (date) => {
    if (!date) return { date: '', time: '' };
    const localDate = props.utcMode ? date.local() : date;
    return {
        date: localDate.format('YYYY-MM-DD'),
        time: localDate.format('HH:mm')
    };
};

const inputValues = ref(formatForInput(props.modelValue));

const updateDateTime = () => {
    if (inputValues.value.date && inputValues.value.time) {
        const [year, month, day] = inputValues.value.date.split('-');
        const [hour, minute] = inputValues.value.time.split(':');
        
        let newDate = moment()
            .year(year)
            .month(month - 1)
            .date(day)
            .hour(hour)
            .minute(minute);
            
        if (props.utcMode) {
            newDate = newDate.utc();
        }
        
        emit('update:modelValue', newDate);
    }
};

watch(() => props.modelValue, (newVal) => {
    inputValues.value = formatForInput(newVal);
});
</script>

<template>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ label }}</label>
        <div class="flex space-x-2">
            <input
                type="date"
                class="border px-2 py-1 rounded w-full"
                :value="inputValues.date"
                @input="inputValues.date = $event.target.value; updateDateTime()"
            />
            <input
                type="time"
                class="border px-2 py-1 rounded w-full"
                :value="inputValues.time"
                @input="inputValues.time = $event.target.value; updateDateTime()"
            />
        </div>
    </div>
</template>

<style scoped>
</style>
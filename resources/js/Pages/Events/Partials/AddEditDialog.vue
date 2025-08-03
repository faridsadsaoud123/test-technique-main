<script setup>
import { ref,watch } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";
import Dialog from "@/Components/Common/DialogModal";
import Button from "@/Components/Common/Button";
import Input from "@/Components/Common/Input";
import moment from "moment";
import DateTimePicker from "@/Components/Common/DateTimePickers/DateTimePicker.vue";


const emit = defineEmits(["close","openAddDialog"]);

const props = defineProps({
    show: Boolean,
    itemToEdit: {
        type: Object,
        default: null,
    },
});


const form = useForm({
    title: "",
    starts_at: null,
    ends_at: null
});

watch(() => props.itemToEdit, (newVal) => {
    if (newVal) {
        form.title = newVal.title;
        form.starts_at = newVal?.starts_at ? moment(newVal.starts_at) : moment();
    form.ends_at = newVal?.ends_at ? moment(newVal.ends_at) : null;
    } else {
        form.reset();
        form.starts_at = moment();// Initialiser avec l'heure locale
    }
}, { immediate: true });

// Un efonction pour verifier que les dates sont valides start date > end date
// et que l'heure de début est avant l'heure de fin si elles sont le même jour
const validateDates = () => {

  const start = form.starts_at.valueOf();
  const end = form.ends_at.valueOf();
  
  if (start > end) {
    alert("La date de fin doit être après la date de début");
    return false;
  }
  
  return true;
};


// Called when the user submits the form
const onSubmit = () => {
    if (!validateDates()) return;
    form.transform((data) => ({
    ...data,
    starts_at: form.starts_at?.format("YYYY-MM-DD HH:mm"),
        ends_at: form.ends_at?.format("YYYY-MM-DD HH:mm")
  }));
    if (props.itemToEdit) {
        form.put(route('events.update', props.itemToEdit.id), {

            preserveScroll: true,
            onSuccess: () => emit("close")
        });
    } else {
        form.post(route('events.store'), {
            
            preserveScroll: true,
            
            onSuccess: () => emit("close")
        });
        
    }
};

// Called when the dialog closes
const onClose = () => {
    form.reset();
    emit("close");
};

</script>

<template>
    <div>
        <!-- 
            ouvrir le formulaire d'ajout via un événement émis au parent (`openAddDialog`)
            La boite de dialogue `Dialog` s'affiche en fonction de la prop `show`. 
        -->
        <Button @click="$emit('openAddDialog')">
            <vue-feather type="plus" />
            <span class="ml-2">Add new</span>
        </Button>
        <Dialog :show="props.show" @close="$emit('close')">
            <template #header>{{ itemToEdit ? "Edit event" : "Add new event" }}</template>

            <Input
                name="title"
                label="Title"
                v-model="form.title"
                class="mb-6"
            />
            <DateTimePicker
                 v-model="form.starts_at"
                 label="Start Date"
                 class="mb-6"
            />
            <DateTimePicker
                  v-model="form.ends_at"
                 label="End Date"
                 class="mb-6"
            />

            <template #footer>
                <Button variant="secondary" class="mr-3" @click="onClose"
                    >Cancel</Button
                >
                <Button @click="onSubmit">Submit</Button>
            </template>
        </Dialog>
    </div>
</template>

<style scoped></style>

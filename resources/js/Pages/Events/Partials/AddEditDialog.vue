<script setup>
import { ref,watch } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";
import Dialog from "@/Components/Common/DialogModal";
import Button from "@/Components/Common/Button";
import Input from "@/Components/Common/Input";
import moment from "moment";
const emit = defineEmits(["close","openAddDialog"]);

const props = defineProps({
    show: Boolean,
    itemToEdit: {
        type: Object,
        default: null,
    },
});

const editing = ref(false);

const form = useForm({
    title: "",
    starts_at: null,
    ends_at: null
});
const toDateTimeLocal = (date) => {
    if (!date) return null;
    return moment(date).format('YYYY-MM-DDTHH:mm');
};
const displayDateTime = (date) => {
    if (!date) return "";
    return date.replace('T', ' ');
};
watch(() => props.itemToEdit, (newVal) => {
    if (newVal) {
        form.title = newVal.title;
       form.starts_at = toDateTimeLocal(newVal.starts_at);
        form.ends_at = toDateTimeLocal(newVal.ends_at);
    } else {
        form.reset();
    }
}, { immediate: true });


// // Called when the user clicks on the "Add new" button
// const onAddNew = () => {
//     form.reset();
//     editing.value = false;
// };

// Called when the user submits the form
const onSubmit = () => {
    const payload = {
        title: form.title,
        starts_at: form.starts_at ? moment(form.starts_at.replace(' ', 'T')).format('YYYY-MM-DD HH:mm') : null,
        ends_at: form.ends_at ? moment(form.ends_at.replace(' ', 'T')).format('YYYY-MM-DD HH:mm') : null
    };
    if (props.itemToEdit) {
        form.put(route('events.update', props.itemToEdit.id), {
            ...payload,
            preserveScroll: true,
            onSuccess: () => emit("close")
        });
    } else {
        form.post(route('events.store'), {
            ...payload,
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
            <Input
                type="datetime-local"
                name="starts_at"
                label="Start Date"
                :value="form.starts_at"
                @input="form.starts_at = displayDateTime($event.target.value)"
                class="mb-6"
            />
            <Input
                type="datetime-local"
                name="ends_at"
                label="End Date"
                :value="form.ends_at"
                @input="form.ends_at = displayDateTime($event.target.value)"
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

<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";
import Dialog from "@/Components/Common/DialogModal";
import Button from "@/Components/Common/Button";
import Input from "@/Components/Common/Input";
import moment from "moment";
const emit = defineEmits(["close"]);

const props = defineProps({
    itemToEdit: {
        type: Object,
        default: null,
    },
});

const show = ref(false);
const editing = ref(false);

const form = useForm({
    title: "",
    starts_at: null,
    ends_at: null,
});

// Called when the user clicks on the "Add new" button
const onAddNew = () => {
    form.reset();
    show.value = true;
    editing.value = false;
};

// Called when the user submits the form
const onSubmit = () => {
     const transformedData = {
        title: form.title,
        starts_at: form.starts_at ? moment(form.starts_at).format("YYYY-MM-DD HH:mm") : null,
        ends_at: form.ends_at ? moment(form.ends_at).format("YYYY-MM-DD HH:mm") : null,
    };

    const requestParams = {
        preserveScroll: true,
        onSuccess: onClose,
    };

    // Stores or updates the item
    if (props.itemToEdit) {
        form.put(route("events.update", props.itemToEdit.id), {
            ...transformedData,
            preserveScroll: true,
            onSuccess: () => onClose(),
        });
    } else {
        form.post(route("events.store"), {
            ...transformedData,
            preserveScroll: true,
            onSuccess: () => onClose(),
        });
    }
};

// Called when the dialog closes
const onClose = () => {
    form.reset();
    show.value = false;
    emit("close");
};
</script>

<template>
    <div>
        <Button @click="onAddNew">
            <vue-feather type="plus" />
            <span class="ml-2">Add new</span>
        </Button>
        <Dialog :show="show" @close="onClose">
            <template #header>{{
                editing ? "Edit event" : "Add new event"
            }}</template>

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
                v-model="form.starts_at"
                class="mb-6"
            />
            <Input
                type="datetime-local"
                name="ends_at"
                label="End Date"
                v-model="form.ends_at"
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

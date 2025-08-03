<script setup>
import { onMounted, ref, computed  } from "vue";
import { Inertia } from "@inertiajs/inertia";
import moment from "moment";
import AppLayout from "@/Layouts/AppLayout";
import AddEditDialog from "./Partials/AddEditDialog";
import Button from "@/Components/Common/Button";
import Dialog from "@/Components/Common/DialogModal";
import Table from "@/Components/Common/Table";

const format = "YYYY-MM-DD";

const props = defineProps({
    events: {
        type: Array,
        default: [],
    },
    starts_at: String,
    ends_at: String,
});

const dateFilters = ref([null, null]);
const showDialog = ref(false); // Controle l'affichage du dialogue
const selectedEvent = ref(null); // Stocke l'evenement sélectionne
const searchQuery = ref('');

const handleAddNew = () => {
  selectedEvent.value = null; 
  showDialog.value = true; 
};
// Load url params into state if any existed on mount
onMounted(() => {
    if (props.starts_at) {
        dateFilters.value[0] = moment(props.starts_at, format);
    }
    if (props.ends_at) {
        dateFilters.value[1] = moment(props.ends_at, format);
    }
});
// const itemToEdit = ref(null);
const itemToDelete = ref(null);

const onDelete = () => {
    Inertia.delete(route("events.destroy", itemToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            itemToDelete.value = null;
        },
    });
};
const filteredEvents = computed(() => {
    if (!searchQuery.value) {
        return props.events;
    }
    const query = searchQuery.value.toLowerCase();
    return props.events.filter(event => 
        event.title.toLowerCase().includes(query) ||
        moment(event.starts_at).format("DD/MM/YYYY").includes(query) ||
        moment(event.ends_at).format("DD/MM/YYYY").includes(query)
    );
});
</script>

<template>
    <AppLayout title="Events">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Events
            </h2>
        </template>

        <div class="card">
            <div class="mb-3">
                <div class="mb-6 flex flex-row justify-between items-end">
                    <div class="flex items-center space-x-4 w-full">
                        <!-- Barre de recherche -->
                        <div class="relative flex-grow max-w-md">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <vue-feather type="search" size="1rem" class="text-gray-400" />
                            </div>
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Search by title or date..."
                                class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>
                        
                       
                    </div>
                    <div>
                         <!--  :item-to-edit="selectedEvent" permet de pré-remplir le formulaire en mode édition-->
                        <AddEditDialog
                             :show="showDialog"
                             :item-to-edit="selectedEvent" 
                            @close="showDialog = false"
                            @openAddDialog="handleAddNew"
                        />
                    </div>
                </div>
                <Dialog
                    :show="itemToDelete != null"
                    @close="itemToDelete = null"
                >
                    <template #header>Deleting event</template>
                    Are you sure you want to delete this event ?
                    <template #footer>
                        <Button
                            variant="secondary"
                            class="mr-3"
                            @click="itemToDelete = null"
                            >Cancel</Button
                        >
                        <Button variant="danger" @click="onDelete"
                            >Submit</Button
                        >
                    </template>
                </Dialog>
            </div>
            <Table :data="filteredEvents" :headings="['Title', 'Start Date','End Date', 'Actions']">
                <template #row="{ item }">
                    <td class="px-4 py-3">{{ item.title }}</td>
                        <td class="px-4 py-3">
                             {{ moment.parseZone(item.starts_at).format("HH:mm DD/MM/YYYY") }}
                        </td>
                        <td class="px-4 py-3">
                             {{ moment.parseZone(item.ends_at).format("HH:mm DD/MM/YYYY") }}
                        </td>
                        <td class="px-4 py-3">
                        <span
                            class="px-2 text-gray-700 hover:text-blue-500 cursor-pointer transition"
                        >
                            <vue-feather
                                 type="edit"
                                size="1.3rem"
                                @click.stop="selectedEvent = item; showDialog = true"
                            />
                        </span>
                        <span
                            class="px-2 text-gray-700 hover:text-red-500 cursor-pointer transition"
                        >
                            <vue-feather
                                type="trash"
                                size="1.3rem"
                                @click="itemToDelete = item"
                            />
                        </span>
                    </td>
                </template>
            </Table>
        </div>
    </AppLayout>
</template>

<style scoped></style>

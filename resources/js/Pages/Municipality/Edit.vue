<script setup>
import { ref, onMounted } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

import NavLink from '@/Components/NavLink.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Card from '@/Components/Card.vue';
import SuccessButton from '@/Components/SuccessButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import LocationFormEdit from "./Partials/LocationFormEdit.vue";

/**
 * @typedef {Object} Location
 * @property {string} name - The name of the location.
 * @property {string} address - The address of the location.
 * @property {number[]} geolocation - The geolocation of the location as an array [latitude, longitude].
 * @property {string[]} tags
 */

const props = defineProps({
    title: String,
    municipality: Object
});

const loading = ref(false);

const breadcrumbs = ref([
    { "name": "Mapa", "href": route('map')},
    { "name": "Oficinas", "href": route('municipality.index') },
    { "name": props.title??"Editar", "href": '' },
]);

const form = useForm({
    cvegeo: props.municipality.cvegeo,
    locations: []
});

onMounted(()=>{
    form.locations = props.municipality.locations;
});

/**
 * @returns {Location}
 */
function newLocationRecord(){
    return {
        name: "Nuevo elemento",
        address: "",
        geolocation: [0, 0]
    };
}

function newOfficeClick(){
    form.locations.push( newLocationRecord() );
}

function saveChangesClick(){
    form.patch( route('municipality.update', props.municipality._id),{
        replace: true
    });
}

/**
 * @param {number} index 
 */
function removeOfficeClick(index){
    const ele = form.locations[index];
    const userConfirmed = confirm(`¿Desea remover la oficina '${ele.name}'?`);
    if (userConfirmed) {
        form.locations.splice(index, 1);
    }
}

function cancelClick(){
    router.visit( route('municipality.index'), {
        replace: true
    });
}

function updateLocationTags(index, tags){
    form.locations[index].tags = tags.value;
}

</script>

<template>

    <Head title="Administrador" />

    <AuthenticatedLayout>

        <template #header>
            <Breadcrumb :breadcrumbs="breadcrumbs"/>
        </template>

        <div class="px-4 py-4 rounded-lg h-full max-w-screen-xl mx-auto">
            <Card class="h-full">
                <template #content>
                    <div class="p-2 flex flex-col gap-2 h-full">

                        <div class="p-2 flex gap-4 items-center">

                            <div role="form-group" class="min-w-[14rem]">
                                <p class="text-gray-600 dark:text-gray-200 uppercase truncate text-xl">{{ municipality.name }}</p>
                                <h2 class="text-gray-600 dark:text-gray-300 uppercase font-semibold text-sm">Municipio</h2>
                            </div>

                            <div role="form-group" class="min-w-[10rem]">
                                <p class="text-gray-600 dark:text-gray-200 uppercase truncate text-xl">{{ municipality.cvegeo }}</p>
                                <h2 class="text-gray-600 dark:text-gray-300 uppercase font-semibold text-sm">Cve Geo</h2>
                            </div>

                            <div role="form-group" class="min-w-[10rem]">
                                <p class="text-gray-600 dark:text-gray-200 uppercase truncate text-xl">{{ municipality.cve_mun }}</p>
                                <h2 class="text-gray-600 dark:text-gray-300 uppercase font-semibold text-sm">Cve Mun</h2>
                            </div>
                        </div>

                        <div class="w-full mt-4 col-span-3 border rounded bg-gray-100 h-full overflow-y-scroll">
                            <PrimaryButton class="m-2" v-on:click="newOfficeClick"> Agregar Oficina</PrimaryButton>

                            <LocationFormEdit v-for="(location, index) in form.locations"
                                :key="index"
                                :location="location"
                                :errors="form.errors"
                                v-on:removeOffice="removeOfficeClick(index)"
                                v-on:tagsUpdated="((tags) => updateLocationTags(index, tags))"
                            />

                        </div>

                        <div class="mx-auto flex justify-between w-full max-w-screen-md">
                            <DangerButton class="px-12" v-on:click="cancelClick"> Cancelar </DangerButton>
                            <SuccessButton class="px-6 py-4" v-on:click="saveChangesClick">Guardar Cambios</SuccessButton>
                        </div>

                    </div>
                </template>
            </Card>
        </div>

    </AuthenticatedLayout>
</template>

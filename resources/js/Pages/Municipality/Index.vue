<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { formatDatetime } from '@/utils/date.js';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

import NavLink from '@/Components/NavLink.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import SearchInput from '@/Components/SearchInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputSelect from '@/Components/InputSelect.vue';
import AnimateSpin from '@/Components/Icons/AnimateSpin.vue';

const props = defineProps({
    municipalities: Array
});

const loading = ref(false);

const breadcrumbs = ref([
    { "name": "Mapa", "href": route('map')},
    { "name": "Oficinas", "href": '' }
]);

function handleInputSearch(params) {
    loading.value = true;
    setTimeout(() => {
        loading.value = false;
    }, 1000);
}

</script>

<template>

    <Head title="Administrador" />

    <AuthenticatedLayout>
        <template #header>
            <Breadcrumb :breadcrumbs="breadcrumbs" />
        </template>

        <div class="px-4 py-4 rounded-lg min-h-screen max-w-screen-xl mx-auto">
            
            <!-- filter data area -->
            <div class="grid grid-cols-3 gap-2 px-2 pt-2 pb-4 bg-white border-x border-t dark:bg-gray-700 dark:border-gray-500">
                <SearchInput class="col-span-3" placeHolder="" v-on:search="handleInputSearch"/>
            </div>

            <!-- data table -->
            <table class="table-fixed w-full shadow text-sm text-left border rtl:text-right text-gray-500 dark:text-gray-400 dark:border-gray-500">
                <thead class="sticky top-0 z-20 text-xs uppercase text-gray-700 border bg-gradient-to-b from-gray-50 to-slate-100 dark:from-gray-800 dark:to-gray-700 dark:text-gray-200 dark:border-gray-500">
                    <AnimateSpin v-if="loading" class="w-4 h-4 mx-2 absolute top-2.5" />
                    <tr>
                        <th scope="col" class="w-1/5 text-center px-6 py-3">
                            Municipio
                        </th>
                        <th scope="col" class="w-1/5 text-center px-6 py-3">
                            Clave
                        </th>
                        <th scope="col" class="w-1/5 text-center px-6 py-3">
                            Municipios
                        </th>
                        <th scope="col" class="w-1/5 text-center px-6 py-3">
                            Ultima Actualizacion
                        </th>
                        <th scope="col" class="w-1/5 text-center px-6 py-3">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody id="table-body" class="bg-white dark:bg-gray-800 dark:border-gray-500">
                    <template v-if="municipalities && municipalities.length > 0">
                        <tr v-for="mun in municipalities" class="border-b">

                            <td class="p-2 text-center">
                                {{ mun.name }}
                            </td>

                            <td class="p-2 text-center">
                                {{ mun.cvegeo }}
                            </td>

                            <td class="p-2 text-center">
                                {{ mun.locations.length }}
                            </td>

                            <td class="p-2 text-center uppercase">
                                {{ formatDatetime(mun.updated_at) }}
                            </td>

                            <td class="p-2 text-center">
                                <NavLink :href="route('municipality.edit', mun._id)">
                                    <div class="flex gap-2 shadow bg-slate-200 px-4 py-1">
                                        <span>Editar</span>
                                    </div>
                                </NavLink>
                            </td>
                        </tr>
                    </template>
                    <template v-else>
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center font-medium whitespace-nowrap dark:text-white">
                                No hay registros de Empleados.
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

        </div>

    </AuthenticatedLayout>
</template>

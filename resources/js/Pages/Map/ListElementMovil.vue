<script setup>

import Location from '@/Components/Icons/Location.vue';

const props = defineProps({
    municipality: Object
});

const emit = defineEmits(['municipalityClick', 'locationClick']);

const handleOnMunicipalityClick = ()=> emit('municipalityClick', props.municipality);

const handleLocationClick = (e, location)=>{
    emit('locationClick', props.municipality, location);
};

</script>

<template>
    <li class="bg-white border px-2 py-4 rounded-md shadow-md mb-2 hover:bg-gray-100" :id="municipality.cvegeo">
        <h2 v-on:click="handleOnMunicipalityClick" class="uppercase py-1 font-semibold text-gray-800 cursor-pointer hover:underline">
            {{ municipality.name }}
        </h2>

        <ul class="mt-0 ml-2 space-y-1">
            <li v-for="l in municipality.locations">
                <p v-on:click="(e) => handleLocationClick(e, l)" class="text-gray-500 hover:underline cursor-pointer flex items-center">
                    <Location class="h-5 w-5 mr-2" />
                    <div>{{ l.name }}</div>
                </p>
            </li>
        </ul>
    </li>
</template>
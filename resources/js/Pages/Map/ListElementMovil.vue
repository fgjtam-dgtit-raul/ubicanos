<script setup>

import EyeIcon from '@/Components/Icons/EyeIcon.vue';

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
    <li class="border-b p-2 rounded-b-lg hover:bg-gray-100" :id="municipality.cvegeo">
        <h2 v-on:click="handleOnMunicipalityClick" class="hover:underline uppercase py-1 font-semibold sticky top-0 bg-inherit cursor-pointer">
            {{ municipality.name }}
        </h2>

        <ul class="mt-0 ml-2 space-y-1">
            <li v-for="l in municipality.locations">
                <p v-on:click="(e) => handleLocationClick(e, l)" class="text-sm text-gray-600 hover:underline cursor-pointer flex items-center">
                    <EyeIcon class="h-3 mx-1" />
                    <div>{{ l.name }}</div>
                </p>
            </li>
        </ul>
    </li>
</template>
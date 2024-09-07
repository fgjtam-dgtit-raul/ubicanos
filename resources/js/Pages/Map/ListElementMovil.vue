<script setup>

import Location from '@/Components/Icons/Location.vue';

const props = defineProps({
    municipality: Object,
    isSelected: Boolean, // Prop to indicate if this municipality is selected
});

const emit = defineEmits(['municipalityClick', 'locationClick']);

const handleOnMunicipalityClick = ()=> emit('municipalityClick', props.municipality);

const handleLocationClick = (e, location)=>{
    emit('locationClick', props.municipality, location);
};

</script>

<template>
    <li 
        :class="{ 'bg-gray-700': isSelected, 'bg-white hover:bg-gray-200': !isSelected }"
        class="bg-white border px-2 py-4 rounded-md shadow-md mb-2" 
        :id="municipality.cvegeo"
    >
        <h2 
            :class="{ 'text-gray-100': isSelected, 'text-gray-700': !isSelected }"
            v-on:click="handleOnMunicipalityClick" 
            class="uppercase mb-1 font-semibold cursor-pointer hover:underline"
        >
            {{ municipality.name }}
        </h2>

        <ul class="mt-0 ml-4 space-y-1">
            <li v-for="l in municipality.locations">
                <p 
                    v-on:click="(e) => handleLocationClick(e, l)" 
                    class="cursor-pointer flex items-center hover:underline"
                    :class="{ 'text-gray-200': isSelected, 'text-gray-500': !isSelected }"
                >
                    <Location class="h-5 w-5 mr-2" />
                    <div>{{ l.name }}</div>
                </p>
            </li>
        </ul>
    </li>
</template>
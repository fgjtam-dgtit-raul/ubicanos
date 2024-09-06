<script setup>
import { ref, onMounted } from 'vue';
import CardTitle from '@/Components/CardTitle.vue';
import InputText from '@/Components/InputText.vue';
import InputNumber from '@/Components/InputNumber.vue';
import DangerButton from '@/Components/DangerButton.vue';
import TrashCanIcon from '@/Components/Icons/TrashCanIcon.vue';
import InputError from '@/Components/InputError.vue';
import CloseICon from '@/Components/Icons/CloseIcon.vue';

/**
 * @typedef {Object} Location
 * @property {string} name - The name of the location.
 * @property {string} address - The address of the location.
 * @property {number[]} geolocation - The geolocation of the location as an array [latitude, longitude].
 * @property {string[]} tags
 */

const props = defineProps({
    index: Number,
    location: Object,
    errors: Object|Array
});

const emit = defineEmits(['removeOffice', 'tagsUpdated']);

const form = ref(props.location);

const tags = ref([...(props.location.tags?props.location.tags:[])]);

const textInput = ref("");

function removeOfficeClick(){
    emit('removeOffice', props.index);
}

function onInputTagKeyDown(e){
    if(e.key == 'Enter'){
        if(textInput.value){
            textInput.value.split(" ").forEach((newTag)=>{
                if( !tags.value.includes(newTag.toUpperCase())){
                    tags.value.push(newTag.toUpperCase());
                }
            });
            textInput.value = '';
        }
    }
    emit('tagsUpdated', tags);
}

function onRemoveTag(index){
    tags.value.splice(index, 1);
    emit('tagsUpdated', tags);
}

</script>

<template>
    <div :key="index" class=" grid grid-cols-[1fr_24rem_auto] gap-2 m-2 p-2 shadow bg-white border rounded-lg">

        <div class="w-full">
            <CardTitle>Nombre</CardTitle>
            <InputText v-model="location.name" />
            <InputError class="col-span-3" :message="errors[`locations.${index}.name`]" />
        
            <CardTitle class="mt-2">Direccion</CardTitle>
            <InputText v-model="location.address" />
            <InputError class="col-span-3" :message="errors[`locations.${index}.address`]" />
        </div>

        <div class="w-[24rem] pl-2">
            <CardTitle>Latitud</CardTitle>
            <InputNumber v-model="location.geolocation[0]" />
            <InputError class="col-span-3" :message="errors[`locations.${index}.geolocation.0`]" />

            <CardTitle class="mt-2">Longitud</CardTitle>
            <InputNumber v-model="location.geolocation[1]" />
            <InputError class="col-span-3" :message="errors[`locations.${index}.geolocation.1`]" />
        </div>

        <div class="w-fit pl-2 flex">
            <DangerButton v-on:click="removeOfficeClick(index)">
                <TrashCanIcon class="w-4 h-4" />
            </DangerButton>
        </div>

        <div class="col-span-3">
            <CardTitle>Etiquetas</CardTitle>

            <div class="flex items-center bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full dark:bg-gray-700 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 min-h-10">

                <div v-for="(tag, i) in tags" :key="i" class="mx-1 bg-emerald-600 text-white text-xs p-2 rounded-lg flex items-center h-[1.5rem] w-fit">
                    <div class="w-fit line-clamp-1 break-keep">{{ tag }}</div>
                    <button v-on:click="onRemoveTag(i)" class="text-white rounded-full hover:bg-emerald-800 ml-1 mb-1">
                        <CloseICon class="w-3 h-3"/>
                    </button>
                </div>

                <input type="text" v-model="textInput" v-on:keydown="onInputTagKeyDown" class="uppercase w-full rounded-lg border-0 focus-visible:outline-0 focus-visible:border-0 focus-visible:shadow-none focus:outline-0 focus:border-0 focus:shadow-none" />
            </div>

            <InputError class="col-span-3" :message="errors[`locations.${index}.tags`]" />
        </div>

        <InputError class="col-span-3" :message="errors[`locations.${index}.geolocation`]" />

    </div>
</template>

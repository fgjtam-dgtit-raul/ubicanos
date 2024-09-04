<script setup>
import {onMounted} from 'vue';
import { Head } from '@inertiajs/vue3';

import Card from '@/Components/Card.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import AnimateSpin from '@/Components/Icons/AnimateSpin.vue';

onMounted(()=>{
    setTimeout(()=>{
        window.location.href = "https://fiscaliadigital.fgjtam.gob.mx/mi-perfil";
    },500);
});

</script>

<template>
    <Head title="Dashboard" />

    <GuestLayout>
        <div class="h-full bg-gradient-to-bl from-[#374151] to-[#1b2341] overflow-y-clip flex text-white ">
            
            <div class="flex items-center w-full">

                <div class="flex flex-col bg-white px-12 mb-4 rounded-md dark:bg-slate-6 dark:text-gray-200 dark:bg-gray-700 dark:border-gray-500 shadow border py-4 mx-auto">
                    <div class="uppercase w-full h-full p-2 text-gray-600 dark:text-gray-100 transition-opacity duration-500 ease-out hover:ease-in opacity-100">Redireccionando</div>
                    <AnimateSpin class=" text-gray-600 w-4 h-4 mx-auto"/>
                </div>

            </div>

        </div>
    </GuestLayout>
</template>

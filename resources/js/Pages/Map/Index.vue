<script setup>
import { ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import L from "leaflet";

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    title: String,
    centerMap: {
        type: Array,
        default: [23.739622, -99.147968]
    },
    municipalities: Array,
    municipalitiesGeom: Object,
    officesLocations: Array,
});

const map = ref({});

const polygons = ref([]);

const bounds = ref({});

const municipalitySelected = ref(null);

onMounted(()=>{
    
    // var map = L.map('map').setView([51.505, -0.09], 13);

    initializeMap();

})

function initializeMap() {
    
    // * laod map
    document.map = L.map('map', {
        center: L.latLng( props.centerMap[0], props.centerMap[1]),
        zoom: 8,
        dragging: false,
        scrollWheelZoom: false,
        touchZoom: false,
        zoomControl: false,
        doubleClickZoom: false
    });

    // * center the view based on the bound
    var corner1 = L.latLng(28.115100, -100.261120), corner2 = L.latLng(22.143872, -97.082750);
    bounds.value = L.latLngBounds(corner1, corner2);
    document.map.fitBounds(bounds.value)

    // * draw the map
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        opacity:.5,
    }).addTo(document.map);

    drawGeometries();

    drawPushpins();

}

function drawGeometries(){
    props.municipalitiesGeom.forEach(element => {
        var polygon = L.polygon( element.geometry, {
            color: "#0c1f4a",
            weight: 1,
            opacity: .5,
            fill: true,
            fillColor: "#2A3B67",
            fillOpacity: .15
        }).addTo(document.map);
        polygon.data = element.properties;
        polygon.on('click', handlePoligonClick);

        // save the ref of the polygon
        polygons.value.push(polygon);

    });
}

function handlePoligonClick(e){
    const municipalityData = e.target.data;
    moveMap(e.latlng, 9.5)

    municipalitySelected.value = municipalityData;
}

function moveMap(latlng, zoom){
    document.map.flyTo(latlng, zoom,{
        duration: .5
    });
}

function resetMapPosition(e){
    if(e){
        e.stopPropagation();
    }
    document.map.flyToBounds(bounds.value,{
        duration: .5
    });

    municipalitySelected.value = null;
}

function drawPushpins(){
    props.officesLocations.forEach(element => {
        var pushpin = L.marker( L.latLng(element[0], element[1]),{
            icon: L.icon({
                iconUrl: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png',
                iconSize: [24, 24]
            }),
            title: element[2],
            opacity: .75,
            riseOnHover: true
        })
        .addTo(document.map)
        .bindPopup( element[3]);

        pushpin.on('click', handlePushpinOnClick);
        
    });
}

function handlePushpinOnClick(e){
    //console.dir(e.target._latlng);
    moveMap(e.target._latlng, 16.5);
}

function handleMunicipalityListItem(municipality){
    const municipalityName = municipality.name;
    const polygon = polygons.value.filter( item => item.data.NOM_MUN == municipalityName);
    try {
        const munyGeo = props.municipalitiesGeom.filter(item => item.properties.CVE_MUN == polygon[0].data.CVE_MUN);
        const center = calculateCenter(munyGeo[0].geometry);
        moveMap(center, 9.5);

        // save the selected municipality
        municipalitySelected.value = polygon[0].data;
    } catch (error) {
        resetMapPosition(null);
    }
}

function calculateCenter(coordinates){
    var latSum = 0;
    var lonSum = 0;

    coordinates.forEach(coord => {
        latSum += coord[0];
        lonSum += coord[1];
    });

    const center = [latSum / coordinates.length, lonSum / coordinates.length];

    return center;
}

</script>

<template>

    <Head title="Profile" />

    <AuthenticatedLayout>

        <div class="max-w-screen-2xl mx-auto p-2 w-full h-full gap-2 grid grid-cols-map grid-rows-map" >

            <div class="h-full outline outline-1 outline-gray-200 rounded-lg bg-white overflow-y-auto row-span-2 select-none">
                <ul class="">
                    <li v-for="m in municipalities" v-on:click="handleMunicipalityListItem(m)" class="border-b p-2 rounded-b-lg hover:bg-gray-100">
                        
                        <h2 class="uppercase py-2 text-lg font-semibold sticky top-0 bg-inherit cursor-pointer">{{m.name}}</h2>

                        <ul class="mt-2 space-y-2">
                            <li v-for="l in m.locations">
                                <p class="text-sm text-gray-600 hover:underline cursor-pointer flex items-center">
                                    <svg fill="currentColor" class="h-3 mx-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM432 256c0 79.5-64.5 144-144 144s-144-64.5-144-144s64.5-144 144-144s144 64.5 144 144zM288 192c0 35.3-28.7 64-64 64c-11.5 0-22.3-3-31.6-8.4c-.2 2.8-.4 5.5-.4 8.4c0 53 43 96 96 96s96-43 96-96s-43-96-96-96c-2.8 0-5.6 .1-8.4 .4c5.3 9.3 8.4 20.1 8.4 31.6z"/></svg>
                                    
                                    <div>{{ l.title }}</div>
                                </p>
                            </li>
                        </ul>

                    </li>
                </ul>
            </div>

            <div class="h-full outline outline-1 outline-gray-200 rounded-lg bg-emerald-100 overflow-clip" :class="[municipalitySelected ?'row-span-1' :'row-span-2']">
                <div id="map" class="w-full h-full" />
            </div>

            <div v-if="municipalitySelected" class="outline outline-1 opacity-50 outline-gray-200 rounded-lg bg-blue-100 flex flex-col">
                <div class="flex">
                    <svg v-on:click="resetMapPosition" fill="currentColor" aria-hidden="true" data-prefix="fas" data-icon="times" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 352 512" class="cursor-pointer hover:text-gray-900 hover:bg-white ml-auto w-5 h-5 p-1 svg-inline--fa fa-times fa-w-11 fa-7x"><path fill="currentColor" d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z" class=""></path></svg>
                </div>
                <div class="w-full h-full overflow-auto">
                    <div v-for="key in Object.keys(municipalitySelected)" class="flex gap-2 border-b border-gray-400">
                        <div>{{ key }}</div>
                        <div>{{ municipalitySelected[key] }}</div>
                    </div>
                </div>
            </div>
            
        </div>
        
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import L from "leaflet";

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ListElement from './ListElement.vue';

const props = defineProps({
    title: String,
    centerMap: {
        type: Array,
        default: [23.739622, -99.147968]
    },
    municipalities: Array,
    municipalitiesGeom: Object
});

const map = ref({});

const polygons = ref([]);

const markers = ref([]);

const bounds = ref({});

const municipalitySelected = ref(null);

onMounted(()=>{
    initializeMap();
})

function initializeMap() {
    
    // * load map
    map.value = L.map('map', {
        center: L.latLng( props.centerMap[0], props.centerMap[1]),
        zoom: 8,
        dragging: true,
        scrollWheelZoom: true,
        touchZoom: true,
        zoomControl: true,
        doubleClickZoom: true
    });

    // * center the view based on the bound
    var corner1 = L.latLng(28.115100, -100.261120), corner2 = L.latLng(22.143872, -97.082750);
    bounds.value = L.latLngBounds(corner1, corner2);
    map.value.fitBounds(bounds.value)

    // * draw the map
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        opacity:.5,
    }).addTo(map.value);

    drawGeometries();

    // * get array of markers
    const locations = props.municipalities.reduce((acc, element) => {
        return acc.concat(element.locations);
    }, []);
    drawMarkers(locations);

}

function drawGeometries(){
    props.municipalitiesGeom.forEach(element => {
        var polygon = L.polygon( element.geometry, {
            color: "#0c1f4a",
            weight: 1,
            opacity: .5,
            fill: true,
            fillColor: "#2A3B67",
            fillOpacity: .05
        }).addTo(map.value);
        polygon.data = element.properties;
        polygon.on('click', handlePoligonOnClick);

        // * save the ref of the polygon
        polygons.value.push(polygon);
    });
}

function handlePoligonOnClick(e){
    const municipalityData = e.target.data;
    moveMap(e.latlng, 9.5)
    municipalitySelected.value = municipalityData;
}

function moveMap(latlng, zoom){
    map.value.flyTo(latlng, zoom, {
        duration: 1
    });
}

function resetMapPosition(e){
    if(e){
        e.stopPropagation();
    }

    map.value.flyToBounds(bounds.value, {
        duration: .5
    });
    municipalitySelected.value = null;

    // * display all the layers
    setTimeout(() => {
            const locations = props.municipalities.reduce((acc, element) => {
                return acc.concat(element.locations);
            }, []);
            drawMarkers(locations);
        }, 750);

}

function drawMarkers(locations){
    // * clear the markers
    markers.value.forEach(layer => layer.remove());
    markers.value = [];

    // draw the new layers
    locations.forEach(location => {
        if( location.geolocation && location.geolocation.length == 2){

            var marker = L.marker( L.latLng( location.geolocation[0], location.geolocation[1]),{
                title: location.name,
                icon: L.icon({
                    iconUrl: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png',
                    iconSize: [24, 24]
                }),
                opacity: .75,
                riseOnHover: true
            })
            .addTo(map.value)
            .bindPopup( location.address);

            marker.on('click', handleMarkerOnClick);

            // * save the marker
            markers.value.push(marker);
        }
    });
}

function handleMarkerOnClick(e){
    moveMap(e.target._latlng, 16.5);
}

function handleMunicipalityListItem(municipality){

    const polygon = polygons.value.filter( item => item.data.NOM_MUN == municipality.name);

    try {

        // * get the center of the municipality selected and moved to it
        const munyGeo = props.municipalitiesGeom.filter(item => item.properties.CVE_MUN == polygon[0].data.CVE_MUN);
        const center = munyGeo[0].center;
        moveMap(center, 9.5);

        // * display only the markes of the current municipalitie
        setTimeout(() => {
            drawMarkers(municipality.locations);
        }, 1000);

        // * save the selected municipality
        municipalitySelected.value = polygon[0].data;

    } catch (error) {
        resetMapPosition(null);
    }
}

function handleMunicipalityListItemLocation(municipality, location){

    try {

        // * move map
        moveMap(location.geolocation, 16.5);

        // * display only the markes of the current municipalitie
        setTimeout(() => {
            // * draw new markers
            drawMarkers(municipality.locations);

            // * show popup of the marker
            const marker = markers.value.find(m=>m.options.title == location.name);
            if(marker){
                marker.openPopup();
            }

        }, 1000);

        // * save the selected municipality
        municipalitySelected.value = municipality;

    } catch (error) {
        resetMapPosition(null);
    }
}

</script>

<template>

    <Head title="Ubicacion oficinas" />

    <AuthenticatedLayout>

        <div class="p-0 w-full h-full gap-0 flex flex-col items-center">

            <div class="w-full h-full outline outline-1 outline-gray-200 bg-emerald-100 overflow-clip">
                <div id="map" class="w-full h-full" />

                <div v-if="municipalitySelected" class="fixed top-[4rem] right-0 w-full h-[10rem] opacity-50 outline outline-1 outline-gray-200 rounded-lg bg-blue-100 flex flex-col z-[999]">
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

            <div class="w-full h-[16rem] outline outline-1 outline-gray-200 bg-white overflow-y-auto row-span-2 select-none">
                <ul class="h-full">
                    <ListElement v-for="m in municipalities"
                        :key="m.cvegeo"
                        :municipality="m"
                        v-on:municipalityClick="handleMunicipalityListItem"
                        v-on:locationClick="handleMunicipalityListItemLocation"
                    />
                </ul>
            </div>

        </div>

    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import L from "leaflet";

import GuestLayout from '@/Layouts/GuestLayout.vue';

import ListElement from './ListElementMovil.vue';
import CloseIcon from '@/Components/Icons/CloseIcon.vue';

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

const dataSelected = ref(null);

onMounted(()=>{
    initializeMap();
})

function initializeMap() {
    
    // * load map
    map.value = L.map('map', {
        center: L.latLng( props.centerMap[0], props.centerMap[1]),
        zoom: 8,
        dragging: true,
        scrollWheelZoom: false,
        touchZoom: false,
        zoomControl: false,
        doubleClickZoom: false
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
    dataSelected.value = {
        "Municipio" : municipalityData.NOM_MUN
    };
    scrollElementIntoView(municipalityData.CVEGEO);
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
    dataSelected.value = null;

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
            .bindPopup(`<b style="text-transform:uppercase;">${location.name}</b><p>${location.address}</p>
            <a href="https://www.google.com/maps?q=${location.geolocation[0]},${location.geolocation[1]}" target="_blank" style="text-align: right; display: block;">Ver en Google Maps</a>
            `);

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
        dataSelected.value = {
            "Municipio" : municipality.name
        };

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
        dataSelected.value = {
            "Municipio" : municipality.name,
            "Oficina" : location.name,
            "Direccion" : location.address,
        };

    } catch (error) {
        resetMapPosition(null);
    }
}

function scrollElementIntoView(cvegeo){
    try {
        document.getElementById(cvegeo).scrollIntoView();
    } catch (error) { }
}
</script>

<template>

    <Head title="Ubicacion oficinas" />
    
    <GuestLayout>
        <div class="max-w-screen-2xl mx-auto p-2 w-full h-full gap-1 grid grid-cols-map grid-rows-map shadow-lg" >

            <div class="h-full outline outline-1 outline-gray-200 rounded-lg bg-white overflow-y-auto row-span-2 select-none">
                <ul class="">
                    <ListElement v-for="m in municipalities"
                        :key="m.cvegeo"
                        :municipality="m"
                        v-on:municipalityClick="handleMunicipalityListItem"
                        v-on:locationClick="handleMunicipalityListItemLocation"
                    />
                </ul>
            </div>

            <div class=" flex flex-col items-center h-full outline outline-1 outline-gray-200 rounded-lg bg-emerald-100 overflow-clip row-span-2">

                <div v-if="dataSelected" class="absolute flex items-center justify-center">
                    <div class="p-2 shadow-lg outline outline-1 min-w-[32rem] outline-gray-200 rounded-lg opacity-90 bg-gray-100 flex flex-col items-end z-[990]">
                        <div class="absolute flex">
                            <button v-on:click="resetMapPosition" class="cursor-pointer text-gray-500 rounded-2xl hover:bg-white ml-auto">
                                <CloseIcon class="w-5 h-5 p-1"/>
                            </button>
                        </div>
                        <div class="w-full h-full overflow-auto flex flex-col gap-1 items-center">
                            <div class="text-gray-800 text-xl uppercase">{{ dataSelected.Municipio }}</div>
                            <div class="text-gray-800 text-sm uppercase">{{ dataSelected.Oficina }}</div>
                            <div class="text-gray-800 text-xs uppercase">{{ dataSelected.Direccion }}</div>
                        </div>
                    </div>
                </div>

                <div id="map" class="w-full h-full" />

            </div>
            
        </div>
    </GuestLayout>

</template>

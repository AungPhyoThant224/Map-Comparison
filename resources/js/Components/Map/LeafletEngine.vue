<script setup>
import { onMounted, onUnmounted, ref, watch } from "vue";
import L from "leaflet";
import "leaflet/dist/leaflet.css";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    customers: Array,
    selectedCustomer: Object,
});

const emit = defineEmits(["select"]);
const mapContainer = ref(null);
let map = null;
let markers = {};

onMounted(() => {
    // Initialize Map
    map = L.map(mapContainer.value).setView([16.7863, 96.1604], 13);

    map.on("moveend", () => {
        const bounds = map.getBounds();
        const sw = bounds.getSouthWest();
        const ne = bounds.getNorthEast();

        // partial reload
        router.reload({
            data: {
                sw_lat: sw.lat,
                sw_lng: sw.lng,
                ne_lat: ne.lat,
                ne_lng: ne.lng,
            },
            only: ["customers"],
            preserveState: true,
        });
    });

    // Add Tile Layer
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "© OpenStreetMap",
    }).addTo(map);

    // Add Markers
    props.customers.forEach((customer) => {
        // Create custom icon using the URL from CustomerResource
        const customIcon = L.icon({
            iconUrl: customer.marker,
            iconSize: [50, 50], // Size of the icon
            iconAnchor: [25, 45], // Point of the icon which will correspond to marker's location
            popupAnchor: [1, -34], // Point from which the popup should open relative to the iconAnchor
            shadowUrl:
                "https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png",
            shadowSize: [41, 41],
        });

        const marker = L.marker([customer.lat, customer.lng], {
            icon: customIcon,
        })
            .addTo(map)
            .on("click", () => emit("select", customer));

        // Add a simple popup with the name as seen in the reference UI
        marker.bindPopup(`<b>${customer.name}</b><br>${customer.category}`);

        markers[customer.id] = marker;
    });
});

// Watch for selection changes from the sidebar
watch(
    () => props.selectedCustomer,
    (newVal) => {
        if (newVal && map) {
            map.flyTo([newVal.lat, newVal.lng], 16);
            markers[newVal.id]?.openPopup();
        }
    },
);

onUnmounted(() => {
    if (map) map.remove();
});
</script>

<template>
    <div ref="mapContainer" class="h-full w-full"></div>
</template>

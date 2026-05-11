<script setup>
import { onMounted, onUnmounted, ref, watch } from "vue";
import L from "leaflet";
import "leaflet/dist/leaflet.css";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    customers: Array,
    selectedCustomer: Object,
    initialBounds: Object,
});

const emit = defineEmits(["select"]);
const mapContainer = ref(null);
let map = null;
let markers = {};
let markerLayer = null;

onMounted(() => {
    // Initialize Map
    map = L.map(mapContainer.value, {zoomControl: false}).setView(
        [props.initialBounds.lat, props.initialBounds.lng],
        13.5,
    );

    // Initialize Marker Layer
    markerLayer = L.layerGroup().addTo(map);

    // Add Markers
    renderMarkers(props.customers);

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

    // Zoom Control Position
    L.control.zoom({
        position: "topright",
    }).addTo(map);

    // Add Tile Layer
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "© OpenStreetMap",
    }).addTo(map);
});

watch(
    () => props.customers,
    (newCustomers) => {
        renderMarkers(newCustomers);
    },
    { deep: true },
);

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

const renderMarkers = (customerData) => {
    if (!markerLayer) return;

    markerLayer.clearLayers(); // Remove old markers so they don't double up
    markers = {}; // Reset our marker reference object

    customerData.forEach((customer) => {
        const customIcon = L.icon({
            iconUrl: customer.marker,
            iconSize: [50, 50],
            iconAnchor: [25, 45],
            popupAnchor: [1, -34],
        });

        const marker = L.marker([customer.lat, customer.lng], {
            icon: customIcon,
        }).on("click", () => emit("select", customer));

        marker.bindPopup(`<b>${customer.name}</b><br>${customer.category}`);

        markerLayer.addLayer(marker); // Add to group instead of map directly
        markers[customer.id] = marker;
    });
};

onUnmounted(() => {
    if (map) map.remove();
});
</script>

<template>
    <div ref="mapContainer" class="h-full w-full"></div>
</template>

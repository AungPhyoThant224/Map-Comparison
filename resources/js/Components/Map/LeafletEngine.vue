<script setup>
import { onMounted, onUnmounted, ref, watch, render, h } from "vue";
import L from "leaflet";
import "leaflet/dist/leaflet.css";
import { router } from "@inertiajs/vue3";
import CustomerPopup from "./CustomerPopup.vue";

const props = defineProps({
    customers: Array,
    selectedCustomer: Object,
    initialBounds: Object,
    getStars: Function
});

const emit = defineEmits(["select"]);
const mapContainer = ref(null);
let map = null;
let markers = {};
let markerLayer = null;
let selectionMarker = null;

onMounted(() => {
    // Initialize Map
    map = L.map(mapContainer.value, { zoomControl: false }).setView(
        [props.initialBounds.lat, props.initialBounds.lng],
        props.initialBounds.zoom,
    );

    // Initialize Marker Layer
    markerLayer = L.layerGroup().addTo(map);

    // Add Markers
    renderMarkers(props.customers);

    // Initialize the selection marker
    const selectionIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 55],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });
    selectionMarker = L.marker([0, 0], { icon: selectionIcon });

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
    L.control
        .zoom({
            position: "topright",
        })
        .addTo(map);

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
            const pos = [newVal.lat, newVal.lng];
            map.flyTo(pos, 16, { animate: true });

            selectionMarker.setLatLng(pos);
            if (!map.hasLayer(selectionMarker)) {
                selectionMarker.addTo(map);
            }
            selectionMarker.setZIndexOffset(1000);
        }
        else if (!newCustomer && selectionMarker) {
            selectionMarker.remove();
        }
    },
);

const renderMarkers = (customerData) => {
    if (!markerLayer) return;

    markerLayer.clearLayers();
    markers = {};

    customerData.forEach((customer) => {
        const customIcon = L.icon({
            iconUrl: customer.marker,
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -40],
        });

        const marker = L.marker([customer.lat, customer.lng], {
            icon: customIcon,
        });

        const container = document.createElement("div");

        render(
            h(CustomerPopup, {
                customer: customer,
                getStars: props.getStars,
            }),
            container,
        );

        marker.bindPopup(container, {
            closeButton: false,
            offset: L.point(170, 150),
            minWidth: 256,
            className: "modern-popup",
        });

        marker.on("mouseover", function () {
            this.openPopup();
        });
        marker.on("mouseout", function () {
            this.closePopup();
        });
        marker.on("click", () => {
            emit("select", customer);
        });

        markerLayer.addLayer(marker);
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

<style scoped>
:deep(.leaflet-popup-content-wrapper) {
    padding: 0;
    overflow: hidden;
    border-radius: 8px;
}

:deep(.leaflet-popup-content) {
    margin: 0;
    width: 300px !important;
}

:deep(.leaflet-popup-tip-container) {
    display: none; /* Removes the little triangle at the bottom for a cleaner look */
}
</style>

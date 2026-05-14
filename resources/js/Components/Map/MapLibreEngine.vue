<script setup>
import { onMounted, onUnmounted, ref, watch, render, h } from "vue";
import maplibregl from "maplibre-gl";
import "maplibre-gl/dist/maplibre-gl.css";
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
let selectionMarker = null;
let hoverPopup = null;

onMounted(() => {
    // Initialize MapLibre
    map = new maplibregl.Map({
        container: mapContainer.value,
        style: 'https://tiles.basemaps.cartocdn.com/gl/positron-gl-style/style.json',
        center: [props.initialBounds.lng, props.initialBounds.lat],
        zoom: props.initialBounds.zoom,
    });

    // Intialize Hover Popup
    hoverPopup = new maplibregl.Popup({
        closeButton: false,
        closeOnClick: false,
        offset: 20,
        className: 'modern-popup'
    });
    map.on('movestart', () => hoverPopup.remove());
    map.on('zoomstart', () => hoverPopup.remove());

    map.addControl(new maplibregl.NavigationControl(), 'top-right');

    // Bound Filtering Logic
    map.on("moveend", () => {
        const bounds = map.getBounds();
        router.reload({
            data: {
                sw_lat: bounds.getSouthWest().lat,
                sw_lng: bounds.getSouthWest().lng,
                ne_lat: bounds.getNorthEast().lat,
                ne_lng: bounds.getNorthEast().lng,
            },
            only: ["customers"],
            preserveState: true,
        });
    });

    // Initialize Selection Marker
    const el = document.createElement('div');
    el.className = 'selection-pin';
    selectionMarker = new maplibregl.Marker(el);

    map.on('load', () => {
        renderMarkers(props.customers);
    });
});

// Watch for data updates from Inertia
watch(() => props.customers, (newCustomers) => {
    renderMarkers(newCustomers);
}, { deep: true });

// Watch for selection from sidebar or clicks
watch(() => props.selectedCustomer, (newVal) => {
    if (newVal && map) {
        const pos = [newVal.lng, newVal.lat];
        map.flyTo({ center: pos, zoom: 15, essential: true });

        selectionMarker.setLngLat(pos).addTo(map);
    } else if (!newVal && selectionMarker) {
        selectionMarker.remove();
    }
});

const renderMarkers = (customerData) => {
    if (!map) return;

    // Clear existing markers
    Object.values(markers).forEach(m => m.remove());
    markers = {};

    customerData.forEach((customer) => {
        // Create the custom icon
        const el = document.createElement('div');
        el.className = 'business-marker';
        el.style.backgroundImage = `url(${customer.marker})`;
        el.style.width = '40px';
        el.style.height = '40px';
        el.style.backgroundSize = 'contain';
        el.style.backgroundRepeat = 'no-repeat';
        el.style.cursor = 'pointer';

        const marker = new maplibregl.Marker({ element: el })
            .setLngLat([customer.lng, customer.lat])
            .addTo(map);

        // Hover Popup Logic
        el.addEventListener('mouseenter', () => {
            const popupNode = document.createElement('div');
            render(h(CustomerPopup, {
                customer: customer,
                getStars: props.getStars
            }), popupNode);

            hoverPopup.setDOMContent(popupNode).setLngLat([customer.lng, customer.lat]).addTo(map);
        });

        el.addEventListener('mouseleave', () => {
            hoverPopup.remove();
        });

        el.addEventListener('click', () => {
            hoverPopup.remove();
            emit('select', customer);
        });


        markers[customer.id] = marker;
    });
};

onUnmounted(() => { if (map) map.remove(); });
</script>

<template>
    <div ref="mapContainer" class="h-full w-full"></div>
</template>

<style scoped>

:deep(.selection-pin) {
    pointer-events: none;
    z-index: 998;
}

:deep(.maplibregl-popup){
    max-width: 300px !important;
    z-index: 999;
}

:deep(.modern-popup .maplibregl-popup-content) {
    padding: 0;
    border-radius: 8px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    width: 300px;
}
:deep(.modern-popup .maplibregl-popup-tip) { display: none; }
</style>

<script setup>
import { onMounted, onUnmounted, ref, watch, render, h } from "vue";
import { router } from "@inertiajs/vue3";
import "ol/ol.css";
import Map from "ol/Map";
import View from "ol/View";
import TileLayer from "ol/layer/Tile";
import OSM from "ol/source/OSM";
import { fromLonLat, toLonLat } from "ol/proj";
import Feature from "ol/Feature";
import Point from "ol/geom/Point";
import VectorSource from "ol/source/Vector";
import VectorLayer from "ol/layer/Vector";
import { Style, Icon } from "ol/style";
import Overlay from "ol/Overlay";
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
let vectorSource = null;
let hoverOverlay = null;
let selectionOverlay = null;
let selectionEl = null;

onMounted(() => {
    // Initialize Vector Source and Layer for Custom Business Markers
    vectorSource = new VectorSource();
    const vectorLayer = new VectorLayer({
        source: vectorSource,
    });

    // Initialize Map
    map = new Map({
        target: mapContainer.value,
        layers: [
            new TileLayer({
                source: new OSM(),
            }),
            vectorLayer,
        ],
        view: new View({
            center: fromLonLat([props.initialBounds.lng, props.initialBounds.lat]),
            zoom: props.initialBounds.zoom,
        }),
        controls: [],
    });

    // Initialize Hover Popup Overlay
    const popupEl = document.createElement("div");
    popupEl.className = "ol-popup-container";
    hoverOverlay = new Overlay({
        element: popupEl,
        positioning: "bottom-center",
        offset: [0, -55],
        stopEvent: false,
    });
    map.addOverlay(hoverOverlay);

    // Initialize Selection Pin Overlay
    selectionEl = document.createElement("div");
    selectionEl.className = "selection-pin";
    selectionOverlay = new Overlay({
        element: selectionEl,
        positioning: "bottom-center",
        offset: [0, 5],
        stopEvent: false,
    });
    map.addOverlay(selectionOverlay);

    // Initial render
    renderMarkers(props.customers);

    // Bound Filtering Logic
    map.on("moveend", () => {
        const extent = map.getView().calculateExtent(map.getSize());
        // Transform to standard geographic coordinates (Lng/Lat)
        const bottomLeft = toLonLat([extent[0], extent[1]]);
        const topRight = toLonLat([extent[2], extent[3]]);

        router.reload({
            data: {
                sw_lat: bottomLeft[1],
                sw_lng: bottomLeft[0],
                ne_lat: topRight[1],
                ne_lng: topRight[0],
            },
            only: ["customers"],
            preserveState: true,
        });
    });

    // Map Pointer & Click Interaction Handlers
    map.on("pointermove", (event) => {
        if (event.dragging) {
            hoverOverlay.setPosition(undefined);
            return;
        }

        // Detect if hovering over a business feature marker
        const feature = map.forEachFeatureAtPixel(event.pixel, (feat) => feat);

        if (feature) {
            map.getTargetElement().style.cursor = "pointer";
            const customer = feature.get("customerData");
            const coordinates = feature.getGeometry().getCoordinates();

            // Clear old content and mount the dynamic Vue popup component
            popupEl.innerHTML = "";
            const popupNode = document.createElement("div");
            render(
                h(CustomerPopup, {
                    customer: customer,
                    getStars: props.getStars,
                }),
                popupNode
            );
            popupEl.appendChild(popupNode);

            hoverOverlay.setPosition(coordinates);
        } else {
            map.getTargetElement().style.cursor = "";
            hoverOverlay.setPosition(undefined);
        }
    });

    // Dismiss active hovers
    map.getView().on("change:center", () => hoverOverlay.setPosition(undefined));
    map.getView().on("change:resolution", () => hoverOverlay.setPosition(undefined));

    map.on("click", (event) => {
        const feature = map.forEachFeatureAtPixel(event.pixel, (feat) => feat);
        if (feature) {
            const customer = feature.get("customerData");
            hoverOverlay.setPosition(undefined);
            emit("select", customer);
        }
    });
});

// Watch for data updates from Inertia
watch(
    () => props.customers,
    (newCustomers) => {
        renderMarkers(newCustomers);
    },
    { deep: true }
);

// Watch for selection from sidebar or clicks
watch(
    () => props.selectedCustomer,
    (newVal) => {
        if (newVal && map) {
            const coordinates = fromLonLat([newVal.lng, newVal.lat]);

            // Fly smooth camera perspective transformation to target focus point
            map.getView().animate({
                center: coordinates,
                zoom: 16,
                duration: 1000,
            });

            selectionOverlay.setPosition(coordinates);
        } else if (!newVal) {
            selectionOverlay.setPosition(undefined);
        }
    }
);

const renderMarkers = (customerData) => {
    if (!vectorSource) return;

    vectorSource.clear();

    customerData.forEach((customer) => {
        const feature = new Feature({
            geometry: new Point(fromLonLat([customer.lng, customer.lat])),
        });

        // Store entire data context contextually directly into OpenLayers feature references
        feature.set("customerData", customer);

        // Apply dynamic theme custom icon definitions mapped by business types
        feature.setStyle(
            new Style({
                image: new Icon({
                    src: customer.marker,
                    scale: 0.8,
                    anchor: [0.5, 1.2],
                }),
            })
        );

        vectorSource.addFeature(feature);
    });
};

onUnmounted(() => {
    if (map) map.setTarget(null);
});
</script>

<template>
    <div ref="mapContainer" class="h-full w-full relative"></div>
</template>

<style scoped>
:deep(.ol-popup-container) {
    background: white;
    border-radius: 8px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    width: 300px;
    pointer-events: none;
    z-index: 999;
}

:deep(.selection-pin) {
    background-image: url("https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png");
    background-size: contain;
    background-repeat: no-repeat;
    width: 25px;
    height: 41px;
    pointer-events: none;
    transform: translateY(-41px);
    z-index: 998;
}
</style>

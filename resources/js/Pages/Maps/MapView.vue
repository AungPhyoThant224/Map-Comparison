<script setup>
import { Head } from '@inertiajs/vue3';
import { ref, defineAsyncComponent } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CustomerList from '@/Components/Map/CustomerList.vue';
import CustomerDetail from '@/Components/Map/CustomerDetail.vue';
import { Search, ChevronLeft, ChevronRight } from 'lucide-vue-next';
import MapLibreEngine from '@/Components/Map/MapLibreEngine.vue';

const LeafletEngine = defineAsyncComponent(() => import('@/Components/Map/LeafletEngine.vue'));

const props = defineProps({
    customers: Object,
    mapLibrary: String,
    initialBounds: Object
});

const selectedCustomer = ref(null);
const isSidebarOpen = ref(true);

const selectCustomer = (customer) => {
    selectedCustomer.value = customer;
    isSidebarOpen.value = true;
};

// Logic for star ratings
const getStars = (rating) => {
    const stars = [];
    for (let i = 1; i <= 5; i++) {
        if (i <= Math.floor(rating)) stars.push('full');
        else if (i === Math.ceil(rating) && rating % 1 !== 0) stars.push('half');
        else stars.push('empty');
    }
    return stars;
};
</script>

<template>
    <Head title="Map Assignment" />

    <AuthenticatedLayout>
        <div class="relative flex h-[calc(100vh-65px)] w-full overflow-hidden bg-gray-100">

            <button
                @click="isSidebarOpen = !isSidebarOpen"
                class="hidden transition-all duration-300 ease-in-out md:flex absolute left-0 top-1/2 -translate-y-1/2 z-[999] bg-white border shadow-md p-1 rounded-r-lg"
                :style="{ left: isSidebarOpen ? '392px' : '0' }"
            >
                <ChevronLeft v-if="isSidebarOpen" />
                <ChevronRight v-else />
            </button>

            <aside
                :class="[
                    'absolute z-[1000] transition-all duration-300 ease-in-out bg-white shadow-2xl flex flex-col',
                    'bottom-0 w-full md:top-4 md:left-4 md:w-96 md:h-[90%] md:rounded-xl',
                    isSidebarOpen ? 'h-[60%] md:translate-x-0' : 'h-12 md:-translate-x-[110%]'
                ]"
            >
                <div class="md:hidden flex justify-center p-2" @click="isSidebarOpen = !isSidebarOpen">
                    <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
                </div>

                <div class="px-4 py-4 border-b">
                    <div class="relative">
                        <input
                            type="text"
                            placeholder="Search Map..."
                            class="w-full rounded-full border-gray-200 py-2 pl-10 focus:border-blue-400 focus:ring-0 shadow-sm text-sm"
                        />
                        <Search class="absolute left-3 top-2.5 text-gray-400 w-4 h-4" />
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto custom-scrollbar">
                    <CustomerList
                        v-if="!selectedCustomer"
                        :customers="customers.data"
                        :getStars="getStars"
                        @select="selectCustomer"
                    />
                    <CustomerDetail
                        v-else
                        :customer="selectedCustomer"
                        :getStars="getStars"
                        @back="selectedCustomer = null"
                    />
                </div>
            </aside>

            <div id="map-wrapper" class="h-full w-full relative z-0">
                <LeafletEngine
                    v-if="mapLibrary === 'leaflet'"
                    :customers="customers.data"
                    :selectedCustomer="selectedCustomer"
                    :initialBounds="initialBounds"
                    :getStars="getStars"
                    @select="selectCustomer"
                />

                <MapLibreEngine
                    v-if="mapLibrary === 'maplibre'"
                    :customers="customers.data"
                    :selectedCustomer="selectedCustomer"
                    :initialBounds="initialBounds"
                    :getStars="getStars"
                    @select="selectCustomer"
                />

                <div v-else class="flex h-full items-center justify-center">
                    <p class="text-gray-400 italic">Engine for {{ mapLibrary }} coming soon...</p>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-gray-200 rounded-full;
}
</style>

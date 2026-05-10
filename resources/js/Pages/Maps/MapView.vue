<script setup>
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
// Import icons
import {
    Search, MapPin, Phone, Globe, ChevronLeft,
    Star, StarHalf, Navigation, Bookmark, Share2
} from 'lucide-vue-next';

const props = defineProps({
    customers: Object,
    mapLibrary: String
});

const selectedCustomer = ref(null);
const isSidebarOpen = ref(true); // Toggle for mobile view

const selectCustomer = (customer) => {
    selectedCustomer.value = customer;
    if (window.innerWidth < 768) isSidebarOpen.value = true;
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

            <aside
                :class="[
                    'absolute z-[1000] transition-all duration-300 ease-in-out bg-white shadow-2xl flex flex-col',
                    'bottom-0 w-full h-[50%] rounded-t-2xl md:top-4 md:left-4 md:w-96 md:h-[90%] md:rounded-xl',
                    !isSidebarOpen && window.innerWidth < 768 ? 'translate-y-full' : 'translate-y-0'
                ]"
            >
                <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto my-3 md:hidden"></div>

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
                    <div v-if="!selectedCustomer" class="divide-y divide-gray-100">
                        <div
                            v-for="customer in customers.data"
                            :key="customer.id"
                            @click="selectCustomer(customer)"
                            class="p-4 hover:bg-gray-50 cursor-pointer transition flex justify-between items-start"
                        >
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-900">{{ customer.name }}</h3>
                                <div class="flex items-center text-sm mb-1">
                                    <span class="text-gray-700 font-semibold mr-1">{{ customer.rating }}</span>
                                    <div class="flex text-yellow-500">
                                        <template v-for="(type, index) in getStars(customer.rating)" :key="index">
                                            <Star v-if="type === 'full'" class="w-3.5 h-3.5 fill-current" />
                                            <StarHalf v-else-if="type === 'half'" class="w-3.5 h-3.5 fill-current" />
                                            <Star v-else class="w-3.5 h-3.5 text-gray-300" />
                                        </template>
                                    </div>
                                    <span class="text-gray-500 ml-1">({{ customer.review_count }})</span>
                                </div>
                                <p class="text-xs text-gray-500">{{ customer.category }}</p>
                            </div>
                            <img v-if="customer.photos?.[0]" :src="customer.photos[0].url" class="w-16 h-16 rounded-lg object-cover ml-3" />
                        </div>
                    </div>

                    <div v-else class="animate-in slide-in-from-right duration-300">
                        <div class="relative">
                            <button
                                @click="selectedCustomer = null"
                                class="absolute top-4 left-4 bg-white rounded-full p-2 shadow-lg z-10 hover:bg-gray-100 transition"
                            >
                                <ChevronLeft class="w-5 h-5 text-gray-700" />
                            </button>
                            <img
                                :src="selectedCustomer.photos?.[0]?.url || 'https://via.placeholder.com/400x200'"
                                class="w-full h-48 md:h-56 object-cover"
                            />
                        </div>

                        <div class="p-5">
                            <h2 class="text-2xl font-bold text-gray-900 leading-tight">{{ selectedCustomer.name }}</h2>
                            <p class="text-sm text-gray-500 mt-1">{{ selectedCustomer.category }}</p>

                            <div class="flex justify-between gap-2 my-6 overflow-x-auto pb-2">
                                <button class="action-btn"><Navigation class="w-4 h-4 mr-1"/> Directions</button>
                                <button class="action-btn"><Bookmark class="w-4 h-4 mr-1"/> Save</button>
                                <button class="action-btn"><Share2 class="w-4 h-4 mr-1"/> Share</button>
                            </div>

                            <div class="space-y-5 text-gray-700 border-t pt-5">
                                <div class="flex items-start gap-4">
                                    <MapPin class="w-5 h-5 text-blue-500 shrink-0" />
                                    <p class="text-sm leading-relaxed">{{ selectedCustomer.address }}</p>
                                </div>
                                <div v-if="selectedCustomer.phone" class="flex items-center gap-4">
                                    <Phone class="w-5 h-5 text-blue-500 shrink-0" />
                                    <p class="text-sm">{{ selectedCustomer.phone }}</p>
                                </div>
                                <div v-if="selectedCustomer.website" class="flex items-center gap-4">
                                    <Globe class="w-5 h-5 text-blue-500 shrink-0" />
                                    <p class="text-sm text-blue-600 truncate">{{ selectedCustomer.website }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <div id="map" class="h-full w-full relative z-0">
                <div class="absolute inset-0 flex items-center justify-center bg-gray-50">
                    <div class="text-center animate-pulse">
                        <div class="w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
                        <p class="text-gray-500 font-medium">Loading {{ mapLibrary }} Engine...</p>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.action-btn {
    @apply flex items-center justify-center border border-gray-200 rounded-full px-4 py-2 text-blue-600 text-xs font-semibold hover:bg-blue-50 transition min-w-[100px];
}
.custom-scrollbar::-webkit-scrollbar {
    width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-gray-200 rounded-full;
}
</style>

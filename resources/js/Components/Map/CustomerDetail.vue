<script setup>
import {
    ChevronLeft, Navigation, Bookmark, Share2,
    MapPin, Phone, Globe, Star, StarHalf
} from 'lucide-vue-next';

const props = defineProps({
    customer: Object,
    getStars: Function // Passing this from MapView to keep logic consistent
});

defineEmits(['back']);
</script>

<template>
    <div class="animate-in slide-in-from-right duration-300">
        <div class="relative group">
            <button
                @click="$emit('back')"
                class="absolute top-4 left-4 bg-white rounded-full p-2 shadow-lg z-10 hover:bg-gray-100 transition"
            >
                <ChevronLeft class="w-5 h-5 text-gray-700" />
            </button>

            <div v-if="customer.photos && customer.photos.length > 0" class="flex overflow-x-auto snap-x snap-mandatory hide-scrollbar bg-gray-200">
                <div v-for="photo in customer.photos" :key="photo.id" class="flex-none w-full snap-center">
                    <img
                        :src="photo.url"
                        class="w-full h-48 md:h-56 object-cover"
                        alt="Customer photo"
                    />
                </div>
            </div>
            <div v-else>
                <img
                    src="https://placehold.co/600x400?text=No+Image+Available"
                    class="w-full h-48 md:h-56 object-cover"
                />
            </div>
        </div>

        <div class="p-5">
            <h2 class="text-2xl font-bold text-gray-900 leading-tight">{{ customer.name }}</h2>

            <div class="flex items-center mt-2 text-sm">
                <span class="text-gray-700 font-semibold mr-1">{{ customer.rating }}</span>
                <div class="flex text-yellow-500 mr-2">
                    <template v-for="(type, index) in getStars(customer.rating)" :key="index">
                        <Star v-if="type === 'full'" class="w-4 h-4 fill-current" />
                        <StarHalf v-else-if="type === 'half'" class="w-4 h-4 fill-current" />
                        <Star v-else class="w-4 h-4 text-gray-300" />
                    </template>
                </div>
                <span class="text-blue-600 hover:underline cursor-pointer">({{ customer.review_count }} reviews)</span>
            </div>

            <p class="text-sm text-gray-500 mt-1">{{ customer.category }}</p>

            <div class="flex justify-between gap-2 my-6 overflow-x-auto pb-2">
                <button class="action-btn"><Navigation class="w-4 h-4 mr-1"/> Directions</button>
                <button class="action-btn"><Bookmark class="w-4 h-4 mr-1"/> Save</button>
                <button class="action-btn"><Share2 class="w-4 h-4 mr-1"/> Share</button>
            </div>

            <div class="space-y-5 text-gray-700 border-t pt-5">
                <div class="flex items-start gap-4">
                    <MapPin class="w-5 h-5 text-blue-500 shrink-0" />
                    <p class="text-sm leading-relaxed">{{ customer.address }}</p>
                </div>
                <div v-if="customer.phone" class="flex items-center gap-4">
                    <Phone class="w-5 h-5 text-blue-500 shrink-0" />
                    <p class="text-sm">{{ customer.phone }}</p>
                </div>
                <div v-if="customer.website" class="flex items-center gap-4">
                    <Globe class="w-5 h-5 text-blue-500 shrink-0" />
                    <p class="text-sm text-blue-600 truncate">{{ customer.website }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.action-btn {
    @apply flex items-center justify-center border border-gray-200 rounded-full px-4 py-2 text-blue-600 text-xs font-semibold hover:bg-blue-50 transition min-w-[100px];
}
/* .hide-scrollbar::-webkit-scrollbar {
    display: none;
}
.hide-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
} */
</style>

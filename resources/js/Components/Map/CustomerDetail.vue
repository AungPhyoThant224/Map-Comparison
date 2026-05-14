<script setup>
import {
    ChevronLeft, Navigation, Bookmark, Share2,
    MapPin, Phone, Globe, Star, StarHalf, ChevronRight
} from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
    customer: Object,
    getStars: Function
});

defineEmits(['back']);

const scrollContainer = ref(null);
const activeIndex = ref(0);

// Update active dot on scroll
const handleScroll = (event) => {
    const width = event.target.offsetWidth;
    activeIndex.value = Math.round(event.target.scrollLeft / width);
};

// Manual navigation for Desktop
const scroll = (direction) => {
    const width = scrollContainer.value.offsetWidth;
    scrollContainer.value.scrollBy({
        left: direction === 'next' ? width : -width,
        behavior: 'smooth'
    });
};
</script>

<template>
    <div class="animate-in slide-in-from-right duration-300">
        <div class="relative group">
            <button
                @click="$emit('back')"
                class="absolute top-4 left-4 bg-white/90 backdrop-blur rounded-full p-2 shadow-lg z-20 hover:bg-white transition"
            >
                <ChevronLeft class="w-5 h-5 text-gray-700" />
            </button>

            <template v-if="customer.photos?.length > 1">
                <button
                    @click="scroll('prev')"
                    v-show="activeIndex > 0"
                    class="absolute left-2 top-1/2 -translate-y-1/2 z-20 bg-black/30 hover:bg-black/50 text-white p-1 rounded-full backdrop-blur-sm hidden md:block"
                >
                    <ChevronLeft class="w-6 h-6" />
                </button>
                <button
                    @click="scroll('next')"
                    v-show="activeIndex < customer.photos.length - 1"
                    class="absolute right-2 top-1/2 -translate-y-1/2 z-20 bg-black/30 hover:bg-black/50 text-white p-1 rounded-full backdrop-blur-sm hidden md:block"
                >
                    <ChevronRight class="w-6 h-6" />
                </button>
            </template>

            <div
                ref="scrollContainer"
                @scroll="handleScroll"
                class="flex overflow-x-auto snap-x snap-mandatory hide-scrollbar bg-gray-200 max-h-72"
            >
                <div v-if="customer.photos?.length > 0" v-for="photo in customer.photos" :key="photo.id" class="flex-none w-full snap-center">
                    <img :src="photo.url" class="w-full h-full object-cover" alt="Customer photo" />
                </div>
                <div v-else class="flex-none w-full bg-gray-100 flex items-center justify-center">
                    <img src="https://placehold.co/600x400?text=No+Image+Available" class="w-full h-full object-cover opacity-50" />
                </div>
            </div>

            <div v-if="customer.photos?.length > 1" class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-1.5 z-20">
                <div
                    v-for="(_, index) in customer.photos"
                    :key="index"
                    :class="[
                        'w-2 h-2 rounded-full transition-all duration-300 shadow-sm',
                        activeIndex === index ? 'bg-white w-4' : 'bg-white/50'
                    ]"
                ></div>
            </div>

            <div v-if="customer.photos?.length > 1" class="absolute bottom-4 right-4 bg-black/60 text-white text-[10px] px-2 py-1 rounded-md backdrop-blur-sm z-20">
                {{ activeIndex + 1 }} / {{ customer.photos.length }}
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
                <span class="text-gray-500">({{ customer.review_count }} reviews)</span>
            </div>

            <p class="text-sm text-gray-500 mt-1 tracking-wide font-medium">{{ customer.category }}</p>

            <div class="flex justify-between gap-2 my-6 overflow-x-auto pb-2 custom-scrollbar">
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
                    <p class="text-sm font-medium">{{ customer.phone }}</p>
                </div>
                <div v-if="customer.website" class="flex items-center gap-4">
                    <Globe class="w-5 h-5 text-blue-500 shrink-0" />
                    <p class="text-sm text-blue-600 truncate hover:underline cursor-pointer">{{ customer.website }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.action-btn {
    @apply flex items-center justify-center border border-gray-200 rounded-full px-4 py-2 text-blue-600 text-xs font-semibold hover:bg-blue-50 transition min-w-[100px] whitespace-nowrap;
}
.hide-scrollbar::-webkit-scrollbar { display: none; }
.hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

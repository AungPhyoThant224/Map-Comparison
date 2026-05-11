<script setup>
import { ChevronLeft, Navigation, Bookmark, Share2, MapPin, Phone, Globe } from 'lucide-vue-next';

const props = defineProps({
    customer: Object
});

defineEmits(['back']);
</script>

<template>
    <div class="animate-in slide-in-from-right duration-300">
        <div class="relative">
            <button
                @click="$emit('back')"
                class="absolute top-4 left-4 bg-white rounded-full p-2 shadow-lg z-10 hover:bg-gray-100 transition"
            >
                <ChevronLeft class="w-5 h-5 text-gray-700" />
            </button>
            <img
                :src="customer.photos?.[0]?.url || 'https://placehold.net/default.svg?text=No+Image'"
                class="w-full h-48 md:h-56 object-cover"
            />
        </div>

        <div class="p-5">
            <h2 class="text-2xl font-bold text-gray-900 leading-tight">{{ customer.name }}</h2>
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
</style>

<script setup>
import { Star, StarHalf } from 'lucide-vue-next';

const props = defineProps({
    customer: Object,
    getStars: Function
});
</script>

<template>
    <div class="bg-white overflow-hidden rounded-lg font-sans shadow-none">
        <img
            :src="customer.photos?.[0]?.url || 'https://placehold.co/400x200?text=No+Image'"
            class="w-full h-32 object-cover"
        />
        <div class="p-3">
            <span class="text-lg font-bold text-gray-900 leading-tight">{{ customer.name }}</span>

            <div class="flex items-center mt-1 text-sm">
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

            <div class="text-sm text-gray-500 mt-1">{{ customer.category }}</div>
        </div>
    </div>
</template>

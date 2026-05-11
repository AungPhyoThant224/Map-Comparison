<script setup>
import {Star, StarHalf} from 'lucide-vue-next';

defineProps({
    customers: Array,
    getStars: Function
});

defineEmits(['select'])
</script>

<template>
    <div class="divide-y divide-gray-100">
        <div
            v-for="customer in customers"
            :key="customer.id + 1"
            @click="$emit('select', customer)"
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
</template>

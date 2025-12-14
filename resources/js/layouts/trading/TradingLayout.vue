<script setup lang="ts">
import { onMounted, ref, computed, watchEffect } from 'vue';
import { tradingApi } from '@/composables/useTradingApi';
import { useTrading } from '@/composables/useTrading';
import UserMenu from '@/components/trading/UserMenu.vue';
import TradingNav from '@/components/trading/TradingNav.vue';
import { useUserChannelSingleton } from '@/composables/useUserChannelSingleton';

defineProps<{
  title?: string
  currentPage: string
}>()

const { profile, isProfileLoading, isLoading, handleOrderMatched } = useTrading();

useUserChannelSingleton(handleOrderMatched);
onMounted(async () => {
  if (!profile.value) {
    await loadData();
  }
});
async function loadData() {
  isProfileLoading.value = true;
  try {
    profile.value = await tradingApi.fetchProfile();
  } catch (error) {
    console.error('Failed to load trading data:', error);
  } finally {
    isProfileLoading.value = false;
  }
}
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
        <div class="container mx-auto px-4 py-8">
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-white mb-2">Cryptrader</h1>
                    <p class="text-slate-400">{{ title }}</p>
                </div>
                <UserMenu :profile="profile" />
            </div>

        <TradingNav :current-page="currentPage" />

        <slot :profile="profile" :isLoading="isLoading" />
    </div>
  </div>
</template>
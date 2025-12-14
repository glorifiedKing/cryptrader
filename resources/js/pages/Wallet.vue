<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useTrading } from '@/composables/useTrading';
import WalletOverview from '@/components/trading/WalletOverview.vue';
import TradingLayout from '@/layouts/trading/TradingLayout.vue';

defineProps<{
    supportedSymbols: string[];
}>();

const { profile, isLoading, handleOrderMatched } = useTrading();

onMounted(async () => {
  
});


</script>

<template>
  <TradingLayout title="Manage your cryptocurrency wallet" :current-page="'wallet'">
    <template #default="{ profile, isLoading }">
      <div v-if="isLoading || !profile" class="flex items-center justify-center h-64">
        <div class="text-white text-xl">Loading...</div>
      </div>

      <div v-else class="max-w-2xl">
        <WalletOverview :profile="profile" />
      </div>
    </template>
  </TradingLayout>
</template>

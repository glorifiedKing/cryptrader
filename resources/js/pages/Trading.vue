<script setup lang="ts">
import { onMounted, ref, computed, watchEffect } from 'vue';
import { tradingApi } from '@/composables/useTradingApi';
import { useTrading } from '@/composables/useTrading';
import { useUserChannelListener } from '@/composables/useUserChannelListener';
import LimitOrderForm from '@/components/trading/LimitOrderForm.vue';
import OrderBook from '@/components/trading/OrderBook.vue';
import OrderHistory from '@/components/trading/OrderHistory.vue';
import TradingNav from '@/components/trading/TradingNav.vue';
import UserMenu from '@/components/trading/UserMenu.vue';
import TradingLayout from '@/layouts/trading/TradingLayout.vue';
import { useEcho } from '@laravel/echo-vue';

defineProps<{
    supportedSymbols: string[];
}>();

const { orders, userOrders, isOrdersLoading, handleOrderMatched } = useTrading();
const selectedSymbol = ref<string>('BTC');


onMounted(async () => {
  await loadData();
  
});



async function loadData() {
  isOrdersLoading.value = true;
  try {
    
    orders.value = await tradingApi.fetchOrders(selectedSymbol.value);
    const allOrders = await tradingApi.fetchOrders();
    userOrders.value = allOrders;
  } catch (error) {
    console.error('Failed to load trading data:', error);
  } finally {
    isOrdersLoading.value = false;
  }
}



async function handleOrderCreated() {
  await loadData();
}

async function handleSymbolChange(symbol: string) {
  selectedSymbol.value = symbol;
  orders.value = await tradingApi.fetchOrders(symbol);
}
</script>

<template>
  <TradingLayout title="Trade cryptocurrencies in real-time" :current-page="'trading'">    
    <template #default="{ isLoading }">
      <div v-if="isLoading" class="flex items-center justify-center h-64">
        <div class="text-white text-xl">Loading...</div>
      </div>

      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Order Form -->
        <div>
          <LimitOrderForm
            :selected-symbol="selectedSymbol"
            :supported-symbols="supportedSymbols"
            @order-created="handleOrderCreated"
            @symbol-change="handleSymbolChange"
          />
        </div>

        <!-- Middle Column: OrderBook -->
        <div>
          <OrderBook
            :orders="orders"
            :selected-symbol="selectedSymbol"
            :supported-symbols="supportedSymbols"
          />
        </div>

        <!-- Right Column: Order History -->
        <div>
          <OrderHistory
            :orders="userOrders"
            :supported-symbols="supportedSymbols"
            @order-cancelled="handleOrderCreated"
          />
        </div>
      </div>
    </template>
  </TradingLayout>
</template>

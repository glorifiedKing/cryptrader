<script setup lang="ts">
import { ref, computed } from 'vue';
import type { Order } from '@/types/trading';
import { tradingApi } from '@/composables/useTradingApi';

interface Props {
  orders: Order[];
  supportedSymbols: string[];
}

const props = defineProps<Props>();
const emit = defineEmits<{
  orderCancelled: [];
}>();

const filterSymbol = ref<string>('all');
const filterSide = ref<string>('all');
const filterStatus = ref<string>('all');

const filteredOrders = computed(() => {
  return props.orders.filter((order) => {
    if (filterSymbol.value !== 'all' && order.symbol !== filterSymbol.value) return false;
    if (filterSide.value !== 'all' && order.side !== filterSide.value) return false;
    if (filterStatus.value !== 'all' && order.status.toString() !== filterStatus.value) return false;
    return true;
  });
});

const statusText = (status: number) => {
  switch (status) {
    case 1:
      return 'Open';
    case 2:
      return 'Filled';
    case 3:
      return 'Cancelled';
    default:
      return 'Unknown';
  }
};

const statusColor = (status: number) => {
  switch (status) {
    case 1:
      return 'text-blue-400';
    case 2:
      return 'text-green-400';
    case 3:
      return 'text-slate-400';
    default:
      return 'text-slate-400';
  }
};

async function cancelOrder(orderId: number) {
  try {
    await tradingApi.cancelOrder(orderId);
    emit('orderCancelled');
  } catch (error) {
    console.error('Failed to cancel order:', error);
  }
}
</script>

<template>
  <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-6 border border-slate-700">
    <h2 class="text-xl font-semibold text-white mb-4">My Orders</h2>

    <!-- Filters -->
    <div class="grid grid-cols-3 gap-2 mb-4">
      <select
        v-model="filterSymbol"
        class="bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
        <option value="all">All Symbols</option>
        <option v-for="symbol in supportedSymbols" :key="symbol" :value="symbol">{{ symbol }}</option>
      </select>

      <select
        v-model="filterSide"
        class="bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
        <option value="all">All Sides</option>
        <option value="buy">Buy</option>
        <option value="sell">Sell</option>
      </select>

      <select
        v-model="filterStatus"
        class="bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
        <option value="all">All Status</option>
        <option value="1">Open</option>
        <option value="2">Filled</option>
        <option value="3">Cancelled</option>
      </select>
    </div>

    <!-- Orders List -->
    <div class="space-y-2 max-h-96 overflow-y-auto">
      <div
        v-for="order in filteredOrders"
        :key="order.id"
        class="bg-slate-700/50 rounded-lg p-3 hover:bg-slate-700 transition-colors"
      >
        <div class="flex justify-between items-start mb-2">
          <div>
            <div class="flex items-center gap-2">
              <span class="font-semibold text-white">{{ order.symbol }}</span>
              <span
                :class="[
                  'text-xs px-2 py-0.5 rounded',
                  order.side === 'buy' ? 'bg-green-600/20 text-green-400' : 'bg-red-600/20 text-red-400',
                ]"
              >
                {{ order.side.toUpperCase() }}
              </span>
              <span :class="['text-xs px-2 py-0.5 rounded bg-slate-600', statusColor(order.status)]">
                {{ statusText(order.status) }}
              </span>
            </div>
          </div>
          <button
            v-if="order.status === 1"
            @click="cancelOrder(order.id)"
            class="text-xs text-red-400 hover:text-red-300 transition-colors"
          >
            Cancel
          </button>
        </div>

        <div class="grid grid-cols-2 gap-2 text-sm">
          <div>
            <span class="text-slate-400">Price:</span>
            <span class="text-white ml-1">${{ parseFloat(order.price).toLocaleString() }}</span>
          </div>
          <div>
            <span class="text-slate-400">Amount:</span>
            <span class="text-white ml-1">{{ order.amount }}</span>
          </div>
        </div>

        <div class="text-xs text-slate-500 mt-2">
          {{ new Date(order.created_at).toLocaleString() }}
        </div>
      </div>

      <div v-if="filteredOrders.length === 0" class="text-center text-slate-500 py-8">
        No orders found
      </div>
    </div>
  </div>
</template>

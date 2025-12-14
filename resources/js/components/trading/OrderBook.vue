<script setup lang="ts">
import type { Order } from '@/types/trading';
import { computed } from 'vue';

interface Props {
  orders: Order[];
  selectedSymbol: string;
}

const props = defineProps<Props>();

const buyOrders = computed(() =>
  props.orders
    .filter((o) => o.side === 'buy')
    .sort((a, b) => parseFloat(b.price) - parseFloat(a.price))
    .slice(0, 10)
);

const sellOrders = computed(() =>
  props.orders
    .filter((o) => o.side === 'sell')
    .sort((a, b) => parseFloat(a.price) - parseFloat(b.price))
    .slice(0, 10)
);
</script>

<template>
  <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-6 border border-slate-700">
    <h2 class="text-xl font-semibold text-white mb-4">
      Order Book - {{ selectedSymbol }}
    </h2>

    <div class="space-y-6">
      <!-- Sell Orders -->
      <div>
        <h3 class="text-sm font-medium text-red-400 mb-3">Sell Orders</h3>
        <div class="space-y-1">
          <div class="grid grid-cols-3 gap-2 text-xs text-slate-400 mb-2">
            <div>Price (USD)</div>
            <div class="text-right">Amount</div>
            <div class="text-right">Total</div>
          </div>
          <div
            v-for="order in sellOrders"
            :key="order.id"
            class="grid grid-cols-3 gap-2 text-sm py-1 hover:bg-slate-700/30 rounded transition-colors"
          >
            <div class="text-red-400 font-medium">
              {{ parseFloat(order.price).toLocaleString() }}
            </div>
            <div class="text-right text-slate-300">{{ order.amount }}</div>
            <div class="text-right text-slate-400">
              {{ (parseFloat(order.price) * parseFloat(order.amount)).toFixed(2) }}
            </div>
          </div>
          <div v-if="sellOrders.length === 0" class="text-center text-slate-500 py-4">
            No sell orders
          </div>
        </div>
      </div>

      <!-- Divider -->
      <div class="border-t border-slate-700"></div>

      <!-- Buy Orders -->
      <div>
        <h3 class="text-sm font-medium text-green-400 mb-3">Buy Orders</h3>
        <div class="space-y-1">
          <div class="grid grid-cols-3 gap-2 text-xs text-slate-400 mb-2">
            <div>Price (USD)</div>
            <div class="text-right">Amount</div>
            <div class="text-right">Total</div>
          </div>
          <div
            v-for="order in buyOrders"
            :key="order.id"
            class="grid grid-cols-3 gap-2 text-sm py-1 hover:bg-slate-700/30 rounded transition-colors"
          >
            <div class="text-green-400 font-medium">
              {{ parseFloat(order.price).toLocaleString() }}
            </div>
            <div class="text-right text-slate-300">{{ order.amount }}</div>
            <div class="text-right text-slate-400">
              {{ (parseFloat(order.price) * parseFloat(order.amount)).toFixed(2) }}
            </div>
          </div>
          <div v-if="buyOrders.length === 0" class="text-center text-slate-500 py-4">
            No buy orders
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

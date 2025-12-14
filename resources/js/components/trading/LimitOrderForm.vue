<script setup lang="ts">
import { ref, computed } from 'vue';
import { tradingApi } from '@/composables/useTradingApi';
import type { CreateOrderPayload } from '@/types/trading';

interface Props {
  selectedSymbol: string;
  supportedSymbols: string[];
}

const props = defineProps<Props>();
const emit = defineEmits<{
  orderCreated: [];
  symbolChange: [symbol: string];
}>();

const symbol = ref(props.selectedSymbol);
const side = ref<'buy' | 'sell'>('buy');
const price = ref<number | null>(null);
const amount = ref<number | null>(null);
const isSubmitting = ref(false);
const error = ref<string | null>(null);

const volume = computed(() => {
  if (price.value && amount.value) {
    return (price.value * amount.value).toFixed(2);
  }
  return '0.00';
});

const isBuy = computed(() => side.value === 'buy');

async function submitOrder() {
  if (!price.value || !amount.value) {
    error.value = 'Please fill in all fields';
    return;
  }

  isSubmitting.value = true;
  error.value = null;

  try {
    const payload: CreateOrderPayload = {
      symbol: symbol.value,
      side: side.value,
      price: price.value,
      amount: amount.value,
    };

    await tradingApi.createOrder(payload);

    // Reset form
    price.value = null;
    amount.value = null;

    emit('orderCreated');
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to create order';
  } finally {
    isSubmitting.value = false;
  }
}

function handleSymbolChange() {
  emit('symbolChange', symbol.value);
}
</script>

<template>
  <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-6 border border-slate-700">
    <h2 class="text-xl font-semibold text-white mb-4">Place Order</h2>

    <div class="space-y-4">
      <!-- Symbol Selection -->
      <div>
        <label class="block text-sm font-medium text-slate-300 mb-2">Symbol</label>
        <select
          v-model="symbol"
          @change="handleSymbolChange"
          class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option v-for="symbol in supportedSymbols" :key="symbol" :value="symbol">{{ symbol }}</option>
        </select>
      </div>

      <!-- Side Selection -->
      <div>
        <label class="block text-sm font-medium text-slate-300 mb-2">Side</label>
        <div class="flex gap-2">
          <button
            @click="side = 'buy'"
            :class="[
              'flex-1 py-2 px-4 rounded-lg font-medium transition-all',
              isBuy
                ? 'bg-green-600 text-white'
                : 'bg-slate-700 text-slate-400 hover:bg-slate-600',
            ]"
          >
            Buy
          </button>
          <button
            @click="side = 'sell'"
            :class="[
              'flex-1 py-2 px-4 rounded-lg font-medium transition-all',
              !isBuy
                ? 'bg-red-600 text-white'
                : 'bg-slate-700 text-slate-400 hover:bg-slate-600',
            ]"
          >
            Sell
          </button>
        </div>
      </div>

      <!-- Price Input -->
      <div>
        <label class="block text-sm font-medium text-slate-300 mb-2">Price (USD)</label>
        <input
          v-model.number="price"
          type="number"
          step="0.01"
          min="0"
          placeholder="0.00"
          class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>

      <!-- Amount Input -->
      <div>
        <label class="block text-sm font-medium text-slate-300 mb-2">Amount</label>
        <input
          v-model.number="amount"
          type="number"
          step="0.00000001"
          min="0"
          placeholder="0.00000000"
          class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>

      <!-- Volume Preview -->
      <div class="bg-slate-700/50 rounded-lg p-3">
        <div class="flex justify-between items-center">
          <span class="text-sm text-slate-400">Total Volume</span>
          <span class="text-lg font-semibold text-white">${{ volume }}</span>
        </div>
      </div>

      <!-- Error Message -->
      <div v-if="error" class="bg-red-500/10 border border-red-500 rounded-lg p-3">
        <p class="text-sm text-red-400">{{ error }}</p>
      </div>

      <!-- Submit Button -->
      <button
        @click="submitOrder"
        :disabled="isSubmitting"
        :class="[
          'w-full py-3 px-4 rounded-lg font-semibold transition-all',
          isBuy
            ? 'bg-green-600 hover:bg-green-700 text-white'
            : 'bg-red-600 hover:bg-red-700 text-white',
          isSubmitting && 'opacity-50 cursor-not-allowed',
        ]"
      >
        {{ isSubmitting ? 'Placing Order...' : `Place ${side.toUpperCase()} Order` }}
      </button>
    </div>
  </div>
</template>

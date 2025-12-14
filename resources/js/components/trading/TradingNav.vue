<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
  currentPage: 'wallet' | 'trading';
}

const props = defineProps<Props>();

const isWallet = computed(() => props.currentPage === 'wallet');
const isTrading = computed(() => props.currentPage === 'trading');
</script>

<template>
  <nav class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-2 border border-slate-700 mb-6">
    <div class="flex gap-2">
      <Link
        href="/wallet"
        :class="[
          'flex-1 py-3 px-6 rounded-lg font-semibold transition-all text-center',
          isWallet
            ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/50'
            : 'bg-slate-700/50 text-slate-300 hover:bg-slate-700 hover:text-white',
        ]"
      >
        <div class="flex items-center justify-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
            <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd" />
          </svg>
          <span>Wallet</span>
        </div>
      </Link>

      <Link
        href="/trading"
        :class="[
          'flex-1 py-3 px-6 rounded-lg font-semibold transition-all text-center',
          isTrading
            ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/50'
            : 'bg-slate-700/50 text-slate-300 hover:bg-slate-700 hover:text-white',
        ]"
      >
        <div class="flex items-center justify-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
          </svg>
          <span>Trading</span>
        </div>
      </Link>
    </div>
  </nav>
</template>

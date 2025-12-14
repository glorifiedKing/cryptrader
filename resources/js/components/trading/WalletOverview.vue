<script setup lang="ts">
import type { Profile } from '@/types/trading';

interface Props {
  profile: Profile | null;
}

defineProps<Props>();
</script>

<template>
  <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-6 border border-slate-700">
    <h2 class="text-xl font-semibold text-white mb-4">Wallet</h2>

    <div class="space-y-4">
      <!-- USD Balance -->
      <div class="bg-gradient-to-r from-blue-600/20 to-purple-600/20 rounded-lg p-4 border border-blue-500/30">
        <div class="text-sm text-slate-400 mb-1">USD Balance</div>
        <div class="text-3xl font-bold text-white">
          ${{ profile?.balance ? parseFloat(profile.balance).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '0.00' }}
        </div>
      </div>

      <!-- Assets -->
      <div>
        <h3 class="text-sm font-medium text-slate-300 mb-3">Assets</h3>
        <div class="space-y-2">
          <div
            v-for="asset in profile?.assets"
            :key="asset.symbol"
            class="bg-slate-700/50 rounded-lg p-3"
          >
            <div class="flex justify-between items-center mb-1">
              <span class="font-semibold text-white">{{ asset.symbol }}</span>
              <span class="text-sm text-slate-400">
                {{ parseFloat(asset.amount).toFixed(8) }}
              </span>
            </div>
            <div v-if="parseFloat(asset.locked_amount) > 0" class="text-xs text-amber-400">
              Locked: {{ parseFloat(asset.locked_amount).toFixed(8) }}
            </div>
          </div>
          <div v-if="!profile?.assets || profile.assets.length === 0" class="text-center text-slate-500 py-4">
            No assets
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

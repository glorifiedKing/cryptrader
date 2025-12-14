<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { logout } from '@/routes';
import type { Profile } from '@/types/trading';

interface Props {
  profile: Profile | null;
}

const props = defineProps<Props>();
const isOpen = ref(false);
const menuRef = ref<HTMLElement | null>(null);

function toggleDropdown() {
  isOpen.value = !isOpen.value;
}

function closeDropdown() {
  isOpen.value = false;
}

function handleLogout() {
  router.flushAll();
}

// Get user initials for avatar
function getInitials(name: string): string {
  return name
    .split(' ')
    .map(n => n[0])
    .join('')
    .toUpperCase()
    .slice(0, 2);
}

// Handle click outside
function handleClickOutside(event: MouseEvent) {
  if (menuRef.value && !menuRef.value.contains(event.target as Node)) {
    closeDropdown();
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
  <div v-if="profile" ref="menuRef" class="relative">
    <button
      @click="toggleDropdown"
      class="flex items-center gap-3 bg-slate-800/50 backdrop-blur-sm rounded-lg px-4 py-2 border border-slate-700 hover:border-slate-600 transition-all"
    >
      <!-- Avatar -->
      <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm">
        {{ getInitials(profile.name) }}
      </div>
      
      <!-- User Name -->
      <span class="text-white font-medium hidden sm:block">{{ profile.name }}</span>
      
      <!-- Dropdown Arrow -->
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-4 w-4 text-slate-400 transition-transform"
        :class="{ 'rotate-180': isOpen }"
        viewBox="0 0 20 20"
        fill="currentColor"
      >
        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
      </svg>
    </button>

    <!-- Dropdown Menu -->
    <Transition
      enter-active-class="transition ease-out duration-100"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <div
        v-if="isOpen"
        class="absolute right-0 mt-2 w-56 bg-slate-800 border border-slate-700 rounded-lg shadow-xl overflow-hidden z-50"
      >
        <!-- User Info -->
        <div class="px-4 py-3 border-b border-slate-700">
          <p class="text-sm font-medium text-white">{{ profile.name }}</p>
          <p class="text-xs text-slate-400 truncate">{{ profile.email }}</p>
        </div>

        <!-- Menu Items -->
        <div class="py-1">
          <Link
            href="/settings/profile"
            @click="closeDropdown"
            class="flex items-center gap-3 px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 hover:text-white transition-colors"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
            </svg>
            <span>Profile Settings</span>
          </Link>

         
          <Link
            class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-400 hover:bg-slate-700 hover:text-red-300 transition-colors"
            :href="logout()"
            @click="handleLogout"
            as="button"
            data-test="logout-button"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" />
            </svg>
            Log out
        </Link>
        </div>
      </div>
    </Transition>
  </div>
</template>

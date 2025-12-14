<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { wallet, login, register } from '@/routes';

defineProps<{
    canRegister: boolean;
    initialBalance: number;
    supportedSymbols: string[];
}>();
</script>

<template>
    <Head title="Welcome to Cryptrader" />
    
    <div class="min-h-screen bg-slate-900 text-white flex flex-col">
        <!-- Header -->
        <header class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="text-2xl font-bold text-blue-500">Cryptrader</div>
            <nav v-if="!$page.props.auth.user" class="space-x-4">
                <Link :href="login()" class="text-slate-300 hover:text-white">Log in</Link>
                <Link v-if="canRegister" :href="register()" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg transition">Register</Link>
            </nav>
            <nav v-else>
                 <Link :href="wallet()" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg transition">Go to Wallet</Link>
            </nav>
        </header>

        <!-- Hero Section -->
        <main class="flex-grow flex items-center justify-center text-center px-4">
            <div class="max-w-3xl">
                <h1 class="text-5xl md:text-6xl font-extrabold mb-6 bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-purple-600">
                    Trade Crypto with Confidence
                </h1>
                <p class="text-xl text-slate-400 mb-8">
                    Experience real-time trading with zero hassle. Join thousands of traders on the most intuitive platform.
                </p>
                
                <!-- Benefits -->
                <div class="grid md:grid-cols-3 gap-6 mb-12 text-left">
                    <div class="bg-slate-800 p-6 rounded-xl border border-slate-700">
                        <div class="text-blue-400 text-2xl mb-2">🚀</div>
                        <h3 class="font-bold text-lg mb-2">Fast Execution</h3>
                        <p class="text-slate-400 text-sm">Real-time order matching engine for instant trades.</p>
                    </div>
                    <div class="bg-slate-800 p-6 rounded-xl border border-slate-700">
                        <div class="text-green-400 text-2xl mb-2">💰</div>
                        <h3 class="font-bold text-lg mb-2">Free Start</h3>
                        <p class="text-slate-400 text-sm">Get <span class="text-white font-bold">${{ initialBalance }}</span> free balance when you register today!</p>
                    </div>
                    <div class="bg-slate-800 p-6 rounded-xl border border-slate-700">
                        <div class="text-purple-400 text-2xl mb-2">🔒</div>
                        <h3 class="font-bold text-lg mb-2">Secure</h3>
                        <p class="text-slate-400 text-sm">Your assets are protected with industry-leading security.</p>
                    </div>
                </div>

                <!-- Supported Assets -->
                <div class="mb-12">
                    <p class="text-slate-500 mb-4 uppercase tracking-wider text-sm font-semibold">Supported Assets</p>
                    <div class="flex justify-center gap-4 flex-wrap">
                        <span v-for="symbol in supportedSymbols" :key="symbol" class="px-4 py-2 bg-slate-800 rounded-full text-slate-300 font-mono border border-slate-700">
                            {{ symbol }}
                        </span>
                    </div>
                </div>

                <!-- CTA -->
                <div v-if="!$page.props.auth.user">
                    <Link :href="register()" class="inline-block bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-lg font-bold px-8 py-4 rounded-full shadow-lg transform hover:scale-105 transition duration-200">
                        Start Trading Now
                    </Link>
                    <p class="mt-4 text-slate-500 text-sm">No credit card required</p>
                </div>
                 <div v-else>
                    <Link :href="wallet()" class="inline-block bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-lg font-bold px-8 py-4 rounded-full shadow-lg transform hover:scale-105 transition duration-200">
                        Go to Dashboard
                    </Link>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="container mx-auto px-6 py-8 text-center text-slate-600 text-sm">
            &copy; {{ new Date().getFullYear() }} Cryptrader. All rights reserved.
        </footer>
    </div>
</template>

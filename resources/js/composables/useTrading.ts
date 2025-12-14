import { ref, computed } from 'vue';
import type { Asset, Order, Profile, Trade, PaginatedResponse } from '@/types/trading';

const profile = ref<Profile | null>(null);
const orders = ref<Order[]>([]);
const userOrders = ref<Order[]>([]);
const ordersPagination = ref<PaginatedResponse<Order> | null>(null);
const userOrdersPagination = ref<PaginatedResponse<Order> | null>(null);
const isProfileLoading = ref(false);
const isOrdersLoading = ref(false);

export function useTrading() {
    const isLoading = computed(() => isProfileLoading.value || isOrdersLoading.value);
    const balance = computed(() => profile.value?.balance || '0');
    const assets = computed(() => profile.value?.assets || []);

    const buyOrders = computed(() =>
        orders.value.filter((o) => o.side === 'buy').sort((a, b) => parseFloat(b.price) - parseFloat(a.price))
    );

    const sellOrders = computed(() =>
        orders.value.filter((o) => o.side === 'sell').sort((a, b) => parseFloat(a.price) - parseFloat(b.price))
    );

    function handleOrderMatched(event: { trade: Trade }) {
        console.log('Received data from private channel:', event);
        const trade = event.trade;
        const userId = profile.value?.id;

        if (!userId) return;

        // Update order statuses
        const updateOrderStatus = (orderId: number) => {
            const orderIndex = userOrders.value.findIndex((o) => o.id === orderId);
            if (orderIndex !== -1) {
                userOrders.value[orderIndex].status = 2; // Filled
            }
        };

        if (trade.buyer_id === userId) {
            updateOrderStatus(trade.order_id);
            // Update balance and assets
            if (profile.value) {
                // Balance already deducted when order was created
                // Add asset
                const assetIndex = profile.value.assets.findIndex((a) => a.symbol === trade.symbol);
                if (assetIndex !== -1) {
                    const currentAmount = parseFloat(profile.value.assets[assetIndex].amount);
                    const tradeAmount = parseFloat(trade.amount);
                    profile.value.assets[assetIndex].amount = (currentAmount + tradeAmount).toFixed(8);
                } else {
                    profile.value.assets.push({
                        symbol: trade.symbol,
                        amount: trade.amount,
                        locked_amount: '0',
                    });
                }
            }
        }

        if (trade.seller_id === userId) {
            updateOrderStatus(trade.counter_order_id);
            // Update balance
            if (profile.value) {
                const currentBalance = parseFloat(profile.value.balance);
                const totalValue = parseFloat(trade.total_value);
                const commission = parseFloat(trade.commission);
                const received = totalValue - commission;
                profile.value.balance = (currentBalance + received).toFixed(8);

                // Update locked amount
                const assetIndex = profile.value.assets.findIndex((a) => a.symbol === trade.symbol);
                if (assetIndex !== -1) {
                    const currentLocked = parseFloat(profile.value.assets[assetIndex].locked_amount);
                    const tradeAmount = parseFloat(trade.amount);
                    profile.value.assets[assetIndex].locked_amount = (currentLocked - tradeAmount).toFixed(8);
                }
            }
        }

        // Remove matched orders from orderbook
        orders.value = orders.value.filter(
            (o) => o.id !== trade.order_id && o.id !== trade.counter_order_id
        );
    }

    return {
        profile,
        orders,
        userOrders,
        isLoading,
        isProfileLoading,
        isOrdersLoading,
        balance,
        assets,
        buyOrders,
        sellOrders,
        handleOrderMatched,
        ordersPagination,
        userOrdersPagination,
    };
}

import axios from 'axios';
import type {
    CreateOrderPayload,
    Order,
    Profile,
} from '@/types/trading';

axios.defaults.withCredentials = true;

export const tradingApi = {
    async fetchProfile(): Promise<Profile> {
        const response = await axios.get('/api/profile');
        return response.data;
    },

    async fetchOrders(symbol?: string): Promise<Order[]> {
        const response = await axios.get('/api/orders', {
            params: symbol ? { symbol } : {},
        });
        return response.data;
    },

    async createOrder(payload: CreateOrderPayload): Promise<Order> {
        const response = await axios.post('/api/orders', payload);
        return response.data;
    },

    async cancelOrder(orderId: number): Promise<void> {
        await axios.post(`/api/orders/${orderId}/cancel`);
    },

    async matchOrder(orderId: number): Promise<void> {
        await axios.post(`/api/orders/${orderId}/match`);
    },
};

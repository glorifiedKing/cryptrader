import axios from 'axios';
import type {
    CreateOrderPayload,
    Order,
    PaginatedResponse,
    Profile,
} from '@/types/trading';

axios.defaults.withCredentials = true;

export const tradingApi = {
    async fetchProfile(): Promise<Profile> {
        const response = await axios.get('/api/profile');
        return response.data;
    },

    async fetchOrders(symbol?: string, page = 1, perPage = 5, side?: string, status?: string): Promise<PaginatedResponse<Order>> {
        const params: any = { page, per_page: perPage };
        if (symbol && symbol !== 'all') params.symbol = symbol;
        if (side && side !== 'all') params.side = side;
        if (status && status !== 'all') params.status = status;
        const response = await axios.get('/api/orders', { params });
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

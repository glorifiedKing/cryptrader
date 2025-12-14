export interface Asset {
  symbol: string;
  amount: string;
  locked_amount: string;
}

export interface Profile {
  id: number;
  name: string;
  email: string;
  balance: string;
  assets: Asset[];
}

export interface Order {
  id: number;
  user_id: number;
  user_name?: string;
  symbol: string;
  side: 'buy' | 'sell';
  price: string;
  amount: string;
  status: number;
  created_at: string;
}

export interface Trade {
  id: number;
  symbol: string;
  price: string;
  amount: string;
  total_value: string;
  commission: string;
  buyer_id: number;
  seller_id: number;
  order_id: number;
  counter_order_id: number;
}

export interface OrderBook {
  buys: Order[];
  sells: Order[];
}

export interface CreateOrderPayload {
  symbol: string;
  side: 'buy' | 'sell';
  price: number;
  amount: number;
}

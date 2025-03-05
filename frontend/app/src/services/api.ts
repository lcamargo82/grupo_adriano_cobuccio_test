import axios from 'axios';
import { useAuthStore } from '@/stores/authStore';

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
});

export const registerUser = async (data: { name: string; email: string; password: string; password_confirmation: string }) => {
  try {
    const response = await api.post('/register', data);
    return response.data;
  } catch (error) {
    console.error('Erro ao registrar o usuário:', error);
    throw error;
  }
};

export const loginUser = async (email: string, password: string) => {
  try {
    const response = await api.post('/login', { email, password });
    return response.data;
  } catch (error) {
    console.error('Erro ao fazer login:', error);
    throw error;
  }
};

export const fetchAccounts = async (token: string) => {
  try {
    const response = await api.get('/accounts', {
      headers: {
        'Accept': 'application/json',
        'Authorization': token ? `Bearer ${token}` : '',
      },
    });
    return response.data;
  } catch (error) {
    console.error('Erro ao buscar contas:', error);
    throw error;
  }
};

export const createAccount = async (name: string, token: string) => {
  try {
    const response = await api.post(
      '/accounts',
      { name},
      {
        headers: {
          'Accept': 'application/json',
          'Authorization': token ? `Bearer ${token}` : '',
        },
      });
    return response.data;
  } catch (error) {
    console.error('Erro ao criar conta:', error);
    throw error;
  }
};

export const fetchAccountStatement = async (accountId: string, token: string) => {
  try {
    const response = await api.get(`/transactions/${accountId}`,{
      headers: {
        'Accept': 'application/json',
        'Authorization': token ? `Bearer ${token}` : '',
      },
    });
    return response.data;
  } catch (error) {
    throw new Error('Erro ao carregar extrato');
  }
};

export const fetchAccountBalance = async (accountId: string, token: string) => {
  try {
    const response = await api.get(`accounts/${accountId}`, {
      headers: {
        Authorization: token ? `Bearer ${token}` : '',
      },
    });

    return response.data;
  } catch (error) {
    console.error("Erro ao buscar saldo da conta:", error);
    throw error;
  }
};

export const performTransaction = async (data: any, token: string, type: string) => {
  return api.post(`/transactions/${type}`, data, {
    headers: {
      Authorization: token ? `Bearer ${token}` : '',
    },
  })
}

export const revertTransaction = async (transactionData: { accountId: string, transaction_id: string; reason: string, type: string }, token: string) => {
  try {
    const response = await api.post(
      `/reversals/${transactionData.type}/${transactionData.accountId}`,
      {
        account_id: transactionData.accountId,
        transaction_id: transactionData.transaction_id,
        reason: transactionData.reason
      },
      {
        headers: {
          'Accept': 'application/json',
          'Authorization': token ? `Bearer ${token}` : '',
        },
      }
    );

    return response.data;
  } catch (error) {
    console.error('Erro ao reverter transação:', error);
    throw error;
  }
};

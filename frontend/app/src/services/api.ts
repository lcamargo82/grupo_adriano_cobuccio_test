import axios from 'axios';

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

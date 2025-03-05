import { defineStore } from 'pinia';
import { ref } from 'vue';
import { fetchAccounts } from '../services/api'; // Supondo que fetchAccounts esteja em api.ts
import { useAuthStore } from "@/stores/authStore.ts";

interface Account {
  id: number;
  name: string;
  balance: number;
}

export const useAccountStore = defineStore('account', () => {
  const accounts = ref<Account[]>([]);
  const loading = ref<boolean>(false);
  const authStore = useAuthStore();
  const token = authStore.token || '';

  const loadAccounts = async () => {
    loading.value = true;
    try {
      accounts.value = await fetchAccounts(token);
    } catch (error) {
      console.error('Erro ao carregar contas:', error);
    } finally {
      loading.value = false;
    }
  };

  const addAccount = (account: Account) => {
    accounts.value.push(account);
  };

  return { accounts, loading, loadAccounts, addAccount };
});

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useAccountStore } from '@/stores/accountStore';
import { createAccount } from '@/services/api';
import { useToast } from 'vue-toastification';
import { useAuthStore } from '@/stores/authStore';
import router from "@/router";

const accountStore = useAccountStore();
const authStore = useAuthStore();
const toast = useToast();
const showModal = ref(false);
const accountName = ref('');
const errorMessage = ref('');
const isSubmitting = ref(false);

onMounted(async () => {
  await accountStore.loadAccounts();
});

const submitNewAccount = async () => {
  if (!accountName.value) {
    errorMessage.value = 'O nome da conta é obrigatório.';
    return;
  }

  try {
    const token = authStore.token || '';
    const response = await createAccount(accountName.value, token);

    accountStore.addAccount(response);

    await accountStore.loadAccounts();

    toast.success('Conta criada com sucesso');
    showModal.value = false;
    accountName.value = '';
  } catch (error) {
    toast.error('Erro ao criar a conta');
  } finally {
    isSubmitting.value = false;
  }
};

const viewAccountStatement = (accountId: number) => {
  router.push({ name: 'AccountStatement', params: { accountId } });
};
</script>

<template>
  <div class="card p-4">
    <h2 class="mb-4">Contas Ativas</h2>
    <button class="btn btn-primary mb-3" @click="showModal = true">Criar Nova Conta</button>

    <div v-if="accountStore.accounts.length === 0 && accountStore.loading" class="text-center">
      Carregando...
    </div>

    <template v-else>
      <template v-if="accountStore.accounts.length">
        <table class="table table-striped">
          <thead>
          <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Saldo</th>
            <th>Ações</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="account in accountStore.accounts" :key="account.id">
            <td>{{ account.id }}</td>
            <td>{{ account.name }}</td>
            <td>{{ account.balance }}</td>
            <td>
              <button type="button" class="btn btn-info" @click="viewAccountStatement(account.id)">
                <i class="bi bi-eye"></i>
              </button>
            </td>
          </tr>
          </tbody>
        </table>
      </template>
      <p v-else class="text-center text-muted">Nenhuma conta ativa encontrada.</p>
    </template>

    <div class="modal fade" :class="{ show: showModal, 'd-block': showModal }" tabindex="-1" v-if="showModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Criar Nova Conta</h5>
            <button type="button" class="btn-close" @click="showModal = false"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="submitNewAccount">
              <div class="mb-3">
                <label for="accountName" class="form-label">Nome da Conta</label>
                <input
                  type="text"
                  id="accountName"
                  class="form-control"
                  v-model="accountName"
                  placeholder="Digite o nome da conta"
                  required
                />
                <div v-if="errorMessage" class="text-danger mt-2">{{ errorMessage }}</div>
              </div>
              <button type="submit" class="btn btn-primary">Criar Conta</button>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="showModal = false">Fechar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

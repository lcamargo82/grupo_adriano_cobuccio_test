<script setup lang="ts">
import { defineProps, ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useToast } from 'vue-toastification';
import BaseLayout from '@/components/Dashboard/BaseLayout.vue'
import BaseButton from '@/components/BaseButton.vue'
import { useAuthStore } from '@/stores/authStore.ts'
import {fetchAccountBalance, fetchAccountStatement, revertTransaction} from '@/services/api.ts'
import router from "@/router";

const route = useRoute()
const accountId = ref<string>('')
const statement = ref<any[]>([])
const balance = ref<number | null>(null)
const openingDate = ref<string>('')
const loading = ref(false)
const authStore = useAuthStore()
const toast = useToast();

const showModal = ref(false)
const selectedTransaction = ref<any>(null)
const reason = ref('')

const depositar = () => {
  router.push({ name: 'TransactionForm', params: { accountId: accountId.value, type: 'deposit' } })
}

const transferir = () => {
  router.push({ name: 'TransactionForm', params: { accountId: accountId.value, type: 'transfer' } })
}

const reverter = (transaction: any) => {
  selectedTransaction.value = transaction
  showModal.value = true
}

const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('pt-BR').format(date)
}

const loadStatement = async () => {
  loading.value = true
  const token = authStore.token || ''

  try {
    const accountIdValue = Array.isArray(accountId.value) ? accountId.value[0] : accountId.value

    statement.value = await fetchAccountStatement(accountIdValue, token)
  } catch (error) {
    console.error('Erro ao carregar extrato:', error)
  } finally {
    loading.value = false
  }
}

const loadAccountBalance = async () => {
  const token = authStore.token || ''

  try {
    const accountIdValue = Array.isArray(accountId.value) ? accountId.value[0] : accountId.value
    const data = await fetchAccountBalance(accountIdValue, token)
    const dateNow = new Date().toLocaleDateString('pt-BR');
    const hourNow = new Date().toLocaleTimeString('pt-BR');

    balance.value = data.balance;
    openingDate.value = dateNow + ' às ' + hourNow
  } catch (error) {
    console.error('Erro ao carregar saldo:', error)
  }
}

const confirmarReversao = async () => {
  if (selectedTransaction.value && reason.value) {
    const transactionData = {
      transaction_id: selectedTransaction.value.id,
      reason: reason.value,
      accountId: accountId.value,
      type: selectedTransaction.value.account_name.includes('Depósito') ? 'deposit' : 'transfer'
    };

    try {
      const token = authStore.token || '';
      const result = await revertTransaction(transactionData, token);

      await loadAccountBalance();

      showModal.value = false;

      toast.success('Reversão confirmada com sucesso!');
    } catch (error) {
      console.error('Falha ao reverter transação:', error);

      toast.error('Falha ao confirmar reversão. Tente novamente.');
    }
  } else {
    console.error('Falta informações para confirmar a reversão');

    toast.error('Por favor, preencha o motivo da reversão.');
  }
};

onMounted(() => {
  const id = route.params.accountId
  accountId.value = Array.isArray(id) ? id[0] : id || ''
  loadStatement()
  loadAccountBalance()
})
</script>

<template>
  <BaseLayout>
    <div class="mb-4 text-center">
      <h1 class="fw-bold">Saldo Total da Conta #{{ accountId }}</h1>
      <p v-if="openingDate">
        Extrato aberto em: <strong>{{ openingDate }}</strong>
      </p>
      <h2 v-if="balance !== null" class="text-success">R$ {{ balance }}</h2>
    </div>
    <div class="actions mb-2">
      <div class="buttons-container d-flex gap-3">
        <BaseButton text="Depositar" color="success" icon="bi bi-wallet" :onClick="depositar" />

        <BaseButton
          text="Transferir"
          color="primary"
          icon="bi bi-arrow-left-right"
          :onClick="transferir"
        />
      </div>
    </div>

    <div class="card p-4">
      <h2>Extrato da Conta #{{ accountId }}</h2>

      <div v-if="loading" class="text-center">Carregando extrato...</div>

      <div v-else>
        <template v-if="statement.length > 0">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>ID</th>
                <th>Data</th>
                <th>Descrição</th>
                <th>Valor</th>
                <th>Ação</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="entry in statement" :key="entry.id">
                <td>{{ entry.id }}</td>
                <td>{{ formatDate(entry.created_at) }}</td>
                <td>{{ entry.account_name }}</td>
                <td>{{ entry.amount }}</td>
                <td>
                  <BaseButton
                    text="Reverter"
                    color="danger"
                    icon="bi bi-arrow-counterclockwise"
                    :onClick="() => reverter(entry)"
                    :disabled="!entry.status"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </template>

        <template v-else>
          <p class="text-center text-muted">Nenhum extrato encontrado.</p>
        </template>
      </div>
    </div>

    <div v-if="showModal" class="modal show" tabindex="-1" style="display: block;">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Confirmar Reversão</h5>
            <button type="button" class="btn-close" @click="showModal = false"></button>
          </div>
          <div class="modal-body">
            <p><strong>ID da Transação:</strong> {{ selectedTransaction?.id }}</p>
            <p><strong>Descrição:</strong> {{ selectedTransaction?.account_name }}</p>
            <p><strong>Valor:</strong> R$ {{ selectedTransaction?.amount }}</p>
            <div class="form-group">
              <label for="reason">Motivo da Reversão</label>
              <input type="text" id="reason" class="form-control" v-model="reason" placeholder="Digite o motivo" />
            </div>
          </div>
          <div class="modal-footer">
            <BaseButton text="Cancelar" color="secondary" :onClick="() => showModal = false" />
            <BaseButton text="Confirmar Reversão" color="danger" :onClick="confirmarReversao" />
          </div>
        </div>
      </div>
    </div>
  </BaseLayout>
</template>

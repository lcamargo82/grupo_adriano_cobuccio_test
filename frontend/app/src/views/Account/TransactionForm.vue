<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import BaseLayout from '@/components/Dashboard/BaseLayout.vue'
import BaseButton from '@/components/BaseButton.vue'
import { useAuthStore } from '@/stores/authStore.ts'
import { performTransaction } from '@/services/api.ts'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const accountId = ref<string>('')
const type = ref<string>('')

const recipientId = ref<string>('')
const amount = ref<number | null>(null)
const loading = ref(false)
const successMessage = ref<string | null>(null)
const errorMessage = ref<string | null>(null)

const formTitle = computed(() => (type.value === 'transfer' ? 'Transferência' : 'Depósito'))

const submitTransaction = async () => {
  loading.value = true
  successMessage.value = null
  errorMessage.value = null
  const token = authStore.token || ''

  try {
    let payload

    if (type.value === 'transfer') {
      if (!recipientId.value) {
        errorMessage.value = 'O ID da conta de destino é obrigatório.'
        loading.value = false
        return
      }
      payload = {
        sender_id: parseInt(accountId.value),
        recipient_id: parseInt(recipientId.value),
        amount: amount.value,
        type: 'transfer',
      }
    } else {
      payload = {
        receiver_id: parseInt(accountId.value),
        amount: amount.value,
        type: 'deposit',
      }
    }

    await performTransaction(payload, token, type.value)
    successMessage.value = 'Transação realizada com sucesso!'

    router.push({ name: 'AccountStatement', params: { accountId: accountId.value } })
  } catch (error) {
    console.error('Erro ao realizar transação:', error)
    errorMessage.value = 'Erro ao realizar transação. Verifique os dados e tente novamente.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  accountId.value = Array.isArray(route.params.accountId) ? route.params.accountId[0] : route.params.accountId || ''
  type.value = Array.isArray(route.params.type) ? route.params.type[0] : route.params.type || ''
})
</script>

<template>
  <BaseLayout>
    <div class="card p-4">
      <h1 class="text-center fw-bold">{{ formTitle }}</h1>

      <div v-if="successMessage" class="alert alert-success text-center">
        {{ successMessage }}
      </div>
      <div v-if="errorMessage" class="alert alert-danger text-center">
        {{ errorMessage }}
      </div>

      <form @submit.prevent="submitTransaction">
        <div class="mb-3">
          <label class="form-label">ID da Conta {{ type === 'transfer' ? 'Remetente' : 'Destino' }}</label>
          <input type="text" class="form-control" :value="accountId" disabled />
        </div>

        <div v-if="type === 'transfer'" class="mb-3">
          <label class="form-label">ID da Conta Destino</label>
          <input v-model="recipientId" type="number" class="form-control" placeholder="Informe o ID da conta destino" required />
        </div>

        <div class="mb-3">
          <label class="form-label">Valor</label>
          <input v-model="amount" type="number" class="form-control" placeholder="Informe o valor" required />
        </div>

        <BaseButton :text="'Confirmar ' + formTitle" color="success" icon="bi bi-check" :disabled="loading"  @click="submitTransaction"/>
      </form>

      <div class="text-center mt-3">
        <BaseButton text="Cancelar" color="secondary" icon="bi bi-arrow-left" :onClick="() => router.back()" />
      </div>
    </div>
  </BaseLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { encryptPassword } from '@/services/cryptoService';
import { useToast } from 'vue-toastification';
import { loginUser } from '@/services/api';
import { useAuthStore } from '@/stores/authStore';
import BaseLayout from "@/components/BaseLayout.vue";

const router = useRouter();
const toast = useToast();
const authStore = useAuthStore();

const form = ref({
  email: '',
  password: ''
});

const submitLogin = async () => {
  if (!form.value.email || !form.value.password) {
    toast.error('Campos de e-mail e senha são obrigatórios');
    return;
  }

  const encryptedPassword = encryptPassword(form.value.password);

  try {
    const response = await loginUser(form.value.email, encryptedPassword);

    if (response && response.token && response.username) {
      const token = response.token.token;
      const username = response.username;

      authStore.setToken(token);
      authStore.setUsername(username);

      toast.success('Login bem-sucedido');
      router.push('/dashboard');
    } else {
      toast.error('Erro: Token ou nome do usuário não encontrado na resposta da API');
    }
  } catch (error) {
    console.error(error);
    toast.error('Erro ao fazer login');
  }
};
</script>

<template>
  <BaseLayout>
    <div class="d-flex justify-content-center align-items-center" style="min-height: 100vh;">
      <div class="card p-4 w-100" style="max-width: 400px;">
        <h2 class="text-center">Login</h2>
        <form @submit.prevent="submitLogin">
          <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input id="email" v-model="form.email" type="email" class="form-control" required />
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Senha</label>
            <input id="password" v-model="form.password" type="password" class="form-control" required />
          </div>

          <button type="submit" class="btn btn-primary w-100">Entrar</button>
        </form>
        <div class="mt-3 text-center">
          <p>Não tem uma conta? <router-link to="/register">Cadastre-se</router-link></p>
        </div>
      </div>
    </div>
  </BaseLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { encryptPassword } from '@/services/cryptoService'; // Importando o serviço de criptografia
import { useToast } from 'vue-toastification'; // Para mostrar alertas
import { registerUser } from '@/services/api';
import BaseLayout from "@/components/BaseLayout.vue"; // Serviço de API para registro

const router = useRouter();
const toast = useToast();

const form = ref({
  name: '',
  email: '',
  password: '',
  confirmPassword: ''
});

const submitRegister = async () => {
  if (form.value.password !== form.value.confirmPassword) {
    toast.error('As senhas não conferem!');
    return;
  }

  if (!form.value.name || !form.value.email || !form.value.password || !form.value.confirmPassword) {
    toast.error('Por favor, preencha todos os campos.');
    return;
  }

  const encryptedPassword = encryptPassword(form.value.password);

  try {
    await registerUser({
      name: form.value.name,
      email: form.value.email,
      password: encryptedPassword,
      password_confirmation: encryptedPassword
    });
    toast.success('Cadastro bem-sucedido!');
    router.push('/');  // Redireciona para a tela de login após o cadastro
  } catch (error) {
    toast.error('Erro ao cadastrar');
  }
};
</script>

<template>
  <BaseLayout>
    <div class="d-flex justify-content-center align-items-center" style="min-height: 100vh;">
      <div class="card p-4 w-100" style="max-width: 400px;">
        <h2 class="text-center">Cadastro</h2>
        <form @submit.prevent="submitRegister">
          <div class="mb-3">
            <label for="name" class="form-label">Nome</label>
            <input id="name" v-model="form.name" type="text" class="form-control" required />
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input id="email" v-model="form.email" type="email" class="form-control" required />
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Senha</label>
            <input id="password" v-model="form.password" type="password" class="form-control" required />
          </div>

          <div class="mb-3">
            <label for="confirmPassword" class="form-label">Confirmar Senha</label>
            <input id="confirmPassword" v-model="form.confirmPassword" type="password" class="form-control" required />
          </div>

          <button type="submit" class="btn btn-primary w-100">Cadastrar</button>
        </form>
        <div class="mt-3 text-center">
          <p>Já tem uma conta? <router-link to="/">Faça login</router-link></p>
        </div>
      </div>
    </div>
  </BaseLayout>
</template>

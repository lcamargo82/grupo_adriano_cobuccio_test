<script setup lang="ts">
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';

const authStore = useAuthStore();
const router = useRouter();

const logout = () => {
  authStore.logout();
  router.push('/');
};
</script>

<template>
  <div>
    <header class="navbar navbar-expand-lg navbar-light bg-light">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">Minhas aplicações</a>
        <div class="dropdown">
          <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
            Bem-vindo, {{ authStore.username || 'Usuário' }}
          </button>
          <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <li><a class="dropdown-item" href="#">Perfil</a></li>
            <li><a class="dropdown-item" href="#" @click="logout">Logout</a></li>
          </ul>
        </div>
      </div>
    </header>
    <main>
      <slot></slot>
    </main>
  </div>
</template>

<style scoped>
.navbar {
  margin-bottom: 20px;
}
</style>

<script setup lang="ts">
import BaseLayout from "@/components/Dashboard/BaseLayout.vue";
import AccountList from "@/views/Account/AccountList.vue";
</script>

<template>
  <BaseLayout>
    <div class="container mt-5">
      <h1>Bem-vindo ao Dashboard!</h1>
      <p>Você está autenticado e pronto para começar.</p>
    </div>

    <AccountList />
  </BaseLayout>
</template>

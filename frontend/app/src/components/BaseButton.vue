<script setup lang="ts">
import { defineProps } from 'vue';

defineProps<{
  color?: string;
  text: string;
  icon?: string;
  onClick: () => void;
}>();
</script>

<template>
  <button
    :class="['btn', color ? `btn-${color}` : 'btn-primary']"
    @click="onClick"
  >
    <i v-if="icon" :class="icon"></i>
    <span>{{ text }}</span>
  </button>
</template>

<style scoped>
.btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 20px;
  border-radius: 8px;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.2s ease-in-out;
  width: 180px; /* Tamanho fixo */
  height: 50px; /* Altura uniforme */
  border: none;
}

.btn-primary {
  background-color: #007bff;
  color: white;
}

.btn-success {
  background-color: #28a745;
  color: white;
}

.btn-danger {
  background-color: #dc3545;
  color: white;
}

.btn:hover {
  opacity: 0.8;
}
</style>

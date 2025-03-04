<template>
  <div class="d-flex flex-column vh-100">
    <header class="bg-primary text-white text-center py-3">
      <h2>Grupo Adriano Cobuccio</h2>
    </header>

    <main class="flex-grow-1 container mt-4">
      <slot></slot>
    </main>

    <footer class="bg-dark text-white text-center py-2 mt-4">
      <small>© 2025 Grupo Adriano Cobuccio</small>
    </footer>
  </div>
</template>

import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';
import type { RouteRecordRaw } from 'vue-router';

import Login from '../views/Auth/Login.vue';
import Register from '../views/Auth/Register.vue';
import Dashboard from '@/views/Dashboard.vue';
import AccountStatement from "@/views/Account/AccountStatement.vue";
import TransactionForm from "@/views/Account/TransactionForm.vue";

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    name: 'Login',
    component: Login,
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true },
  },
  {
    path: '/account-statement/:accountId',
    name: 'AccountStatement',
    component: AccountStatement,
    props: true,
    meta: { requiresAuth: true },
  },
  {
    path: '/transaction/:accountId/:type',
    name: 'TransactionForm',
    component: TransactionForm,
    meta: { requiresAuth: true },
  }

];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/');
  } else {
    next();
  }
});

export default router;

import { defineStore } from 'pinia';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('auth_token') || null,
    username: null as string | null,
  }),
  actions: {
    setToken(token: string) {
      this.token = token;
      localStorage.setItem('auth_token', token);
    },
    setUsername(username: string) {
      this.username = username;
    },
    logout() {
      this.token = null;
      this.username = null;
      localStorage.removeItem('auth_token');
    }
  },
  getters: {
    isAuthenticated(): boolean {
      return !!this.token;
    }
  }
});

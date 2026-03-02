import { defineStore } from 'pinia';
import axios from 'axios';
const LOCAL_STORAGE_KEY = 'user_data';
const TOKEN_STORAGE_KEY = 'auth_token';

export const useAuthUserStore = defineStore('authUser', {
  state: () => ({
    authUser: null,
    allUser:'',
    refferUser:'',
    token: null
  }),
  actions: {

    isAuthenticated() {
     return !!this.token
    },
    setAuthUser(newUser) {
      this.authUser = newUser;
      this.saveUserToLocalStorage(newUser);
    },
    setToken(token) {
      this.token = token;
      if (token) {
        localStorage.setItem(TOKEN_STORAGE_KEY, token);
        axios.defaults.headers.common.Authorization = `Bearer ${token}`;
      } else {
        localStorage.removeItem(TOKEN_STORAGE_KEY);
        delete axios.defaults.headers.common.Authorization;
      }
    },
    async login(payload) {
      const response = await axios.post('/api/auth/login', payload);
      this.setToken(response.data.token);
      this.setAuthUser(response.data.authUser);
      return response.data;
    },
    async register(payload) {
      const response = await axios.post('/api/auth/register', payload);
      this.setToken(response.data.token);
      this.setAuthUser(response.data.authUser);
      return response.data;
    },
    async logout() {
      await axios.post('/api/auth/logout');
      this.setToken(null);
      this.clearAuthUser();
    },
    setAllUser(newUser) {
      this.allUser = newUser;
    },
    async getUser(){
      try {
        const response = await axios.post("/api/auth/me");
        this.setAuthUser(response.data.authUser);
        return response.data; // Return the fetched data
      } catch (error) {
        console.log(error);
      }
   
    },
    async getAllUser(){
      try {
        const response = await axios.get("/api/all.user");
        this.setAllUser(response.data.alluser);
       return  response.data.alluser
      } catch (error) {
        console.log(error);
      }
   
    },
    async getRefferUser(){
      try {
        const response = await axios.get("/api/reffer.user");
        this.refferUser=response.data.alluser;
       return  response.data.alluser
      } catch (error) {
        console.log(error);
      }
   
    },
    async reSetAuthUser() {
      await this.getUser(); // Wait for the data to be fetched
      const data = this.authUser;
   
      return data;
    },

    deleteUser(id) {
      
      this.allUser = this.allUser.filter((item) => item.id !== id);


      return this.allUser
      
    },









    clearAuthUser() {
      this.authUser = null;
      this.saveUserToLocalStorage(null);
    },
    loadUserFromLocalStorage() {
      const savedUser = JSON.parse(localStorage.getItem(LOCAL_STORAGE_KEY));
      const savedToken = localStorage.getItem(TOKEN_STORAGE_KEY);
      if (savedUser) {
        this.authUser = savedUser;
      }
      if (savedToken) {
        this.setToken(savedToken);
      }
    },
    saveUserToLocalStorage(user) {
      localStorage.setItem(LOCAL_STORAGE_KEY, JSON.stringify(user));
    },
  },
});

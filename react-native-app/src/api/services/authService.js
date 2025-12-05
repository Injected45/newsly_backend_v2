import apiClient from '../client';
import { ENDPOINTS } from '../endpoints';
import AsyncStorage from '@react-native-async-storage/async-storage';

export const authService = {
  /**
   * Register new user
   */
  register: async (name, email, password) => {
    const response = await apiClient.post(ENDPOINTS.REGISTER, {
      name,
      email,
      password,
      password_confirmation: password,
    });
    
    if (response.success) {
      await AsyncStorage.setItem('auth_token', response.data.token);
      await AsyncStorage.setItem('user', JSON.stringify(response.data.user));
      return response.data;
    }
    
    throw new Error(response.message);
  },

  /**
   * Login user
   */
  login: async (email, password) => {
    const response = await apiClient.post(ENDPOINTS.LOGIN, {
      email,
      password,
    });
    
    if (response.success) {
      await AsyncStorage.setItem('auth_token', response.data.token);
      await AsyncStorage.setItem('user', JSON.stringify(response.data.user));
      return response.data;
    }
    
    throw new Error(response.message);
  },

  /**
   * Logout user
   */
  logout: async () => {
    try {
      await apiClient.post(ENDPOINTS.LOGOUT);
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      await AsyncStorage.removeItem('auth_token');
      await AsyncStorage.removeItem('user');
    }
  },

  /**
   * Get current user
   */
  getCurrentUser: async () => {
    const response = await apiClient.get(ENDPOINTS.ME);
    
    if (response.success) {
      await AsyncStorage.setItem('user', JSON.stringify(response.data));
      return response.data;
    }
    
    throw new Error(response.message);
  },

  /**
   * Check if authenticated
   */
  isAuthenticated: async () => {
    const token = await AsyncStorage.getItem('auth_token');
    return !!token;
  },

  /**
   * Get stored user
   */
  getStoredUser: async () => {
    const userJson = await AsyncStorage.getItem('user');
    return userJson ? JSON.parse(userJson) : null;
  },
};




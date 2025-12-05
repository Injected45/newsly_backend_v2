import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

// IMPORTANT: غير هذا الـ URL لعنوان الـ Server الخاص بك
export const API_BASE_URL = 'http://192.168.1.100:8080/api';

const apiClient = axios.create({
  baseURL: API_BASE_URL,
  timeout: 30000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Request interceptor - Add auth token
apiClient.interceptors.request.use(
  async (config) => {
    try {
      const token = await AsyncStorage.getItem('auth_token');
      if (token) {
        config.headers.Authorization = `Bearer ${token}`;
      }
    } catch (error) {
      console.error('Error reading token:', error);
    }
    return config;
  },
  (error) => Promise.reject(error)
);

// Response interceptor - Handle errors
apiClient.interceptors.response.use(
  (response) => response.data, // Return only data
  async (error) => {
    if (error.response) {
      const { status, data } = error.response;
      
      if (status === 401) {
        // Unauthorized - clear token
        await AsyncStorage.removeItem('auth_token');
        await AsyncStorage.removeItem('user');
      }
      
      return Promise.reject({
        message: data.message || 'حدث خطأ',
        errors: data.errors,
        status,
      });
    } else if (error.request) {
      return Promise.reject({
        message: 'خطأ في الاتصال بالإنترنت',
        status: 0,
      });
    }
    
    return Promise.reject({
      message: error.message,
      status: -1,
    });
  }
);

export default apiClient;




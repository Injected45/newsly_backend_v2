# 📱 React Native Integration Guide - Newsly App

## 📋 Pre-Development Checklist

### ✅ Backend Setup
- [ ] Backend running on accessible URL (not localhost for physical devices)
- [ ] CORS configured for your domain
- [ ] Firebase Project created
- [ ] Firebase credentials configured in backend
- [ ] Queue worker running (`php artisan queue:work`)
- [ ] Scheduler running (`php artisan schedule:work`)

### ✅ React Native Setup
- [ ] React Native project initialized
- [ ] Required packages installed
- [ ] Firebase configured for iOS & Android
- [ ] Development environment ready

---

## 📦 Required Packages

```bash
# Core Dependencies
npm install axios @react-native-async-storage/async-storage

# Firebase & Notifications
npm install @react-native-firebase/app @react-native-firebase/messaging

# Navigation
npm install @react-navigation/native @react-navigation/stack @react-navigation/bottom-tabs
npm install react-native-screens react-native-safe-area-context

# Device Info
npm install react-native-device-info

# Image Handling
npm install react-native-fast-image

# Date Handling
npm install dayjs

# Pull to Refresh
npm install react-native-gesture-handler

# State Management (optional)
npm install @tanstack/react-query
# or
npm install zustand
```

---

## 🗂️ Project Structure

```
src/
├── api/
│   ├── client.js              # Axios instance
│   ├── endpoints.js           # API endpoint constants
│   └── services/
│       ├── auth.js           # Authentication API
│       ├── articles.js       # Articles API
│       ├── bookmarks.js      # Bookmarks API
│       ├── subscriptions.js  # Subscriptions API
│       └── notifications.js  # Notifications API
├── components/
│   ├── ArticleCard.js
│   ├── ArticleList.js
│   ├── BreakingNewsBanner.js
│   └── LoadingSpinner.js
├── screens/
│   ├── Auth/
│   │   ├── LoginScreen.js
│   │   └── RegisterScreen.js
│   ├── Home/
│   │   ├── HomeScreen.js
│   │   └── ArticleDetailScreen.js
│   ├── Bookmarks/
│   │   └── BookmarksScreen.js
│   ├── Profile/
│   │   └── ProfileScreen.js
│   └── Notifications/
│       └── NotificationsScreen.js
├── navigation/
│   ├── AuthNavigator.js
│   └── MainNavigator.js
├── utils/
│   ├── storage.js            # AsyncStorage helpers
│   ├── notifications.js      # FCM setup
│   └── constants.js
└── contexts/
    └── AuthContext.js
```

---

## 🔧 Configuration Files

### **1. API Client Setup**

```javascript
// src/api/client.js
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

// IMPORTANT: Change this to your server URL
export const API_BASE_URL = 'http://YOUR_SERVER_IP:8080/api';

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
      // Server responded with error
      const { status, data } = error.response;
      
      if (status === 401) {
        // Unauthorized - clear token and navigate to login
        await AsyncStorage.removeItem('auth_token');
        // You'll need to handle navigation here
        console.log('Session expired');
      }
      
      // Return structured error
      return Promise.reject({
        message: data.message || 'حدث خطأ',
        errors: data.errors,
        status,
      });
    } else if (error.request) {
      // Network error
      return Promise.reject({
        message: 'خطأ في الاتصال بالإنترنت',
        status: 0,
      });
    } else {
      return Promise.reject({
        message: error.message,
        status: -1,
      });
    }
  }
);

export default apiClient;
```

### **2. API Endpoints Constants**

```javascript
// src/api/endpoints.js
export const ENDPOINTS = {
  // Auth
  REGISTER: '/auth/register',
  LOGIN: '/auth/login',
  LOGOUT: '/auth/logout',
  ME: '/auth/me',
  UPDATE_PASSWORD: '/auth/update-password',
  
  // Articles
  ARTICLES: '/articles',
  ARTICLES_LATEST: '/articles/latest',
  ARTICLES_BREAKING: '/articles/breaking',
  ARTICLE_DETAIL: (id) => `/articles/${id}`,
  MARK_READ: '/articles/mark-read',
  READ_HISTORY: '/articles/history',
  
  // Bookmarks
  BOOKMARKS: '/bookmarks',
  ADD_BOOKMARK: '/bookmarks',
  REMOVE_BOOKMARK: (id) => `/bookmarks/${id}`,
  
  // Subscriptions
  SUBSCRIPTIONS: '/subscriptions',
  SYNC_SUBSCRIPTIONS: '/subscriptions/sync',
  
  // User
  UPDATE_PROFILE: '/user',
  REGISTER_DEVICE: '/user/device',
  UNREGISTER_DEVICE: '/user/device',
  NOTIFICATION_SETTINGS: '/user/notifications/settings',
  
  // Notifications
  NOTIFICATIONS: '/notifications',
  UNREAD_COUNT: '/notifications/unread-count',
  MARK_NOTIFICATION_READ: (id) => `/notifications/${id}/read`,
  MARK_ALL_READ: '/notifications/mark-all-read',
  
  // Reference Data
  COUNTRIES: '/countries',
  CATEGORIES: '/categories',
  SOURCES: '/sources',
};
```

---

## 🔐 Authentication Service

```javascript
// src/api/services/auth.js
import apiClient from '../client';
import { ENDPOINTS } from '../endpoints';
import AsyncStorage from '@react-native-async-storage/async-storage';

export const authService = {
  /**
   * Register new user
   */
  register: async (name, email, password, countryId) => {
    const response = await apiClient.post(ENDPOINTS.REGISTER, {
      name,
      email,
      password,
      password_confirmation: password,
      country_id: countryId,
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
      console.error('Logout API error:', error);
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
   * Update password
   */
  updatePassword: async (currentPassword, newPassword) => {
    const response = await apiClient.post(ENDPOINTS.UPDATE_PASSWORD, {
      current_password: currentPassword,
      password: newPassword,
      password_confirmation: newPassword,
    });
    
    return response;
  },

  /**
   * Check if user is authenticated
   */
  isAuthenticated: async () => {
    const token = await AsyncStorage.getItem('auth_token');
    return !!token;
  },

  /**
   * Get stored token
   */
  getToken: async () => {
    return await AsyncStorage.getItem('auth_token');
  },
};
```

---

## 📰 Articles Service

```javascript
// src/api/services/articles.js
import apiClient from '../client';
import { ENDPOINTS } from '../endpoints';

export const articlesService = {
  /**
   * Get articles with filters
   */
  getArticles: async (params = {}, page = 1) => {
    const response = await apiClient.get(ENDPOINTS.ARTICLES, {
      params: {
        ...params,
        page,
        per_page: 20,
      },
    });
    
    if (response.success) {
      return {
        articles: response.data.items,
        meta: response.data.meta,
      };
    }
    
    throw new Error(response.message);
  },

  /**
   * Get latest articles
   */
  getLatest: async (countryId, limit = 20) => {
    const response = await apiClient.get(ENDPOINTS.ARTICLES_LATEST, {
      params: {
        country_id: countryId,
        limit,
      },
    });
    
    if (response.success) {
      return response.data;
    }
    
    throw new Error(response.message);
  },

  /**
   * Get breaking news
   */
  getBreaking: async (countryId, limit = 10) => {
    const response = await apiClient.get(ENDPOINTS.ARTICLES_BREAKING, {
      params: {
        country_id: countryId,
        limit,
      },
    });
    
    if (response.success) {
      return response.data;
    }
    
    throw new Error(response.message);
  },

  /**
   * Get article details
   */
  getArticle: async (articleId) => {
    const response = await apiClient.get(ENDPOINTS.ARTICLE_DETAIL(articleId));
    
    if (response.success) {
      return response.data;
    }
    
    throw new Error(response.message);
  },

  /**
   * Mark article as read
   */
  markAsRead: async (articleId) => {
    const response = await apiClient.post(ENDPOINTS.MARK_READ, {
      article_id: articleId,
    });
    
    return response.success;
  },

  /**
   * Get read history
   */
  getReadHistory: async (page = 1) => {
    const response = await apiClient.get(ENDPOINTS.READ_HISTORY, {
      params: { page, per_page: 20 },
    });
    
    if (response.success) {
      return {
        articles: response.data.items,
        meta: response.data.meta,
      };
    }
    
    throw new Error(response.message);
  },

  /**
   * Search articles
   */
  search: async (query, filters = {}, page = 1) => {
    return articlesService.getArticles({
      search: query,
      ...filters,
    }, page);
  },
};
```

---

## 🔔 Push Notifications Setup

```javascript
// src/utils/notifications.js
import messaging from '@react-native-firebase/messaging';
import { Platform, Alert } from 'react-native';
import apiClient from '../api/client';
import { ENDPOINTS } from '../api/endpoints';
import DeviceInfo from 'react-native-device-info';

export const notificationService = {
  /**
   * Request notification permission
   */
  requestPermission: async () => {
    try {
      const authStatus = await messaging().requestPermission();
      const enabled =
        authStatus === messaging.AuthorizationStatus.AUTHORIZED ||
        authStatus === messaging.AuthorizationStatus.PROVISIONAL;

      if (enabled) {
        console.log('✅ Notification permission granted');
        return true;
      } else {
        console.log('❌ Notification permission denied');
        return false;
      }
    } catch (error) {
      console.error('Permission request error:', error);
      return false;
    }
  },

  /**
   * Get FCM Token
   */
  getToken: async () => {
    try {
      const token = await messaging().getToken();
      console.log('FCM Token:', token);
      return token;
    } catch (error) {
      console.error('Error getting FCM token:', error);
      return null;
    }
  },

  /**
   * Register device with backend
   */
  registerDevice: async () => {
    try {
      const fcmToken = await notificationService.getToken();
      if (!fcmToken) {
        throw new Error('Failed to get FCM token');
      }

      const deviceInfo = {
        fcm_token: fcmToken,
        platform: Platform.OS,
        device_id: await DeviceInfo.getUniqueId(),
        device_name: await DeviceInfo.getDeviceName(),
        device_model: DeviceInfo.getModel(),
        os_version: DeviceInfo.getSystemVersion(),
        app_version: DeviceInfo.getVersion(),
      };

      const response = await apiClient.post(ENDPOINTS.REGISTER_DEVICE, deviceInfo);
      
      if (response.success) {
        console.log('✅ Device registered successfully');
        return true;
      }
      
      return false;
    } catch (error) {
      console.error('Device registration error:', error);
      return false;
    }
  },

  /**
   * Unregister device
   */
  unregisterDevice: async () => {
    try {
      const fcmToken = await notificationService.getToken();
      if (!fcmToken) return;

      await apiClient.delete(ENDPOINTS.UNREGISTER_DEVICE, {
        data: { fcm_token: fcmToken },
      });
      
      console.log('✅ Device unregistered');
    } catch (error) {
      console.error('Device unregistration error:', error);
    }
  },

  /**
   * Setup notification listeners
   */
  setupListeners: (navigation) => {
    // Foreground message handler
    const unsubscribeForeground = messaging().onMessage(async (remoteMessage) => {
      console.log('📩 Foreground Notification:', remoteMessage);
      
      // Show local notification or alert
      Alert.alert(
        remoteMessage.notification?.title || 'إشعار جديد',
        remoteMessage.notification?.body || '',
        [
          {
            text: 'عرض',
            onPress: () => {
              if (remoteMessage.data?.article_id) {
                navigation.navigate('ArticleDetail', {
                  articleId: remoteMessage.data.article_id,
                });
              }
            },
          },
          { text: 'إغلاق', style: 'cancel' },
        ]
      );
    });

    // Background/Quit notification handler
    messaging().setBackgroundMessageHandler(async (remoteMessage) => {
      console.log('📩 Background Notification:', remoteMessage);
    });

    // Notification opened app handler
    const unsubscribeNotificationOpen = messaging().onNotificationOpenedApp(
      (remoteMessage) => {
        console.log('📩 Notification opened app:', remoteMessage);
        
        if (remoteMessage.data?.article_id) {
          navigation.navigate('ArticleDetail', {
            articleId: remoteMessage.data.article_id,
          });
        }
      }
    );

    // Check if app was opened from notification (quit state)
    messaging()
      .getInitialNotification()
      .then((remoteMessage) => {
        if (remoteMessage) {
          console.log('📩 App opened from notification:', remoteMessage);
          
          if (remoteMessage.data?.article_id) {
            navigation.navigate('ArticleDetail', {
              articleId: remoteMessage.data.article_id,
            });
          }
        }
      });

    // Token refresh listener
    const unsubscribeTokenRefresh = messaging().onTokenRefresh(async (token) => {
      console.log('🔄 FCM Token refreshed:', token);
      await notificationService.registerDevice();
    });

    // Return cleanup function
    return () => {
      unsubscribeForeground();
      unsubscribeNotificationOpen();
      unsubscribeTokenRefresh();
    };
  },
};
```

---

## 🎨 Example Components

### **Article Card Component**

```javascript
// src/components/ArticleCard.js
import React from 'react';
import { View, Text, Image, TouchableOpacity, StyleSheet } from 'react-native';
import FastImage from 'react-native-fast-image';
import dayjs from 'dayjs';
import 'dayjs/locale/ar';

dayjs.locale('ar');

const ArticleCard = ({ article, onPress, isRTL = true }) => {
  return (
    <TouchableOpacity style={styles.card} onPress={() => onPress(article.id)}>
      {/* Image */}
      {article.image_url && (
        <FastImage
          source={{ uri: article.image_url }}
          style={styles.image}
          resizeMode={FastImage.resizeMode.cover}
        />
      )}

      <View style={styles.content}>
        {/* Breaking Badge */}
        {article.is_breaking && (
          <View style={styles.breakingBadge}>
            <Text style={styles.breakingText}>⚡ عاجل</Text>
          </View>
        )}

        {/* Title */}
        <Text style={[styles.title, isRTL && styles.rtl]} numberOfLines={2}>
          {article.title}
        </Text>

        {/* Summary */}
        <Text style={[styles.summary, isRTL && styles.rtl]} numberOfLines={2}>
          {article.summary}
        </Text>

        {/* Footer */}
        <View style={styles.footer}>
          {/* Source */}
          {article.source && (
            <View style={styles.source}>
              {article.source.logo && (
                <FastImage
                  source={{ uri: article.source.logo }}
                  style={styles.sourceLogo}
                />
              )}
              <Text style={styles.sourceName}>
                {article.source.name_ar || article.source.name_en}
              </Text>
            </View>
          )}

          {/* Time */}
          <Text style={styles.time}>
            {dayjs(article.published_at).fromNow()}
          </Text>
        </View>
      </View>
    </TouchableOpacity>
  );
};

const styles = StyleSheet.create({
  card: {
    backgroundColor: '#fff',
    marginHorizontal: 16,
    marginVertical: 8,
    borderRadius: 12,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
    overflow: 'hidden',
  },
  image: {
    width: '100%',
    height: 200,
    backgroundColor: '#f0f0f0',
  },
  content: {
    padding: 12,
  },
  breakingBadge: {
    backgroundColor: '#ff4444',
    alignSelf: 'flex-start',
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 4,
    marginBottom: 8,
  },
  breakingText: {
    color: '#fff',
    fontSize: 12,
    fontWeight: 'bold',
  },
  title: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#333',
    marginBottom: 4,
  },
  summary: {
    fontSize: 14,
    color: '#666',
    marginBottom: 8,
  },
  rtl: {
    textAlign: 'right',
    writingDirection: 'rtl',
  },
  footer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginTop: 8,
    paddingTop: 8,
    borderTopWidth: 1,
    borderTopColor: '#f0f0f0',
  },
  source: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  sourceLogo: {
    width: 20,
    height: 20,
    borderRadius: 10,
    marginRight: 6,
  },
  sourceName: {
    fontSize: 12,
    color: '#666',
  },
  time: {
    fontSize: 12,
    color: '#999',
  },
});

export default ArticleCard;
```

---

## 🚀 Getting Started Checklist

### **Week 1: Setup & Authentication**
- [ ] Initialize React Native project
- [ ] Install all dependencies
- [ ] Setup Firebase (iOS & Android)
- [ ] Create API client and services
- [ ] Implement Login screen
- [ ] Implement Register screen
- [ ] Test authentication flow
- [ ] Setup FCM and test notifications

### **Week 2: Main Features**
- [ ] Implement Home screen with articles list
- [ ] Implement Article detail screen
- [ ] Add pull-to-refresh
- [ ] Add infinite scroll
- [ ] Implement Bookmarks feature
- [ ] Test with real data

### **Week 3: Advanced Features**
- [ ] Implement Subscriptions management
- [ ] Add Notifications screen
- [ ] Implement Profile screen
- [ ] Add Search functionality
- [ ] Implement Filters (Country, Category, Source)

### **Week 4: Polish & Testing**
- [ ] UI/UX improvements
- [ ] Error handling
- [ ] Loading states
- [ ] Offline support (optional)
- [ ] Performance optimization
- [ ] Testing on real devices

---

## 📞 Contact Backend

Remember to update `API_BASE_URL` in `src/api/client.js`:

```javascript
// For development (use your actual IP)
export const API_BASE_URL = 'http://192.168.1.100:8080/api';

// For production
export const API_BASE_URL = 'https://api.newsly.app/api';
```

---

## 🔍 Debugging Tips

1. **Check Network Requests:**
   - Use React Native Debugger
   - Check axios interceptors
   - Verify API responses

2. **FCM Issues:**
   - Check Firebase console
   - Verify credentials
   - Test with FCM console

3. **Authentication:**
   - Check AsyncStorage
   - Verify token in headers
   - Test with Postman first

---

**Good luck with your React Native app! 🚀**


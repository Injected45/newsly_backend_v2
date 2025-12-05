# 🎨 Advanced Features Guide - Newsly React Native App

## 📋 المحتويات

1. [Splash Screen](#splash-screen)
2. [Onboarding](#onboarding)
3. [Multi-Language (i18n)](#multi-language)
4. [Dark/Light Mode](#dark-light-mode)
5. [Modern UI Design](#modern-ui-design)
6. [Updated Implementation Plan](#updated-implementation-plan)

---

## 📦 Additional Required Packages

```bash
# Splash Screen
npm install react-native-splash-screen
npm install react-native-bootsplash

# Onboarding
npm install react-native-onboarding-swiper
# or
npm install react-native-app-intro-slider

# Internationalization (i18n)
npm install i18next react-i18next
npm install @react-native-async-storage/async-storage

# Animations
npm install react-native-reanimated
npm install react-native-animatable
npm install lottie-react-native

# UI Components
npm install react-native-vector-icons
npm install @react-native-community/slider

# Theme & Colors
npm install styled-components
# or use React Context (built-in)

# Linear Gradient
npm install react-native-linear-gradient

# Status Bar
npm install @react-native-community/hooks
```

---

## 🚀 1. Splash Screen

### **Setup (iOS & Android)**

#### **Option 1: react-native-bootsplash (Recommended)**

```bash
npm install react-native-bootsplash

# Generate splash screen
npx react-native generate-bootsplash assets/splash.png \
  --background-color=1E3A5F \
  --logo-width=200 \
  --assets-path=assets \
  --flavor=main
```

#### **App.js Integration:**

```javascript
// App.js
import React, { useEffect } from 'react';
import BootSplash from 'react-native-bootsplash';
import { NavigationContainer } from '@react-navigation/native';

const App = () => {
  useEffect(() => {
    const init = async () => {
      // Perform your app initialization here
      await loadResources();
      
      // Hide splash screen
      await BootSplash.hide({ fade: true });
    };
    
    init();
  }, []);

  return (
    <NavigationContainer>
      {/* Your app content */}
    </NavigationContainer>
  );
};

export default App;
```

#### **Custom Splash Screen Component:**

```javascript
// src/screens/SplashScreen.js
import React, { useEffect } from 'react';
import { View, StyleSheet, Animated, Image } from 'react-native';
import LinearGradient from 'react-native-linear-gradient';

const SplashScreen = ({ onFinish }) => {
  const fadeAnim = new Animated.Value(0);
  const scaleAnim = new Animated.Value(0.3);

  useEffect(() => {
    // Fade in and scale animation
    Animated.parallel([
      Animated.timing(fadeAnim, {
        toValue: 1,
        duration: 1000,
        useNativeDriver: true,
      }),
      Animated.spring(scaleAnim, {
        toValue: 1,
        tension: 10,
        friction: 2,
        useNativeDriver: true,
      }),
    ]).start();

    // Auto hide after 2 seconds
    setTimeout(() => {
      onFinish();
    }, 2500);
  }, []);

  return (
    <LinearGradient
      colors={['#1E3A5F', '#2C5F8D', '#3A7EBD']}
      style={styles.container}
    >
      <Animated.View
        style={{
          opacity: fadeAnim,
          transform: [{ scale: scaleAnim }],
        }}
      >
        <Image
          source={require('../assets/logo-white.png')}
          style={styles.logo}
          resizeMode="contain"
        />
      </Animated.View>
    </LinearGradient>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  logo: {
    width: 200,
    height: 200,
  },
});

export default SplashScreen;
```

---

## 📖 2. Onboarding Screen

### **Professional Onboarding Component:**

```javascript
// src/screens/OnboardingScreen.js
import React, { useRef } from 'react';
import {
  View,
  Text,
  StyleSheet,
  Image,
  TouchableOpacity,
  Dimensions,
  I18nManager,
} from 'react-native';
import AppIntroSlider from 'react-native-app-intro-slider';
import AsyncStorage from '@react-native-async-storage/async-storage';
import LinearGradient from 'react-native-linear-gradient';
import { useTranslation } from 'react-i18next';

const { width, height } = Dimensions.get('window');
const isRTL = I18nManager.isRTL;

const OnboardingScreen = ({ navigation }) => {
  const { t } = useTranslation();
  const sliderRef = useRef(null);

  const slides = [
    {
      key: 'slide1',
      title: t('onboarding.slide1.title'),
      text: t('onboarding.slide1.description'),
      image: require('../assets/onboarding/news.png'),
      backgroundColor: '#1E3A5F',
      gradient: ['#1E3A5F', '#2C5F8D'],
    },
    {
      key: 'slide2',
      title: t('onboarding.slide2.title'),
      text: t('onboarding.slide2.description'),
      image: require('../assets/onboarding/personalize.png'),
      backgroundColor: '#2C5F8D',
      gradient: ['#2C5F8D', '#3A7EBD'],
    },
    {
      key: 'slide3',
      title: t('onboarding.slide3.title'),
      text: t('onboarding.slide3.description'),
      image: require('../assets/onboarding/notifications.png'),
      backgroundColor: '#3A7EBD',
      gradient: ['#3A7EBD', '#4A9EED'],
    },
  ];

  const renderSlide = ({ item }) => (
    <LinearGradient colors={item.gradient} style={styles.slide}>
      <View style={styles.imageContainer}>
        <Image source={item.image} style={styles.image} resizeMode="contain" />
      </View>
      <View style={styles.contentContainer}>
        <Text style={styles.title}>{item.title}</Text>
        <Text style={styles.text}>{item.text}</Text>
      </View>
    </LinearGradient>
  );

  const renderNextButton = () => (
    <View style={styles.buttonContainer}>
      <Text style={styles.buttonText}>{t('onboarding.next')}</Text>
    </View>
  );

  const renderDoneButton = () => (
    <View style={[styles.buttonContainer, styles.doneButton]}>
      <Text style={styles.buttonText}>{t('onboarding.getStarted')}</Text>
    </View>
  );

  const renderSkipButton = () => (
    <View style={styles.skipContainer}>
      <Text style={styles.skipText}>{t('onboarding.skip')}</Text>
    </View>
  );

  const onDone = async () => {
    await AsyncStorage.setItem('onboarding_completed', 'true');
    navigation.replace('Auth');
  };

  return (
    <View style={styles.container}>
      <AppIntroSlider
        ref={sliderRef}
        data={slides}
        renderItem={renderSlide}
        renderNextButton={renderNextButton}
        renderDoneButton={renderDoneButton}
        renderSkipButton={renderSkipButton}
        onDone={onDone}
        showSkipButton
        dotStyle={styles.dot}
        activeDotStyle={styles.activeDot}
        bottomButton
        isRTL={isRTL}
      />
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  slide: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'space-around',
    paddingBottom: 100,
  },
  imageContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    width: width * 0.9,
  },
  image: {
    width: width * 0.7,
    height: height * 0.4,
  },
  contentContainer: {
    paddingHorizontal: 30,
    alignItems: 'center',
  },
  title: {
    fontSize: 28,
    fontWeight: 'bold',
    color: '#FFFFFF',
    textAlign: 'center',
    marginBottom: 15,
  },
  text: {
    fontSize: 16,
    color: '#E0E0E0',
    textAlign: 'center',
    lineHeight: 24,
    paddingHorizontal: 20,
  },
  buttonContainer: {
    backgroundColor: '#FFFFFF',
    paddingHorizontal: 40,
    paddingVertical: 12,
    borderRadius: 25,
    marginHorizontal: 20,
  },
  doneButton: {
    backgroundColor: '#4CAF50',
  },
  buttonText: {
    color: '#1E3A5F',
    fontSize: 16,
    fontWeight: 'bold',
    textAlign: 'center',
  },
  skipContainer: {
    paddingHorizontal: 20,
    paddingVertical: 10,
  },
  skipText: {
    color: '#FFFFFF',
    fontSize: 16,
  },
  dot: {
    backgroundColor: 'rgba(255, 255, 255, 0.3)',
    width: 8,
    height: 8,
    borderRadius: 4,
    marginHorizontal: 4,
  },
  activeDot: {
    backgroundColor: '#FFFFFF',
    width: 20,
    height: 8,
    borderRadius: 4,
    marginHorizontal: 4,
  },
});

export default OnboardingScreen;
```

---

## 🌍 3. Multi-Language Support (i18n)

### **Setup i18next:**

```javascript
// src/i18n/index.js
import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { I18nManager } from 'react-native';
import RNRestart from 'react-native-restart';

import ar from './locales/ar.json';
import en from './locales/en.json';

const LANGUAGE_DETECTOR = {
  type: 'languageDetector',
  async: true,
  detect: async (callback) => {
    const savedLanguage = await AsyncStorage.getItem('user-language');
    callback(savedLanguage || 'ar');
  },
  init: () => {},
  cacheUserLanguage: async (language) => {
    await AsyncStorage.setItem('user-language', language);
  },
};

i18n
  .use(LANGUAGE_DETECTOR)
  .use(initReactI18next)
  .init({
    compatibilityJSON: 'v3',
    resources: {
      ar: { translation: ar },
      en: { translation: en },
    },
    fallbackLng: 'ar',
    interpolation: {
      escapeValue: false,
    },
    react: {
      useSuspense: false,
    },
  });

// Function to change language
export const changeLanguage = async (language) => {
  const isRTL = language === 'ar';
  const currentRTL = I18nManager.isRTL;

  // Update i18n
  await i18n.changeLanguage(language);
  
  // Update RTL direction if needed
  if (isRTL !== currentRTL) {
    I18nManager.forceRTL(isRTL);
    I18nManager.allowRTL(isRTL);
    
    // Restart app to apply RTL changes
    setTimeout(() => {
      RNRestart.Restart();
    }, 100);
  }
};

export default i18n;
```

### **Translation Files:**

```json
// src/i18n/locales/ar.json
{
  "common": {
    "appName": "نيوزلي",
    "loading": "جاري التحميل...",
    "error": "حدث خطأ",
    "retry": "إعادة المحاولة",
    "cancel": "إلغاء",
    "save": "حفظ",
    "delete": "حذف",
    "edit": "تعديل",
    "search": "بحث",
    "filter": "تصفية",
    "refresh": "تحديث"
  },
  "onboarding": {
    "skip": "تخطي",
    "next": "التالي",
    "getStarted": "ابدأ الآن",
    "slide1": {
      "title": "أخبار من جميع أنحاء العالم",
      "description": "احصل على آخر الأخبار من مصادر موثوقة في مكان واحد"
    },
    "slide2": {
      "title": "خصص تجربتك",
      "description": "اختر المصادر والفئات التي تهمك"
    },
    "slide3": {
      "title": "إشعارات فورية",
      "description": "احصل على إشعارات فورية للأخبار العاجلة"
    }
  },
  "auth": {
    "login": "تسجيل الدخول",
    "register": "إنشاء حساب",
    "email": "البريد الإلكتروني",
    "password": "كلمة المرور",
    "name": "الاسم",
    "forgotPassword": "نسيت كلمة المرور؟",
    "noAccount": "ليس لديك حساب؟",
    "hasAccount": "لديك حساب بالفعل؟",
    "loginSuccess": "تم تسجيل الدخول بنجاح",
    "registerSuccess": "تم إنشاء الحساب بنجاح"
  },
  "home": {
    "breaking": "عاجل",
    "latest": "الأحدث",
    "forYou": "لك",
    "trending": "الأكثر تداولاً",
    "categories": "التصنيفات"
  },
  "article": {
    "readMore": "اقرأ المزيد",
    "share": "مشاركة",
    "bookmark": "حفظ",
    "bookmarked": "محفوظ",
    "source": "المصدر",
    "relatedNews": "أخبار ذات صلة"
  },
  "bookmarks": {
    "title": "المحفوظات",
    "empty": "لا توجد مقالات محفوظة",
    "emptyDescription": "احفظ المقالات المهمة لقراءتها لاحقاً"
  },
  "profile": {
    "title": "الملف الشخصي",
    "settings": "الإعدادات",
    "language": "اللغة",
    "theme": "المظهر",
    "notifications": "الإشعارات",
    "about": "عن التطبيق",
    "logout": "تسجيل الخروج"
  },
  "settings": {
    "general": "عام",
    "appearance": "المظهر",
    "lightMode": "الوضع النهاري",
    "darkMode": "الوضع الليلي",
    "systemDefault": "حسب النظام",
    "language": {
      "title": "اللغة",
      "ar": "العربية",
      "en": "English"
    },
    "notifications": {
      "title": "الإشعارات",
      "enable": "تفعيل الإشعارات",
      "breakingOnly": "الأخبار العاجلة فقط",
      "dnd": "عدم الإزعاج"
    }
  }
}
```

```json
// src/i18n/locales/en.json
{
  "common": {
    "appName": "Newsly",
    "loading": "Loading...",
    "error": "An error occurred",
    "retry": "Retry",
    "cancel": "Cancel",
    "save": "Save",
    "delete": "Delete",
    "edit": "Edit",
    "search": "Search",
    "filter": "Filter",
    "refresh": "Refresh"
  },
  "onboarding": {
    "skip": "Skip",
    "next": "Next",
    "getStarted": "Get Started",
    "slide1": {
      "title": "News from Around the World",
      "description": "Get the latest news from trusted sources in one place"
    },
    "slide2": {
      "title": "Personalize Your Experience",
      "description": "Choose the sources and categories that interest you"
    },
    "slide3": {
      "title": "Instant Notifications",
      "description": "Get instant notifications for breaking news"
    }
  },
  "auth": {
    "login": "Login",
    "register": "Sign Up",
    "email": "Email",
    "password": "Password",
    "name": "Name",
    "forgotPassword": "Forgot Password?",
    "noAccount": "Don't have an account?",
    "hasAccount": "Already have an account?",
    "loginSuccess": "Logged in successfully",
    "registerSuccess": "Account created successfully"
  },
  "home": {
    "breaking": "Breaking",
    "latest": "Latest",
    "forYou": "For You",
    "trending": "Trending",
    "categories": "Categories"
  },
  "article": {
    "readMore": "Read More",
    "share": "Share",
    "bookmark": "Save",
    "bookmarked": "Saved",
    "source": "Source",
    "relatedNews": "Related News"
  },
  "bookmarks": {
    "title": "Bookmarks",
    "empty": "No saved articles",
    "emptyDescription": "Save important articles to read later"
  },
  "profile": {
    "title": "Profile",
    "settings": "Settings",
    "language": "Language",
    "theme": "Theme",
    "notifications": "Notifications",
    "about": "About",
    "logout": "Logout"
  },
  "settings": {
    "general": "General",
    "appearance": "Appearance",
    "lightMode": "Light Mode",
    "darkMode": "Dark Mode",
    "systemDefault": "System Default",
    "language": {
      "title": "Language",
      "ar": "العربية",
      "en": "English"
    },
    "notifications": {
      "title": "Notifications",
      "enable": "Enable Notifications",
      "breakingOnly": "Breaking News Only",
      "dnd": "Do Not Disturb"
    }
  }
}
```

### **Using Translations in Components:**

```javascript
// Example: HomeScreen.js
import React from 'react';
import { View, Text } from 'react-native';
import { useTranslation } from 'react-i18next';

const HomeScreen = () => {
  const { t } = useTranslation();

  return (
    <View>
      <Text>{t('home.latest')}</Text>
      <Text>{t('home.breaking')}</Text>
    </View>
  );
};
```

---

## 🎨 4. Dark/Light Mode

### **Theme System with Context:**

```javascript
// src/contexts/ThemeContext.js
import React, { createContext, useState, useEffect, useContext } from 'react';
import { useColorScheme } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';

const ThemeContext = createContext();

export const ThemeProvider = ({ children }) => {
  const systemColorScheme = useColorScheme();
  const [themeMode, setThemeMode] = useState('system'); // 'light', 'dark', 'system'
  const [isDark, setIsDark] = useState(systemColorScheme === 'dark');

  useEffect(() => {
    loadTheme();
  }, []);

  useEffect(() => {
    if (themeMode === 'system') {
      setIsDark(systemColorScheme === 'dark');
    }
  }, [systemColorScheme, themeMode]);

  const loadTheme = async () => {
    try {
      const savedTheme = await AsyncStorage.getItem('theme-mode');
      if (savedTheme) {
        setThemeMode(savedTheme);
        if (savedTheme !== 'system') {
          setIsDark(savedTheme === 'dark');
        }
      }
    } catch (error) {
      console.error('Error loading theme:', error);
    }
  };

  const changeTheme = async (mode) => {
    setThemeMode(mode);
    await AsyncStorage.setItem('theme-mode', mode);
    
    if (mode === 'light') {
      setIsDark(false);
    } else if (mode === 'dark') {
      setIsDark(true);
    } else {
      setIsDark(systemColorScheme === 'dark');
    }
  };

  const theme = isDark ? darkTheme : lightTheme;

  return (
    <ThemeContext.Provider value={{ theme, isDark, themeMode, changeTheme }}>
      {children}
    </ThemeContext.Provider>
  );
};

export const useTheme = () => useContext(ThemeContext);

// Light Theme
const lightTheme = {
  mode: 'light',
  colors: {
    // Primary
    primary: '#1E3A5F',
    primaryLight: '#2C5F8D',
    primaryDark: '#0F1F3F',
    
    // Secondary
    secondary: '#4A9EED',
    secondaryLight: '#6BB5FF',
    secondaryDark: '#3A7EBD',
    
    // Background
    background: '#FFFFFF',
    backgroundSecondary: '#F5F5F5',
    backgroundTertiary: '#E0E0E0',
    
    // Surface
    surface: '#FFFFFF',
    surfaceSecondary: '#F9F9F9',
    
    // Text
    text: '#1A1A1A',
    textSecondary: '#666666',
    textTertiary: '#999999',
    textInverse: '#FFFFFF',
    
    // Status
    success: '#4CAF50',
    error: '#F44336',
    warning: '#FF9800',
    info: '#2196F3',
    
    // UI Elements
    border: '#E0E0E0',
    divider: '#F0F0F0',
    shadow: 'rgba(0, 0, 0, 0.1)',
    overlay: 'rgba(0, 0, 0, 0.5)',
    
    // Breaking News
    breaking: '#FF4444',
    
    // Gradients
    gradientPrimary: ['#1E3A5F', '#2C5F8D'],
    gradientSecondary: ['#4A9EED', '#6BB5FF'],
  },
  spacing: {
    xs: 4,
    sm: 8,
    md: 16,
    lg: 24,
    xl: 32,
  },
  borderRadius: {
    sm: 4,
    md: 8,
    lg: 12,
    xl: 16,
    full: 9999,
  },
  fontSize: {
    xs: 12,
    sm: 14,
    md: 16,
    lg: 18,
    xl: 20,
    xxl: 24,
    xxxl: 32,
  },
  fontWeight: {
    regular: '400',
    medium: '500',
    semibold: '600',
    bold: '700',
  },
};

// Dark Theme
const darkTheme = {
  ...lightTheme,
  mode: 'dark',
  colors: {
    ...lightTheme.colors,
    // Primary (lighter in dark mode)
    primary: '#4A9EED',
    primaryLight: '#6BB5FF',
    primaryDark: '#3A7EBD',
    
    // Background
    background: '#121212',
    backgroundSecondary: '#1E1E1E',
    backgroundTertiary: '#2A2A2A',
    
    // Surface
    surface: '#1E1E1E',
    surfaceSecondary: '#2A2A2A',
    
    // Text
    text: '#FFFFFF',
    textSecondary: '#B0B0B0',
    textTertiary: '#808080',
    textInverse: '#1A1A1A',
    
    // UI Elements
    border: '#333333',
    divider: '#2A2A2A',
    shadow: 'rgba(0, 0, 0, 0.5)',
    overlay: 'rgba(0, 0, 0, 0.7)',
    
    // Gradients
    gradientPrimary: ['#1E1E1E', '#2A2A2A'],
    gradientSecondary: ['#2C5F8D', '#4A9EED'],
  },
};
```

### **Using Theme in Components:**

```javascript
// Example: Themed Button Component
import React from 'react';
import { TouchableOpacity, Text, StyleSheet } from 'react-native';
import { useTheme } from '../contexts/ThemeContext';

const ThemedButton = ({ title, onPress, variant = 'primary' }) => {
  const { theme } = useTheme();

  const styles = StyleSheet.create({
    button: {
      backgroundColor: variant === 'primary' 
        ? theme.colors.primary 
        : theme.colors.surface,
      paddingVertical: theme.spacing.md,
      paddingHorizontal: theme.spacing.lg,
      borderRadius: theme.borderRadius.md,
      alignItems: 'center',
      borderWidth: variant === 'outline' ? 1 : 0,
      borderColor: theme.colors.border,
    },
    text: {
      color: variant === 'primary' 
        ? theme.colors.textInverse 
        : theme.colors.text,
      fontSize: theme.fontSize.md,
      fontWeight: theme.fontWeight.semibold,
    },
  });

  return (
    <TouchableOpacity style={styles.button} onPress={onPress}>
      <Text style={styles.text}>{title}</Text>
    </TouchableOpacity>
  );
};

export default ThemedButton;
```

### **Theme Settings Screen:**

```javascript
// src/screens/ThemeSettingsScreen.js
import React from 'react';
import { View, Text, TouchableOpacity, StyleSheet } from 'react-native';
import { useTheme } from '../contexts/ThemeContext';
import { useTranslation } from 'react-i18next';
import Icon from 'react-native-vector-icons/MaterialCommunityIcons';

const ThemeSettingsScreen = () => {
  const { theme, themeMode, changeTheme, isDark } = useTheme();
  const { t } = useTranslation();

  const themeOptions = [
    { id: 'light', label: t('settings.lightMode'), icon: 'white-balance-sunny' },
    { id: 'dark', label: t('settings.darkMode'), icon: 'moon-waning-crescent' },
    { id: 'system', label: t('settings.systemDefault'), icon: 'cellphone' },
  ];

  const styles = createStyles(theme);

  return (
    <View style={styles.container}>
      <Text style={styles.title}>{t('settings.appearance')}</Text>
      
      {themeOptions.map((option) => (
        <TouchableOpacity
          key={option.id}
          style={[
            styles.option,
            themeMode === option.id && styles.optionSelected,
          ]}
          onPress={() => changeTheme(option.id)}
        >
          <Icon
            name={option.icon}
            size={24}
            color={
              themeMode === option.id
                ? theme.colors.primary
                : theme.colors.textSecondary
            }
          />
          <Text
            style={[
              styles.optionText,
              themeMode === option.id && styles.optionTextSelected,
            ]}
          >
            {option.label}
          </Text>
          {themeMode === option.id && (
            <Icon name="check" size={24} color={theme.colors.primary} />
          )}
        </TouchableOpacity>
      ))}
    </View>
  );
};

const createStyles = (theme) => StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: theme.colors.background,
    padding: theme.spacing.lg,
  },
  title: {
    fontSize: theme.fontSize.xl,
    fontWeight: theme.fontWeight.bold,
    color: theme.colors.text,
    marginBottom: theme.spacing.lg,
  },
  option: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: theme.colors.surface,
    padding: theme.spacing.lg,
    borderRadius: theme.borderRadius.md,
    marginBottom: theme.spacing.md,
    borderWidth: 2,
    borderColor: 'transparent',
  },
  optionSelected: {
    borderColor: theme.colors.primary,
    backgroundColor: theme.colors.primary + '10',
  },
  optionText: {
    flex: 1,
    fontSize: theme.fontSize.md,
    color: theme.colors.text,
    marginLeft: theme.spacing.md,
  },
  optionTextSelected: {
    fontWeight: theme.fontWeight.semibold,
    color: theme.colors.primary,
  },
});

export default ThemeSettingsScreen;
```

---

## 🎨 5. Modern UI Design System

### **Design Tokens:**

```javascript
// src/theme/design-tokens.js
export const DesignTokens = {
  // Colors inspired by Admin Panel
  colors: {
    brand: {
      primary: '#1E3A5F',
      secondary: '#4A9EED',
      accent: '#FF6B6B',
    },
    neutral: {
      white: '#FFFFFF',
      gray50: '#F9FAFB',
      gray100: '#F3F4F6',
      gray200: '#E5E7EB',
      gray300: '#D1D5DB',
      gray400: '#9CA3AF',
      gray500: '#6B7280',
      gray600: '#4B5563',
      gray700: '#374151',
      gray800: '#1F2937',
      gray900: '#111827',
      black: '#000000',
    },
    semantic: {
      success: '#10B981',
      warning: '#F59E0B',
      error: '#EF4444',
      info: '#3B82F6',
    },
  },
  
  // Typography
  typography: {
    fontFamily: {
      arabic: 'Cairo',
      english: 'Inter',
    },
    fontSize: {
      xs: 12,
      sm: 14,
      base: 16,
      lg: 18,
      xl: 20,
      '2xl': 24,
      '3xl': 30,
      '4xl': 36,
    },
    lineHeight: {
      tight: 1.25,
      normal: 1.5,
      relaxed: 1.75,
    },
  },
  
  // Spacing (8px base)
  spacing: {
    0: 0,
    1: 4,
    2: 8,
    3: 12,
    4: 16,
    5: 20,
    6: 24,
    8: 32,
    10: 40,
    12: 48,
    16: 64,
  },
  
  // Border Radius
  radius: {
    none: 0,
    sm: 4,
    base: 8,
    md: 12,
    lg: 16,
    xl: 24,
    full: 9999,
  },
  
  // Shadows
  shadows: {
    sm: {
      shadowColor: '#000',
      shadowOffset: { width: 0, height: 1 },
      shadowOpacity: 0.05,
      shadowRadius: 2,
      elevation: 1,
    },
    md: {
      shadowColor: '#000',
      shadowOffset: { width: 0, height: 2 },
      shadowOpacity: 0.1,
      shadowRadius: 4,
      elevation: 3,
    },
    lg: {
      shadowColor: '#000',
      shadowOffset: { width: 0, height: 4 },
      shadowOpacity: 0.15,
      shadowRadius: 8,
      elevation: 5,
    },
    xl: {
      shadowColor: '#000',
      shadowOffset: { width: 0, height: 8 },
      shadowOpacity: 0.2,
      shadowRadius: 16,
      elevation: 8,
    },
  },
};
```

### **Reusable Components:**

```javascript
// src/components/Card.js
import React from 'react';
import { View, StyleSheet } from 'react-native';
import { useTheme } from '../contexts/ThemeContext';
import LinearGradient from 'react-native-linear-gradient';

const Card = ({ children, variant = 'default', gradient = false, style }) => {
  const { theme } = useTheme();

  const styles = StyleSheet.create({
    card: {
      backgroundColor: theme.colors.surface,
      borderRadius: theme.borderRadius.lg,
      padding: theme.spacing.md,
      ...theme.shadows.md,
      ...style,
    },
    elevated: {
      ...theme.shadows.lg,
    },
  });

  if (gradient) {
    return (
      <LinearGradient
        colors={theme.colors.gradientPrimary}
        style={[styles.card, variant === 'elevated' && styles.elevated]}
      >
        {children}
      </LinearGradient>
    );
  }

  return (
    <View style={[styles.card, variant === 'elevated' && styles.elevated]}>
      {children}
    </View>
  );
};

export default Card;
```

---

## 📅 6. Updated Implementation Plan (6 Weeks)

### **Week 1: Foundation & Setup**
**Days 1-2: Project Setup**
- [ ] Initialize React Native project with TypeScript
- [ ] Install all required packages
- [ ] Setup folder structure
- [ ] Configure Firebase (iOS & Android)
- [ ] Setup ESLint & Prettier

**Days 3-4: Core Systems**
- [ ] Implement Theme System (Light/Dark)
- [ ] Setup i18n (Arabic/English)
- [ ] Configure navigation
- [ ] Create design system components

**Days 5-7: Initial Screens**
- [ ] Splash Screen with animations
- [ ] Onboarding screens (3 slides)
- [ ] Test theme switching
- [ ] Test language switching

---

### **Week 2: Authentication**
**Days 1-3: Auth UI**
- [ ] Login screen (themed)
- [ ] Register screen (themed)
- [ ] Password validation
- [ ] Form error handling

**Days 4-5: Auth Logic**
- [ ] API integration
- [ ] Token management
- [ ] Auth context
- [ ] Protected routes

**Days 6-7: Polish**
- [ ] Loading states
- [ ] Error messages (i18n)
- [ ] Remember me functionality
- [ ] Test both languages

---

### **Week 3: Home & Articles**
**Days 1-2: Home Screen**
- [ ] Breaking news banner
- [ ] Latest articles list
- [ ] Categories tabs
- [ ] Pull to refresh

**Days 3-4: Article Details**
- [ ] Article detail screen
- [ ] Image viewer
- [ ] Share functionality
- [ ] Related articles

**Days 5-7: Features**
- [ ] Bookmark functionality
- [ ] Mark as read
- [ ] Infinite scroll
- [ ] Search functionality

---

### **Week 4: Advanced Features**
**Days 1-2: Notifications**
- [ ] FCM setup & testing
- [ ] Device registration
- [ ] Notification list screen
- [ ] Handle notification tap

**Days 3-4: Subscriptions**
- [ ] Subscriptions management
- [ ] Source selection
- [ ] Category selection
- [ ] Save preferences

**Days 5-7: Profile & Settings**
- [ ] Profile screen
- [ ] Settings screen
- [ ] Theme selector
- [ ] Language selector
- [ ] Notification settings

---

### **Week 5: Polish & Optimization**
**Days 1-2: UI/UX**
- [ ] Animations & transitions
- [ ] Loading skeletons
- [ ] Empty states
- [ ] Error states

**Days 3-4: Performance**
- [ ] Image optimization
- [ ] List optimization (FlatList)
- [ ] Memory management
- [ ] API caching

**Days 5-7: Testing**
- [ ] Test all features
- [ ] Test theme switching
- [ ] Test language switching
- [ ] Test on both platforms

---

### **Week 6: Final Testing & Deployment**
**Days 1-2: Bug Fixes**
- [ ] Fix reported bugs
- [ ] Polish UI details
- [ ] Improve performance

**Days 3-4: Deployment Prep**
- [ ] Build release APK/IPA
- [ ] Test on real devices
- [ ] Prepare app store assets
- [ ] Write app description

**Days 5-7: Deployment**
- [ ] Submit to Google Play
- [ ] Submit to App Store
- [ ] Monitor crash reports
- [ ] User feedback

---

## 🎨 Design Guidelines

### **Color Usage:**
- **Primary:** Navigation, CTAs, Important actions
- **Secondary:** Links, Less important actions
- **Breaking News:** Red badge/banner
- **Success:** Bookmark added, Actions completed
- **Error:** Form errors, Failed actions

### **Typography:**
- **Headlines:** Bold, Large (24-32px)
- **Body:** Regular, Medium (16-18px)
- **Captions:** Regular, Small (12-14px)
- **Arabic:** Right-aligned, Cairo font
- **English:** Left-aligned, Inter font

### **Spacing:**
- **Cards:** 16px padding
- **Sections:** 24px margin
- **List items:** 12px between items
- **Screen edges:** 16px padding

### **Animations:**
- **Duration:** 200-300ms
- **Easing:** ease-in-out
- **Transitions:** Fade, Scale, Slide

---

## 🚀 App.js with All Features

```javascript
// App.js
import React, { useEffect, useState } from 'react';
import { StatusBar, I18nManager } from 'react-native';
import { NavigationContainer } from '@react-navigation/native';
import BootSplash from 'react-native-bootsplash';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { ThemeProvider, useTheme } from './src/contexts/ThemeContext';
import { AuthProvider } from './src/contexts/AuthContext';
import './src/i18n';
import { notificationService } from './src/utils/notifications';
import RootNavigator from './src/navigation/RootNavigator';

// Force RTL for Arabic
I18nManager.allowRTL(true);
I18nManager.forceRTL(I18nManager.isRTL);

const App = () => {
  const [isReady, setIsReady] = useState(false);

  useEffect(() => {
    const init = async () => {
      try {
        // Setup notifications
        const hasPermission = await notificationService.requestPermission();
        if (hasPermission) {
          await notificationService.registerDevice();
        }
        
        // Hide splash screen
        await BootSplash.hide({ fade: true });
        
        setIsReady(true);
      } catch (error) {
        console.error('App initialization error:', error);
        setIsReady(true);
      }
    };

    init();
  }, []);

  if (!isReady) {
    return null;
  }

  return (
    <ThemeProvider>
      <AuthProvider>
        <AppContent />
      </AuthProvider>
    </ThemeProvider>
  );
};

const AppContent = () => {
  const { theme, isDark } = useTheme();

  return (
    <>
      <StatusBar
        barStyle={isDark ? 'light-content' : 'dark-content'}
        backgroundColor={theme.colors.background}
      />
      <NavigationContainer
        theme={{
          dark: isDark,
          colors: {
            primary: theme.colors.primary,
            background: theme.colors.background,
            card: theme.colors.surface,
            text: theme.colors.text,
            border: theme.colors.border,
          },
        }}
      >
        <RootNavigator />
      </NavigationContainer>
    </>
  );
};

export default App;
```

---

## 🎯 Summary Checklist

### **Core Features:**
- [x] Splash Screen with animations
- [x] Onboarding (3 professional slides)
- [x] Multi-language (AR/EN) with RTL support
- [x] Dark/Light/System theme modes
- [x] Modern UI matching admin panel
- [x] Complete API integration
- [x] Push notifications
- [x] 6-week implementation plan

### **Next Steps:**
1. Initialize React Native project
2. Install all packages from this guide
3. Copy all code examples
4. Follow the 6-week plan
5. Test on real devices

---

**🎉 Everything is ready to build a professional, modern news app! 🚀**




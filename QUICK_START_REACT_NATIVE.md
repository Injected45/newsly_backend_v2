# 🚀 Quick Start - إنشاء تطبيق Newsly React Native

## 📋 المتطلبات الأساسية

قبل البدء، تأكد من تثبيت:
- ✅ Node.js (v16+)
- ✅ npm or yarn
- ✅ React Native CLI
- ✅ Android Studio (للـ Android)
- ✅ Xcode (للـ iOS - Mac only)

---

## 🎯 الإعداد السريع (خطوة بخطوة)

### **الخطوة 1: إنشاء المشروع**

```bash
# إنشاء مشروع React Native جديد
npx react-native@latest init NewslyApp --version 0.73.0

# الانتقال للمجلد
cd NewslyApp
```

### **الخطوة 2: تثبيت الحزم الأساسية**

```bash
# Core Dependencies
npm install axios@1.6.2
npm install @react-native-async-storage/async-storage@1.21.0

# Navigation
npm install @react-navigation/native@6.1.9
npm install @react-navigation/stack@6.3.20
npm install @react-navigation/bottom-tabs@6.5.11
npm install react-native-screens@3.29.0
npm install react-native-safe-area-context@4.8.2
npm install react-native-gesture-handler@2.14.1

# UI Components
npm install react-native-vector-icons@10.0.3
npm install react-native-linear-gradient@2.8.3

# Internationalization
npm install i18next@23.7.11
npm install react-i18next@14.0.0

# Date handling
npm install dayjs@1.11.10

# Fast Image
npm install react-native-fast-image@8.6.3
```

### **الخطوة 3: Link Native Modules**

```bash
# For iOS
cd ios && pod install && cd ..

# تشغيل المشروع للتأكد من أنه يعمل
npm run android
# أو
npm run ios
```

---

## 📁 إنشاء البنية الأساسية

```bash
# إنشاء المجلدات الأساسية
mkdir -p src/{api,components,screens,navigation,contexts,utils,theme}
mkdir -p src/api/services
mkdir -p src/screens/{Auth,Home}
mkdir -p src/i18n/locales
mkdir -p src/assets/{images,fonts}
```

---

## 🔧 الملفات الأساسية للبدء

سأقوم الآن بإنشاء الملفات الأساسية لجعل التطبيق يعمل:

### 1. **API Client**
### 2. **Theme Context**
### 3. **Auth Context**
### 4. **i18n Setup**
### 5. **Navigation**
### 6. **Basic Screens**

---

## 🎨 تغييرات على الملفات الأساسية

### **babel.config.js:**
```javascript
module.exports = {
  presets: ['module:metro-react-native-babel-preset'],
  plugins: [
    'react-native-reanimated/plugin',
  ],
};
```

### **metro.config.js:**
```javascript
const {getDefaultConfig, mergeConfig} = require('@react-native/metro-config');

const config = {};

module.exports = mergeConfig(getDefaultConfig(__dirname), config);
```

---

## ✅ Checklist

- [ ] Node.js مُثبت
- [ ] React Native CLI مُثبت
- [ ] Android Studio مُثبت
- [ ] المشروع تم إنشاؤه
- [ ] الحزم تم تثبيتها
- [ ] البنية تم إنشاؤها
- [ ] الملفات الأساسية تم نسخها
- [ ] التطبيق يعمل

---

**التالي: نسخ الملفات من المجلد التالي**




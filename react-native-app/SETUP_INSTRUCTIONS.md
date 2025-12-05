# 🚀 دليل تشغيل تطبيق Newsly React Native

## ✅ تم إنشاء الملفات التالية:

```
react-native-app/
├── App.js                              ✅ Main App Component
├── src/
│   ├── api/
│   │   ├── client.js                   ✅ Axios Client
│   │   ├── endpoints.js                ✅ API Endpoints
│   │   └── services/
│   │       └── authService.js          ✅ Auth Service
│   ├── contexts/
│   │   ├── ThemeContext.js             ✅ Theme Context
│   │   └── AuthContext.js              ✅ Auth Context
│   ├── screens/
│   │   ├── Auth/
│   │   │   ├── LoginScreen.js          ✅ Login Screen
│   │   │   └── RegisterScreen.js       ✅ Register Screen
│   │   └── Home/
│   │       └── HomeScreen.js           ✅ Home Screen
│   └── navigation/
│       └── RootNavigator.js            ✅ Root Navigator
└── SETUP_INSTRUCTIONS.md              ✅ هذا الملف
```

---

## 📋 خطوات التشغيل

### **الخطوة 1: إنشاء مشروع React Native**

```bash
# افتح Terminal في مكان تريد إنشاء المشروع فيه
npx react-native@latest init NewslyApp --version 0.73.0

# انتقل لمجلد المشروع
cd NewslyApp
```

### **الخطوة 2: نسخ الملفات**

انسخ جميع الملفات من مجلد `react-native-app/` إلى مجلد `NewslyApp/`:

```bash
# انسخ:
- App.js (استبدل الموجود)
- src/ (انسخ المجلد كاملاً)
```

### **الخطوة 3: تثبيت الحزم**

```bash
# Core Dependencies
npm install axios@1.6.2
npm install @react-native-async-storage/async-storage@1.21.0

# Navigation
npm install @react-navigation/native@6.1.9
npm install @react-navigation/stack@6.3.20
npm install react-native-screens@3.29.0
npm install react-native-safe-area-context@4.8.2
npm install react-native-gesture-handler@2.14.1

# UI
npm install react-native-vector-icons@10.0.3
npm install react-native-linear-gradient@2.8.3
```

### **الخطوة 4: Link Native Modules (iOS only)**

```bash
cd ios
pod install
cd ..
```

### **الخطوة 5: تحديث عنوان API**

افتح ملف `src/api/client.js` وغيّر `API_BASE_URL`:

```javascript
// استبدل 192.168.1.100 بـ IP Address الخاص بجهازك
export const API_BASE_URL = 'http://192.168.1.100:8080/api';
```

**كيف تعرف IP Address الخاص بك:**

**Windows:**
```bash
ipconfig
# ابحث عن IPv4 Address
```

**Mac/Linux:**
```bash
ifconfig | grep "inet "
# أو
ip addr show
```

### **الخطوة 6: تشغيل Backend**

في terminal منفصل، شغل Laravel Backend:

```bash
cd D:\Newsly\backend
php artisan serve --host=0.0.0.0 --port=8080
```

تأكد من أن:
- ✅ Queue worker يعمل: `php artisan queue:work`
- ✅ Scheduler يعمل: `php artisan schedule:work`

### **الخطوة 7: تشغيل التطبيق**

**Android:**
```bash
# شغل Metro Bundler
npm start

# في terminal آخر، شغل التطبيق
npm run android
```

**iOS:**
```bash
# شغل Metro Bundler
npm start

# في terminal آخر، شغل التطبيق
npm run ios
```

---

## 🔧 حل المشاكل الشائعة

### **مشكلة 1: Error: Unable to resolve module**
```bash
# احذف node_modules وأعد التثبيت
rm -rf node_modules
npm install

# نظف Cache
npm start -- --reset-cache
```

### **مشكلة 2: Android Build Failed**
```bash
cd android
./gradlew clean
cd ..
npm run android
```

### **مشكلة 3: iOS Build Failed**
```bash
cd ios
pod deintegrate
pod install
cd ..
npm run ios
```

### **مشكلة 4: Network Error**
- ✅ تأكد من أن Backend يعمل
- ✅ تأكد من IP Address صحيح
- ✅ تأكد من أن Firewall لا يمنع الاتصال
- ✅ تأكد من أن الجهاز والكمبيوتر على نفس الشبكة

### **مشكلة 5: Cannot connect to Backend**
```bash
# جرب:
# 1. تأكد من أن Backend يعمل
curl http://localhost:8080/api/health

# 2. من الهاتف/المحاكي، جرب:
# Android: استخدم IP الحقيقي (لا تستخدم localhost)
# iOS Simulator: استخدم localhost أو IP الحقيقي
```

---

## 📱 اختبار التطبيق

### **1. تسجيل حساب جديد:**
- افتح التطبيق
- اضغط "سجل الآن"
- أدخل البيانات
- اضغط "إنشاء حساب"

### **2. تسجيل الدخول:**
- أدخل Email و Password
- اضغط "دخول"

### **3. عرض الأخبار:**
- بعد تسجيل الدخول، ستظهر قائمة الأخبار
- اسحب للأسفل للتحديث

---

## 🎨 الميزات المُنفذة

### ✅ **Core Features:**
- [x] Authentication (Login/Register)
- [x] Auto Token Management
- [x] Theme Context (Light/Dark)
- [x] Auth Context
- [x] API Client with Interceptors
- [x] Articles List
- [x] Pull to Refresh
- [x] Error Handling

### ⏳ **التالي (في الأدلة الأخرى):**
- [ ] User Setup Flow (Country + Sources)
- [ ] Splash Screen
- [ ] Onboarding
- [ ] i18n (Multi-language)
- [ ] Article Details
- [ ] Bookmarks
- [ ] Push Notifications
- [ ] Settings

---

## 📚 الخطوات التالية

بعد التأكد من أن التطبيق الأساسي يعمل، يمكنك إضافة:

1. **User Setup Flow:**
   - راجع: `USER_ONBOARDING_SETUP_GUIDE.md`

2. **Advanced Features:**
   - راجع: `ADVANCED_FEATURES_GUIDE.md`

3. **Modern UI Components:**
   - راجع: `MODERN_UI_COMPONENTS.md`

---

## 🎯 Checklist

### **قبل التشغيل:**
- [ ] تم إنشاء المشروع
- [ ] تم نسخ الملفات
- [ ] تم تثبيت الحزم
- [ ] تم تحديث API_BASE_URL
- [ ] Backend يعمل

### **للاختبار:**
- [ ] التطبيق يفتح بدون Errors
- [ ] شاشة Login تظهر
- [ ] يمكن التسجيل
- [ ] يمكن تسجيل الدخول
- [ ] الأخبار تظهر
- [ ] Pull to refresh يعمل

---

## 📞 في حالة وجود مشاكل:

1. **تحقق من Logs:**
   ```bash
   # React Native
   npx react-native log-android
   # أو
   npx react-native log-ios
   ```

2. **تحقق من Backend:**
   ```bash
   curl http://YOUR_IP:8080/api/health
   ```

3. **تحقق من Network:**
   - هل الجهاز والكمبيوتر على نفس الشبكة؟
   - هل Firewall يسمح بالاتصال؟

---

## 🎉 نجاح!

إذا وصلت لهنا والتطبيق يعمل، تهانينا! 🎊

الآن يمكنك:
1. ✅ تسجيل الدخول
2. ✅ عرض الأخبار
3. ✅ التحديث

**التالي:** إضافة المزيد من الميزات من الأدلة الأخرى!

---

**تم إنشاؤه:** 4 ديسمبر 2025  
**الإصدار:** 1.0.0




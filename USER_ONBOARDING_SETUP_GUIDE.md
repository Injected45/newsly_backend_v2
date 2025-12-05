# 🎯 User Onboarding Setup - دليل الإعداد الأولي للمستخدم

## 📋 نظرة عامة

هذه الميزة تسمح للمستخدمين الجدد بإكمال إعداد حساباتهم بعد التسجيل مباشرة من خلال:
1. **اختيار الدولة** - لتخصيص المحتوى حسب الموقع
2. **اختيار المصادر** - لتخصيص الأخبار حسب اهتماماتهم

**ملاحظة:** هذه الخطوات تظهر **مرة واحدة فقط** بعد التسجيل، ويمكن للمستخدم تغييرها لاحقاً من الإعدادات.

---

## 🔧 Backend Implementation

### **1. Database Changes**

#### **Migration:**
```php
// database/migrations/2025_12_04_122128_add_setup_completed_at_to_users_table.php

Schema::table('users', function (Blueprint $table) {
    $table->timestamp('setup_completed_at')->nullable()->after('last_login_ip');
    $table->index('setup_completed_at');
});
```

#### **User Model Updates:**
```php
// app/Models/User.php

protected $fillable = [
    // ... existing fields
    'setup_completed_at',
];

protected $casts = [
    // ... existing casts
    'setup_completed_at' => 'datetime',
];

// New Methods:
public function hasCompletedSetup(): bool
{
    return !is_null($this->setup_completed_at);
}

public function completeSetup(): void
{
    $this->update(['setup_completed_at' => now()]);
}
```

---

### **2. API Endpoints**

#### **✅ Check Setup Status**
```http
GET /api/setup/check
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "needs_setup": true,
    "setup_completed_at": null
  }
}
```

---

#### **✅ Complete Setup**
```http
POST /api/setup/complete
Authorization: Bearer {token}
Content-Type: application/json

{
  "country_id": 1,
  "source_ids": [1, 2, 3, 5]
}
```

**Validation Rules:**
- `country_id`: required, must exist in countries table
- `source_ids`: required, array, minimum 1 source
- `source_ids.*`: must exist in sources table

**Response:**
```json
{
  "success": true,
  "message": "Setup completed successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "Ahmed Mohamed",
      "email": "ahmed@example.com",
      "country": {
        "id": 1,
        "name_ar": "مصر",
        "name_en": "Egypt"
      },
      "setup_completed_at": "2025-12-04T12:30:00.000000Z",
      "needs_setup": false,
      "subscriptions": [
        {
          "id": 1,
          "source": {
            "id": 1,
            "name_ar": "اليوم السابع",
            "name_en": "Youm7"
          }
        }
        // ... more subscriptions
      ]
    }
  }
}
```

**Error Responses:**
```json
// If setup already completed
{
  "success": false,
  "message": "تم إكمال الإعداد مسبقاً"
}

// If validation fails
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "country_id": ["يجب اختيار الدولة"],
    "source_ids": ["يجب اختيار مصدر واحد على الأقل"]
  }
}
```

---

#### **✅ Skip Setup**
```http
POST /api/setup/skip
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Setup skipped",
  "data": {
    "message": "تم تخطي الإعداد"
  }
}
```

**Note:** حتى عند التخطي، يتم تعيين `setup_completed_at` حتى لا تظهر شاشة الإعداد مرة أخرى.

---

### **3. Updated User Resource**

```php
// app/Http/Resources/UserResource.php

public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        'email' => $this->email,
        // ... other fields
        'setup_completed_at' => $this->setup_completed_at?->toISOString(),
        'needs_setup' => !$this->hasCompletedSetup(), // ✨ New field
        // ... other fields
    ];
}
```

---

### **4. Registration Flow Update**

بعد التسجيل، الـ Response يتضمن `needs_setup`:

```json
// POST /api/auth/register
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "token": "1|abc123...",
    "user": {
      "id": 1,
      "name": "Ahmed Mohamed",
      "email": "ahmed@example.com",
      "needs_setup": true,  // ✨ New field
      "setup_completed_at": null
    }
  }
}
```

---

## 📱 React Native Implementation

### **1. Setup Flow Screens**

#### **File Structure:**
```
src/
├── screens/
│   ├── Setup/
│   │   ├── SetupNavigator.js
│   │   ├── CountrySelectionScreen.js
│   │   ├── SourceSelectionScreen.js
│   │   └── SetupCompleteScreen.js
```

---

### **2. Setup Navigator**

```javascript
// src/screens/Setup/SetupNavigator.js
import React from 'react';
import { createStackNavigator } from '@react-navigation/stack';
import CountrySelectionScreen from './CountrySelectionScreen';
import SourceSelectionScreen from './SourceSelectionScreen';
import SetupCompleteScreen from './SetupCompleteScreen';

const Stack = createStackNavigator();

const SetupNavigator = () => {
  return (
    <Stack.Navigator
      screenOptions={{
        headerShown: false,
        gestureEnabled: false, // Prevent going back
      }}
    >
      <Stack.Screen name="CountrySelection" component={CountrySelectionScreen} />
      <Stack.Screen name="SourceSelection" component={SourceSelectionScreen} />
      <Stack.Screen name="SetupComplete" component={SetupCompleteScreen} />
    </Stack.Navigator>
  );
};

export default SetupNavigator;
```

---

### **3. Country Selection Screen**

```javascript
// src/screens/Setup/CountrySelectionScreen.js
import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  FlatList,
  TouchableOpacity,
  StyleSheet,
  ActivityIndicator,
} from 'react-native';
import LinearGradient from 'react-native-linear-gradient';
import Icon from 'react-native-vector-icons/MaterialCommunityIcons';
import { useTheme } from '../../contexts/ThemeContext';
import { useTranslation } from 'react-i18next';
import apiClient from '../../api/client';

const CountrySelectionScreen = ({ navigation }) => {
  const { theme } = useTheme();
  const { t, i18n } = useTranslation();
  const isRTL = i18n.language === 'ar';
  
  const [countries, setCountries] = useState([]);
  const [selectedCountry, setSelectedCountry] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadCountries();
  }, []);

  const loadCountries = async () => {
    try {
      const response = await apiClient.get('/countries');
      if (response.success) {
        setCountries(response.data);
      }
    } catch (error) {
      console.error('Error loading countries:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleNext = () => {
    if (selectedCountry) {
      navigation.navigate('SourceSelection', { countryId: selectedCountry.id });
    }
  };

  const styles = createStyles(theme, isRTL);

  if (loading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color={theme.colors.primary} />
      </View>
    );
  }

  return (
    <LinearGradient
      colors={theme.colors.gradientPrimary}
      style={styles.container}
    >
      {/* Header */}
      <View style={styles.header}>
        <Text style={styles.step}>{t('setup.step')} 1/2</Text>
        <Text style={styles.title}>{t('setup.selectCountry')}</Text>
        <Text style={styles.subtitle}>{t('setup.selectCountryDescription')}</Text>
      </View>

      {/* Countries List */}
      <View style={styles.content}>
        <FlatList
          data={countries}
          keyExtractor={(item) => item.id.toString()}
          renderItem={({ item }) => (
            <TouchableOpacity
              style={[
                styles.countryCard,
                selectedCountry?.id === item.id && styles.countryCardSelected,
              ]}
              onPress={() => setSelectedCountry(item)}
            >
              <View style={styles.countryInfo}>
                <Text style={styles.countryFlag}>{item.flag}</Text>
                <Text style={styles.countryName}>
                  {isRTL ? item.name_ar : item.name_en}
                </Text>
              </View>
              {selectedCountry?.id === item.id && (
                <Icon name="check-circle" size={24} color={theme.colors.primary} />
              )}
            </TouchableOpacity>
          )}
          contentContainerStyle={styles.listContent}
        />
      </View>

      {/* Footer */}
      <View style={styles.footer}>
        <TouchableOpacity
          style={[
            styles.nextButton,
            !selectedCountry && styles.nextButtonDisabled,
          ]}
          onPress={handleNext}
          disabled={!selectedCountry}
        >
          <Text style={styles.nextButtonText}>{t('common.next')}</Text>
          <Icon name="arrow-right" size={20} color="#FFF" />
        </TouchableOpacity>

        <TouchableOpacity
          style={styles.skipButton}
          onPress={() => navigation.replace('Main')}
        >
          <Text style={styles.skipButtonText}>{t('setup.skipForNow')}</Text>
        </TouchableOpacity>
      </View>
    </LinearGradient>
  );
};

const createStyles = (theme, isRTL) => StyleSheet.create({
  container: {
    flex: 1,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  header: {
    padding: theme.spacing.xl,
    paddingTop: theme.spacing.xl * 2,
  },
  step: {
    fontSize: theme.fontSize.sm,
    color: 'rgba(255, 255, 255, 0.8)',
    marginBottom: theme.spacing.sm,
  },
  title: {
    fontSize: theme.fontSize.xxxl,
    fontWeight: theme.fontWeight.bold,
    color: '#FFFFFF',
    marginBottom: theme.spacing.sm,
    textAlign: isRTL ? 'right' : 'left',
  },
  subtitle: {
    fontSize: theme.fontSize.md,
    color: 'rgba(255, 255, 255, 0.9)',
    lineHeight: 24,
    textAlign: isRTL ? 'right' : 'left',
  },
  content: {
    flex: 1,
    backgroundColor: theme.colors.background,
    borderTopLeftRadius: theme.borderRadius.xl,
    borderTopRightRadius: theme.borderRadius.xl,
    paddingTop: theme.spacing.lg,
  },
  listContent: {
    padding: theme.spacing.lg,
  },
  countryCard: {
    flexDirection: isRTL ? 'row-reverse' : 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    backgroundColor: theme.colors.surface,
    padding: theme.spacing.lg,
    borderRadius: theme.borderRadius.md,
    marginBottom: theme.spacing.md,
    borderWidth: 2,
    borderColor: 'transparent',
  },
  countryCardSelected: {
    borderColor: theme.colors.primary,
    backgroundColor: theme.colors.primary + '10',
  },
  countryInfo: {
    flexDirection: isRTL ? 'row-reverse' : 'row',
    alignItems: 'center',
  },
  countryFlag: {
    fontSize: 32,
    marginRight: isRTL ? 0 : theme.spacing.md,
    marginLeft: isRTL ? theme.spacing.md : 0,
  },
  countryName: {
    fontSize: theme.fontSize.lg,
    fontWeight: theme.fontWeight.medium,
    color: theme.colors.text,
  },
  footer: {
    padding: theme.spacing.xl,
    backgroundColor: theme.colors.background,
  },
  nextButton: {
    flexDirection: isRTL ? 'row-reverse' : 'row',
    backgroundColor: theme.colors.primary,
    paddingVertical: theme.spacing.lg,
    paddingHorizontal: theme.spacing.xl,
    borderRadius: theme.borderRadius.md,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: theme.spacing.md,
  },
  nextButtonDisabled: {
    backgroundColor: theme.colors.border,
  },
  nextButtonText: {
    color: '#FFFFFF',
    fontSize: theme.fontSize.lg,
    fontWeight: theme.fontWeight.semibold,
    marginRight: isRTL ? 0 : theme.spacing.sm,
    marginLeft: isRTL ? theme.spacing.sm : 0,
  },
  skipButton: {
    paddingVertical: theme.spacing.md,
    alignItems: 'center',
  },
  skipButtonText: {
    color: theme.colors.textSecondary,
    fontSize: theme.fontSize.md,
  },
});

export default CountrySelectionScreen;
```

---

### **4. Source Selection Screen**

```javascript
// src/screens/Setup/SourceSelectionScreen.js
import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  FlatList,
  TouchableOpacity,
  StyleSheet,
  ActivityIndicator,
  Image,
} from 'react-native';
import LinearGradient from 'react-native-linear-gradient';
import Icon from 'react-native-vector-icons/MaterialCommunityIcons';
import FastImage from 'react-native-fast-image';
import { useTheme } from '../../contexts/ThemeContext';
import { useTranslation } from 'react-i18next';
import apiClient from '../../api/client';

const SourceSelectionScreen = ({ navigation, route }) => {
  const { countryId } = route.params;
  const { theme } = useTheme();
  const { t, i18n } = useTranslation();
  const isRTL = i18n.language === 'ar';
  
  const [sources, setSources] = useState([]);
  const [selectedSources, setSelectedSources] = useState([]);
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);

  useEffect(() => {
    loadSources();
  }, []);

  const loadSources = async () => {
    try {
      const response = await apiClient.get('/sources', {
        params: { country_id: countryId },
      });
      if (response.success) {
        setSources(response.data);
      }
    } catch (error) {
      console.error('Error loading sources:', error);
    } finally {
      setLoading(false);
    }
  };

  const toggleSource = (sourceId) => {
    setSelectedSources((prev) => {
      if (prev.includes(sourceId)) {
        return prev.filter((id) => id !== sourceId);
      } else {
        return [...prev, sourceId];
      }
    });
  };

  const handleComplete = async () => {
    if (selectedSources.length === 0) {
      // Show error
      return;
    }

    setSubmitting(true);
    try {
      const response = await apiClient.post('/setup/complete', {
        country_id: countryId,
        source_ids: selectedSources,
      });

      if (response.success) {
        navigation.replace('SetupComplete');
      }
    } catch (error) {
      console.error('Error completing setup:', error);
      // Show error message
    } finally {
      setSubmitting(false);
    }
  };

  const styles = createStyles(theme, isRTL);

  if (loading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color={theme.colors.primary} />
      </View>
    );
  }

  return (
    <LinearGradient
      colors={theme.colors.gradientPrimary}
      style={styles.container}
    >
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity
          onPress={() => navigation.goBack()}
          style={styles.backButton}
        >
          <Icon
            name={isRTL ? 'arrow-right' : 'arrow-left'}
            size={24}
            color="#FFF"
          />
        </TouchableOpacity>
        
        <Text style={styles.step}>{t('setup.step')} 2/2</Text>
        <Text style={styles.title}>{t('setup.selectSources')}</Text>
        <Text style={styles.subtitle}>
          {t('setup.selectSourcesDescription')}
        </Text>
        
        {selectedSources.length > 0 && (
          <Text style={styles.selectedCount}>
            {t('setup.selectedSources', { count: selectedSources.length })}
          </Text>
        )}
      </View>

      {/* Sources Grid */}
      <View style={styles.content}>
        <FlatList
          data={sources}
          keyExtractor={(item) => item.id.toString()}
          numColumns={2}
          renderItem={({ item }) => {
            const isSelected = selectedSources.includes(item.id);
            return (
              <TouchableOpacity
                style={[
                  styles.sourceCard,
                  isSelected && styles.sourceCardSelected,
                ]}
                onPress={() => toggleSource(item.id)}
              >
                {item.logo && (
                  <FastImage
                    source={{ uri: item.logo }}
                    style={styles.sourceLogo}
                    resizeMode={FastImage.resizeMode.contain}
                  />
                )}
                <Text style={styles.sourceName} numberOfLines={2}>
                  {isRTL ? item.name_ar : item.name_en}
                </Text>
                
                {isSelected && (
                  <View style={styles.checkmark}>
                    <Icon name="check" size={16} color="#FFF" />
                  </View>
                )}
              </TouchableOpacity>
            );
          }}
          contentContainerStyle={styles.gridContent}
          columnWrapperStyle={styles.row}
        />
      </View>

      {/* Footer */}
      <View style={styles.footer}>
        <TouchableOpacity
          style={[
            styles.completeButton,
            (selectedSources.length === 0 || submitting) &&
              styles.completeButtonDisabled,
          ]}
          onPress={handleComplete}
          disabled={selectedSources.length === 0 || submitting}
        >
          {submitting ? (
            <ActivityIndicator color="#FFF" />
          ) : (
            <>
              <Text style={styles.completeButtonText}>
                {t('setup.complete')}
              </Text>
              <Icon name="check" size={20} color="#FFF" />
            </>
          )}
        </TouchableOpacity>
      </View>
    </LinearGradient>
  );
};

const createStyles = (theme, isRTL) => StyleSheet.create({
  container: {
    flex: 1,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  header: {
    padding: theme.spacing.xl,
    paddingTop: theme.spacing.xl * 2,
  },
  backButton: {
    marginBottom: theme.spacing.md,
  },
  step: {
    fontSize: theme.fontSize.sm,
    color: 'rgba(255, 255, 255, 0.8)',
    marginBottom: theme.spacing.sm,
  },
  title: {
    fontSize: theme.fontSize.xxxl,
    fontWeight: theme.fontWeight.bold,
    color: '#FFFFFF',
    marginBottom: theme.spacing.sm,
    textAlign: isRTL ? 'right' : 'left',
  },
  subtitle: {
    fontSize: theme.fontSize.md,
    color: 'rgba(255, 255, 255, 0.9)',
    lineHeight: 24,
    textAlign: isRTL ? 'right' : 'left',
  },
  selectedCount: {
    fontSize: theme.fontSize.sm,
    color: '#FFF',
    marginTop: theme.spacing.md,
    fontWeight: theme.fontWeight.semibold,
  },
  content: {
    flex: 1,
    backgroundColor: theme.colors.background,
    borderTopLeftRadius: theme.borderRadius.xl,
    borderTopRightRadius: theme.borderRadius.xl,
    paddingTop: theme.spacing.lg,
  },
  gridContent: {
    padding: theme.spacing.md,
  },
  row: {
    justifyContent: 'space-between',
  },
  sourceCard: {
    flex: 1,
    aspectRatio: 1,
    backgroundColor: theme.colors.surface,
    borderRadius: theme.borderRadius.md,
    padding: theme.spacing.md,
    margin: theme.spacing.sm,
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 2,
    borderColor: 'transparent',
    position: 'relative',
  },
  sourceCardSelected: {
    borderColor: theme.colors.primary,
    backgroundColor: theme.colors.primary + '10',
  },
  sourceLogo: {
    width: 60,
    height: 60,
    marginBottom: theme.spacing.sm,
  },
  sourceName: {
    fontSize: theme.fontSize.sm,
    fontWeight: theme.fontWeight.medium,
    color: theme.colors.text,
    textAlign: 'center',
  },
  checkmark: {
    position: 'absolute',
    top: 8,
    right: 8,
    backgroundColor: theme.colors.primary,
    borderRadius: theme.borderRadius.full,
    width: 24,
    height: 24,
    alignItems: 'center',
    justifyContent: 'center',
  },
  footer: {
    padding: theme.spacing.xl,
    backgroundColor: theme.colors.background,
  },
  completeButton: {
    flexDirection: isRTL ? 'row-reverse' : 'row',
    backgroundColor: theme.colors.success,
    paddingVertical: theme.spacing.lg,
    paddingHorizontal: theme.spacing.xl,
    borderRadius: theme.borderRadius.md,
    alignItems: 'center',
    justifyContent: 'center',
  },
  completeButtonDisabled: {
    backgroundColor: theme.colors.border,
  },
  completeButtonText: {
    color: '#FFFFFF',
    fontSize: theme.fontSize.lg,
    fontWeight: theme.fontWeight.semibold,
    marginRight: isRTL ? 0 : theme.spacing.sm,
    marginLeft: isRTL ? theme.spacing.sm : 0,
  },
});

export default SourceSelectionScreen;
```

---

### **5. Setup Complete Screen**

```javascript
// src/screens/Setup/SetupCompleteScreen.js
import React, { useEffect } from 'react';
import { View, Text, StyleSheet } from 'react-native';
import LottieView from 'lottie-react-native';
import LinearGradient from 'react-native-linear-gradient';
import { useTheme } from '../../contexts/ThemeContext';
import { useTranslation } from 'react-i18next';

const SetupCompleteScreen = ({ navigation }) => {
  const { theme } = useTheme();
  const { t } = useTranslation();

  useEffect(() => {
    // Auto navigate to main app after 2 seconds
    const timer = setTimeout(() => {
      navigation.replace('Main');
    }, 2000);

    return () => clearTimeout(timer);
  }, []);

  const styles = createStyles(theme);

  return (
    <LinearGradient
      colors={[theme.colors.success, theme.colors.primary]}
      style={styles.container}
    >
      <LottieView
        source={require('../../assets/animations/success.json')}
        autoPlay
        loop={false}
        style={styles.animation}
      />
      
      <Text style={styles.title}>{t('setup.allSet')}</Text>
      <Text style={styles.subtitle}>{t('setup.allSetDescription')}</Text>
    </LinearGradient>
  );
};

const createStyles = (theme) => StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: theme.spacing.xl,
  },
  animation: {
    width: 200,
    height: 200,
  },
  title: {
    fontSize: theme.fontSize.xxxl,
    fontWeight: theme.fontWeight.bold,
    color: '#FFFFFF',
    marginTop: theme.spacing.xl,
    marginBottom: theme.spacing.md,
    textAlign: 'center',
  },
  subtitle: {
    fontSize: theme.fontSize.lg,
    color: 'rgba(255, 255, 255, 0.9)',
    textAlign: 'center',
    lineHeight: 28,
  },
});

export default SetupCompleteScreen;
```

---

### **6. Main App Navigator Update**

```javascript
// src/navigation/RootNavigator.js
import React, { useEffect, useState } from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createStackNavigator } from '@react-navigation/stack';
import { useAuth } from '../contexts/AuthContext';
import AsyncStorage from '@react-native-async-storage/async-storage';

import AuthNavigator from './AuthNavigator';
import SetupNavigator from '../screens/Setup/SetupNavigator';
import MainNavigator from './MainNavigator';
import SplashScreen from '../screens/SplashScreen';

const Stack = createStackNavigator();

const RootNavigator = () => {
  const { user, isAuthenticated, loading } = useAuth();
  const [initialRoute, setInitialRoute] = useState(null);

  useEffect(() => {
    determineInitialRoute();
  }, [isAuthenticated, user]);

  const determineInitialRoute = async () => {
    if (loading) return;

    if (!isAuthenticated) {
      setInitialRoute('Auth');
      return;
    }

    // Check if user needs setup
    if (user?.needs_setup) {
      setInitialRoute('Setup');
    } else {
      setInitialRoute('Main');
    }
  };

  if (loading || !initialRoute) {
    return <SplashScreen />;
  }

  return (
    <Stack.Navigator
      screenOptions={{ headerShown: false }}
      initialRouteName={initialRoute}
    >
      <Stack.Screen name="Auth" component={AuthNavigator} />
      <Stack.Screen name="Setup" component={SetupNavigator} />
      <Stack.Screen name="Main" component={MainNavigator} />
    </Stack.Navigator>
  );
};

export default RootNavigator;
```

---

### **7. Translation Keys**

```json
// src/i18n/locales/ar.json
{
  "setup": {
    "step": "الخطوة",
    "selectCountry": "اختر دولتك",
    "selectCountryDescription": "اختر الدولة لتخصيص المحتوى حسب موقعك",
    "selectSources": "اختر مصادر الأخبار",
    "selectSourcesDescription": "اختر المصادر التي تهمك لتخصيص تجربتك",
    "selectedSources": "تم اختيار {{count}} مصدر",
    "skipForNow": "تخطي الآن",
    "complete": "إكمال",
    "allSet": "كل شيء جاهز!",
    "allSetDescription": "تم إعداد حسابك بنجاح. استمتع بتجربتك!"
  }
}
```

```json
// src/i18n/locales/en.json
{
  "setup": {
    "step": "Step",
    "selectCountry": "Select Your Country",
    "selectCountryDescription": "Choose your country to personalize content",
    "selectSources": "Select News Sources",
    "selectSourcesDescription": "Choose sources that interest you to personalize your experience",
    "selectedSources": "{{count}} sources selected",
    "skipForNow": "Skip for now",
    "complete": "Complete",
    "allSet": "All Set!",
    "allSetDescription": "Your account is ready. Enjoy your experience!"
  }
}
```

---

## 🔄 User Flow

### **Registration → Setup → Main App**

```
1. User registers
   ↓
2. Backend returns: needs_setup: true
   ↓
3. App navigates to Setup flow
   ↓
4. Step 1: Select Country
   ↓
5. Step 2: Select Sources (minimum 1)
   ↓
6. POST /api/setup/complete
   ↓
7. Backend:
   - Updates user.country_id
   - Creates subscriptions
   - Sets setup_completed_at
   ↓
8. Success animation
   ↓
9. Navigate to Main App
```

### **Skip Flow:**

```
User can skip at any step
   ↓
POST /api/setup/skip
   ↓
Backend sets setup_completed_at
   ↓
Navigate to Main App
```

---

## ⚙️ Settings Integration

المستخدم يمكنه تغيير الإعدادات لاحقاً من:

### **1. Change Country:**
```http
PUT /api/user
{
  "country_id": 2
}
```

### **2. Manage Subscriptions:**
```http
# Add subscription
POST /api/subscriptions
{
  "source_id": 5
}

# Remove subscription
DELETE /api/subscriptions/{id}
```

---

## 📊 Database Schema

```sql
-- users table
ALTER TABLE users ADD COLUMN setup_completed_at TIMESTAMP NULL;
ALTER TABLE users ADD INDEX idx_setup_completed_at (setup_completed_at);

-- Check users who haven't completed setup
SELECT * FROM users WHERE setup_completed_at IS NULL;

-- Check users who completed setup today
SELECT * FROM users 
WHERE DATE(setup_completed_at) = CURDATE();
```

---

## 🧪 Testing

### **Backend Tests:**

```bash
# Test setup endpoints
php artisan test --filter=SetupTest
```

### **Postman Collection:**

```json
{
  "name": "User Setup",
  "item": [
    {
      "name": "Check Setup Status",
      "request": {
        "method": "GET",
        "url": "{{base_url}}/setup/check",
        "header": [
          {
            "key": "Authorization",
            "value": "Bearer {{token}}"
          }
        ]
      }
    },
    {
      "name": "Complete Setup",
      "request": {
        "method": "POST",
        "url": "{{base_url}}/setup/complete",
        "header": [
          {
            "key": "Authorization",
            "value": "Bearer {{token}}"
          }
        ],
        "body": {
          "mode": "raw",
          "raw": "{\n  \"country_id\": 1,\n  \"source_ids\": [1, 2, 3]\n}"
        }
      }
    },
    {
      "name": "Skip Setup",
      "request": {
        "method": "POST",
        "url": "{{base_url}}/setup/skip",
        "header": [
          {
            "key": "Authorization",
            "value": "Bearer {{token}}"
          }
        ]
      }
    }
  ]
}
```

---

## ✅ Checklist

### **Backend:**
- [x] Migration created and run
- [x] User model updated
- [x] SetupController created
- [x] CompleteSetupRequest validation
- [x] API routes added
- [x] UserResource updated

### **React Native:**
- [ ] SetupNavigator created
- [ ] CountrySelectionScreen
- [ ] SourceSelectionScreen
- [ ] SetupCompleteScreen
- [ ] RootNavigator updated
- [ ] Translation keys added
- [ ] API service methods
- [ ] Success animation

---

## 🎯 Best Practices

1. **✅ Always check `needs_setup`** في الـ Login/Register response
2. **✅ Prevent back navigation** في Setup screens
3. **✅ Allow skip** لكن احفظ `setup_completed_at`
4. **✅ Minimum 1 source** مطلوب
5. **✅ Show progress** (Step 1/2, 2/2)
6. **✅ Success feedback** بعد الإكمال
7. **✅ Allow changes** من Settings لاحقاً

---

## 🚀 Summary

### **Backend Changes:**
- ✅ Added `setup_completed_at` column
- ✅ Added 3 new endpoints
- ✅ Updated UserResource
- ✅ Validation rules

### **React Native Changes:**
- ✅ 3 new screens
- ✅ Setup Navigator
- ✅ Flow logic
- ✅ Translations

### **User Experience:**
1. Register → Setup (2 steps) → Main App
2. Can skip anytime
3. Can change from Settings
4. Shows only once

---

**✨ الميزة جاهزة للتنفيذ! 🎉**




# 🎨 Modern UI Components Library - Newsly App

تصميمات حديثة واحترافية مشابهة للوحة التحكم Admin Panel

---

## 📋 المحتويات

1. [Modern Article Card](#modern-article-card)
2. [Breaking News Banner](#breaking-news-banner)
3. [Skeleton Loading](#skeleton-loading)
4. [Bottom Sheet](#bottom-sheet)
5. [Search Bar](#search-bar)
6. [Category Chips](#category-chips)
7. [Empty States](#empty-states)
8. [Pull to Refresh](#pull-to-refresh)

---

## 1. 🎴 Modern Article Card

```javascript
// src/components/ModernArticleCard.js
import React from 'react';
import {
  View,
  Text,
  Image,
  TouchableOpacity,
  StyleSheet,
  Dimensions,
} from 'react-native';
import LinearGradient from 'react-native-linear-gradient';
import FastImage from 'react-native-fast-image';
import Icon from 'react-native-vector-icons/MaterialCommunityIcons';
import { useTheme } from '../contexts/ThemeContext';
import { useTranslation } from 'react-i18next';
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import 'dayjs/locale/ar';

dayjs.extend(relativeTime);

const { width } = Dimensions.get('window');
const CARD_WIDTH = width - 32;

const ModernArticleCard = ({
  article,
  onPress,
  onBookmark,
  isBookmarked = false,
  variant = 'default', // 'default', 'featured', 'compact'
}) => {
  const { theme, isDark } = useTheme();
  const { t, i18n } = useTranslation();
  const isRTL = i18n.language === 'ar';
  
  dayjs.locale(i18n.language);

  const styles = createStyles(theme, variant, isRTL);

  if (variant === 'featured') {
    return (
      <TouchableOpacity
        style={styles.featuredCard}
        onPress={() => onPress(article.id)}
        activeOpacity={0.9}
      >
        <FastImage
          source={{ uri: article.image_url }}
          style={styles.featuredImage}
          resizeMode={FastImage.resizeMode.cover}
        >
          <LinearGradient
            colors={['transparent', 'rgba(0,0,0,0.8)']}
            style={styles.featuredGradient}
          >
            {article.is_breaking && (
              <View style={styles.breakingBadge}>
                <Icon name="alert-circle" size={16} color="#FFF" />
                <Text style={styles.breakingText}>{t('home.breaking')}</Text>
              </View>
            )}
            
            <View style={styles.featuredContent}>
              <Text style={styles.featuredTitle} numberOfLines={3}>
                {article.title}
              </Text>
              
              <View style={styles.featuredFooter}>
                {article.source && (
                  <View style={styles.sourceContainer}>
                    {article.source.logo && (
                      <FastImage
                        source={{ uri: article.source.logo }}
                        style={styles.sourceLogo}
                      />
                    )}
                    <Text style={styles.sourceNameWhite}>
                      {isRTL ? article.source.name_ar : article.source.name_en}
                    </Text>
                  </View>
                )}
                <Text style={styles.timeWhite}>
                  {dayjs(article.published_at).fromNow()}
                </Text>
              </View>
            </View>
          </LinearGradient>
        </FastImage>
      </TouchableOpacity>
    );
  }

  if (variant === 'compact') {
    return (
      <TouchableOpacity
        style={styles.compactCard}
        onPress={() => onPress(article.id)}
        activeOpacity={0.9}
      >
        <View style={styles.compactContent}>
          {article.is_breaking && (
            <View style={styles.compactBreakingBadge}>
              <Text style={styles.compactBreakingText}>⚡</Text>
            </View>
          )}
          
          <Text style={styles.compactTitle} numberOfLines={2}>
            {article.title}
          </Text>
          
          <View style={styles.compactFooter}>
            <Text style={styles.compactSource}>
              {isRTL ? article.source?.name_ar : article.source?.name_en}
            </Text>
            <Text style={styles.compactTime}>
              {dayjs(article.published_at).fromNow()}
            </Text>
          </View>
        </View>
        
        {article.image_url && (
          <FastImage
            source={{ uri: article.image_url }}
            style={styles.compactImage}
            resizeMode={FastImage.resizeMode.cover}
          />
        )}
      </TouchableOpacity>
    );
  }

  // Default Card
  return (
    <TouchableOpacity
      style={styles.card}
      onPress={() => onPress(article.id)}
      activeOpacity={0.9}
    >
      {/* Image */}
      {article.image_url && (
        <View style={styles.imageContainer}>
          <FastImage
            source={{ uri: article.image_url }}
            style={styles.image}
            resizeMode={FastImage.resizeMode.cover}
          />
          
          {/* Bookmark Button */}
          <TouchableOpacity
            style={styles.bookmarkButton}
            onPress={(e) => {
              e.stopPropagation();
              onBookmark(article.id);
            }}
          >
            <Icon
              name={isBookmarked ? 'bookmark' : 'bookmark-outline'}
              size={24}
              color={isBookmarked ? theme.colors.secondary : '#FFF'}
            />
          </TouchableOpacity>
        </View>
      )}

      <View style={styles.content}>
        {/* Breaking Badge */}
        {article.is_breaking && (
          <View style={styles.breakingBadge}>
            <Icon name="alert-circle" size={14} color="#FFF" />
            <Text style={styles.breakingText}>{t('home.breaking')}</Text>
          </View>
        )}

        {/* Title */}
        <Text style={styles.title} numberOfLines={2}>
          {article.title}
        </Text>

        {/* Summary */}
        <Text style={styles.summary} numberOfLines={2}>
          {article.summary}
        </Text>

        {/* Footer */}
        <View style={styles.footer}>
          {/* Source */}
          {article.source && (
            <View style={styles.sourceContainer}>
              {article.source.logo && (
                <FastImage
                  source={{ uri: article.source.logo }}
                  style={styles.sourceLogo}
                />
              )}
              <Text style={styles.sourceName}>
                {isRTL ? article.source.name_ar : article.source.name_en}
              </Text>
            </View>
          )}

          {/* Time & Views */}
          <View style={styles.metaContainer}>
            <Icon name="clock-outline" size={14} color={theme.colors.textTertiary} />
            <Text style={styles.time}>
              {dayjs(article.published_at).fromNow()}
            </Text>
            {article.views_count > 0 && (
              <>
                <Icon name="eye-outline" size={14} color={theme.colors.textTertiary} />
                <Text style={styles.views}>{article.views_count}</Text>
              </>
            )}
          </View>
        </View>
      </View>
    </TouchableOpacity>
  );
};

const createStyles = (theme, variant, isRTL) => StyleSheet.create({
  // Featured Card
  featuredCard: {
    width: CARD_WIDTH,
    height: 400,
    marginHorizontal: 16,
    marginVertical: 8,
    borderRadius: theme.borderRadius.xl,
    overflow: 'hidden',
    ...theme.shadows.lg,
  },
  featuredImage: {
    width: '100%',
    height: '100%',
  },
  featuredGradient: {
    flex: 1,
    justifyContent: 'flex-end',
    padding: theme.spacing.lg,
  },
  featuredContent: {
    gap: theme.spacing.md,
  },
  featuredTitle: {
    fontSize: theme.fontSize.xxl,
    fontWeight: theme.fontWeight.bold,
    color: '#FFFFFF',
    lineHeight: 32,
    textAlign: isRTL ? 'right' : 'left',
  },
  featuredFooter: {
    flexDirection: isRTL ? 'row-reverse' : 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  sourceNameWhite: {
    fontSize: theme.fontSize.sm,
    color: '#FFFFFF',
    marginHorizontal: theme.spacing.sm,
  },
  timeWhite: {
    fontSize: theme.fontSize.sm,
    color: 'rgba(255, 255, 255, 0.8)',
  },

  // Compact Card
  compactCard: {
    flexDirection: isRTL ? 'row-reverse' : 'row',
    backgroundColor: theme.colors.surface,
    marginHorizontal: 16,
    marginVertical: 6,
    borderRadius: theme.borderRadius.md,
    overflow: 'hidden',
    ...theme.shadows.sm,
  },
  compactContent: {
    flex: 1,
    padding: theme.spacing.md,
    justifyContent: 'space-between',
  },
  compactBreakingBadge: {
    alignSelf: 'flex-start',
    marginBottom: theme.spacing.sm,
  },
  compactBreakingText: {
    fontSize: 16,
  },
  compactTitle: {
    fontSize: theme.fontSize.md,
    fontWeight: theme.fontWeight.semibold,
    color: theme.colors.text,
    lineHeight: 22,
    textAlign: isRTL ? 'right' : 'left',
  },
  compactFooter: {
    flexDirection: isRTL ? 'row-reverse' : 'row',
    justifyContent: 'space-between',
    marginTop: theme.spacing.sm,
  },
  compactSource: {
    fontSize: theme.fontSize.xs,
    color: theme.colors.textSecondary,
  },
  compactTime: {
    fontSize: theme.fontSize.xs,
    color: theme.colors.textTertiary,
  },
  compactImage: {
    width: 100,
    height: 100,
    backgroundColor: theme.colors.backgroundSecondary,
  },

  // Default Card
  card: {
    backgroundColor: theme.colors.surface,
    marginHorizontal: 16,
    marginVertical: 8,
    borderRadius: theme.borderRadius.lg,
    overflow: 'hidden',
    ...theme.shadows.md,
  },
  imageContainer: {
    position: 'relative',
  },
  image: {
    width: '100%',
    height: 200,
    backgroundColor: theme.colors.backgroundSecondary,
  },
  bookmarkButton: {
    position: 'absolute',
    top: theme.spacing.md,
    right: isRTL ? undefined : theme.spacing.md,
    left: isRTL ? theme.spacing.md : undefined,
    backgroundColor: 'rgba(0, 0, 0, 0.5)',
    borderRadius: theme.borderRadius.full,
    padding: theme.spacing.sm,
  },
  content: {
    padding: theme.spacing.md,
  },
  breakingBadge: {
    flexDirection: isRTL ? 'row-reverse' : 'row',
    alignItems: 'center',
    backgroundColor: theme.colors.breaking,
    alignSelf: 'flex-start',
    paddingHorizontal: theme.spacing.sm,
    paddingVertical: theme.spacing.xs,
    borderRadius: theme.borderRadius.sm,
    marginBottom: theme.spacing.sm,
    gap: 4,
  },
  breakingText: {
    color: '#FFFFFF',
    fontSize: theme.fontSize.xs,
    fontWeight: theme.fontWeight.bold,
  },
  title: {
    fontSize: theme.fontSize.lg,
    fontWeight: theme.fontWeight.bold,
    color: theme.colors.text,
    marginBottom: theme.spacing.sm,
    lineHeight: 24,
    textAlign: isRTL ? 'right' : 'left',
  },
  summary: {
    fontSize: theme.fontSize.sm,
    color: theme.colors.textSecondary,
    marginBottom: theme.spacing.md,
    lineHeight: 20,
    textAlign: isRTL ? 'right' : 'left',
  },
  footer: {
    flexDirection: isRTL ? 'row-reverse' : 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingTop: theme.spacing.sm,
    borderTopWidth: 1,
    borderTopColor: theme.colors.divider,
  },
  sourceContainer: {
    flexDirection: isRTL ? 'row-reverse' : 'row',
    alignItems: 'center',
  },
  sourceLogo: {
    width: 20,
    height: 20,
    borderRadius: theme.borderRadius.full,
    marginRight: isRTL ? 0 : theme.spacing.sm,
    marginLeft: isRTL ? theme.spacing.sm : 0,
  },
  sourceName: {
    fontSize: theme.fontSize.sm,
    color: theme.colors.textSecondary,
    fontWeight: theme.fontWeight.medium,
  },
  metaContainer: {
    flexDirection: isRTL ? 'row-reverse' : 'row',
    alignItems: 'center',
    gap: 4,
  },
  time: {
    fontSize: theme.fontSize.xs,
    color: theme.colors.textTertiary,
    marginRight: theme.spacing.sm,
  },
  views: {
    fontSize: theme.fontSize.xs,
    color: theme.colors.textTertiary,
  },
});

export default ModernArticleCard;
```

---

## 2. 🔥 Breaking News Banner

```javascript
// src/components/BreakingNewsBanner.js
import React, { useRef, useEffect } from 'react';
import {
  View,
  Text,
  ScrollView,
  TouchableOpacity,
  StyleSheet,
  Animated,
  Dimensions,
} from 'react-native';
import LinearGradient from 'react-native-linear-gradient';
import Icon from 'react-native-vector-icons/MaterialCommunityIcons';
import { useTheme } from '../contexts/ThemeContext';
import { useTranslation } from 'react-i18next';

const { width } = Dimensions.get('window');

const BreakingNewsBanner = ({ articles, onPress }) => {
  const { theme } = useTheme();
  const { t, i18n } = useTranslation();
  const isRTL = i18n.language === 'ar';
  const pulseAnim = useRef(new Animated.Value(1)).current;

  useEffect(() => {
    // Pulsing animation for breaking badge
    Animated.loop(
      Animated.sequence([
        Animated.timing(pulseAnim, {
          toValue: 1.2,
          duration: 500,
          useNativeDriver: true,
        }),
        Animated.timing(pulseAnim, {
          toValue: 1,
          duration: 500,
          useNativeDriver: true,
        }),
      ])
    ).start();
  }, []);

  if (!articles || articles.length === 0) {
    return null;
  }

  const styles = createStyles(theme, isRTL);

  return (
    <LinearGradient
      colors={['#FF4444', '#CC0000']}
      start={{ x: 0, y: 0 }}
      end={{ x: 1, y: 0 }}
      style={styles.container}
    >
      <Animated.View style={[styles.iconContainer, { transform: [{ scale: pulseAnim }] }]}>
        <Icon name="alert-circle" size={24} color="#FFF" />
      </Animated.View>

      <ScrollView
        horizontal
        showsHorizontalScrollIndicator={false}
        contentContainerStyle={styles.scrollContent}
      >
        {articles.map((article, index) => (
          <TouchableOpacity
            key={article.id}
            style={styles.articleContainer}
            onPress={() => onPress(article.id)}
          >
            <Text style={styles.articleTitle} numberOfLines={1}>
              {article.title}
            </Text>
            {index < articles.length - 1 && (
              <View style={styles.separator}>
                <Icon name="circle-small" size={20} color="rgba(255,255,255,0.5)" />
              </View>
            )}
          </TouchableOpacity>
        ))}
      </ScrollView>
    </LinearGradient>
  );
};

const createStyles = (theme, isRTL) => StyleSheet.create({
  container: {
    flexDirection: isRTL ? 'row-reverse' : 'row',
    alignItems: 'center',
    paddingVertical: theme.spacing.md,
    paddingHorizontal: theme.spacing.lg,
    marginHorizontal: 16,
    marginTop: 16,
    borderRadius: theme.borderRadius.md,
    ...theme.shadows.md,
  },
  iconContainer: {
    marginRight: isRTL ? 0 : theme.spacing.md,
    marginLeft: isRTL ? theme.spacing.md : 0,
  },
  scrollContent: {
    paddingRight: theme.spacing.lg,
  },
  articleContainer: {
    flexDirection: isRTL ? 'row-reverse' : 'row',
    alignItems: 'center',
  },
  articleTitle: {
    color: '#FFFFFF',
    fontSize: theme.fontSize.md,
    fontWeight: theme.fontWeight.semibold,
  },
  separator: {
    marginHorizontal: theme.spacing.sm,
  },
});

export default BreakingNewsBanner;
```

---

## 3. 💀 Skeleton Loading

```javascript
// src/components/SkeletonLoader.js
import React, { useEffect, useRef } from 'react';
import { View, Animated, StyleSheet } from 'react-native';
import { useTheme } from '../contexts/ThemeContext';

const SkeletonLoader = ({ width, height, borderRadius = 8, style }) => {
  const { theme } = useTheme();
  const animatedValue = useRef(new Animated.Value(0)).current;

  useEffect(() => {
    Animated.loop(
      Animated.sequence([
        Animated.timing(animatedValue, {
          toValue: 1,
          duration: 1000,
          useNativeDriver: true,
        }),
        Animated.timing(animatedValue, {
          toValue: 0,
          duration: 1000,
          useNativeDriver: true,
        }),
      ])
    ).start();
  }, []);

  const opacity = animatedValue.interpolate({
    inputRange: [0, 1],
    outputRange: [0.3, 0.7],
  });

  return (
    <Animated.View
      style={[
        {
          width,
          height,
          borderRadius,
          backgroundColor: theme.colors.backgroundSecondary,
          opacity,
        },
        style,
      ]}
    />
  );
};

// Article Card Skeleton
export const ArticleCardSkeleton = () => {
  const { theme } = useTheme();
  
  return (
    <View style={[styles.card, { backgroundColor: theme.colors.surface }]}>
      <SkeletonLoader width="100%" height={200} borderRadius={0} />
      <View style={styles.content}>
        <SkeletonLoader width="60%" height={16} style={{ marginBottom: 8 }} />
        <SkeletonLoader width="100%" height={20} style={{ marginBottom: 8 }} />
        <SkeletonLoader width="100%" height={40} style={{ marginBottom: 16 }} />
        <View style={styles.footer}>
          <SkeletonLoader width={80} height={16} />
          <SkeletonLoader width={60} height={16} />
        </View>
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  card: {
    marginHorizontal: 16,
    marginVertical: 8,
    borderRadius: 12,
    overflow: 'hidden',
  },
  content: {
    padding: 16,
  },
  footer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
  },
});

export default SkeletonLoader;
```

---

## 4. 📄 Bottom Sheet

```javascript
// src/components/BottomSheet.js
import React, { useEffect, useRef } from 'react';
import {
  View,
  Text,
  Modal,
  Animated,
  TouchableOpacity,
  StyleSheet,
  Dimensions,
  TouchableWithoutFeedback,
} from 'react-native';
import { useTheme } from '../contexts/ThemeContext';

const { height } = Dimensions.get('window');

const BottomSheet = ({ visible, onClose, title, children, height: customHeight }) => {
  const { theme } = useTheme();
  const slideAnim = useRef(new Animated.Value(height)).current;
  const fadeAnim = useRef(new Animated.Value(0)).current;

  useEffect(() => {
    if (visible) {
      Animated.parallel([
        Animated.timing(slideAnim, {
          toValue: 0,
          duration: 300,
          useNativeDriver: true,
        }),
        Animated.timing(fadeAnim, {
          toValue: 1,
          duration: 300,
          useNativeDriver: true,
        }),
      ]).start();
    } else {
      Animated.parallel([
        Animated.timing(slideAnim, {
          toValue: height,
          duration: 250,
          useNativeDriver: true,
        }),
        Animated.timing(fadeAnim, {
          toValue: 0,
          duration: 250,
          useNativeDriver: true,
        }),
      ]).start();
    }
  }, [visible]);

  const styles = createStyles(theme, customHeight);

  return (
    <Modal
      visible={visible}
      transparent
      animationType="none"
      onRequestClose={onClose}
    >
      <TouchableWithoutFeedback onPress={onClose}>
        <Animated.View style={[styles.overlay, { opacity: fadeAnim }]}>
          <TouchableWithoutFeedback>
            <Animated.View
              style={[
                styles.sheet,
                { transform: [{ translateY: slideAnim }] },
              ]}
            >
              {/* Handle */}
              <View style={styles.handle} />

              {/* Header */}
              {title && (
                <View style={styles.header}>
                  <Text style={styles.title}>{title}</Text>
                </View>
              )}

              {/* Content */}
              <View style={styles.content}>{children}</View>
            </Animated.View>
          </TouchableWithoutFeedback>
        </Animated.View>
      </TouchableWithoutFeedback>
    </Modal>
  );
};

const createStyles = (theme, customHeight) => StyleSheet.create({
  overlay: {
    flex: 1,
    backgroundColor: 'rgba(0, 0, 0, 0.5)',
    justifyContent: 'flex-end',
  },
  sheet: {
    backgroundColor: theme.colors.surface,
    borderTopLeftRadius: theme.borderRadius.xl,
    borderTopRightRadius: theme.borderRadius.xl,
    minHeight: customHeight || height * 0.5,
    maxHeight: height * 0.9,
    paddingBottom: 20,
  },
  handle: {
    width: 40,
    height: 4,
    backgroundColor: theme.colors.border,
    borderRadius: 2,
    alignSelf: 'center',
    marginTop: 12,
    marginBottom: 8,
  },
  header: {
    paddingHorizontal: theme.spacing.lg,
    paddingVertical: theme.spacing.md,
    borderBottomWidth: 1,
    borderBottomColor: theme.colors.divider,
  },
  title: {
    fontSize: theme.fontSize.xl,
    fontWeight: theme.fontWeight.bold,
    color: theme.colors.text,
  },
  content: {
    flex: 1,
    padding: theme.spacing.lg,
  },
});

export default BottomSheet;
```

---

## 5. 🔍 Modern Search Bar

```javascript
// src/components/SearchBar.js
import React, { useState } from 'react';
import {
  View,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  Animated,
} from 'react-native';
import Icon from 'react-native-vector-icons/MaterialCommunityIcons';
import { useTheme } from '../contexts/ThemeContext';
import { useTranslation } from 'react-i18next';

const SearchBar = ({ onSearch, onFocus, onBlur, placeholder }) => {
  const { theme } = useTheme();
  const { t, i18n } = useTranslation();
  const [searchText, setSearchText] = useState('');
  const [isFocused, setIsFocused] = useState(false);
  const isRTL = i18n.language === 'ar';

  const handleSearch = () => {
    if (onSearch && searchText.trim()) {
      onSearch(searchText.trim());
    }
  };

  const handleClear = () => {
    setSearchText('');
    if (onSearch) {
      onSearch('');
    }
  };

  const styles = createStyles(theme, isFocused, isRTL);

  return (
    <View style={styles.container}>
      <Icon
        name="magnify"
        size={24}
        color={isFocused ? theme.colors.primary : theme.colors.textTertiary}
        style={styles.searchIcon}
      />
      
      <TextInput
        style={styles.input}
        value={searchText}
        onChangeText={setSearchText}
        placeholder={placeholder || t('common.search')}
        placeholderTextColor={theme.colors.textTertiary}
        onFocus={() => {
          setIsFocused(true);
          onFocus?.();
        }}
        onBlur={() => {
          setIsFocused(false);
          onBlur?.();
        }}
        onSubmitEditing={handleSearch}
        returnKeyType="search"
        textAlign={isRTL ? 'right' : 'left'}
      />

      {searchText.length > 0 && (
        <TouchableOpacity onPress={handleClear} style={styles.clearButton}>
          <Icon name="close-circle" size={20} color={theme.colors.textTertiary} />
        </TouchableOpacity>
      )}
    </View>
  );
};

const createStyles = (theme, isFocused, isRTL) => StyleSheet.create({
  container: {
    flexDirection: isRTL ? 'row-reverse' : 'row',
    alignItems: 'center',
    backgroundColor: theme.colors.surface,
    borderRadius: theme.borderRadius.lg,
    paddingHorizontal: theme.spacing.md,
    marginHorizontal: theme.spacing.lg,
    marginVertical: theme.spacing.md,
    borderWidth: 2,
    borderColor: isFocused ? theme.colors.primary : theme.colors.border,
    ...theme.shadows.sm,
  },
  searchIcon: {
    marginRight: isRTL ? 0 : theme.spacing.sm,
    marginLeft: isRTL ? theme.spacing.sm : 0,
  },
  input: {
    flex: 1,
    fontSize: theme.fontSize.md,
    color: theme.colors.text,
    paddingVertical: theme.spacing.md,
    textAlign: isRTL ? 'right' : 'left',
  },
  clearButton: {
    padding: theme.spacing.sm,
  },
});

export default SearchBar;
```

---

## 6. 🏷️ Category Chips

```javascript
// src/components/CategoryChips.js
import React from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  ScrollView,
  StyleSheet,
} from 'react-native';
import Icon from 'react-native-vector-icons/MaterialCommunityIcons';
import { useTheme } from '../contexts/ThemeContext';
import { useTranslation } from 'react-i18next';

const CategoryChips = ({ categories, selectedId, onSelect }) => {
  const { theme } = useTheme();
  const { i18n } = useTranslation();
  const isRTL = i18n.language === 'ar';

  const styles = createStyles(theme, isRTL);

  return (
    <ScrollView
      horizontal
      showsHorizontalScrollIndicator={false}
      contentContainerStyle={styles.container}
    >
      {categories.map((category) => {
        const isSelected = category.id === selectedId;
        return (
          <TouchableOpacity
            key={category.id}
            style={[styles.chip, isSelected && styles.chipSelected]}
            onPress={() => onSelect(category.id)}
            activeOpacity={0.7}
          >
            {category.icon && (
              <Icon
                name={category.icon}
                size={18}
                color={isSelected ? theme.colors.textInverse : theme.colors.text}
                style={styles.icon}
              />
            )}
            <Text
              style={[styles.chipText, isSelected && styles.chipTextSelected]}
            >
              {isRTL ? category.name_ar : category.name_en}
            </Text>
          </TouchableOpacity>
        );
      })}
    </ScrollView>
  );
};

const createStyles = (theme, isRTL) => StyleSheet.create({
  container: {
    paddingHorizontal: theme.spacing.lg,
    paddingVertical: theme.spacing.md,
    gap: theme.spacing.sm,
  },
  chip: {
    flexDirection: isRTL ? 'row-reverse' : 'row',
    alignItems: 'center',
    backgroundColor: theme.colors.surface,
    paddingHorizontal: theme.spacing.lg,
    paddingVertical: theme.spacing.sm,
    borderRadius: theme.borderRadius.full,
    marginRight: theme.spacing.sm,
    borderWidth: 1,
    borderColor: theme.colors.border,
  },
  chipSelected: {
    backgroundColor: theme.colors.primary,
    borderColor: theme.colors.primary,
  },
  icon: {
    marginRight: isRTL ? 0 : theme.spacing.xs,
    marginLeft: isRTL ? theme.spacing.xs : 0,
  },
  chipText: {
    fontSize: theme.fontSize.sm,
    fontWeight: theme.fontWeight.medium,
    color: theme.colors.text,
  },
  chipTextSelected: {
    color: theme.colors.textInverse,
    fontWeight: theme.fontWeight.semibold,
  },
});

export default CategoryChips;
```

---

## 7. 📭 Empty States

```javascript
// src/components/EmptyState.js
import React from 'react';
import { View, Text, TouchableOpacity, StyleSheet } from 'react-native';
import Icon from 'react-native-vector-icons/MaterialCommunityIcons';
import { useTheme } from '../contexts/ThemeContext';
import { useTranslation } from 'react-i18next';

const EmptyState = ({
  icon = 'inbox-outline',
  title,
  description,
  actionText,
  onAction,
}) => {
  const { theme } = useTheme();
  const { i18n } = useTranslation();
  const isRTL = i18n.language === 'ar';

  const styles = createStyles(theme, isRTL);

  return (
    <View style={styles.container}>
      <View style={styles.iconContainer}>
        <Icon name={icon} size={64} color={theme.colors.textTertiary} />
      </View>
      
      <Text style={styles.title}>{title}</Text>
      
      {description && (
        <Text style={styles.description}>{description}</Text>
      )}

      {actionText && onAction && (
        <TouchableOpacity style={styles.button} onPress={onAction}>
          <Text style={styles.buttonText}>{actionText}</Text>
        </TouchableOpacity>
      )}
    </View>
  );
};

const createStyles = (theme, isRTL) => StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: theme.spacing.xl,
  },
  iconContainer: {
    width: 120,
    height: 120,
    borderRadius: theme.borderRadius.full,
    backgroundColor: theme.colors.backgroundSecondary,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: theme.spacing.lg,
  },
  title: {
    fontSize: theme.fontSize.xl,
    fontWeight: theme.fontWeight.bold,
    color: theme.colors.text,
    marginBottom: theme.spacing.sm,
    textAlign: 'center',
  },
  description: {
    fontSize: theme.fontSize.md,
    color: theme.colors.textSecondary,
    textAlign: 'center',
    marginBottom: theme.spacing.lg,
    lineHeight: 24,
  },
  button: {
    backgroundColor: theme.colors.primary,
    paddingHorizontal: theme.spacing.xl,
    paddingVertical: theme.spacing.md,
    borderRadius: theme.borderRadius.md,
  },
  buttonText: {
    color: theme.colors.textInverse,
    fontSize: theme.fontSize.md,
    fontWeight: theme.fontWeight.semibold,
  },
});

export default EmptyState;
```

---

## 8. 🔄 Pull to Refresh with Custom Indicator

```javascript
// src/components/PullToRefresh.js
import React from 'react';
import { RefreshControl } from 'react-native';
import { useTheme } from '../contexts/ThemeContext';

const PullToRefresh = ({ refreshing, onRefresh, ...props }) => {
  const { theme } = useTheme();

  return (
    <RefreshControl
      refreshing={refreshing}
      onRefresh={onRefresh}
      colors={[theme.colors.primary]} // Android
      tintColor={theme.colors.primary} // iOS
      progressBackgroundColor={theme.colors.surface} // Android
      {...props}
    />
  );
};

export default PullToRefresh;
```

---

## 🎯 Usage Examples

### **Home Screen Example:**

```javascript
// src/screens/HomeScreen.js
import React, { useState, useEffect } from 'react';
import { View, FlatList, StyleSheet } from 'react-native';
import ModernArticleCard from '../components/ModernArticleCard';
import BreakingNewsBanner from '../components/BreakingNewsBanner';
import SearchBar from '../components/SearchBar';
import CategoryChips from '../components/CategoryChips';
import EmptyState from '../components/EmptyState';
import { ArticleCardSkeleton } from '../components/SkeletonLoader';
import PullToRefresh from '../components/PullToRefresh';
import { useTheme } from '../contexts/ThemeContext';
import { useTranslation } from 'react-i18next';
import { articlesService } from '../api/services/articles';

const HomeScreen = ({ navigation }) => {
  const { theme } = useTheme();
  const { t } = useTranslation();
  const [articles, setArticles] = useState([]);
  const [breakingNews, setBreakingNews] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    try {
      const [articlesData, breakingData] = await Promise.all([
        articlesService.getLatest(),
        articlesService.getBreaking(),
      ]);
      setArticles(articlesData);
      setBreakingNews(breakingData);
    } catch (error) {
      console.error('Error loading data:', error);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  const handleRefresh = () => {
    setRefreshing(true);
    loadData();
  };

  const handleArticlePress = (articleId) => {
    navigation.navigate('ArticleDetail', { articleId });
  };

  if (loading) {
    return (
      <View style={[styles.container, { backgroundColor: theme.colors.background }]}>
        <ArticleCardSkeleton />
        <ArticleCardSkeleton />
        <ArticleCardSkeleton />
      </View>
    );
  }

  return (
    <View style={[styles.container, { backgroundColor: theme.colors.background }]}>
      <FlatList
        data={articles}
        keyExtractor={(item) => item.id.toString()}
        renderItem={({ item, index }) => (
          <ModernArticleCard
            article={item}
            onPress={handleArticlePress}
            variant={index === 0 ? 'featured' : 'default'}
          />
        )}
        ListHeaderComponent={
          <>
            <SearchBar onSearch={(query) => console.log(query)} />
            {breakingNews.length > 0 && (
              <BreakingNewsBanner
                articles={breakingNews}
                onPress={handleArticlePress}
              />
            )}
          </>
        }
        ListEmptyComponent={
          <EmptyState
            icon="newspaper-variant-outline"
            title={t('home.noArticles')}
            description={t('home.noArticlesDescription')}
          />
        }
        refreshControl={
          <PullToRefresh refreshing={refreshing} onRefresh={handleRefresh} />
        }
      />
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
});

export default HomeScreen;
```

---

**✨ Components جاهزة للاستخدام في تطبيقك!**




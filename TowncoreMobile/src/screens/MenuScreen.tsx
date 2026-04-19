import React, { useEffect, useRef } from 'react';
import {
  View, Text, StyleSheet, TouchableOpacity, ScrollView,
  Animated, Dimensions, Platform, StatusBar,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { useAuth } from '../contexts/AuthContext';
import { useTheme } from '../contexts/ThemeContext';
import { spacing, fontSize } from '../theme';

const { width: SCREEN_WIDTH } = Dimensions.get('window');
const COLUMN_COUNT = 3;
const GRID_GAP = 14;
const GRID_PADDING = 20;
const TILE_SIZE = (SCREEN_WIDTH - GRID_PADDING * 2 - GRID_GAP * (COLUMN_COUNT - 1)) / COLUMN_COUNT;

interface MenuItem {
  icon: string;
  label: string;
  screen: string;
  color: string;
  roles?: string[];
}

const MENU_ITEMS: MenuItem[] = [
  { icon: 'person-outline', label: 'Profile', screen: 'Profile', color: '#3b82f6' },
  { icon: 'card-outline', label: 'Subscription', screen: 'Subscription', color: '#16a34a' },
  { icon: 'folder-outline', label: 'My Files', screen: 'Files', color: '#8b5cf6', roles: ['client'] },
  { icon: 'checkbox-outline', label: 'Checklist', screen: 'Checklist', color: '#14b8a6', roles: ['client', 'freelancer'] },
  { icon: 'receipt-outline', label: 'Invoices', screen: 'Invoices', color: '#f59e0b', roles: ['admin', 'client'] },
  { icon: 'images-outline', label: 'Portfolio', screen: 'Portfolio', color: '#ec4899', roles: ['admin'] },
  { icon: 'newspaper-outline', label: 'Blog', screen: 'Blog', color: '#6366f1', roles: ['admin'] },
  { icon: 'settings-outline', label: 'Settings', screen: 'NotificationSettings', color: '#64748b' },
  { icon: 'notifications-outline', label: 'Alerts', screen: 'Notifications', color: '#dc2626' },
];

export default function MenuScreen({ navigation }: any) {
  const { user, logout } = useAuth();
  const { colors, isDark, toggleTheme } = useTheme();
  const insets = useSafeAreaInsets();

  // Filter items by role
  const visibleItems = MENU_ITEMS.filter(
    item => !item.roles || item.roles.includes(user?.role || '')
  );

  // Animations
  const headerAnim = useRef(new Animated.Value(0)).current;
  const tileAnims = useRef(visibleItems.map(() => new Animated.Value(0))).current;
  const footerAnim = useRef(new Animated.Value(0)).current;

  useEffect(() => {
    // Header fade in
    Animated.timing(headerAnim, {
      toValue: 1, duration: 300, useNativeDriver: true,
    }).start();

    // Staggered tile entrance
    const tileAnimations = tileAnims.map((anim, i) =>
      Animated.spring(anim, {
        toValue: 1, friction: 7, tension: 50, delay: 80 + i * 60,
        useNativeDriver: true,
      })
    );
    Animated.stagger(60, tileAnimations).start();

    // Footer
    Animated.timing(footerAnim, {
      toValue: 1, duration: 400, delay: 300 + visibleItems.length * 60,
      useNativeDriver: true,
    }).start();
  }, []);

  const initials = user?.name?.split(' ').map((w: string) => w[0]).join('').toUpperCase().slice(0, 2) || '?';

  function handlePress(screen: string) {
    navigation.navigate(screen);
  }

  return (
    <View style={[styles.container, { backgroundColor: colors.background }]}>
      <StatusBar barStyle={isDark ? 'light-content' : 'dark-content'} />

      {/* Header with user info */}
      <Animated.View style={[
        styles.header,
        { backgroundColor: colors.primary, paddingTop: insets.top + 12, opacity: headerAnim },
      ]}>
        <View style={styles.headerContent}>
          <View style={styles.avatarCircle}>
            <Text style={styles.avatarText}>{initials}</Text>
          </View>
          <View style={styles.userInfo}>
            <Text style={styles.userName}>{user?.name}</Text>
            <Text style={styles.userEmail}>{user?.email}</Text>
            <View style={styles.roleBadge}>
              <Text style={styles.roleText}>{user?.role?.toUpperCase()}</Text>
            </View>
          </View>
        </View>
      </Animated.View>

      <ScrollView
        style={styles.scrollArea}
        contentContainerStyle={styles.scrollContent}
        showsVerticalScrollIndicator={false}
      >
        {/* Grid */}
        <View style={styles.grid}>
          {visibleItems.map((item, index) => {
            const scale = tileAnims[index]?.interpolate({
              inputRange: [0, 1],
              outputRange: [0.5, 1],
            }) || 1;
            const opacity = tileAnims[index] || 1;

            return (
              <Animated.View
                key={item.screen}
                style={[styles.tileWrapper, { opacity, transform: [{ scale: scale as any }] }]}
              >
                <TouchableOpacity
                  style={[styles.tile, { backgroundColor: colors.card }]}
                  onPress={() => handlePress(item.screen)}
                  activeOpacity={0.7}
                >
                  <View style={[styles.tileIconBg, { backgroundColor: item.color + '15' }]}>
                    <Ionicons name={item.icon as any} size={26} color={item.color} />
                  </View>
                  <Text style={[styles.tileLabel, { color: colors.text }]}>{item.label}</Text>
                </TouchableOpacity>
              </Animated.View>
            );
          })}
        </View>

        {/* Theme Toggle Card */}
        <Animated.View style={{ opacity: footerAnim, transform: [{ translateY: footerAnim.interpolate({ inputRange: [0, 1], outputRange: [20, 0] }) }] }}>
          <TouchableOpacity
            style={[styles.themeCard, { backgroundColor: colors.card }]}
            onPress={toggleTheme}
            activeOpacity={0.7}
          >
            <View style={[styles.themeIconBg, { backgroundColor: (isDark ? '#6366f1' : '#f59e0b') + '15' }]}>
              <Ionicons name={isDark ? 'moon' : 'sunny'} size={22} color={isDark ? '#6366f1' : '#f59e0b'} />
            </View>
            <Text style={[styles.themeLabel, { color: colors.text }]}>
              {isDark ? 'Dark Mode' : 'Light Mode'}
            </Text>
            <View style={[styles.toggleTrack, { backgroundColor: isDark ? colors.primary : colors.border }]}>
              <View style={[styles.toggleThumb, isDark && { transform: [{ translateX: 18 }] }]} />
            </View>
          </TouchableOpacity>
        </Animated.View>

        {/* Logout Button */}
        <Animated.View style={{ opacity: footerAnim, transform: [{ translateY: footerAnim.interpolate({ inputRange: [0, 1], outputRange: [20, 0] }) }] }}>
          <TouchableOpacity
            style={[styles.logoutBtn, { backgroundColor: '#dc262612' }]}
            onPress={logout}
            activeOpacity={0.7}
          >
            <Ionicons name="log-out-outline" size={22} color="#dc2626" />
            <Text style={styles.logoutText}>Logout</Text>
          </TouchableOpacity>
        </Animated.View>

        <View style={{ height: 100 }} />
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1 },

  // Header
  header: { paddingBottom: 20, paddingHorizontal: GRID_PADDING },
  headerContent: { flexDirection: 'row', alignItems: 'center' },
  avatarCircle: {
    width: 52, height: 52, borderRadius: 26,
    backgroundColor: 'rgba(255,255,255,0.25)',
    justifyContent: 'center', alignItems: 'center',
    borderWidth: 2, borderColor: 'rgba(255,255,255,0.4)',
  },
  avatarText: { fontSize: 20, fontWeight: '800', color: '#fff' },
  userInfo: { flex: 1, marginLeft: spacing.md },
  userName: { fontSize: 18, fontWeight: '700', color: '#fff' },
  userEmail: { fontSize: 12, color: 'rgba(255,255,255,0.7)', marginTop: 1 },
  roleBadge: {
    alignSelf: 'flex-start',
    backgroundColor: 'rgba(255,255,255,0.2)',
    paddingHorizontal: 8, paddingVertical: 2, borderRadius: 6, marginTop: 4,
  },
  roleText: { fontSize: 10, fontWeight: '700', color: '#fff', letterSpacing: 1 },

  // Scroll
  scrollArea: { flex: 1 },
  scrollContent: { paddingTop: 20, paddingHorizontal: GRID_PADDING },

  // Grid
  grid: {
    flexDirection: 'row', flexWrap: 'wrap',
    gap: GRID_GAP,
  },
  tileWrapper: { width: TILE_SIZE, height: TILE_SIZE },
  tile: {
    flex: 1, borderRadius: 18, alignItems: 'center', justifyContent: 'center',
    shadowColor: '#000', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.06, shadowRadius: 8, elevation: 3,
  },
  tileIconBg: {
    width: 50, height: 50, borderRadius: 16, justifyContent: 'center', alignItems: 'center', marginBottom: 8,
  },
  tileLabel: { fontSize: 12, fontWeight: '600' },

  // Theme card
  themeCard: {
    flexDirection: 'row', alignItems: 'center', borderRadius: 16, padding: 16, marginTop: 20,
    shadowColor: '#000', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.04, shadowRadius: 4, elevation: 1,
  },
  themeIconBg: { width: 40, height: 40, borderRadius: 12, justifyContent: 'center', alignItems: 'center' },
  themeLabel: { flex: 1, fontSize: fontSize.md, fontWeight: '600', marginLeft: 12 },
  toggleTrack: { width: 40, height: 22, borderRadius: 11, justifyContent: 'center', paddingHorizontal: 2 },
  toggleThumb: {
    width: 18, height: 18, borderRadius: 9, backgroundColor: '#fff',
    shadowColor: '#000', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.15, shadowRadius: 2, elevation: 2,
  },

  // Logout
  logoutBtn: {
    flexDirection: 'row', alignItems: 'center', justifyContent: 'center',
    borderRadius: 16, padding: 16, marginTop: 12, gap: 8,
  },
  logoutText: { fontSize: fontSize.md, fontWeight: '700', color: '#dc2626' },
});

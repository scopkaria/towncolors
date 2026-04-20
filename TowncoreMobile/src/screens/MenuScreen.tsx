import React, { useEffect, useRef } from 'react';
import {
  View, Text, StyleSheet, TouchableOpacity, ScrollView,
  Animated, Dimensions, StatusBar, Image,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useAuth } from '../contexts/AuthContext';
import { useTheme } from '../contexts/ThemeContext';
import { useNotifications } from '../hooks/useNotifications';
import { spacing, fontSize } from '../theme';
import { TAB_BAR_TOTAL_HEIGHT } from '../constants/layout';
import ScreenHeader from '../components/ScreenHeader';

const { width: SCREEN_WIDTH } = Dimensions.get('window');
const COLUMN_COUNT = 3;
const GRID_GAP = 14;
const GRID_PADDING = 20;
const TILE_SIZE = (SCREEN_WIDTH - GRID_PADDING * 2 - GRID_GAP * (COLUMN_COUNT - 1)) / COLUMN_COUNT;

interface MenuItem {
  icon: string;
  label: string;
  screen: string;
  roles: string[];
}

// ── Strict role-based menu definitions ─────────────────────────
const ADMIN_MENU: MenuItem[] = [
  { icon: 'person-outline',        label: 'Profile',       screen: 'Profile',              roles: ['admin'] },
  { icon: 'people-outline',        label: 'Users',         screen: 'Users',                roles: ['admin'] },
  { icon: 'receipt-outline',       label: 'Invoices',      screen: 'Invoices',             roles: ['admin'] },
  { icon: 'folder-outline',        label: 'Leads',         screen: 'Projects',             roles: ['admin'] },
  { icon: 'images-outline',        label: 'Portfolio',     screen: 'PortfolioManage',      roles: ['admin'] },
  { icon: 'newspaper-outline',     label: 'Blog',          screen: 'BlogManage',           roles: ['admin'] },
  { icon: 'headset-outline',       label: 'Live Chat',     screen: 'LiveChat',             roles: ['admin'] },
  { icon: 'settings-outline',      label: 'Settings',      screen: 'NotificationSettings', roles: ['admin'] },
];

const CLIENT_MENU: MenuItem[] = [
  { icon: 'person-outline',        label: 'Profile',       screen: 'Profile',              roles: ['client'] },
  { icon: 'folder-outline',        label: 'My Files',      screen: 'Files',                roles: ['client'] },
  { icon: 'receipt-outline',       label: 'Invoices',      screen: 'Invoices',             roles: ['client'] },
  { icon: 'shield-checkmark-outline', label: 'My Plan',    screen: 'Subscription',         roles: ['client'] },
  { icon: 'checkbox-outline',      label: 'Checklist',     screen: 'Checklist',            roles: ['client'] },
  { icon: 'settings-outline',      label: 'Settings',      screen: 'NotificationSettings', roles: ['client'] },
];

const FREELANCER_MENU: MenuItem[] = [
  { icon: 'person-outline',        label: 'Profile',       screen: 'Profile',              roles: ['freelancer'] },
  { icon: 'checkbox-outline',      label: 'Checklist',     screen: 'Checklist',            roles: ['freelancer'] },
  { icon: 'receipt-outline',       label: 'Earnings',      screen: 'Invoices',             roles: ['freelancer'] },
  { icon: 'images-outline',        label: 'Portfolio',     screen: 'Portfolio',             roles: ['freelancer'] },
  { icon: 'settings-outline',      label: 'Settings',      screen: 'NotificationSettings', roles: ['freelancer'] },
];

function getMenuForRole(role: string): MenuItem[] {
  switch (role) {
    case 'admin': return ADMIN_MENU;
    case 'freelancer': return FREELANCER_MENU;
    default: return CLIENT_MENU;
  }
}

export default function MenuScreen({ navigation }: any) {
  const { user, logout } = useAuth();
  const { colors, isDark, toggleTheme } = useTheme();
  const { unreadCount } = useNotifications();

  const visibleItems = getMenuForRole(user?.role || 'client');

  // Animations
  const tileAnims = useRef(visibleItems.map(() => new Animated.Value(0))).current;
  const footerAnim = useRef(new Animated.Value(0)).current;

  useEffect(() => {
    const tileAnimations = tileAnims.map((anim, i) =>
      Animated.spring(anim, {
        toValue: 1, friction: 7, tension: 50, delay: 60 + i * 50,
        useNativeDriver: true,
      })
    );
    Animated.stagger(50, tileAnimations).start();

    Animated.timing(footerAnim, {
      toValue: 1, duration: 400, delay: 200 + visibleItems.length * 50,
      useNativeDriver: true,
    }).start();
  }, []);

  const initials = user?.name?.split(' ').map((w: string) => w[0]).join('').toUpperCase().slice(0, 2) || '?';

  return (
    <View style={[styles.container, { backgroundColor: colors.background }]}>
      <StatusBar barStyle="light-content" />

      <ScreenHeader
        title="Menu"
        rightIcon="notifications-outline"
        onRight={() => navigation.navigate('Notifications')}
        rightBadge={unreadCount}
      />

      <ScrollView
        style={styles.scrollArea}
        contentContainerStyle={styles.scrollContent}
        showsVerticalScrollIndicator={false}
      >
        {/* User Card */}
        <View style={[styles.userCard, { backgroundColor: colors.card }]}>
          {user?.profile_image_url ? (
            <Image source={{ uri: user.profile_image_url }} style={styles.avatarImage} />
          ) : (
            <View style={[styles.avatarCircle, { backgroundColor: colors.primary + '20' }]}>
              <Text style={[styles.avatarText, { color: colors.primary }]}>{initials}</Text>
            </View>
          )}
          <View style={styles.userInfo}>
            <Text style={[styles.userName, { color: colors.text }]}>{user?.name}</Text>
            <Text style={[styles.userEmail, { color: colors.textLight }]}>{user?.email}</Text>
          </View>
          <View style={[styles.roleBadge, { backgroundColor: colors.primary + '15' }]}>
            <Text style={[styles.roleText, { color: colors.primary }]}>{user?.role?.toUpperCase()}</Text>
          </View>
        </View>

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
                key={item.screen + item.label}
                style={[styles.tileWrapper, { opacity, transform: [{ scale: scale as any }] }]}
              >
                <TouchableOpacity
                  style={[styles.tile, { backgroundColor: colors.card }]}
                  onPress={() => navigation.navigate(item.screen)}
                  activeOpacity={0.7}
                >
                  <View style={[styles.tileIconBg, { backgroundColor: colors.primary + '12' }]}>
                    <Ionicons name={item.icon as any} size={26} color={colors.primary} />
                  </View>
                  <Text style={[styles.tileLabel, { color: colors.text }]}>{item.label}</Text>
                </TouchableOpacity>
              </Animated.View>
            );
          })}
        </View>

        {/* Theme Toggle */}
        <Animated.View style={{ opacity: footerAnim, transform: [{ translateY: footerAnim.interpolate({ inputRange: [0, 1], outputRange: [20, 0] }) }] }}>
          <TouchableOpacity
            style={[styles.themeCard, { backgroundColor: colors.card }]}
            onPress={toggleTheme}
            activeOpacity={0.7}
          >
            <View style={[styles.themeIconBg, { backgroundColor: colors.primary + '12' }]}>
              <Ionicons name={isDark ? 'moon' : 'sunny'} size={22} color={colors.primary} />
            </View>
            <Text style={[styles.themeLabel, { color: colors.text }]}>
              {isDark ? 'Dark Mode' : 'Light Mode'}
            </Text>
            <View style={[styles.toggleTrack, { backgroundColor: isDark ? colors.primary : colors.border }]}>
              <View style={[styles.toggleThumb, isDark && { transform: [{ translateX: 18 }] }]} />
            </View>
          </TouchableOpacity>
        </Animated.View>

        {/* Logout */}
        <Animated.View style={{ opacity: footerAnim, transform: [{ translateY: footerAnim.interpolate({ inputRange: [0, 1], outputRange: [20, 0] }) }] }}>
          <TouchableOpacity
            style={[styles.logoutBtn, { backgroundColor: colors.danger + '10' }]}
            onPress={logout}
            activeOpacity={0.7}
          >
            <Ionicons name="log-out-outline" size={22} color={colors.danger} />
            <Text style={[styles.logoutText, { color: colors.danger }]}>Logout</Text>
          </TouchableOpacity>
        </Animated.View>

        <View style={{ height: TAB_BAR_TOTAL_HEIGHT + 20 }} />
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1 },

  // User card
  userCard: {
    flexDirection: 'row', alignItems: 'center', borderRadius: 16,
    padding: spacing.md, marginBottom: spacing.lg,
    shadowColor: '#000', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.04, shadowRadius: 4, elevation: 1,
  },
  avatarCircle: {
    width: 48, height: 48, borderRadius: 24,
    justifyContent: 'center', alignItems: 'center',
  },
  avatarImage: { width: 48, height: 48, borderRadius: 24 },
  avatarText: { fontSize: 18, fontWeight: '800' },
  userInfo: { flex: 1, marginLeft: spacing.md },
  userName: { fontSize: 16, fontWeight: '700' },
  userEmail: { fontSize: 12, marginTop: 1 },
  roleBadge: {
    paddingHorizontal: 10, paddingVertical: 4, borderRadius: 8,
  },
  roleText: { fontSize: 10, fontWeight: '700', letterSpacing: 1 },

  // Scroll
  scrollArea: { flex: 1 },
  scrollContent: { paddingTop: spacing.lg, paddingHorizontal: GRID_PADDING },

  // Grid
  grid: { flexDirection: 'row', flexWrap: 'wrap', gap: GRID_GAP },
  tileWrapper: { width: TILE_SIZE, height: TILE_SIZE },
  tile: {
    flex: 1, borderRadius: 18, alignItems: 'center', justifyContent: 'center',
    shadowColor: '#000', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.04, shadowRadius: 6, elevation: 2,
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
  logoutText: { fontSize: fontSize.md, fontWeight: '700' },
});

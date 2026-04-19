import React, { useEffect, useState, useCallback, useRef } from 'react';
import {
  View, Text, StyleSheet, ScrollView, RefreshControl,
  TouchableOpacity, ActivityIndicator, Animated, Dimensions,
  Modal, Platform, StatusBar,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useAuth } from '../contexts/AuthContext';
import { useTheme } from '../contexts/ThemeContext';
import { useBranding } from '../contexts/BrandingContext';
import { dashboardApi } from '../api';
import { spacing, fontSize, statusColors } from '../theme';

const { width: SCREEN_WIDTH } = Dimensions.get('window');
const DRAWER_WIDTH = SCREEN_WIDTH * 0.78;

export default function DashboardScreen({ navigation }: any) {
  const { user, logout } = useAuth();
  const { colors, isDark, toggleTheme } = useTheme();
  const branding = useBranding();
  const [data, setData] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  // Drawer state
  const [drawerOpen, setDrawerOpen] = useState(false);
  const drawerAnim = useRef(new Animated.Value(-DRAWER_WIDTH)).current;

  // Profile dropdown
  const [profileOpen, setProfileOpen] = useState(false);

  const loadDashboard = useCallback(async () => {
    try {
      const result = await dashboardApi.get();
      setData(result);
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => { loadDashboard(); }, [loadDashboard]);

  const onRefresh = () => { setRefreshing(true); loadDashboard(); };

  // Drawer animations
  function openDrawer() {
    setDrawerOpen(true);
    Animated.spring(drawerAnim, { toValue: 0, useNativeDriver: true, friction: 8 }).start();
  }
  function closeDrawer() {
    Animated.timing(drawerAnim, { toValue: -DRAWER_WIDTH, duration: 250, useNativeDriver: true }).start(() => {
      setDrawerOpen(false);
    });
  }

  const initials = user?.name?.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2) || '?';
  const stats = data?.stats || {};
  const greeting = getGreeting();

  if (loading) {
    return (
      <View style={[styles.center, { backgroundColor: colors.background }]}>
        <ActivityIndicator size="large" color={colors.primary} />
      </View>
    );
  }

  return (
    <View style={[styles.root, { backgroundColor: colors.background }]}>
      <StatusBar
        barStyle={isDark ? 'light-content' : 'dark-content'}
        backgroundColor={colors.primary}
      />

      {/* ── Custom Header ─────────────────────────────── */}
      <View style={[styles.headerBar, { backgroundColor: colors.primary }]}>
        <TouchableOpacity onPress={openDrawer} style={styles.headerBtn} activeOpacity={0.7}>
          <Ionicons name="menu" size={26} color="#fff" />
        </TouchableOpacity>

        <View style={styles.headerCenter}>
          <Text style={styles.headerTitle}>{branding.appName}</Text>
        </View>

        <TouchableOpacity onPress={() => setProfileOpen(true)} style={styles.avatarBtn} activeOpacity={0.7}>
          <View style={styles.headerAvatar}>
            <Text style={styles.headerAvatarText}>{initials}</Text>
          </View>
        </TouchableOpacity>
      </View>

      {/* ── Profile Dropdown Modal ────────────────────── */}
      <Modal visible={profileOpen} transparent animationType="fade" onRequestClose={() => setProfileOpen(false)}>
        <TouchableOpacity style={styles.dropdownOverlay} activeOpacity={1} onPress={() => setProfileOpen(false)}>
          <View style={[styles.profileDropdown, { backgroundColor: colors.card, borderColor: colors.border }]}>
            {/* User info */}
            <View style={styles.profileHeader}>
              <View style={[styles.profileAvatar, { backgroundColor: colors.primary }]}>
                <Text style={styles.profileAvatarText}>{initials}</Text>
              </View>
              <View style={{ flex: 1, marginLeft: spacing.md }}>
                <Text style={[styles.profileName, { color: colors.text }]}>{user?.name}</Text>
                <Text style={[styles.profileEmail, { color: colors.textSecondary }]}>{user?.email}</Text>
                <View style={[styles.profileRoleBadge, { backgroundColor: colors.primary + '18' }]}>
                  <Text style={[styles.profileRoleText, { color: colors.primary }]}>{user?.role}</Text>
                </View>
              </View>
            </View>

            <View style={[styles.profileDivider, { backgroundColor: colors.border }]} />

            {/* Theme toggle */}
            <TouchableOpacity
              style={styles.profileMenuItem}
              onPress={() => { toggleTheme(); }}
              activeOpacity={0.6}
            >
              <Ionicons name={isDark ? 'moon' : 'sunny'} size={20} color={colors.primary} />
              <Text style={[styles.profileMenuText, { color: colors.text }]}>
                {isDark ? 'Dark Mode' : 'Light Mode'}
              </Text>
              <View style={[styles.themeToggleTrack, { backgroundColor: isDark ? colors.primary : colors.border }]}>
                <View style={[styles.themeToggleThumb, isDark && { transform: [{ translateX: 18 }] }]} />
              </View>
            </TouchableOpacity>

            {/* Profile & Settings */}
            <TouchableOpacity
              style={styles.profileMenuItem}
              onPress={() => { setProfileOpen(false); navigation.navigate('Profile'); }}
              activeOpacity={0.6}
            >
              <Ionicons name="person-outline" size={20} color={colors.textSecondary} />
              <Text style={[styles.profileMenuText, { color: colors.text }]}>Profile & Settings</Text>
            </TouchableOpacity>

            {/* Notifications */}
            <TouchableOpacity
              style={styles.profileMenuItem}
              onPress={() => { setProfileOpen(false); navigation.navigate('Notifications'); }}
              activeOpacity={0.6}
            >
              <Ionicons name="notifications-outline" size={20} color={colors.textSecondary} />
              <Text style={[styles.profileMenuText, { color: colors.text }]}>Notifications</Text>
            </TouchableOpacity>

            <View style={[styles.profileDivider, { backgroundColor: colors.border }]} />

            {/* Logout */}
            <TouchableOpacity
              style={styles.profileMenuItem}
              onPress={() => { setProfileOpen(false); logout(); }}
              activeOpacity={0.6}
            >
              <Ionicons name="log-out-outline" size={20} color={colors.danger} />
              <Text style={[styles.profileMenuText, { color: colors.danger }]}>Logout</Text>
            </TouchableOpacity>
          </View>
        </TouchableOpacity>
      </Modal>

      {/* ── Off-Canvas Drawer ─────────────────────────── */}
      {drawerOpen && (
        <View style={StyleSheet.absoluteFill}>
          <TouchableOpacity
            style={[styles.drawerOverlay]}
            activeOpacity={1}
            onPress={closeDrawer}
          />
          <Animated.View style={[styles.drawer, { backgroundColor: colors.card, transform: [{ translateX: drawerAnim }] }]}>
            {/* Drawer header */}
            <View style={[styles.drawerHeader, { backgroundColor: colors.primary }]}>
              <View style={styles.drawerAvatarCircle}>
                <Text style={styles.drawerAvatarText}>{initials}</Text>
              </View>
              <Text style={styles.drawerName}>{user?.name}</Text>
              <Text style={styles.drawerEmail}>{user?.email}</Text>
            </View>

            <ScrollView style={styles.drawerMenu}>
              <DrawerItem icon="grid-outline" label="Dashboard" onPress={() => { closeDrawer(); }} colors={colors} active />
              <DrawerItem icon="folder-outline" label="Projects" onPress={() => { closeDrawer(); navigation.navigate('Projects'); }} colors={colors} />
              <DrawerItem icon="chatbubbles-outline" label="Messages" onPress={() => { closeDrawer(); navigation.navigate('Messages'); }} colors={colors} />
              {user?.role === 'admin' && (
                <DrawerItem icon="chatbubble-ellipses-outline" label="Live Chat" onPress={() => { closeDrawer(); navigation.navigate('LiveChat'); }} colors={colors} />
              )}
              {(user?.role === 'admin' || user?.role === 'client') && (
                <DrawerItem icon="receipt-outline" label="Invoices" onPress={() => { closeDrawer(); navigation.navigate('Invoices'); }} colors={colors} />
              )}
              {user?.role === 'admin' && (
                <>
                  <DrawerItem icon="images-outline" label="Portfolio" onPress={() => { closeDrawer(); navigation.navigate('Portfolio'); }} colors={colors} />
                  <DrawerItem icon="newspaper-outline" label="Blog" onPress={() => { closeDrawer(); navigation.navigate('Blog'); }} colors={colors} />
                </>
              )}

              <View style={[styles.drawerDivider, { backgroundColor: colors.border }]} />

              <DrawerItem icon="notifications-outline" label="Notifications" onPress={() => { closeDrawer(); navigation.navigate('Notifications'); }} colors={colors} />
              <DrawerItem icon="settings-outline" label="Notification Settings" onPress={() => { closeDrawer(); navigation.navigate('NotificationSettings'); }} colors={colors} />
              <DrawerItem icon="person-outline" label="Profile & Settings" onPress={() => { closeDrawer(); navigation.navigate('Profile'); }} colors={colors} />

              <View style={[styles.drawerDivider, { backgroundColor: colors.border }]} />

              {/* Theme toggle in drawer */}
              <TouchableOpacity style={styles.drawerItem} onPress={toggleTheme} activeOpacity={0.6}>
                <Ionicons name={isDark ? 'moon' : 'sunny'} size={22} color={colors.primary} />
                <Text style={[styles.drawerLabel, { color: colors.text }]}>{isDark ? 'Dark Mode' : 'Light Mode'}</Text>
              </TouchableOpacity>
            </ScrollView>
          </Animated.View>
        </View>
      )}

      {/* ── Main Content ──────────────────────────────── */}
      <ScrollView
        style={{ flex: 1 }}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor={colors.primary} />}
        showsVerticalScrollIndicator={false}
      >
        {/* Greeting Card */}
        <View style={[styles.greetingCard, { backgroundColor: colors.primary }]}>
          <View style={styles.greetingContent}>
            <Text style={styles.greetingTime}>{greeting}</Text>
            <Text style={styles.greetingName}>{user?.name?.split(' ')[0]}</Text>
            <Text style={styles.greetingDesc}>
              {user?.role === 'admin' ? 'Manage your platform today.' : user?.role === 'client' ? 'Check your project updates.' : 'See your assigned work.'}
            </Text>
          </View>
          <View style={styles.greetingIcon}>
            <Ionicons name={greeting.includes('Morning') ? 'sunny' : greeting.includes('Afternoon') ? 'partly-sunny' : 'moon'} size={48} color="rgba(255,255,255,0.3)" />
          </View>
        </View>

        {/* Quick Actions — role-based */}
        <View style={styles.quickActions}>
          {user?.role === 'admin' && (
            <>
              <QuickAction icon="folder-open" label="Projects" color={colors.primary} bgColor={colors.primary + '15'} textColor={colors.text} onPress={() => navigation.navigate('Projects')} />
              <QuickAction icon="chatbubbles" label="Messages" color="#3b82f6" bgColor="#3b82f615" textColor={colors.text} onPress={() => navigation.navigate('Messages')} />
              <QuickAction icon="chatbubble-ellipses" label="Live Chat" color="#8b5cf6" bgColor="#8b5cf615" textColor={colors.text} onPress={() => navigation.navigate('LiveChat')} />
              <QuickAction icon="receipt" label="Invoices" color="#16a34a" bgColor="#16a34a15" textColor={colors.text} onPress={() => navigation.navigate('Invoices')} />
            </>
          )}
          {user?.role === 'client' && (
            <>
              <QuickAction icon="add-circle" label="New Project" color={colors.primary} bgColor={colors.primary + '15'} textColor={colors.text} onPress={() => navigation.navigate('Projects', { screen: 'CreateProject' })} />
              <QuickAction icon="chatbubbles" label="Messages" color="#3b82f6" bgColor="#3b82f615" textColor={colors.text} onPress={() => navigation.navigate('Messages')} />
              <QuickAction icon="receipt" label="Invoices" color="#16a34a" bgColor="#16a34a15" textColor={colors.text} onPress={() => navigation.navigate('Invoices')} />
              <QuickAction icon="notifications" label="Alerts" color="#f59e0b" bgColor="#f59e0b15" textColor={colors.text} onPress={() => navigation.navigate('Notifications')} />
            </>
          )}
          {user?.role === 'freelancer' && (
            <>
              <QuickAction icon="folder-open" label="Projects" color={colors.primary} bgColor={colors.primary + '15'} textColor={colors.text} onPress={() => navigation.navigate('Projects')} />
              <QuickAction icon="chatbubbles" label="Messages" color="#3b82f6" bgColor="#3b82f615" textColor={colors.text} onPress={() => navigation.navigate('Messages')} />
              <QuickAction icon="notifications" label="Alerts" color="#f59e0b" bgColor="#f59e0b15" textColor={colors.text} onPress={() => navigation.navigate('Notifications')} />
              <QuickAction icon="person" label="Profile" color="#8b5cf6" bgColor="#8b5cf615" textColor={colors.text} onPress={() => navigation.navigate('Profile')} />
            </>
          )}
        </View>

        {/* Stats Grid */}
        <View style={styles.sectionPadded}>
          <Text style={[styles.sectionTitle, { color: colors.text }]}>Overview</Text>
          <View style={styles.statsGrid}>
            {user?.role === 'admin' && (
              <>
                <StatCard icon="folder-open" label="Total Projects" value={stats.total_projects} color="#3b82f6" cardBg={colors.card} textColor={colors.text} subColor={colors.textSecondary} />
                <StatCard icon="time" label="Pending" value={stats.pending_projects} color="#f59e0b" cardBg={colors.card} textColor={colors.text} subColor={colors.textSecondary} />
                <StatCard icon="play-circle" label="Active" value={stats.active_projects} color="#8b5cf6" cardBg={colors.card} textColor={colors.text} subColor={colors.textSecondary} />
                <StatCard icon="checkmark-circle" label="Completed" value={stats.completed_projects} color="#16a34a" cardBg={colors.card} textColor={colors.text} subColor={colors.textSecondary} />
                <StatCard icon="cash" label="Revenue" value={`$${(stats.total_revenue || 0).toLocaleString()}`} color="#16a34a" cardBg={colors.card} textColor={colors.text} subColor={colors.textSecondary} />
                <StatCard icon="hourglass" label="Pending Rev." value={`$${(stats.pending_revenue || 0).toLocaleString()}`} color="#f59e0b" cardBg={colors.card} textColor={colors.text} subColor={colors.textSecondary} />
              </>
            )}
            {user?.role === 'client' && (
              <>
                <StatCard icon="folder-open" label="Projects" value={stats.total_projects} color="#3b82f6" cardBg={colors.card} textColor={colors.text} subColor={colors.textSecondary} />
                <StatCard icon="play-circle" label="Active" value={stats.active_projects} color="#8b5cf6" cardBg={colors.card} textColor={colors.text} subColor={colors.textSecondary} />
                <StatCard icon="checkmark-circle" label="Completed" value={stats.completed_projects} color="#16a34a" cardBg={colors.card} textColor={colors.text} subColor={colors.textSecondary} />
                <StatCard icon="chatbubbles" label="Unread" value={stats.unread_messages} color="#dc2626" cardBg={colors.card} textColor={colors.text} subColor={colors.textSecondary} />
                <StatCard icon="receipt" label="Invoiced" value={`$${(stats.total_invoiced || 0).toLocaleString()}`} color="#f59e0b" cardBg={colors.card} textColor={colors.text} subColor={colors.textSecondary} />
                <StatCard icon="cash" label="Paid" value={`$${(stats.total_paid || 0).toLocaleString()}`} color="#16a34a" cardBg={colors.card} textColor={colors.text} subColor={colors.textSecondary} />
              </>
            )}
            {user?.role === 'freelancer' && (
              <>
                <StatCard icon="play-circle" label="Active" value={stats.active_projects} color="#8b5cf6" cardBg={colors.card} textColor={colors.text} subColor={colors.textSecondary} />
                <StatCard icon="checkmark-circle" label="Completed" value={stats.completed_projects} color="#16a34a" cardBg={colors.card} textColor={colors.text} subColor={colors.textSecondary} />
                <StatCard icon="cash" label="Earnings" value={`$${(stats.total_earnings || 0).toLocaleString()}`} color="#16a34a" cardBg={colors.card} textColor={colors.text} subColor={colors.textSecondary} />
                <StatCard icon="hourglass" label="Pending" value={`$${(stats.pending_payments || 0).toLocaleString()}`} color="#f59e0b" cardBg={colors.card} textColor={colors.text} subColor={colors.textSecondary} />
              </>
            )}
          </View>
        </View>

        {/* Recent Projects */}
        <View style={styles.sectionPadded}>
          <View style={styles.sectionHeader}>
            <Text style={[styles.sectionTitle, { color: colors.text }]}>Recent Projects</Text>
            <TouchableOpacity onPress={() => navigation.navigate('Projects')}>
              <Text style={[styles.seeAll, { color: colors.primary }]}>See All</Text>
            </TouchableOpacity>
          </View>
          {(data?.recent_projects || []).length === 0 && (
            <View style={[styles.emptyCard, { backgroundColor: colors.card }]}>
              <Ionicons name="folder-open-outline" size={40} color={colors.textLight} />
              <Text style={[styles.emptyText, { color: colors.textLight }]}>No projects yet</Text>
            </View>
          )}
          {(data?.recent_projects || []).map((project: any) => (
            <TouchableOpacity
              key={project.id}
              style={[styles.projectCard, { backgroundColor: colors.card }]}
              onPress={() => navigation.navigate('Projects', { screen: 'ProjectDetail', params: { id: project.id } })}
              activeOpacity={0.7}
            >
              <View style={[styles.projectIconCircle, { backgroundColor: (statusColors[project.status] || '#94a3b8') + '18' }]}>
                <Ionicons name="document-text" size={20} color={statusColors[project.status] || '#94a3b8'} />
              </View>
              <View style={styles.projectInfo}>
                <Text style={[styles.projectTitle, { color: colors.text }]} numberOfLines={1}>{project.title}</Text>
                <Text style={[styles.projectMeta, { color: colors.textSecondary }]}>
                  {project.client?.name || project.freelancer?.name || '—'}
                </Text>
              </View>
              <View style={[styles.statusBadge, { backgroundColor: (statusColors[project.status] || '#94a3b8') + '18' }]}>
                <Text style={[styles.statusText, { color: statusColors[project.status] || '#94a3b8' }]}>
                  {project.status?.replace('_', ' ')}
                </Text>
              </View>
            </TouchableOpacity>
          ))}
        </View>

        <View style={{ height: 96 }} />
      </ScrollView>
    </View>
  );
}

// ── Sub-components ───────────────────────────────────────

function DrawerItem({ icon, label, onPress, colors, active }: any) {
  return (
    <TouchableOpacity style={[styles.drawerItem, active && { backgroundColor: colors.primary + '12' }]} onPress={onPress} activeOpacity={0.6}>
      <Ionicons name={icon} size={22} color={active ? colors.primary : colors.textSecondary} />
      <Text style={[styles.drawerLabel, { color: active ? colors.primary : colors.text, fontWeight: active ? '700' : '500' }]}>{label}</Text>
    </TouchableOpacity>
  );
}

function QuickAction({ icon, label, color, bgColor, textColor, onPress }: any) {
  return (
    <TouchableOpacity style={[styles.quickActionBtn, { backgroundColor: bgColor }]} onPress={onPress} activeOpacity={0.7}>
      <Ionicons name={icon} size={24} color={color} />
      <Text style={[styles.quickActionLabel, { color: textColor }]} numberOfLines={1}>{label}</Text>
    </TouchableOpacity>
  );
}

function StatCard({ icon, label, value, color, cardBg, textColor, subColor }: any) {
  return (
    <View style={[styles.statCard, { backgroundColor: cardBg }]}>
      <View style={[styles.statIconCircle, { backgroundColor: color + '15' }]}>
        <Ionicons name={icon} size={20} color={color} />
      </View>
      <Text style={[styles.statValue, { color: textColor }]}>{value ?? 0}</Text>
      <Text style={[styles.statLabel, { color: subColor }]}>{label}</Text>
    </View>
  );
}

function getGreeting() {
  const h = new Date().getHours();
  if (h < 12) return 'Good Morning,';
  if (h < 17) return 'Good Afternoon,';
  return 'Good Evening,';
}

// ── Styles ───────────────────────────────────────────────

const styles = StyleSheet.create({
  root: { flex: 1 },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },

  // Header
  headerBar: {
    flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between',
    paddingTop: Platform.OS === 'ios' ? 54 : StatusBar.currentHeight ? StatusBar.currentHeight + 8 : 38,
    paddingBottom: 12, paddingHorizontal: spacing.md,
  },
  headerBtn: { width: 40, height: 40, borderRadius: 20, backgroundColor: 'rgba(255,255,255,0.18)', justifyContent: 'center', alignItems: 'center' },
  headerCenter: { flex: 1, alignItems: 'center' },
  headerTitle: { fontSize: 20, fontWeight: '800', color: '#fff', letterSpacing: 0.3 },
  avatarBtn: {},
  headerAvatar: { width: 38, height: 38, borderRadius: 19, backgroundColor: 'rgba(255,255,255,0.25)', justifyContent: 'center', alignItems: 'center', borderWidth: 2, borderColor: 'rgba(255,255,255,0.4)' },
  headerAvatarText: { fontSize: 14, fontWeight: '800', color: '#fff' },

  // Profile dropdown
  dropdownOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.35)' },
  profileDropdown: {
    position: 'absolute', top: Platform.OS === 'ios' ? 100 : 80, right: spacing.md,
    width: 280, borderRadius: 16, borderWidth: 1,
    shadowColor: '#000', shadowOffset: { width: 0, height: 8 }, shadowOpacity: 0.15, shadowRadius: 20, elevation: 12,
    overflow: 'hidden',
  },
  profileHeader: { flexDirection: 'row', alignItems: 'center', padding: spacing.md },
  profileAvatar: { width: 46, height: 46, borderRadius: 23, justifyContent: 'center', alignItems: 'center' },
  profileAvatarText: { fontSize: 18, fontWeight: '800', color: '#fff' },
  profileName: { fontSize: fontSize.md, fontWeight: '700' },
  profileEmail: { fontSize: fontSize.xs, marginTop: 1 },
  profileRoleBadge: { alignSelf: 'flex-start', paddingHorizontal: 8, paddingVertical: 2, borderRadius: 8, marginTop: 4 },
  profileRoleText: { fontSize: 10, fontWeight: '700', textTransform: 'uppercase' },
  profileDivider: { height: 1, marginHorizontal: spacing.md },
  profileMenuItem: { flexDirection: 'row', alignItems: 'center', paddingHorizontal: spacing.md, paddingVertical: 13, gap: spacing.sm },
  profileMenuText: { flex: 1, fontSize: fontSize.sm, fontWeight: '500' },
  themeToggleTrack: { width: 40, height: 22, borderRadius: 11, justifyContent: 'center', paddingHorizontal: 2 },
  themeToggleThumb: { width: 18, height: 18, borderRadius: 9, backgroundColor: '#fff', shadowColor: '#000', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.15, shadowRadius: 2, elevation: 2 },

  // Drawer
  drawerOverlay: { ...StyleSheet.absoluteFillObject, backgroundColor: 'rgba(0,0,0,0.45)', zIndex: 10 },
  drawer: {
    position: 'absolute', top: 0, left: 0, bottom: 0, width: DRAWER_WIDTH, zIndex: 20,
    shadowColor: '#000', shadowOffset: { width: 4, height: 0 }, shadowOpacity: 0.2, shadowRadius: 16, elevation: 16,
  },
  drawerHeader: {
    paddingTop: Platform.OS === 'ios' ? 60 : StatusBar.currentHeight ? StatusBar.currentHeight + 20 : 44,
    paddingBottom: spacing.lg, paddingHorizontal: spacing.lg,
  },
  drawerAvatarCircle: { width: 56, height: 56, borderRadius: 28, backgroundColor: 'rgba(255,255,255,0.25)', justifyContent: 'center', alignItems: 'center', borderWidth: 2, borderColor: 'rgba(255,255,255,0.4)', marginBottom: spacing.sm },
  drawerAvatarText: { fontSize: 22, fontWeight: '800', color: '#fff' },
  drawerName: { fontSize: fontSize.lg, fontWeight: '700', color: '#fff' },
  drawerEmail: { fontSize: fontSize.xs, color: 'rgba(255,255,255,0.7)', marginTop: 2 },
  drawerMenu: { flex: 1, paddingTop: spacing.sm },
  drawerItem: { flexDirection: 'row', alignItems: 'center', paddingHorizontal: spacing.lg, paddingVertical: 14, gap: spacing.md },
  drawerLabel: { fontSize: fontSize.md },
  drawerDivider: { height: 1, marginVertical: spacing.xs, marginHorizontal: spacing.lg },

  // Greeting
  greetingCard: { marginHorizontal: spacing.md, marginTop: spacing.md, borderRadius: 20, padding: spacing.lg, flexDirection: 'row', alignItems: 'center', overflow: 'hidden' },
  greetingContent: { flex: 1 },
  greetingTime: { fontSize: fontSize.sm, color: 'rgba(255,255,255,0.8)', fontWeight: '500' },
  greetingName: { fontSize: fontSize.xxl, fontWeight: '800', color: '#fff', marginTop: 2 },
  greetingDesc: { fontSize: fontSize.sm, color: 'rgba(255,255,255,0.7)', marginTop: 4 },
  greetingIcon: { marginLeft: spacing.md },

  // Quick actions
  quickActions: { flexDirection: 'row', paddingHorizontal: spacing.md, marginTop: spacing.md, gap: spacing.sm },
  quickActionBtn: { flex: 1, borderRadius: 14, paddingVertical: 14, alignItems: 'center', gap: 6 },
  quickActionLabel: { fontSize: 11, fontWeight: '600' },

  // Sections
  sectionPadded: { paddingHorizontal: spacing.md, marginTop: spacing.lg },
  sectionHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: spacing.md },
  sectionTitle: { fontSize: fontSize.lg, fontWeight: '700' },
  seeAll: { fontWeight: '600', fontSize: fontSize.sm },

  // Stats
  statsGrid: { flexDirection: 'row', flexWrap: 'wrap', gap: spacing.sm },
  statCard: {
    width: '47.5%', borderRadius: 16, padding: spacing.md,
    shadowColor: '#000', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.04, shadowRadius: 4, elevation: 1,
  },
  statIconCircle: { width: 36, height: 36, borderRadius: 10, justifyContent: 'center', alignItems: 'center' },
  statValue: { fontSize: fontSize.xl, fontWeight: '800', marginTop: spacing.sm },
  statLabel: { fontSize: fontSize.xs, marginTop: 2 },

  // Projects
  projectCard: {
    flexDirection: 'row', alignItems: 'center', borderRadius: 14, padding: spacing.md,
    marginBottom: spacing.sm,
    shadowColor: '#000', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.04, shadowRadius: 3, elevation: 1,
  },
  projectIconCircle: { width: 40, height: 40, borderRadius: 12, justifyContent: 'center', alignItems: 'center', marginRight: spacing.sm },
  projectInfo: { flex: 1, marginRight: spacing.sm },
  projectTitle: { fontSize: fontSize.md, fontWeight: '600' },
  projectMeta: { fontSize: fontSize.xs, marginTop: 2 },
  statusBadge: { paddingHorizontal: 10, paddingVertical: 4, borderRadius: 8 },
  statusText: { fontSize: fontSize.xs, fontWeight: '700', textTransform: 'capitalize' },

  // Empty
  emptyCard: { borderRadius: 14, padding: spacing.xl, alignItems: 'center', justifyContent: 'center' },
  emptyText: { fontSize: fontSize.sm, marginTop: spacing.sm },
});

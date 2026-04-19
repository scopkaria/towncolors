import React, { useEffect, useState, useCallback } from 'react';
import {
  View, Text, StyleSheet, ScrollView, RefreshControl,
  TouchableOpacity, ActivityIndicator, Dimensions,
  Modal, StatusBar, Platform,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { useAuth } from '../contexts/AuthContext';
import { useTheme } from '../contexts/ThemeContext';
import { dashboardApi } from '../api';
import { spacing, fontSize, statusColors } from '../theme';
import { TAB_BAR_TOTAL_HEIGHT } from '../constants/layout';

const { width: SCREEN_WIDTH } = Dimensions.get('window');

export default function DashboardScreen({ navigation }: any) {
  const { user, logout } = useAuth();
  const { colors, isDark, toggleTheme } = useTheme();
  const insets = useSafeAreaInsets();
  const [data, setData] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

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
      <View style={[styles.headerBar, { backgroundColor: colors.primary, paddingTop: insets.top + 8 }]}>
        <View style={styles.headerBtn}>
          <Ionicons name="home" size={22} color="#fff" />
        </View>

        <View style={styles.headerCenter}>
          <Text style={styles.headerTitle}>TOWNCORE</Text>
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

      {/* ── Main Content ──────────────────────────────── */}
      <ScrollView
        style={{ flex: 1 }}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor={colors.primary} />}
        showsVerticalScrollIndicator={false}
      >
        {/* Greeting */}
        <View style={styles.greetingSection}>
          <Text style={[styles.greetingTime, { color: colors.textSecondary }]}>{greeting}</Text>
          <Text style={[styles.greetingName, { color: colors.text }]}>{user?.name?.split(' ')[0]}</Text>
        </View>

        {/* Key Metrics — 3 cards max */}
        <View style={styles.metricsRow}>
          {user?.role === 'admin' && (
            <>
              <MetricCard label="Active" value={stats.active_projects ?? 0} icon="play-circle" color="#8b5cf6" cardBg={colors.card} textColor={colors.text} subColor={colors.textSecondary} />
              <MetricCard label="Pending" value={stats.pending_projects ?? 0} icon="time" color="#f59e0b" cardBg={colors.card} textColor={colors.text} subColor={colors.textSecondary} />
              <MetricCard label="Revenue" value={`$${((stats.total_revenue || 0) / 1000).toFixed(1)}k`} icon="trending-up" color="#16a34a" cardBg={colors.card} textColor={colors.text} subColor={colors.textSecondary} />
            </>
          )}
          {user?.role === 'client' && (
            <>
              <MetricCard label="Active" value={stats.active_projects ?? 0} icon="play-circle" color="#8b5cf6" cardBg={colors.card} textColor={colors.text} subColor={colors.textSecondary} />
              <MetricCard label="Completed" value={stats.completed_projects ?? 0} icon="checkmark-circle" color="#16a34a" cardBg={colors.card} textColor={colors.text} subColor={colors.textSecondary} />
              <MetricCard label="Unread" value={stats.unread_messages ?? 0} icon="chatbubble" color="#dc2626" cardBg={colors.card} textColor={colors.text} subColor={colors.textSecondary} />
            </>
          )}
          {user?.role === 'freelancer' && (
            <>
              <MetricCard label="Active" value={stats.active_projects ?? 0} icon="play-circle" color="#8b5cf6" cardBg={colors.card} textColor={colors.text} subColor={colors.textSecondary} />
              <MetricCard label="Done" value={stats.completed_projects ?? 0} icon="checkmark-circle" color="#16a34a" cardBg={colors.card} textColor={colors.text} subColor={colors.textSecondary} />
              <MetricCard label="Earnings" value={`$${((stats.total_earnings || 0) / 1000).toFixed(1)}k`} icon="wallet" color="#f59e0b" cardBg={colors.card} textColor={colors.text} subColor={colors.textSecondary} />
            </>
          )}
        </View>

        {/* Quick Actions */}
        <View style={styles.actionsRow}>
          <ActionPill icon="add-circle-outline" label="New Project" color={colors.primary} bg={colors.card} textColor={colors.text} onPress={() => navigation.navigate('Projects', { screen: 'CreateProject' })} />
          <ActionPill icon="chatbubbles-outline" label="Messages" color="#3b82f6" bg={colors.card} textColor={colors.text} onPress={() => navigation.navigate('Messages')} />
          <ActionPill icon="notifications-outline" label="Alerts" color="#f59e0b" bg={colors.card} textColor={colors.text} onPress={() => navigation.navigate('Notifications')} />
        </View>

        {/* Recent Projects */}
        <View style={styles.sectionPadded}>
          <View style={styles.sectionHeader}>
            <Text style={[styles.sectionTitle, { color: colors.text }]}>Recent Projects</Text>
            <TouchableOpacity onPress={() => navigation.navigate('Projects')}>
              <Text style={[styles.seeAll, { color: colors.primary }]}>View All</Text>
            </TouchableOpacity>
          </View>
          {(data?.recent_projects || []).length === 0 ? (
            <View style={[styles.emptyCard, { backgroundColor: colors.card }]}>
              <Ionicons name="folder-open-outline" size={36} color={colors.textLight} />
              <Text style={[styles.emptyText, { color: colors.textLight }]}>No projects yet</Text>
            </View>
          ) : (
            (data?.recent_projects || []).slice(0, 4).map((project: any) => (
              <TouchableOpacity
                key={project.id}
                style={[styles.projectCard, { backgroundColor: colors.card }]}
                onPress={() => navigation.navigate('Projects', { screen: 'ProjectDetail', params: { id: project.id } })}
                activeOpacity={0.7}
              >
                <View style={[styles.projectDot, { backgroundColor: statusColors[project.status] || '#94a3b8' }]} />
                <View style={styles.projectInfo}>
                  <Text style={[styles.projectTitle, { color: colors.text }]} numberOfLines={1}>{project.title}</Text>
                  <Text style={[styles.projectMeta, { color: colors.textSecondary }]}>
                    {project.client?.name || project.freelancer?.name || '—'}
                  </Text>
                </View>
                <View style={[styles.statusBadge, { backgroundColor: (statusColors[project.status] || '#94a3b8') + '15' }]}>
                  <Text style={[styles.statusText, { color: statusColors[project.status] || '#94a3b8' }]}>
                    {project.status?.replace('_', ' ')}
                  </Text>
                </View>
              </TouchableOpacity>
            ))
          )}
        </View>

        <View style={{ height: TAB_BAR_TOTAL_HEIGHT + insets.bottom }} />
      </ScrollView>
    </View>
  );
}

// ── Sub-components ───────────────────────────────────────

function MetricCard({ icon, label, value, color, cardBg, textColor, subColor }: any) {
  return (
    <View style={[styles.metricCard, { backgroundColor: cardBg }]}>
      <View style={[styles.metricIcon, { backgroundColor: color + '15' }]}>
        <Ionicons name={icon} size={18} color={color} />
      </View>
      <Text style={[styles.metricValue, { color: textColor }]}>{value}</Text>
      <Text style={[styles.metricLabel, { color: subColor }]}>{label}</Text>
    </View>
  );
}

function ActionPill({ icon, label, color, bg, textColor, onPress }: any) {
  return (
    <TouchableOpacity style={[styles.actionPill, { backgroundColor: bg }]} onPress={onPress} activeOpacity={0.7}>
      <Ionicons name={icon} size={20} color={color} />
      <Text style={[styles.actionPillLabel, { color: textColor }]}>{label}</Text>
    </TouchableOpacity>
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
    paddingBottom: 12, paddingHorizontal: spacing.md,
  },
  headerBtn: { width: 40, height: 40, borderRadius: 20, backgroundColor: 'rgba(255,255,255,0.18)', justifyContent: 'center', alignItems: 'center' },
  headerCenter: { flex: 1, alignItems: 'center' },
  headerTitle: { fontSize: 18, fontWeight: '900', color: '#fff', letterSpacing: 3 },
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

  // Greeting
  greetingSection: { paddingHorizontal: spacing.lg, paddingTop: spacing.lg },
  greetingTime: { fontSize: 13, fontWeight: '500', textTransform: 'uppercase', letterSpacing: 1 },
  greetingName: { fontSize: 26, fontWeight: '800', marginTop: 2 },

  // Metrics
  metricsRow: { flexDirection: 'row', paddingHorizontal: spacing.md, marginTop: spacing.md, gap: spacing.sm },
  metricCard: {
    flex: 1, borderRadius: 16, padding: 14, alignItems: 'center',
    shadowColor: '#000', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.04, shadowRadius: 4, elevation: 1,
  },
  metricIcon: { width: 34, height: 34, borderRadius: 10, justifyContent: 'center', alignItems: 'center' },
  metricValue: { fontSize: 20, fontWeight: '800', marginTop: 8 },
  metricLabel: { fontSize: 11, fontWeight: '600', marginTop: 2 },

  // Actions
  actionsRow: { flexDirection: 'row', paddingHorizontal: spacing.md, marginTop: spacing.md, gap: spacing.sm },
  actionPill: {
    flex: 1, flexDirection: 'row', alignItems: 'center', justifyContent: 'center',
    borderRadius: 12, paddingVertical: 12, gap: 6,
    shadowColor: '#000', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.04, shadowRadius: 3, elevation: 1,
  },
  actionPillLabel: { fontSize: 12, fontWeight: '600' },

  // Sections
  sectionPadded: { paddingHorizontal: spacing.md, marginTop: spacing.lg },
  sectionHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: spacing.md },
  sectionTitle: { fontSize: fontSize.lg, fontWeight: '700' },
  seeAll: { fontWeight: '600', fontSize: fontSize.sm },

  // Projects
  projectCard: {
    flexDirection: 'row', alignItems: 'center', borderRadius: 14, padding: spacing.md,
    marginBottom: spacing.sm,
    shadowColor: '#000', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.04, shadowRadius: 3, elevation: 1,
  },
  projectDot: { width: 8, height: 8, borderRadius: 4, marginRight: spacing.sm },
  projectInfo: { flex: 1, marginRight: spacing.sm },
  projectTitle: { fontSize: fontSize.md, fontWeight: '600' },
  projectMeta: { fontSize: fontSize.xs, marginTop: 2 },
  statusBadge: { paddingHorizontal: 10, paddingVertical: 4, borderRadius: 8 },
  statusText: { fontSize: fontSize.xs, fontWeight: '700', textTransform: 'capitalize' },

  // Empty
  emptyCard: { borderRadius: 14, padding: spacing.xl, alignItems: 'center', justifyContent: 'center' },
  emptyText: { fontSize: fontSize.sm, marginTop: spacing.sm },
});

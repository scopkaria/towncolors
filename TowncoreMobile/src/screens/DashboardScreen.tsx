import React, { useEffect, useState, useCallback } from 'react';
import {
  View, Text, StyleSheet, ScrollView, RefreshControl,
  TouchableOpacity, ActivityIndicator,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useAuth } from '../contexts/AuthContext';
import { dashboardApi } from '../api';
import { colors, spacing, fontSize, statusColors } from '../theme';

export default function DashboardScreen({ navigation }: any) {
  const { user } = useAuth();
  const [data, setData] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

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

  if (loading) {
    return (
      <View style={styles.center}>
        <ActivityIndicator size="large" color={colors.primary} />
      </View>
    );
  }

  const stats = data?.stats || {};

  return (
    <ScrollView
      style={styles.container}
      refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
    >
      <View style={styles.header}>
        <View>
          <Text style={styles.greeting}>Welcome back,</Text>
          <Text style={styles.name}>{user?.name}</Text>
        </View>
        <View style={styles.roleBadge}>
          <Text style={styles.roleText}>{user?.role}</Text>
        </View>
      </View>

      {/* Stats Grid */}
      <View style={styles.statsGrid}>
        {user?.role === 'admin' && (
          <>
            <StatCard icon="folder-open" label="Total Projects" value={stats.total_projects} color="#3b82f6" />
            <StatCard icon="time" label="Pending" value={stats.pending_projects} color="#f59e0b" />
            <StatCard icon="play-circle" label="Active" value={stats.active_projects} color="#8b5cf6" />
            <StatCard icon="checkmark-circle" label="Completed" value={stats.completed_projects} color="#16a34a" />
            <StatCard icon="cash" label="Revenue" value={`$${(stats.total_revenue || 0).toLocaleString()}`} color="#16a34a" wide />
            <StatCard icon="hourglass" label="Pending Rev." value={`$${(stats.pending_revenue || 0).toLocaleString()}`} color="#f59e0b" wide />
          </>
        )}
        {user?.role === 'client' && (
          <>
            <StatCard icon="folder-open" label="Projects" value={stats.total_projects} color="#3b82f6" />
            <StatCard icon="play-circle" label="Active" value={stats.active_projects} color="#8b5cf6" />
            <StatCard icon="checkmark-circle" label="Completed" value={stats.completed_projects} color="#16a34a" />
            <StatCard icon="chatbubbles" label="Unread" value={stats.unread_messages} color="#dc2626" />
            <StatCard icon="receipt" label="Invoiced" value={`$${(stats.total_invoiced || 0).toLocaleString()}`} color="#f59e0b" wide />
            <StatCard icon="cash" label="Paid" value={`$${(stats.total_paid || 0).toLocaleString()}`} color="#16a34a" wide />
          </>
        )}
        {user?.role === 'freelancer' && (
          <>
            <StatCard icon="play-circle" label="Active" value={stats.active_projects} color="#8b5cf6" />
            <StatCard icon="checkmark-circle" label="Completed" value={stats.completed_projects} color="#16a34a" />
            <StatCard icon="cash" label="Earnings" value={`$${(stats.total_earnings || 0).toLocaleString()}`} color="#16a34a" wide />
            <StatCard icon="hourglass" label="Pending" value={`$${(stats.pending_payments || 0).toLocaleString()}`} color="#f59e0b" wide />
          </>
        )}
      </View>

      {/* Recent Projects */}
      <View style={styles.section}>
        <View style={styles.sectionHeader}>
          <Text style={styles.sectionTitle}>Recent Projects</Text>
          <TouchableOpacity onPress={() => navigation.navigate('Projects')}>
            <Text style={styles.seeAll}>See All</Text>
          </TouchableOpacity>
        </View>
        {(data?.recent_projects || []).map((project: any) => (
          <TouchableOpacity
            key={project.id}
            style={styles.projectCard}
            onPress={() => navigation.navigate('Projects', { screen: 'ProjectDetail', params: { id: project.id } })}
          >
            <View style={styles.projectInfo}>
              <Text style={styles.projectTitle}>{project.title}</Text>
              <Text style={styles.projectMeta}>
                {project.client?.name || project.freelancer?.name || '—'}
              </Text>
            </View>
            <View style={[styles.statusBadge, { backgroundColor: (statusColors[project.status] || '#94a3b8') + '20' }]}>
              <Text style={[styles.statusText, { color: statusColors[project.status] || '#94a3b8' }]}>
                {project.status?.replace('_', ' ')}
              </Text>
            </View>
          </TouchableOpacity>
        ))}
      </View>
    </ScrollView>
  );
}

function StatCard({ icon, label, value, color, wide }: any) {
  return (
    <View style={[styles.statCard, wide && styles.statCardWide]}>
      <Ionicons name={icon} size={24} color={color} />
      <Text style={styles.statValue}>{value ?? 0}</Text>
      <Text style={styles.statLabel}>{label}</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: colors.background },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  header: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', padding: spacing.lg, paddingTop: spacing.xl },
  greeting: { fontSize: fontSize.sm, color: colors.textSecondary },
  name: { fontSize: fontSize.xl, fontWeight: '800', color: colors.text },
  roleBadge: { backgroundColor: colors.primary + '15', paddingHorizontal: 12, paddingVertical: 6, borderRadius: 20 },
  roleText: { color: colors.primary, fontWeight: '700', fontSize: fontSize.xs, textTransform: 'capitalize' },
  statsGrid: { flexDirection: 'row', flexWrap: 'wrap', paddingHorizontal: spacing.md, gap: spacing.sm },
  statCard: { width: '47%', backgroundColor: colors.card, borderRadius: 16, padding: spacing.md, shadowColor: colors.shadow, shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.05, shadowRadius: 4, elevation: 2 },
  statCardWide: { width: '47%' },
  statValue: { fontSize: fontSize.xl, fontWeight: '800', color: colors.text, marginTop: spacing.sm },
  statLabel: { fontSize: fontSize.xs, color: colors.textSecondary, marginTop: 2 },
  section: { padding: spacing.lg, marginTop: spacing.sm },
  sectionHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: spacing.md },
  sectionTitle: { fontSize: fontSize.lg, fontWeight: '700', color: colors.text },
  seeAll: { color: colors.primary, fontWeight: '600', fontSize: fontSize.sm },
  projectCard: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', backgroundColor: colors.card, borderRadius: 12, padding: spacing.md, marginBottom: spacing.sm, shadowColor: colors.shadow, shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.04, shadowRadius: 3, elevation: 1 },
  projectInfo: { flex: 1, marginRight: spacing.sm },
  projectTitle: { fontSize: fontSize.md, fontWeight: '600', color: colors.text },
  projectMeta: { fontSize: fontSize.xs, color: colors.textSecondary, marginTop: 2 },
  statusBadge: { paddingHorizontal: 10, paddingVertical: 4, borderRadius: 8 },
  statusText: { fontSize: fontSize.xs, fontWeight: '700', textTransform: 'capitalize' },
});

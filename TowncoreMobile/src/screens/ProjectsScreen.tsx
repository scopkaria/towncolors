import React, { useEffect, useState, useCallback } from 'react';
import {
  View, Text, StyleSheet, FlatList, TouchableOpacity,
  RefreshControl, ActivityIndicator, TextInput, StatusBar,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { useAuth } from '../contexts/AuthContext';
import { useTheme } from '../contexts/ThemeContext';
import { projectsApi } from '../api';
import { spacing, fontSize, statusColors } from '../theme';
import { TAB_BAR_TOTAL_HEIGHT } from '../constants/layout';
import ScreenHeader from '../components/ScreenHeader';

const STATUS_FILTERS = [
  { key: 'all',         label: 'All',       icon: 'apps-outline' },
  { key: 'pending',     label: 'Pending',   icon: 'time-outline' },
  { key: 'assigned',    label: 'Assigned',  icon: 'person-outline' },
  { key: 'in_progress', label: 'Active',    icon: 'play-circle-outline' },
  { key: 'completed',   label: 'Done',      icon: 'checkmark-circle-outline' },
];

export default function ProjectsScreen({ navigation }: any) {
  const { user } = useAuth();
  const { colors } = useTheme();
  const insets = useSafeAreaInsets();
  const [projects, setProjects] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [page, setPage] = useState(1);
  const [hasMore, setHasMore] = useState(true);
  const [statusFilter, setStatusFilter] = useState('all');
  const [search, setSearch] = useState('');
  const [searchFocused, setSearchFocused] = useState(false);

  const loadProjects = useCallback(async (p = 1, status?: string) => {
    try {
      const result = await projectsApi.list(p, status === 'all' ? undefined : status);
      if (p === 1) {
        setProjects(result.data);
      } else {
        setProjects(prev => [...prev, ...result.data]);
      }
      setHasMore(result.current_page < result.last_page);
      setPage(result.current_page);
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => {
    setLoading(true);
    loadProjects(1, statusFilter);
  }, [statusFilter, loadProjects]);

  const onRefresh = () => { setRefreshing(true); loadProjects(1, statusFilter); };
  const loadMore = () => { if (hasMore) loadProjects(page + 1, statusFilter); };

  const filtered = search.trim()
    ? projects.filter(p => p.title?.toLowerCase().includes(search.toLowerCase()))
    : projects;

  const totalCount = projects.length;
  const filterCounts = STATUS_FILTERS.reduce((acc, f) => {
    acc[f.key] = f.key === 'all' ? totalCount : projects.filter(p => p.status === f.key).length;
    return acc;
  }, {} as Record<string, number>);

  const statusIcon = (status: string) => {
    switch (status) {
      case 'pending': return 'time-outline';
      case 'assigned': return 'person-outline';
      case 'in_progress': return 'play-circle-outline';
      case 'completed': return 'checkmark-circle-outline';
      default: return 'ellipse-outline';
    }
  };

  function timeAgo(dateStr: string) {
    if (!dateStr) return '';
    const diff = Date.now() - new Date(dateStr).getTime();
    const mins = Math.floor(diff / 60000);
    if (mins < 1) return 'just now';
    if (mins < 60) return `${mins}m ago`;
    const hrs = Math.floor(mins / 60);
    if (hrs < 24) return `${hrs}h ago`;
    const days = Math.floor(hrs / 24);
    if (days < 7) return `${days}d ago`;
    return new Date(dateStr).toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
  }

  const renderProject = ({ item }: any) => {
    const sColor = statusColors[item.status] || '#94a3b8';
    const progress = item.status === 'completed' ? 100
      : item.status === 'in_progress' ? 60
      : item.status === 'assigned' ? 25
      : 10;

    return (
      <TouchableOpacity
        style={[styles.card, { backgroundColor: colors.card }]}
        onPress={() => navigation.navigate('ProjectDetail', { id: item.id })}
        activeOpacity={0.7}
      >
        <View style={styles.cardBody}>
          {/* Top: title + badge */}
          <View style={styles.cardTop}>
            <View style={styles.cardTitleWrap}>
              <Text style={[styles.title, { color: colors.text }]} numberOfLines={1}>{item.title}</Text>
            </View>
            <View style={[styles.badge, { backgroundColor: sColor + '15' }]}>
              <View style={[styles.badgeDot, { backgroundColor: sColor }]} />
              <Text style={[styles.badgeText, { color: sColor }]}>
                {item.status?.replace('_', ' ')}
              </Text>
            </View>
          </View>

          {/* Description */}
          {item.description ? (
            <Text style={[styles.desc, { color: colors.textSecondary }]} numberOfLines={2}>{item.description}</Text>
          ) : null}

          {/* Progress bar */}
          <View style={styles.progressWrap}>
            <View style={[styles.progressTrack, { backgroundColor: colors.border + '60' }]}>
              <View style={[styles.progressFill, { width: `${progress}%`, backgroundColor: sColor }]} />
            </View>
            <Text style={[styles.progressText, { color: colors.textLight }]}>{progress}%</Text>
          </View>

          {/* Footer meta */}
          <View style={styles.cardFooter}>
            <View style={styles.cardFooterLeft}>
              {item.client && (
                <View style={styles.metaChip}>
                  <View style={[styles.metaAvatar, { backgroundColor: colors.primary + '18' }]}>
                    <Text style={[styles.metaAvatarText, { color: colors.primary }]}>
                      {item.client.name?.charAt(0).toUpperCase()}
                    </Text>
                  </View>
                  <Text style={[styles.metaText, { color: colors.textSecondary }]} numberOfLines={1}>{item.client.name}</Text>
                </View>
              )}
              {item.freelancer && (
                <View style={styles.metaChip}>
                  <View style={[styles.metaAvatar, { backgroundColor: '#8b5cf6' + '18' }]}>
                    <Text style={[styles.metaAvatarText, { color: '#8b5cf6' }]}>
                      {item.freelancer.name?.charAt(0).toUpperCase()}
                    </Text>
                  </View>
                  <Text style={[styles.metaText, { color: colors.textSecondary }]} numberOfLines={1}>{item.freelancer.name}</Text>
                </View>
              )}
            </View>
            <Text style={[styles.timeAgo, { color: colors.textLight }]}>{timeAgo(item.updated_at)}</Text>
          </View>
        </View>

        <View style={styles.cardArrow}>
          <Ionicons name="chevron-forward" size={16} color={colors.textLight} />
        </View>
      </TouchableOpacity>
    );
  };

  return (
    <View style={[styles.container, { backgroundColor: colors.background }]}>
      <StatusBar barStyle="light-content" backgroundColor={colors.primary} />

      <ScreenHeader
        title={user?.role === 'freelancer' ? 'Assigned Projects' : 'Projects'}
        rightIcon="search-outline"
        onRight={() => setSearchFocused(v => !v)}
        rightIcon2={(user?.role === 'client' || user?.role === 'admin') ? 'add-circle-outline' : undefined}
        onRight2={(user?.role === 'client' || user?.role === 'admin') ? () => navigation.navigate('CreateProject') : undefined}
      />

      {/* ── Search Bar (collapsible) ──────────────────── */}
      {searchFocused && (
        <View style={[styles.searchWrap, { backgroundColor: colors.background }]}>
          <View style={[styles.searchBar, { backgroundColor: colors.card, borderColor: searchFocused ? colors.primary : colors.border }]}>
            <Ionicons name="search" size={18} color={colors.primary} />
            <TextInput
              style={[styles.searchInput, { color: colors.text }]}
              placeholder="Search projects..."
              placeholderTextColor={colors.textLight}
              value={search}
              onChangeText={setSearch}
              autoFocus
            />
            {search.length > 0 && (
              <TouchableOpacity onPress={() => setSearch('')}>
                <Ionicons name="close-circle" size={18} color={colors.textLight} />
              </TouchableOpacity>
            )}
          </View>
        </View>
      )}

      {/* ── Filter Chips ──────────────────────────────── */}
      <View style={[styles.filterRow, { backgroundColor: colors.background }]}>
        <FlatList
          horizontal
          showsHorizontalScrollIndicator={false}
          data={STATUS_FILTERS}
          keyExtractor={item => item.key}
          contentContainerStyle={styles.filterContent}
          renderItem={({ item }) => {
            const active = statusFilter === item.key;
            const chipColor = item.key === 'all' ? colors.primary : (statusColors[item.key] || colors.primary);
            return (
              <TouchableOpacity
                style={[
                  styles.filterBtn,
                  { backgroundColor: active ? chipColor : colors.card, borderColor: active ? chipColor : colors.border },
                ]}
                onPress={() => setStatusFilter(item.key)}
                activeOpacity={0.7}
              >
                <Ionicons
                  name={item.icon as any}
                  size={14}
                  color={active ? '#fff' : colors.textSecondary}
                  style={{ marginRight: 5 }}
                />
                <Text style={[styles.filterText, { color: active ? '#fff' : colors.textSecondary }]}>
                  {item.label}
                </Text>
                {filterCounts[item.key] > 0 && (
                  <View style={[styles.filterCount, { backgroundColor: active ? 'rgba(255,255,255,0.25)' : colors.border + '80' }]}>
                    <Text style={[styles.filterCountText, { color: active ? '#fff' : colors.textLight }]}>
                      {filterCounts[item.key]}
                    </Text>
                  </View>
                )}
              </TouchableOpacity>
            );
          }}
        />
      </View>

      {/* ── Project List ──────────────────────────────── */}
      {loading ? (
        <View style={styles.center}><ActivityIndicator size="large" color={colors.primary} /></View>
      ) : (
        <FlatList
          data={filtered}
          keyExtractor={item => item.id.toString()}
          renderItem={renderProject}
          refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor={colors.primary} />}
          onEndReached={loadMore}
          onEndReachedThreshold={0.3}
          contentContainerStyle={[styles.list, { paddingBottom: TAB_BAR_TOTAL_HEIGHT + insets.bottom + 20 }]}
          showsVerticalScrollIndicator={false}
          ListEmptyComponent={
            <View style={styles.emptyWrap}>
              <View style={[styles.emptyIconCircle, { backgroundColor: colors.card }]}>
                <Ionicons name="folder-open-outline" size={48} color={colors.textLight} />
              </View>
              <Text style={[styles.emptyTitle, { color: colors.text }]}>No projects found</Text>
              <Text style={[styles.emptyDesc, { color: colors.textSecondary }]}>
                {search ? 'Try a different search term' : 'Projects you create or are assigned to will appear here'}
              </Text>
            </View>
          }
        />
      )}

      {/* ── FAB ───────────────────────────────────────── */}
      {(user?.role === 'client' || user?.role === 'admin') && (
        <TouchableOpacity
          style={[styles.fab, { backgroundColor: colors.primary, bottom: TAB_BAR_TOTAL_HEIGHT + insets.bottom + 16 }]}
          onPress={() => navigation.navigate('CreateProject')}
          activeOpacity={0.8}
        >
          <Ionicons name="add" size={28} color="#fff" />
        </TouchableOpacity>
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1 },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center', paddingTop: 80 },

  // Search
  searchWrap: { paddingHorizontal: spacing.md, paddingTop: spacing.sm, paddingBottom: 4 },
  searchBar: {
    flexDirection: 'row', alignItems: 'center', borderRadius: 14,
    paddingHorizontal: spacing.md, height: 44, gap: spacing.sm, borderWidth: 1,
  },
  searchInput: { flex: 1, fontSize: fontSize.md, paddingVertical: 0 },

  // Filters
  filterRow: { paddingBottom: 4 },
  filterContent: { paddingHorizontal: spacing.md, paddingVertical: spacing.sm, gap: 8 },
  filterBtn: {
    flexDirection: 'row', alignItems: 'center',
    paddingHorizontal: 14, paddingVertical: 8, borderRadius: 20, borderWidth: 1,
  },
  filterText: { fontSize: 13, fontWeight: '600' },
  filterCount: { marginLeft: 6, paddingHorizontal: 6, paddingVertical: 1, borderRadius: 10, minWidth: 20, alignItems: 'center' },
  filterCountText: { fontSize: 10, fontWeight: '700' },

  // List
  list: { paddingHorizontal: spacing.md, paddingTop: 4 },

  // Card
  card: {
    flexDirection: 'row', borderRadius: 16, marginBottom: 12, overflow: 'hidden',
    shadowColor: '#000', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.06, shadowRadius: 10, elevation: 3,
  },
  cardBody: { flex: 1, padding: spacing.md },
  cardTop: { flexDirection: 'row', alignItems: 'flex-start', justifyContent: 'space-between', gap: spacing.sm },
  cardTitleWrap: { flex: 1 },
  title: { fontSize: 16, fontWeight: '700', lineHeight: 22 },
  badge: {
    flexDirection: 'row', alignItems: 'center',
    paddingHorizontal: 10, paddingVertical: 4, borderRadius: 10,
  },
  badgeDot: { width: 6, height: 6, borderRadius: 3, marginRight: 5 },
  badgeText: { fontSize: 11, fontWeight: '700', textTransform: 'capitalize' },
  desc: { fontSize: 13, marginTop: 8, lineHeight: 19, letterSpacing: 0.1 },

  // Progress
  progressWrap: { flexDirection: 'row', alignItems: 'center', marginTop: 12, gap: 8 },
  progressTrack: { flex: 1, height: 4, borderRadius: 2, overflow: 'hidden' },
  progressFill: { height: 4, borderRadius: 2 },
  progressText: { fontSize: 11, fontWeight: '700', width: 32, textAlign: 'right' },

  // Footer
  cardFooter: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginTop: 12 },
  cardFooterLeft: { flexDirection: 'row', alignItems: 'center', gap: 10, flex: 1 },
  metaChip: { flexDirection: 'row', alignItems: 'center', gap: 5 },
  metaAvatar: { width: 22, height: 22, borderRadius: 11, justifyContent: 'center', alignItems: 'center' },
  metaAvatarText: { fontSize: 10, fontWeight: '700' },
  metaText: { fontSize: 12, maxWidth: 80 },
  timeAgo: { fontSize: 11 },
  cardArrow: { justifyContent: 'center', paddingRight: 14 },

  // Empty
  emptyWrap: { alignItems: 'center', paddingTop: 60 },
  emptyIconCircle: { width: 96, height: 96, borderRadius: 48, justifyContent: 'center', alignItems: 'center', marginBottom: spacing.md },
  emptyTitle: { fontSize: fontSize.lg, fontWeight: '700' },
  emptyDesc: { fontSize: fontSize.sm, marginTop: spacing.xs, textAlign: 'center', paddingHorizontal: spacing.xl },

  // FAB
  fab: {
    position: 'absolute', right: 24, width: 56, height: 56,
    borderRadius: 28, justifyContent: 'center', alignItems: 'center',
    shadowColor: '#000', shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.2, shadowRadius: 8, elevation: 5,
  },
});

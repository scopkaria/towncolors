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

const STATUS_FILTERS = ['all', 'pending', 'assigned', 'in_progress', 'completed'];

export default function ProjectsScreen({ navigation }: any) {
  const { user } = useAuth();
  const { colors, isDark } = useTheme();
  const insets = useSafeAreaInsets();
  const [projects, setProjects] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [page, setPage] = useState(1);
  const [hasMore, setHasMore] = useState(true);
  const [statusFilter, setStatusFilter] = useState('all');
  const [search, setSearch] = useState('');

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

  const statusIcon = (status: string) => {
    switch (status) {
      case 'pending': return 'time-outline';
      case 'assigned': return 'person-outline';
      case 'in_progress': return 'play-circle-outline';
      case 'completed': return 'checkmark-circle-outline';
      default: return 'ellipse-outline';
    }
  };

  const renderProject = ({ item }: any) => {
    const sColor = statusColors[item.status] || '#94a3b8';
    return (
      <TouchableOpacity
        style={[styles.card, { backgroundColor: colors.card }]}
        onPress={() => navigation.navigate('ProjectDetail', { id: item.id })}
        activeOpacity={0.7}
      >
        {/* Status accent bar */}
        <View style={[styles.cardAccent, { backgroundColor: sColor }]} />

        <View style={styles.cardBody}>
          <View style={styles.cardTop}>
            <View style={[styles.statusIconCircle, { backgroundColor: sColor + '15' }]}>
              <Ionicons name={statusIcon(item.status)} size={20} color={sColor} />
            </View>
            <View style={styles.cardTitleWrap}>
              <Text style={[styles.title, { color: colors.text }]} numberOfLines={1}>{item.title}</Text>
              <View style={[styles.badge, { backgroundColor: sColor + '18' }]}>
                <Text style={[styles.badgeText, { color: sColor }]}>
                  {item.status?.replace('_', ' ')}
                </Text>
              </View>
            </View>
          </View>

          {item.description ? (
            <Text style={[styles.desc, { color: colors.textSecondary }]} numberOfLines={2}>{item.description}</Text>
          ) : null}

          <View style={[styles.cardDivider, { backgroundColor: colors.border }]} />

          <View style={styles.cardFooter}>
            {item.client && (
              <View style={styles.metaChip}>
                <Ionicons name="person" size={12} color={colors.primary} />
                <Text style={[styles.metaText, { color: colors.textSecondary }]}>{item.client.name}</Text>
              </View>
            )}
            {item.freelancer && (
              <View style={styles.metaChip}>
                <Ionicons name="code-working" size={12} color="#8b5cf6" />
                <Text style={[styles.metaText, { color: colors.textSecondary }]}>{item.freelancer.name}</Text>
              </View>
            )}
            {item.categories?.length > 0 && (
              <View style={styles.metaChip}>
                <Ionicons name="pricetag" size={12} color="#f59e0b" />
                <Text style={[styles.metaText, { color: colors.textSecondary }]}>{item.categories.map((c: any) => c.name).join(', ')}</Text>
              </View>
            )}
          </View>
        </View>

        <View style={styles.cardArrow}>
          <Ionicons name="chevron-forward" size={18} color={colors.textLight} />
        </View>
      </TouchableOpacity>
    );
  };

  return (
    <View style={[styles.container, { backgroundColor: colors.background }]}>
      <StatusBar barStyle={isDark ? 'light-content' : 'dark-content'} backgroundColor={colors.primary} />

      {/* ── Header ────────────────────────────────────── */}
      <View style={[styles.header, { backgroundColor: colors.primary, paddingTop: insets.top + 12 }]}>
        <Text style={styles.headerTitle}>Projects</Text>
        <Text style={styles.headerSub}>{filtered.length} project{filtered.length !== 1 ? 's' : ''}</Text>
      </View>

      {/* ── Search Bar ────────────────────────────────── */}
      <View style={[styles.searchWrap, { backgroundColor: colors.background }]}>
        <View style={[styles.searchBar, { backgroundColor: colors.card, borderColor: colors.border }]}>
          <Ionicons name="search" size={18} color={colors.textLight} />
          <TextInput
            style={[styles.searchInput, { color: colors.text }]}
            placeholder="Search projects..."
            placeholderTextColor={colors.textLight}
            value={search}
            onChangeText={setSearch}
          />
          {search.length > 0 && (
            <TouchableOpacity onPress={() => setSearch('')}>
              <Ionicons name="close-circle" size={18} color={colors.textLight} />
            </TouchableOpacity>
          )}
        </View>
      </View>

      {/* ── Status Filters ────────────────────────────── */}
      <FlatList
        horizontal
        showsHorizontalScrollIndicator={false}
        data={STATUS_FILTERS}
        keyExtractor={item => item}
        contentContainerStyle={[styles.filterRow, { backgroundColor: colors.background }]}
        renderItem={({ item }) => (
          <TouchableOpacity
            style={[
              styles.filterBtn,
              { backgroundColor: colors.card, borderColor: colors.border },
              statusFilter === item && { backgroundColor: colors.primary, borderColor: colors.primary },
            ]}
            onPress={() => setStatusFilter(item)}
            activeOpacity={0.7}
          >
            <Text style={[styles.filterText, { color: colors.textSecondary }, statusFilter === item && styles.filterTextActive]}>
              {item === 'all' ? 'All' : item.replace('_', ' ')}
            </Text>
          </TouchableOpacity>
        )}
      />

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
          contentContainerStyle={[styles.list, { paddingBottom: TAB_BAR_TOTAL_HEIGHT + insets.bottom }]}
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
          style={[styles.fab, { backgroundColor: colors.primary }]}
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

  // Header
  header: {
    paddingBottom: 20, paddingHorizontal: spacing.lg,
  },
  headerTitle: { fontSize: 26, fontWeight: '800', color: '#fff' },
  headerSub: { fontSize: fontSize.sm, color: 'rgba(255,255,255,0.7)', marginTop: 2 },

  // Search
  searchWrap: { paddingHorizontal: spacing.md, paddingTop: spacing.sm, paddingBottom: 4 },
  searchBar: {
    flexDirection: 'row', alignItems: 'center', borderRadius: 14,
    paddingHorizontal: spacing.md, height: 44, gap: spacing.sm, borderWidth: 1,
  },
  searchInput: { flex: 1, fontSize: fontSize.md, paddingVertical: 0 },

  // Filters
  filterRow: { paddingHorizontal: spacing.md, paddingVertical: spacing.sm, gap: spacing.xs },
  filterBtn: { paddingHorizontal: 16, paddingVertical: 8, borderRadius: 20, borderWidth: 1 },
  filterText: { fontSize: fontSize.sm, fontWeight: '600', textTransform: 'capitalize' },
  filterTextActive: { color: '#fff' },

  // List
  list: { padding: spacing.md },

  // Card
  card: {
    flexDirection: 'row', borderRadius: 16, marginBottom: spacing.sm, overflow: 'hidden',
    shadowColor: '#000', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.06, shadowRadius: 8, elevation: 2,
  },
  cardAccent: { width: 4 },
  cardBody: { flex: 1, padding: spacing.md },
  cardTop: { flexDirection: 'row', alignItems: 'center', gap: spacing.sm },
  statusIconCircle: { width: 36, height: 36, borderRadius: 10, justifyContent: 'center', alignItems: 'center' },
  cardTitleWrap: { flex: 1 },
  title: { fontSize: fontSize.md, fontWeight: '700', marginBottom: 4 },
  badge: { alignSelf: 'flex-start', paddingHorizontal: 8, paddingVertical: 2, borderRadius: 6 },
  badgeText: { fontSize: 10, fontWeight: '700', textTransform: 'capitalize' },
  desc: { fontSize: fontSize.sm, marginTop: spacing.sm, lineHeight: 20 },
  cardDivider: { height: 1, marginVertical: spacing.sm },
  cardFooter: { flexDirection: 'row', flexWrap: 'wrap', gap: spacing.sm },
  metaChip: { flexDirection: 'row', alignItems: 'center', gap: 4 },
  metaText: { fontSize: fontSize.xs },
  cardArrow: { justifyContent: 'center', paddingRight: spacing.md },

  // Empty
  emptyWrap: { alignItems: 'center', paddingTop: 60 },
  emptyIconCircle: { width: 96, height: 96, borderRadius: 48, justifyContent: 'center', alignItems: 'center', marginBottom: spacing.md },
  emptyTitle: { fontSize: fontSize.lg, fontWeight: '700' },
  emptyDesc: { fontSize: fontSize.sm, marginTop: spacing.xs, textAlign: 'center', paddingHorizontal: spacing.xl },

  // FAB
  fab: {
    position: 'absolute', bottom: 24, right: 24, width: 56, height: 56,
    borderRadius: 28, justifyContent: 'center', alignItems: 'center',
    shadowColor: '#000', shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.2, shadowRadius: 8, elevation: 5,
  },
});

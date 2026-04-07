import React, { useEffect, useState, useCallback } from 'react';
import {
  View, Text, StyleSheet, FlatList, TouchableOpacity,
  RefreshControl, ActivityIndicator,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useAuth } from '../contexts/AuthContext';
import { projectsApi } from '../api';
import { colors, spacing, fontSize, statusColors } from '../theme';

const STATUS_FILTERS = ['all', 'pending', 'assigned', 'in_progress', 'completed'];

export default function ProjectsScreen({ navigation }: any) {
  const { user } = useAuth();
  const [projects, setProjects] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [page, setPage] = useState(1);
  const [hasMore, setHasMore] = useState(true);
  const [statusFilter, setStatusFilter] = useState('all');

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

  const renderProject = ({ item }: any) => (
    <TouchableOpacity
      style={styles.card}
      onPress={() => navigation.navigate('ProjectDetail', { id: item.id })}
    >
      <View style={styles.cardHeader}>
        <Text style={styles.title} numberOfLines={1}>{item.title}</Text>
        <View style={[styles.badge, { backgroundColor: (statusColors[item.status] || '#94a3b8') + '20' }]}>
          <Text style={[styles.badgeText, { color: statusColors[item.status] || '#94a3b8' }]}>
            {item.status?.replace('_', ' ')}
          </Text>
        </View>
      </View>
      <Text style={styles.desc} numberOfLines={2}>{item.description}</Text>
      <View style={styles.cardFooter}>
        {item.client && <Text style={styles.meta}><Ionicons name="person" size={12} /> {item.client.name}</Text>}
        {item.freelancer && <Text style={styles.meta}><Ionicons name="code-working" size={12} /> {item.freelancer.name}</Text>}
        {item.categories?.length > 0 && (
          <Text style={styles.meta}><Ionicons name="pricetag" size={12} /> {item.categories.map((c: any) => c.name).join(', ')}</Text>
        )}
      </View>
    </TouchableOpacity>
  );

  return (
    <View style={styles.container}>
      {/* Status Filters */}
      <FlatList
        horizontal
        showsHorizontalScrollIndicator={false}
        data={STATUS_FILTERS}
        keyExtractor={item => item}
        contentContainerStyle={styles.filterRow}
        renderItem={({ item }) => (
          <TouchableOpacity
            style={[styles.filterBtn, statusFilter === item && styles.filterActive]}
            onPress={() => setStatusFilter(item)}
          >
            <Text style={[styles.filterText, statusFilter === item && styles.filterTextActive]}>
              {item === 'all' ? 'All' : item.replace('_', ' ')}
            </Text>
          </TouchableOpacity>
        )}
      />

      {loading ? (
        <View style={styles.center}><ActivityIndicator size="large" color={colors.primary} /></View>
      ) : (
        <FlatList
          data={projects}
          keyExtractor={item => item.id.toString()}
          renderItem={renderProject}
          refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
          onEndReached={loadMore}
          onEndReachedThreshold={0.3}
          contentContainerStyle={styles.list}
          ListEmptyComponent={
            <View style={styles.center}>
              <Ionicons name="folder-open-outline" size={48} color={colors.textLight} />
              <Text style={styles.emptyText}>No projects found</Text>
            </View>
          }
        />
      )}

      {/* FAB for creating projects (client/admin) */}
      {(user?.role === 'client' || user?.role === 'admin') && (
        <TouchableOpacity
          style={styles.fab}
          onPress={() => navigation.navigate('CreateProject')}
        >
          <Ionicons name="add" size={28} color={colors.white} />
        </TouchableOpacity>
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: colors.background },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center', paddingTop: 80 },
  filterRow: { paddingHorizontal: spacing.md, paddingVertical: spacing.sm, gap: spacing.xs },
  filterBtn: { paddingHorizontal: 16, paddingVertical: 8, borderRadius: 20, backgroundColor: colors.card, borderWidth: 1, borderColor: colors.border },
  filterActive: { backgroundColor: colors.primary, borderColor: colors.primary },
  filterText: { fontSize: fontSize.sm, color: colors.textSecondary, fontWeight: '600', textTransform: 'capitalize' },
  filterTextActive: { color: colors.white },
  list: { padding: spacing.md, paddingBottom: 100 },
  card: { backgroundColor: colors.card, borderRadius: 16, padding: spacing.md, marginBottom: spacing.sm, shadowColor: colors.shadow, shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.05, shadowRadius: 4, elevation: 2 },
  cardHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' },
  title: { fontSize: fontSize.md, fontWeight: '700', color: colors.text, flex: 1, marginRight: spacing.sm },
  badge: { paddingHorizontal: 10, paddingVertical: 4, borderRadius: 8 },
  badgeText: { fontSize: fontSize.xs, fontWeight: '700', textTransform: 'capitalize' },
  desc: { fontSize: fontSize.sm, color: colors.textSecondary, marginTop: spacing.xs },
  cardFooter: { flexDirection: 'row', flexWrap: 'wrap', gap: spacing.sm, marginTop: spacing.sm },
  meta: { fontSize: fontSize.xs, color: colors.textLight },
  emptyText: { fontSize: fontSize.md, color: colors.textLight, marginTop: spacing.sm },
  fab: { position: 'absolute', bottom: 24, right: 24, width: 56, height: 56, borderRadius: 28, backgroundColor: colors.primary, justifyContent: 'center', alignItems: 'center', shadowColor: colors.shadow, shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.2, shadowRadius: 8, elevation: 5 },
});

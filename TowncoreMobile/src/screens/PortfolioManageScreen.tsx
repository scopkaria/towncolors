import React, { useEffect, useState, useCallback } from 'react';
import {
  View, Text, StyleSheet, FlatList, TouchableOpacity,
  ActivityIndicator, Alert, Image, ScrollView,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { useTheme } from '../contexts/ThemeContext';
import { portfolioApi } from '../api';
import { spacing } from '../theme';
import { TAB_BAR_TOTAL_HEIGHT } from '../constants/layout';
import ScreenHeader from '../components/ScreenHeader';
import API_BASE_URL from '../config';

const STATUS_FILTERS = ['all', 'pending', 'approved', 'rejected'] as const;
const STATUS_COLORS: Record<string, string> = {
  pending: '#f59e0b',
  approved: '#22c55e',
  rejected: '#ef4444',
};

export default function PortfolioManageScreen({ navigation }: any) {
  const { colors } = useTheme();
  const insets = useSafeAreaInsets();
  const [items, setItems] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);
  const [hasMore, setHasMore] = useState(true);
  const [statusFilter, setStatusFilter] = useState<string>('pending');

  const loadItems = useCallback(async (p = 1, append = false) => {
    try {
      if (p === 1) setLoading(true);
      const filterStatus = statusFilter === 'all' ? undefined : statusFilter;
      const data = await portfolioApi.adminList(p, filterStatus);
      const list = data.data || data;
      if (append) {
        setItems(prev => [...prev, ...list]);
      } else {
        setItems(list);
      }
      setHasMore(data.next_page_url !== null);
      setPage(p);
    } catch (err: any) {
      Alert.alert('Error', err.message);
    } finally {
      setLoading(false);
    }
  }, [statusFilter]);

  useEffect(() => {
    loadItems(1);
  }, [statusFilter]);

  async function handleStatusChange(id: number, status: string) {
    try {
      await portfolioApi.updateStatus(id, status);
      setItems(prev => prev.map(item =>
        item.id === id ? { ...item, status } : item
      ));
    } catch (err: any) {
      Alert.alert('Error', err.message);
    }
  }

  function getImageUrl(path: string | null) {
    if (!path) return null;
    if (path.startsWith('http')) return path;
    const baseUrl = API_BASE_URL.replace(/\/api$/, '');
    if (path.startsWith('/storage/')) return baseUrl + path;
    return baseUrl + '/storage/' + path;
  }

  function renderItem({ item }: { item: any }) {
    const imgUrl = getImageUrl(item.image_path);
    const statusColor = STATUS_COLORS[item.status] || '#94a3b8';
    return (
      <View style={[styles.card, { backgroundColor: colors.card }]}>
        {imgUrl ? (
          <Image source={{ uri: imgUrl }} style={styles.image} />
        ) : (
          <View style={[styles.imagePlaceholder, { backgroundColor: colors.border + '50' }]}>
            <Ionicons name="image-outline" size={32} color={colors.textLight} />
          </View>
        )}
        <View style={styles.cardBody}>
          <Text style={[styles.cardTitle, { color: colors.text }]} numberOfLines={1}>{item.title}</Text>
          <Text style={[styles.cardSub, { color: colors.textSecondary }]} numberOfLines={1}>
            by {item.freelancer?.name || 'Unknown'}
          </Text>
          <View style={[styles.statusBadge, { backgroundColor: statusColor + '15' }]}>
            <View style={[styles.statusDot, { backgroundColor: statusColor }]} />
            <Text style={[styles.statusLabel, { color: statusColor }]}>{item.status}</Text>
          </View>
        </View>
        <View style={styles.actions}>
          {item.status !== 'approved' && (
            <TouchableOpacity
              style={[styles.actionBtn, { backgroundColor: '#22c55e' + '15' }]}
              onPress={() => handleStatusChange(item.id, 'approved')}
            >
              <Ionicons name="checkmark" size={18} color="#22c55e" />
            </TouchableOpacity>
          )}
          {item.status !== 'rejected' && (
            <TouchableOpacity
              style={[styles.actionBtn, { backgroundColor: '#ef4444' + '15' }]}
              onPress={() => handleStatusChange(item.id, 'rejected')}
            >
              <Ionicons name="close" size={18} color="#ef4444" />
            </TouchableOpacity>
          )}
        </View>
      </View>
    );
  }

  return (
    <View style={[styles.container, { backgroundColor: colors.background }]}>
      <ScreenHeader title="Portfolio Review" onBack={() => navigation.goBack()} />

      <ScrollView horizontal showsHorizontalScrollIndicator={false} style={styles.filterRow} contentContainerStyle={styles.filterContent}>
        {STATUS_FILTERS.map(s => (
          <TouchableOpacity
            key={s}
            style={[styles.filterPill, { backgroundColor: statusFilter === s ? colors.primary : colors.card }]}
            onPress={() => setStatusFilter(s)}
          >
            <Text style={[styles.filterText, { color: statusFilter === s ? '#fff' : colors.textSecondary }]}>
              {s.charAt(0).toUpperCase() + s.slice(1)}
            </Text>
          </TouchableOpacity>
        ))}
      </ScrollView>

      {loading && page === 1 ? (
        <ActivityIndicator size="large" color={colors.primary} style={{ marginTop: 40 }} />
      ) : (
        <FlatList
          data={items}
          keyExtractor={(item) => item.id.toString()}
          renderItem={renderItem}
          contentContainerStyle={{ paddingHorizontal: spacing.md, paddingBottom: TAB_BAR_TOTAL_HEIGHT + insets.bottom + 20 }}
          onEndReached={() => hasMore && !loading && loadItems(page + 1, true)}
          onEndReachedThreshold={0.3}
          ListEmptyComponent={
            <View style={styles.empty}>
              <Ionicons name="images-outline" size={48} color={colors.textLight} />
              <Text style={[styles.emptyText, { color: colors.textLight }]}>No portfolio items</Text>
            </View>
          }
        />
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1 },

  filterRow: { marginTop: spacing.sm, maxHeight: 44 },
  filterContent: { paddingHorizontal: spacing.md, gap: 8 },
  filterPill: { paddingHorizontal: 16, paddingVertical: 8, borderRadius: 20 },
  filterText: { fontSize: 13, fontWeight: '600' },

  card: {
    flexDirection: 'row', alignItems: 'center', padding: 12, borderRadius: 16,
    marginTop: spacing.sm, gap: 12,
    shadowColor: '#000', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.04, shadowRadius: 4, elevation: 2,
  },
  image: { width: 64, height: 64, borderRadius: 12 },
  imagePlaceholder: { width: 64, height: 64, borderRadius: 12, justifyContent: 'center', alignItems: 'center' },
  cardBody: { flex: 1 },
  cardTitle: { fontSize: 15, fontWeight: '600' },
  cardSub: { fontSize: 12, marginTop: 2 },
  statusBadge: { flexDirection: 'row', alignItems: 'center', alignSelf: 'flex-start', paddingHorizontal: 8, paddingVertical: 3, borderRadius: 8, marginTop: 4, gap: 4 },
  statusDot: { width: 6, height: 6, borderRadius: 3 },
  statusLabel: { fontSize: 11, fontWeight: '700', textTransform: 'capitalize' },
  actions: { gap: 6 },
  actionBtn: { width: 36, height: 36, borderRadius: 10, justifyContent: 'center', alignItems: 'center' },

  empty: { alignItems: 'center', paddingTop: 60 },
  emptyText: { fontSize: 15, marginTop: 8 },
});

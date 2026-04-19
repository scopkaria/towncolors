import React, { useEffect, useState, useCallback } from 'react';
import {
  View, Text, StyleSheet, ScrollView, RefreshControl,
  TouchableOpacity, ActivityIndicator, StatusBar,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { useTheme } from '../contexts/ThemeContext';
import { checklistApi } from '../api';
import { spacing, fontSize } from '../theme';
import { TAB_BAR_TOTAL_HEIGHT } from '../constants/layout';

const STATUS_CONFIG: Record<string, { icon: string; color: string; label: string }> = {
  pending: { icon: 'ellipse-outline', color: '#94a3b8', label: 'Pending' },
  in_progress: { icon: 'time-outline', color: '#f59e0b', label: 'In Progress' },
  completed: { icon: 'checkmark-circle', color: '#16a34a', label: 'Completed' },
};

export default function ChecklistScreen({ navigation }: any) {
  const { colors, isDark } = useTheme();
  const insets = useSafeAreaInsets();

  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [items, setItems] = useState<any[]>([]);
  const [counts, setCounts] = useState<any>({});

  const loadData = useCallback(async () => {
    try {
      const result = await checklistApi.list();
      setItems(result.items || []);
      setCounts(result.counts || {});
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => { loadData(); }, [loadData]);

  const onRefresh = () => { setRefreshing(true); loadData(); };

  if (loading) {
    return (
      <View style={[styles.center, { backgroundColor: colors.background }]}>
        <ActivityIndicator size="large" color={colors.primary} />
      </View>
    );
  }

  const completionPct = counts.total > 0 ? Math.round((counts.completed / counts.total) * 100) : 0;

  return (
    <View style={[styles.container, { backgroundColor: colors.background }]}>
      <StatusBar barStyle={isDark ? 'light-content' : 'dark-content'} />

      {/* Header */}
      <View style={[styles.header, { backgroundColor: colors.primary, paddingTop: insets.top + 8 }]}>
        <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backBtn}>
          <Ionicons name="arrow-back" size={24} color="#fff" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Checklist</Text>
        <View style={{ width: 40 }} />
      </View>

      <ScrollView
        style={{ flex: 1 }}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor={colors.primary} />}
        showsVerticalScrollIndicator={false}
      >
        {/* Progress card */}
        <View style={[styles.progressCard, { backgroundColor: colors.card }]}>
          <View style={styles.progressRow}>
            <View style={styles.progressCircle}>
              <Text style={[styles.progressPct, { color: colors.primary }]}>{completionPct}%</Text>
            </View>
            <View style={{ flex: 1, marginLeft: 14 }}>
              <Text style={[styles.progressTitle, { color: colors.text }]}>Progress</Text>
              <View style={[styles.progressBar, { backgroundColor: colors.border }]}>
                <View style={[styles.progressFill, { width: `${completionPct}%`, backgroundColor: colors.primary }]} />
              </View>
              <View style={styles.countsRow}>
                <CountBadge label="Pending" count={counts.pending || 0} color="#94a3b8" bg={colors.inputBg} />
                <CountBadge label="In Progress" count={counts.in_progress || 0} color="#f59e0b" bg={colors.inputBg} />
                <CountBadge label="Done" count={counts.completed || 0} color="#16a34a" bg={colors.inputBg} />
              </View>
            </View>
          </View>
        </View>

        {/* Items */}
        <View style={{ paddingHorizontal: spacing.md }}>
          {items.length === 0 ? (
            <View style={styles.emptyState}>
              <Ionicons name="checkbox-outline" size={48} color={colors.textLight} />
              <Text style={[styles.emptyText, { color: colors.textLight }]}>No checklist items assigned yet</Text>
            </View>
          ) : (
            items.map((item: any) => {
              const config = STATUS_CONFIG[item.status] || STATUS_CONFIG.pending;
              return (
                <View key={item.id} style={[styles.itemCard, { backgroundColor: colors.card }]}>
                  <Ionicons name={config.icon as any} size={24} color={config.color} />
                  <View style={styles.itemInfo}>
                    <Text style={[
                      styles.itemTitle,
                      { color: item.status === 'completed' ? colors.textLight : colors.text },
                      item.status === 'completed' && styles.itemDone,
                    ]}>
                      {item.title}
                    </Text>
                    <View style={[styles.itemBadge, { backgroundColor: config.color + '15' }]}>
                      <Text style={[styles.itemBadgeText, { color: config.color }]}>{config.label}</Text>
                    </View>
                  </View>
                </View>
              );
            })
          )}
        </View>

        <View style={{ height: TAB_BAR_TOTAL_HEIGHT + insets.bottom + 20 }} />
      </ScrollView>
    </View>
  );
}

function CountBadge({ label, count, color, bg }: { label: string; count: number; color: string; bg: string }) {
  return (
    <View style={[styles.countBadge, { backgroundColor: bg }]}>
      <Text style={[styles.countNum, { color }]}>{count}</Text>
      <Text style={[styles.countLabel, { color }]}>{label}</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1 },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },

  header: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', paddingBottom: 14, paddingHorizontal: spacing.md },
  backBtn: { width: 40, height: 40, borderRadius: 20, backgroundColor: 'rgba(255,255,255,0.18)', justifyContent: 'center', alignItems: 'center' },
  headerTitle: { fontSize: 18, fontWeight: '800', color: '#fff', letterSpacing: 1 },

  progressCard: {
    margin: spacing.md, borderRadius: 16, padding: spacing.md,
    shadowColor: '#000', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.06, shadowRadius: 8, elevation: 3,
  },
  progressRow: { flexDirection: 'row', alignItems: 'center' },
  progressCircle: {
    width: 56, height: 56, borderRadius: 28, borderWidth: 3, borderColor: '#FFB162',
    justifyContent: 'center', alignItems: 'center',
  },
  progressPct: { fontSize: 16, fontWeight: '800' },
  progressTitle: { fontSize: 14, fontWeight: '700', marginBottom: 6 },
  progressBar: { height: 6, borderRadius: 3, overflow: 'hidden' },
  progressFill: { height: '100%', borderRadius: 3 },
  countsRow: { flexDirection: 'row', gap: 6, marginTop: 10 },
  countBadge: { flexDirection: 'row', alignItems: 'center', borderRadius: 8, paddingHorizontal: 8, paddingVertical: 4, gap: 4 },
  countNum: { fontSize: 13, fontWeight: '700' },
  countLabel: { fontSize: 10, fontWeight: '500' },

  itemCard: {
    flexDirection: 'row', alignItems: 'center', borderRadius: 14, padding: 14, marginBottom: 8,
    shadowColor: '#000', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.03, shadowRadius: 3, elevation: 1,
  },
  itemInfo: { flex: 1, marginLeft: 12 },
  itemTitle: { fontSize: 14, fontWeight: '600' },
  itemDone: { textDecorationLine: 'line-through' },
  itemBadge: { alignSelf: 'flex-start', paddingHorizontal: 8, paddingVertical: 2, borderRadius: 6, marginTop: 4 },
  itemBadgeText: { fontSize: 10, fontWeight: '700' },

  emptyState: { alignItems: 'center', paddingVertical: 60 },
  emptyText: { fontSize: 14, marginTop: 12 },
});

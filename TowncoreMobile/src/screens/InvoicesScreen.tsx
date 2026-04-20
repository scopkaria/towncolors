import React, { useEffect, useState, useCallback } from 'react';
import {
  View, Text, StyleSheet, FlatList, TouchableOpacity,
  RefreshControl, ActivityIndicator,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useAuth } from '../contexts/AuthContext';
import { useTheme } from '../contexts/ThemeContext';
import { invoicesApi } from '../api';
import { spacing, fontSize, statusColors } from '../theme';
import ScreenHeader from '../components/ScreenHeader';

export default function InvoicesScreen({ navigation }: any) {
  const { user } = useAuth();
  const { colors } = useTheme();
  const [invoices, setInvoices] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const loadInvoices = useCallback(async () => {
    try {
      if (user?.role === 'freelancer') {
        const result = await invoicesApi.freelancerInvoices();
        setInvoices(result.data);
      } else {
        const result = await invoicesApi.list();
        setInvoices(result.data);
      }
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, [user?.role]);

  useEffect(() => { loadInvoices(); }, [loadInvoices]);

  const onRefresh = () => { setRefreshing(true); loadInvoices(); };

  const renderInvoice = ({ item }: any) => {
    const isFreelancerInvoice = user?.role === 'freelancer';

    return (
      <TouchableOpacity
        style={[styles.card, { backgroundColor: colors.card }]}
        onPress={() => !isFreelancerInvoice && navigation.navigate('InvoiceDetail', { id: item.id })}
      >
        <View style={styles.cardHeader}>
          <Ionicons name="receipt" size={20} color={colors.primary} />
          <View style={styles.cardInfo}>
            <Text style={[styles.title, { color: colors.text }]} numberOfLines={1}>
              {item.project?.title || `Invoice #${item.id}`}
            </Text>
            {!isFreelancerInvoice && (
              <Text style={[styles.amount, { color: colors.primary }]}>${parseFloat(item.total_amount).toLocaleString()}</Text>
            )}
          </View>
        </View>

        <View style={styles.cardFooter}>
          {!isFreelancerInvoice && item.project?.client && (
            <Text style={[styles.meta, { color: colors.textSecondary }]}>{item.project.client.name}</Text>
          )}
          <View style={[styles.badge, { backgroundColor: (statusColors[item.status] || '#94a3b8') + '20' }]}>
            <Text style={[styles.badgeText, { color: statusColors[item.status] || '#94a3b8' }]}>
              {item.status}
            </Text>
          </View>
        </View>

        {!isFreelancerInvoice && (
          <View style={[styles.progressBar, { backgroundColor: colors.border }]}>
            <View
              style={[
                styles.progressFill,
                { backgroundColor: colors.success, width: `${Math.min(100, (parseFloat(item.paid_amount) / parseFloat(item.total_amount)) * 100)}%` },
              ]}
            />
          </View>
        )}
      </TouchableOpacity>
    );
  };

  if (loading) {
    return <View style={styles.center}><ActivityIndicator size="large" color={colors.primary} /></View>;
  }

  return (
    <View style={[styles.container, { backgroundColor: colors.background }]}>
      <ScreenHeader
        title={user?.role === 'freelancer' ? 'Earnings' : 'Invoices'}
        onBack={() => navigation.goBack()}
      />
      <FlatList
        data={invoices}
        keyExtractor={item => item.id.toString()}
        renderItem={renderInvoice}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
        contentContainerStyle={styles.list}
        ListEmptyComponent={
          <View style={styles.center}>
            <Ionicons name="receipt-outline" size={48} color={colors.textLight} />
            <Text style={styles.emptyText}>No invoices yet</Text>
          </View>
        }
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1 },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center', paddingTop: 80 },
  list: { padding: spacing.md, paddingBottom: 20 },
  card: { borderRadius: 16, padding: spacing.md, marginBottom: spacing.sm, shadowColor: '#000', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.05, shadowRadius: 4, elevation: 2 },
  cardHeader: { flexDirection: 'row', alignItems: 'center', gap: spacing.sm },
  cardInfo: { flex: 1 },
  title: { fontSize: fontSize.md, fontWeight: '700' },
  amount: { fontSize: fontSize.lg, fontWeight: '800', marginTop: 2 },
  cardFooter: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginTop: spacing.sm },
  meta: { fontSize: fontSize.xs },
  badge: { paddingHorizontal: 10, paddingVertical: 4, borderRadius: 8 },
  badgeText: { fontSize: fontSize.xs, fontWeight: '700', textTransform: 'capitalize' },
  progressBar: { height: 4, borderRadius: 2, marginTop: spacing.sm },
  progressFill: { height: 4, borderRadius: 2 },
  emptyText: { fontSize: fontSize.md, marginTop: spacing.sm },
});

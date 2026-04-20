import React, { useEffect, useState } from 'react';
import {
  View, Text, StyleSheet, ScrollView, ActivityIndicator,
} from 'react-native';
import { invoicesApi } from '../api';
import { useTheme } from '../contexts/ThemeContext';
import { spacing, fontSize, statusColors } from '../theme';
import ScreenHeader from '../components/ScreenHeader';

export default function InvoiceDetailScreen({ route, navigation }: any) {
  const { colors } = useTheme();
  const { id } = route.params;
  const [invoice, setInvoice] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    invoicesApi.get(id).then(setInvoice).catch(console.error).finally(() => setLoading(false));
  }, [id]);

  if (loading) {
    return <View style={styles.center}><ActivityIndicator size="large" color={colors.primary} /></View>;
  }

  if (!invoice) {
    return <View style={styles.center}><Text>Invoice not found</Text></View>;
  }

  const remaining = parseFloat(invoice.total_amount) - parseFloat(invoice.paid_amount);

  return (
    <View style={[styles.container, { backgroundColor: colors.background }]}>
    <ScreenHeader title="Invoice Details" onBack={() => navigation.goBack()} />
    <ScrollView style={{ flex: 1 }}>
      <View style={styles.headerSection}>
        <Text style={[styles.title, { color: colors.text }]}>{invoice.project?.title || `Invoice #${invoice.id}`}</Text>
        <View style={[styles.badge, { backgroundColor: (statusColors[invoice.status] || '#94a3b8') + '20' }]}>
          <Text style={[styles.badgeText, { color: statusColors[invoice.status] || '#94a3b8' }]}>
            {invoice.status}
          </Text>
        </View>
      </View>

      <View style={[styles.amountCard, { backgroundColor: colors.card }]}>
        <View style={styles.amountRow}>
          <Text style={[styles.amountLabel, { color: colors.textSecondary }]}>Total</Text>
          <Text style={[styles.amountValue, { color: colors.text }]}>${parseFloat(invoice.total_amount).toLocaleString()}</Text>
        </View>
        <View style={[styles.divider, { backgroundColor: colors.border }]} />
        <View style={styles.amountRow}>
          <Text style={[styles.amountLabel, { color: colors.textSecondary }]}>Paid</Text>
          <Text style={[styles.amountValue, { color: colors.success }]}>${parseFloat(invoice.paid_amount).toLocaleString()}</Text>
        </View>
        <View style={[styles.divider, { backgroundColor: colors.border }]} />
        <View style={styles.amountRow}>
          <Text style={[styles.amountLabel, { color: colors.textSecondary }]}>Remaining</Text>
          <Text style={[styles.amountValue, { color: remaining > 0 ? colors.danger : colors.success }]}>
            ${remaining.toLocaleString()}
          </Text>
        </View>
      </View>

      {invoice.currency && (
        <View style={styles.section}>
          <Text style={[styles.sectionTitle, { color: colors.text }]}>Currency</Text>
          <Text style={[styles.detail, { color: colors.textSecondary }]}>{invoice.currency} (Rate: {invoice.exchange_rate})</Text>
        </View>
      )}

      {invoice.expires_at && (
        <View style={styles.section}>
          <Text style={[styles.sectionTitle, { color: colors.text }]}>Expires</Text>
          <Text style={[styles.detail, { color: colors.textSecondary }]}>{new Date(invoice.expires_at).toLocaleDateString()}</Text>
        </View>
      )}

      {invoice.payments?.length > 0 && (
        <View style={styles.section}>
          <Text style={[styles.sectionTitle, { color: colors.text }]}>Payment History</Text>
          {invoice.payments.map((p: any) => (
            <View key={p.id} style={[styles.paymentRow, { backgroundColor: colors.card }]}>
              <Text style={[styles.paymentAmount, { color: colors.success }]}>${parseFloat(p.amount).toLocaleString()}</Text>
              <Text style={[styles.paymentDate, { color: colors.textLight }]}>{new Date(p.created_at).toLocaleDateString()}</Text>
            </View>
          ))}
        </View>
      )}
    </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1 },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  headerSection: { padding: spacing.lg },
  title: { fontSize: fontSize.xl, fontWeight: '800', marginBottom: spacing.sm },
  badge: { alignSelf: 'flex-start', paddingHorizontal: 12, paddingVertical: 5, borderRadius: 8 },
  badgeText: { fontSize: fontSize.sm, fontWeight: '700', textTransform: 'capitalize' },
  amountCard: { borderRadius: 16, marginHorizontal: spacing.lg, padding: spacing.lg, shadowColor: '#000', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.08, shadowRadius: 8, elevation: 3 },
  amountRow: { flexDirection: 'row', justifyContent: 'space-between', paddingVertical: spacing.sm },
  amountLabel: { fontSize: fontSize.md },
  amountValue: { fontSize: fontSize.lg, fontWeight: '800' },
  divider: { height: 1 },
  section: { paddingHorizontal: spacing.lg, marginTop: spacing.lg },
  sectionTitle: { fontSize: fontSize.md, fontWeight: '700', marginBottom: spacing.sm },
  detail: { fontSize: fontSize.sm },
  paymentRow: { flexDirection: 'row', justifyContent: 'space-between', padding: spacing.md, borderRadius: 10, marginBottom: spacing.xs },
  paymentAmount: { fontSize: fontSize.md, fontWeight: '700' },
  paymentDate: { fontSize: fontSize.sm },
});

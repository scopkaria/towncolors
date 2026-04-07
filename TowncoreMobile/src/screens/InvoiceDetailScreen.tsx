import React, { useEffect, useState } from 'react';
import {
  View, Text, StyleSheet, ScrollView, ActivityIndicator,
} from 'react-native';
import { invoicesApi } from '../api';
import { colors, spacing, fontSize, statusColors } from '../theme';

export default function InvoiceDetailScreen({ route }: any) {
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
    <ScrollView style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.title}>{invoice.project?.title || `Invoice #${invoice.id}`}</Text>
        <View style={[styles.badge, { backgroundColor: (statusColors[invoice.status] || '#94a3b8') + '20' }]}>
          <Text style={[styles.badgeText, { color: statusColors[invoice.status] || '#94a3b8' }]}>
            {invoice.status}
          </Text>
        </View>
      </View>

      <View style={styles.amountCard}>
        <View style={styles.amountRow}>
          <Text style={styles.amountLabel}>Total</Text>
          <Text style={styles.amountValue}>${parseFloat(invoice.total_amount).toLocaleString()}</Text>
        </View>
        <View style={styles.divider} />
        <View style={styles.amountRow}>
          <Text style={styles.amountLabel}>Paid</Text>
          <Text style={[styles.amountValue, { color: colors.success }]}>${parseFloat(invoice.paid_amount).toLocaleString()}</Text>
        </View>
        <View style={styles.divider} />
        <View style={styles.amountRow}>
          <Text style={styles.amountLabel}>Remaining</Text>
          <Text style={[styles.amountValue, { color: remaining > 0 ? colors.danger : colors.success }]}>
            ${remaining.toLocaleString()}
          </Text>
        </View>
      </View>

      {invoice.currency && (
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Currency</Text>
          <Text style={styles.detail}>{invoice.currency} (Rate: {invoice.exchange_rate})</Text>
        </View>
      )}

      {invoice.expires_at && (
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Expires</Text>
          <Text style={styles.detail}>{new Date(invoice.expires_at).toLocaleDateString()}</Text>
        </View>
      )}

      {invoice.payments?.length > 0 && (
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Payment History</Text>
          {invoice.payments.map((p: any) => (
            <View key={p.id} style={styles.paymentRow}>
              <Text style={styles.paymentAmount}>${parseFloat(p.amount).toLocaleString()}</Text>
              <Text style={styles.paymentDate}>{new Date(p.created_at).toLocaleDateString()}</Text>
            </View>
          ))}
        </View>
      )}
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: colors.background },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  header: { padding: spacing.lg },
  title: { fontSize: fontSize.xl, fontWeight: '800', color: colors.text, marginBottom: spacing.sm },
  badge: { alignSelf: 'flex-start', paddingHorizontal: 12, paddingVertical: 5, borderRadius: 8 },
  badgeText: { fontSize: fontSize.sm, fontWeight: '700', textTransform: 'capitalize' },
  amountCard: { backgroundColor: colors.card, borderRadius: 16, marginHorizontal: spacing.lg, padding: spacing.lg, shadowColor: colors.shadow, shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.08, shadowRadius: 8, elevation: 3 },
  amountRow: { flexDirection: 'row', justifyContent: 'space-between', paddingVertical: spacing.sm },
  amountLabel: { fontSize: fontSize.md, color: colors.textSecondary },
  amountValue: { fontSize: fontSize.lg, fontWeight: '800', color: colors.text },
  divider: { height: 1, backgroundColor: colors.border },
  section: { paddingHorizontal: spacing.lg, marginTop: spacing.lg },
  sectionTitle: { fontSize: fontSize.md, fontWeight: '700', color: colors.text, marginBottom: spacing.sm },
  detail: { fontSize: fontSize.sm, color: colors.textSecondary },
  paymentRow: { flexDirection: 'row', justifyContent: 'space-between', backgroundColor: colors.card, padding: spacing.md, borderRadius: 10, marginBottom: spacing.xs },
  paymentAmount: { fontSize: fontSize.md, fontWeight: '700', color: colors.success },
  paymentDate: { fontSize: fontSize.sm, color: colors.textLight },
});

import React, { useEffect, useState, useCallback } from 'react';
import {
  View, Text, StyleSheet, ScrollView, RefreshControl,
  TouchableOpacity, ActivityIndicator, Alert, Modal,
  TextInput, StatusBar,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { useAuth } from '../contexts/AuthContext';
import { useTheme } from '../contexts/ThemeContext';
import { subscriptionApi } from '../api';
import { spacing, fontSize } from '../theme';
import { TAB_BAR_TOTAL_HEIGHT } from '../constants/layout';
import ScreenHeader from '../components/ScreenHeader';

const PLAN_COLORS: Record<string, string> = {
  green: '#16a34a',
  blue: '#3b82f6',
  purple: '#8b5cf6',
  black: '#1e293b',
};

export default function SubscriptionScreen({ navigation }: any) {
  const { user } = useAuth();
  const { colors, isDark } = useTheme();
  const insets = useSafeAreaInsets();

  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [data, setData] = useState<any>(null);
  const [selectedPlan, setSelectedPlan] = useState<any>(null);
  const [modalVisible, setModalVisible] = useState(false);
  const [billingCycle, setBillingCycle] = useState<'monthly' | 'yearly'>('monthly');
  const [paymentMethod, setPaymentMethod] = useState('');
  const [paymentRef, setPaymentRef] = useState('');
  const [submitting, setSubmitting] = useState(false);

  const loadData = useCallback(async () => {
    try {
      const result = await subscriptionApi.getPlans();
      setData(result);
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => { loadData(); }, [loadData]);

  const onRefresh = () => { setRefreshing(true); loadData(); };

  const handleStartTrial = async () => {
    Alert.alert('Start Free Trial', 'Activate a 5-day free trial?', [
      { text: 'Cancel', style: 'cancel' },
      {
        text: 'Start Trial', onPress: async () => {
          try {
            await subscriptionApi.startTrial();
            Alert.alert('Success', 'Your 5-day free trial is now active!');
            loadData();
          } catch (err: any) {
            Alert.alert('Error', err.message || 'Failed to start trial');
          }
        },
      },
    ]);
  };

  const handleSubscribe = (plan: any) => {
    setSelectedPlan(plan);
    setBillingCycle('monthly');
    setPaymentMethod('');
    setPaymentRef('');
    setModalVisible(true);
  };

  const submitRequest = async () => {
    if (!paymentMethod) {
      Alert.alert('Required', 'Please select a payment method.');
      return;
    }
    setSubmitting(true);
    try {
      await subscriptionApi.requestSubscription({
        plan_id: selectedPlan.id,
        billing_cycle: billingCycle,
        payment_method: paymentMethod,
        payment_reference: paymentRef || undefined,
      });
      setModalVisible(false);
      Alert.alert('Success', 'Subscription request submitted! We will review it shortly.');
      loadData();
    } catch (err: any) {
      Alert.alert('Error', err.message || 'Submission failed');
    } finally {
      setSubmitting(false);
    }
  };

  if (loading) {
    return (
      <View style={[styles.center, { backgroundColor: colors.background }]}>
        <ActivityIndicator size="large" color={colors.primary} />
      </View>
    );
  }

  const status = data?.status || {};
  const activeSub = data?.active_subscription;
  const plans = data?.plans || [];
  const methods = data?.payment_methods || {};

  return (
    <View style={[styles.container, { backgroundColor: colors.background }]}>
      <StatusBar barStyle="light-content" />

      <ScreenHeader title="My Plan" onBack={() => navigation.goBack()} />

      <ScrollView
        style={{ flex: 1 }}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor={colors.primary} />}
        showsVerticalScrollIndicator={false}
      >
        {/* Current Status Card */}
        <View style={[styles.statusCard, { backgroundColor: colors.card }]}>
          <View style={styles.statusRow}>
            <Ionicons
              name={status.has_full_access ? 'shield-checkmark' : 'shield-outline'}
              size={28}
              color={status.has_full_access ? '#16a34a' : colors.textLight}
            />
            <View style={{ flex: 1, marginLeft: 12 }}>
              <Text style={[styles.statusTitle, { color: colors.text }]}>
                {activeSub ? activeSub.plan : status.is_trial_active ? 'Free Trial' : 'No Active Plan'}
              </Text>
              <Text style={[styles.statusSub, { color: colors.textSecondary }]}>
                {activeSub
                  ? `${activeSub.billing_cycle} • ${activeSub.days_left} days left`
                  : status.is_trial_active
                    ? `Trial ends ${status.trial_end_date}`
                    : 'Subscribe to unlock all features'
                }
              </Text>
            </View>
            {activeSub && (
              <View style={[styles.activeBadge, { backgroundColor: '#16a34a15' }]}>
                <Text style={[styles.activeBadgeText, { color: '#16a34a' }]}>ACTIVE</Text>
              </View>
            )}
          </View>

          {/* Trial button */}
          {status.can_start_trial && !activeSub && (
            <TouchableOpacity
              style={[styles.trialBtn, { backgroundColor: colors.primary }]}
              onPress={handleStartTrial}
              activeOpacity={0.8}
            >
              <Ionicons name="flash" size={18} color="#fff" />
              <Text style={styles.trialBtnText}>Start 5-Day Free Trial</Text>
            </TouchableOpacity>
          )}
        </View>

        {/* Plans */}
        <Text style={[styles.sectionLabel, { color: colors.text }]}>Available Plans</Text>

        {plans.map((plan: any) => {
          const planColor = PLAN_COLORS[plan.color] || colors.primary;
          const isCurrentPlan = activeSub?.plan === plan.name;

          return (
            <View key={plan.id} style={[styles.planCard, { backgroundColor: colors.card, borderLeftColor: planColor }]}>
              <View style={styles.planHeader}>
                <View style={[styles.planDot, { backgroundColor: planColor }]} />
                <Text style={[styles.planName, { color: colors.text }]}>{plan.name}</Text>
                {isCurrentPlan && (
                  <View style={[styles.currentBadge, { backgroundColor: planColor + '15' }]}>
                    <Text style={[styles.currentBadgeText, { color: planColor }]}>CURRENT</Text>
                  </View>
                )}
              </View>

              {/* Pricing */}
              <View style={styles.pricingRow}>
                <View style={styles.priceBlock}>
                  <Text style={[styles.priceAmount, { color: colors.text }]}>
                    ${plan.price_monthly}
                  </Text>
                  <Text style={[styles.pricePeriod, { color: colors.textSecondary }]}>/month</Text>
                </View>
                <View style={[styles.priceDivider, { backgroundColor: colors.border }]} />
                <View style={styles.priceBlock}>
                  <Text style={[styles.priceAmount, { color: colors.text }]}>
                    ${plan.price_yearly}
                  </Text>
                  <Text style={[styles.pricePeriod, { color: colors.textSecondary }]}>/year</Text>
                </View>
              </View>

              {/* Features */}
              {(plan.features || []).map((f: string, i: number) => (
                <View key={i} style={styles.featureRow}>
                  <Ionicons name="checkmark-circle" size={16} color={planColor} />
                  <Text style={[styles.featureText, { color: colors.textSecondary }]}>{f}</Text>
                </View>
              ))}

              {!isCurrentPlan && (
                <TouchableOpacity
                  style={[styles.subscribeBtn, { backgroundColor: planColor }]}
                  onPress={() => handleSubscribe(plan)}
                  activeOpacity={0.8}
                >
                  <Text style={styles.subscribeBtnText}>Subscribe</Text>
                </TouchableOpacity>
              )}
            </View>
          );
        })}

        <View style={{ height: TAB_BAR_TOTAL_HEIGHT + insets.bottom + 20 }} />
      </ScrollView>

      {/* Subscribe Modal */}
      <Modal visible={modalVisible} transparent animationType="slide" onRequestClose={() => setModalVisible(false)}>
        <View style={styles.modalOverlay}>
          <View style={[styles.modalContent, { backgroundColor: colors.card }]}>
            <View style={styles.modalHeader}>
              <Text style={[styles.modalTitle, { color: colors.text }]}>
                Subscribe to {selectedPlan?.name}
              </Text>
              <TouchableOpacity onPress={() => setModalVisible(false)}>
                <Ionicons name="close" size={24} color={colors.textSecondary} />
              </TouchableOpacity>
            </View>

            {/* Billing cycle toggle */}
            <Text style={[styles.fieldLabel, { color: colors.textSecondary }]}>Billing Cycle</Text>
            <View style={styles.cycleRow}>
              {(['monthly', 'yearly'] as const).map(cycle => (
                <TouchableOpacity
                  key={cycle}
                  style={[
                    styles.cycleBtn,
                    { backgroundColor: billingCycle === cycle ? colors.primary : colors.inputBg },
                  ]}
                  onPress={() => setBillingCycle(cycle)}
                >
                  <Text style={{ color: billingCycle === cycle ? '#fff' : colors.text, fontWeight: '600' }}>
                    {cycle === 'monthly' ? `Monthly ($${selectedPlan?.price_monthly})` : `Yearly ($${selectedPlan?.price_yearly})`}
                  </Text>
                </TouchableOpacity>
              ))}
            </View>

            {/* Payment method */}
            <Text style={[styles.fieldLabel, { color: colors.textSecondary }]}>Payment Method</Text>
            <View style={styles.methodsGrid}>
              {Object.entries(methods).map(([key, label]) => (
                <TouchableOpacity
                  key={key}
                  style={[
                    styles.methodBtn,
                    { backgroundColor: paymentMethod === key ? colors.primary : colors.inputBg, borderColor: paymentMethod === key ? colors.primary : colors.border },
                  ]}
                  onPress={() => setPaymentMethod(key)}
                >
                  <Ionicons
                    name={key === 'card' ? 'card' : key === 'bank' ? 'business' : 'phone-portrait'}
                    size={18}
                    color={paymentMethod === key ? '#fff' : colors.textSecondary}
                  />
                  <Text style={{ color: paymentMethod === key ? '#fff' : colors.text, fontSize: 12, fontWeight: '600', marginTop: 4 }}>
                    {label as string}
                  </Text>
                </TouchableOpacity>
              ))}
            </View>

            {/* Payment reference */}
            <Text style={[styles.fieldLabel, { color: colors.textSecondary }]}>Payment Reference (optional)</Text>
            <TextInput
              style={[styles.input, { backgroundColor: colors.inputBg, color: colors.text, borderColor: colors.border }]}
              value={paymentRef}
              onChangeText={setPaymentRef}
              placeholder="Transaction ID or reference..."
              placeholderTextColor={colors.textLight}
            />

            <TouchableOpacity
              style={[styles.submitBtn, { backgroundColor: colors.primary, opacity: submitting ? 0.6 : 1 }]}
              onPress={submitRequest}
              disabled={submitting}
              activeOpacity={0.8}
            >
              {submitting ? (
                <ActivityIndicator color="#fff" />
              ) : (
                <Text style={styles.submitBtnText}>Submit Request</Text>
              )}
            </TouchableOpacity>
          </View>
        </View>
      </Modal>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1 },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },

  header: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', paddingBottom: 14, paddingHorizontal: spacing.md },
  backBtn: { width: 40, height: 40, borderRadius: 20, backgroundColor: 'rgba(255,255,255,0.18)', justifyContent: 'center', alignItems: 'center' },
  headerTitle: { fontSize: 18, fontWeight: '800', color: '#fff', letterSpacing: 1 },

  statusCard: {
    margin: spacing.md, borderRadius: 16, padding: spacing.md,
    shadowColor: '#000', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.06, shadowRadius: 8, elevation: 3,
  },
  statusRow: { flexDirection: 'row', alignItems: 'center' },
  statusTitle: { fontSize: 16, fontWeight: '700' },
  statusSub: { fontSize: 12, marginTop: 2 },
  activeBadge: { paddingHorizontal: 10, paddingVertical: 4, borderRadius: 8 },
  activeBadgeText: { fontSize: 10, fontWeight: '800' },

  trialBtn: {
    flexDirection: 'row', alignItems: 'center', justifyContent: 'center',
    borderRadius: 12, paddingVertical: 12, marginTop: 14, gap: 6,
  },
  trialBtnText: { fontSize: 14, fontWeight: '700', color: '#fff' },

  sectionLabel: { fontSize: 16, fontWeight: '700', marginHorizontal: spacing.md, marginTop: spacing.lg, marginBottom: spacing.sm },

  planCard: {
    marginHorizontal: spacing.md, marginBottom: spacing.md, borderRadius: 16, padding: spacing.md,
    borderLeftWidth: 4,
    shadowColor: '#000', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.04, shadowRadius: 4, elevation: 1,
  },
  planHeader: { flexDirection: 'row', alignItems: 'center', marginBottom: 10 },
  planDot: { width: 10, height: 10, borderRadius: 5, marginRight: 8 },
  planName: { fontSize: 17, fontWeight: '700', flex: 1 },
  currentBadge: { paddingHorizontal: 8, paddingVertical: 3, borderRadius: 6 },
  currentBadgeText: { fontSize: 10, fontWeight: '800' },

  pricingRow: { flexDirection: 'row', alignItems: 'center', marginBottom: 12 },
  priceBlock: { flex: 1, alignItems: 'center' },
  priceAmount: { fontSize: 22, fontWeight: '800' },
  pricePeriod: { fontSize: 11, marginTop: 2 },
  priceDivider: { width: 1, height: 30, marginHorizontal: 8 },

  featureRow: { flexDirection: 'row', alignItems: 'center', gap: 8, paddingVertical: 3 },
  featureText: { fontSize: 13, flex: 1 },

  subscribeBtn: { borderRadius: 12, paddingVertical: 12, alignItems: 'center', marginTop: 14 },
  subscribeBtnText: { fontSize: 14, fontWeight: '700', color: '#fff' },

  // Modal
  modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.5)', justifyContent: 'flex-end' },
  modalContent: { borderTopLeftRadius: 24, borderTopRightRadius: 24, padding: spacing.lg, maxHeight: '85%' },
  modalHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: spacing.md },
  modalTitle: { fontSize: 18, fontWeight: '700' },

  fieldLabel: { fontSize: 12, fontWeight: '600', marginTop: spacing.md, marginBottom: 6, textTransform: 'uppercase', letterSpacing: 1 },

  cycleRow: { flexDirection: 'row', gap: 8 },
  cycleBtn: { flex: 1, borderRadius: 10, paddingVertical: 12, alignItems: 'center' },

  methodsGrid: { flexDirection: 'row', flexWrap: 'wrap', gap: 8 },
  methodBtn: {
    width: '30%', borderRadius: 12, paddingVertical: 14, alignItems: 'center', borderWidth: 1,
    flexGrow: 1,
  },

  input: { borderWidth: 1, borderRadius: 12, paddingHorizontal: 14, paddingVertical: 12, fontSize: 14 },

  submitBtn: { borderRadius: 14, paddingVertical: 14, alignItems: 'center', marginTop: spacing.lg },
  submitBtnText: { fontSize: 15, fontWeight: '700', color: '#fff' },
});

import React, { useEffect, useState } from 'react';
import {
  View, Text, StyleSheet, ScrollView, TouchableOpacity,
  ActivityIndicator, Alert, Dimensions, Modal, FlatList,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { useAuth } from '../contexts/AuthContext';
import { useTheme } from '../contexts/ThemeContext';
import { projectsApi, chatApi, adminUsersApi } from '../api';
import { spacing, fontSize, statusColors } from '../theme';
import { TAB_BAR_TOTAL_HEIGHT } from '../constants/layout';
import ScreenHeader from '../components/ScreenHeader';

const { width: SCREEN_WIDTH } = Dimensions.get('window');
const TABS = ['Overview', 'Files', 'Activity'] as const;

export default function ProjectDetailScreen({ route, navigation }: any) {
  const { id } = route.params;
  const { user } = useAuth();
  const { colors } = useTheme();
  const insets = useSafeAreaInsets();
  const [project, setProject] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState<typeof TABS[number]>('Overview');
  const [showAssignModal, setShowAssignModal] = useState(false);
  const [freelancers, setFreelancers] = useState<any[]>([]);
  const [loadingFreelancers, setLoadingFreelancers] = useState(false);

  useEffect(() => {
    loadProject();
  }, [id]);

  async function loadProject() {
    try {
      const data = await projectsApi.get(id);
      setProject(data);
    } catch (err: any) {
      console.error('Failed to load project', err);
    } finally {
      setLoading(false);
    }
  }

  async function handleStatusUpdate(status: string) {
    try {
      await projectsApi.updateStatus(id, status);
      loadProject();
    } catch (err: any) {
      Alert.alert('Error', err.message);
    }
  }

  async function openAssignModal() {
    setShowAssignModal(true);
    setLoadingFreelancers(true);
    try {
      const data = await adminUsersApi.freelancers();
      setFreelancers(data);
    } catch (err: any) {
      Alert.alert('Error', 'Failed to load freelancers');
    } finally {
      setLoadingFreelancers(false);
    }
  }

  async function handleAssign(freelancerId: number) {
    try {
      await projectsApi.assign(id, freelancerId);
      setShowAssignModal(false);
      Alert.alert('Success', 'Freelancer assigned');
      loadProject();
    } catch (err: any) {
      Alert.alert('Error', err.message);
    }
  }

  if (loading) {
    return (
      <View style={[styles.center, { backgroundColor: colors.background }]}>
        <ScreenHeader title="Project" onBack={() => navigation.goBack()} />
        <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
          <ActivityIndicator size="large" color={colors.primary} />
        </View>
      </View>
    );
  }

  if (!project) {
    return (
      <View style={[styles.center, { backgroundColor: colors.background }]}>
        <ScreenHeader title="Project" onBack={() => navigation.goBack()} />
        <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center', padding: spacing.xl }}>
          <View style={[styles.emptyIcon, { backgroundColor: colors.card }]}>
            <Ionicons name="folder-open-outline" size={48} color={colors.textLight} />
          </View>
          <Text style={[styles.emptyTitle, { color: colors.text }]}>Project not found</Text>
          <Text style={[styles.emptyDesc, { color: colors.textSecondary }]}>
            This project may have been removed or is unavailable.
          </Text>
          <TouchableOpacity
            style={[styles.emptyBtn, { backgroundColor: colors.primary }]}
            onPress={() => navigation.goBack()}
          >
            <Text style={styles.emptyBtnText}>Go Back</Text>
          </TouchableOpacity>
        </View>
      </View>
    );
  }

  const sColor = statusColors[project.status] || '#94a3b8';
  const progress = project.status === 'completed' ? 100
    : project.status === 'in_progress' ? 60
    : project.status === 'assigned' ? 25
    : 10;
  const remaining = project.invoice
    ? parseFloat(project.invoice.total_amount) - parseFloat(project.invoice.paid_amount)
    : 0;

  return (
    <View style={[styles.container, { backgroundColor: colors.background }]}>
      <ScreenHeader title="Project Details" onBack={() => navigation.goBack()} />

      <ScrollView
        style={{ flex: 1 }}
        showsVerticalScrollIndicator={false}
        contentContainerStyle={{ paddingBottom: TAB_BAR_TOTAL_HEIGHT + insets.bottom + 20 }}
      >
        {/* ── Hero Summary Card ──────────────────────── */}
        <View style={[styles.heroCard, { backgroundColor: colors.card }]}>
          {/* Status accent header */}
          <View style={[styles.heroHeader, { backgroundColor: sColor + '12' }]}>
            <View style={[styles.heroStatusIcon, { backgroundColor: sColor + '20' }]}>
              <Ionicons
                name={
                  project.status === 'completed' ? 'checkmark-circle' :
                  project.status === 'in_progress' ? 'play-circle' :
                  project.status === 'assigned' ? 'person' : 'time'
                }
                size={24}
                color={sColor}
              />
            </View>
            <View style={[styles.heroStatusBadge, { backgroundColor: sColor + '20' }]}>
              <View style={[styles.heroDot, { backgroundColor: sColor }]} />
              <Text style={[styles.heroStatusText, { color: sColor }]}>
                {project.status?.replace('_', ' ')}
              </Text>
            </View>
          </View>

          <View style={styles.heroBody}>
            <Text style={[styles.heroTitle, { color: colors.text }]}>{project.title}</Text>

            {/* Progress bar */}
            <View style={styles.heroProgressWrap}>
              <View style={styles.heroProgressInfo}>
                <Text style={[styles.heroProgressLabel, { color: colors.textSecondary }]}>Progress</Text>
                <Text style={[styles.heroProgressValue, { color: sColor }]}>{progress}%</Text>
              </View>
              <View style={[styles.heroProgressTrack, { backgroundColor: colors.border + '50' }]}>
                <View style={[styles.heroProgressFill, { width: `${progress}%`, backgroundColor: sColor }]} />
              </View>
            </View>

            {/* Key info row */}
            <View style={styles.heroInfoGrid}>
              {project.client && (
                <View style={styles.heroInfoItem}>
                  <View style={[styles.heroInfoIcon, { backgroundColor: colors.primary + '15' }]}>
                    <Ionicons name="person" size={14} color={colors.primary} />
                  </View>
                  <View>
                    <Text style={[styles.heroInfoLabel, { color: colors.textLight }]}>Client</Text>
                    <Text style={[styles.heroInfoValue, { color: colors.text }]} numberOfLines={1}>{project.client.name}</Text>
                  </View>
                </View>
              )}
              {project.freelancer && (
                <View style={styles.heroInfoItem}>
                  <View style={[styles.heroInfoIcon, { backgroundColor: '#8b5cf6' + '15' }]}>
                    <Ionicons name="code-working" size={14} color="#8b5cf6" />
                  </View>
                  <View>
                    <Text style={[styles.heroInfoLabel, { color: colors.textLight }]}>Freelancer</Text>
                    <Text style={[styles.heroInfoValue, { color: colors.text }]} numberOfLines={1}>{project.freelancer.name}</Text>
                  </View>
                </View>
              )}
              {project.created_at && (
                <View style={styles.heroInfoItem}>
                  <View style={[styles.heroInfoIcon, { backgroundColor: colors.warning + '15' }]}>
                    <Ionicons name="calendar-outline" size={14} color={colors.warning} />
                  </View>
                  <View>
                    <Text style={[styles.heroInfoLabel, { color: colors.textLight }]}>Created</Text>
                    <Text style={[styles.heroInfoValue, { color: colors.text }]}>
                      {new Date(project.created_at).toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' })}
                    </Text>
                  </View>
                </View>
              )}
            </View>
          </View>
        </View>

        {/* ── Action Buttons ────────────────────────────── */}
        <View style={styles.actionRow}>
          {user?.role === 'admin' && !project.freelancer && (
            <TouchableOpacity
              style={[styles.actionBtn, { backgroundColor: '#8b5cf6' }]}
              onPress={openAssignModal}
              activeOpacity={0.8}
            >
              <Ionicons name="person-add" size={16} color="#fff" />
              <Text style={styles.actionBtnText}>Assign</Text>
            </TouchableOpacity>
          )}
          {user?.role === 'admin' && project.freelancer && project.status !== 'completed' && (
            <TouchableOpacity
              style={[styles.actionBtn, { backgroundColor: '#8b5cf6' }]}
              onPress={openAssignModal}
              activeOpacity={0.8}
            >
              <Ionicons name="swap-horizontal" size={16} color="#fff" />
              <Text style={styles.actionBtnText}>Reassign</Text>
            </TouchableOpacity>
          )}
          {user?.role === 'admin' && project.status === 'in_progress' && (
            <TouchableOpacity
              style={[styles.actionBtn, { backgroundColor: colors.success }]}
              onPress={() => handleStatusUpdate('completed')}
              activeOpacity={0.8}
            >
              <Ionicons name="checkmark" size={16} color="#fff" />
              <Text style={styles.actionBtnText}>Complete</Text>
            </TouchableOpacity>
          )}
          {user?.role === 'freelancer' && project.status === 'assigned' && (
            <TouchableOpacity
              style={[styles.actionBtn, { backgroundColor: colors.primary }]}
              onPress={() => handleStatusUpdate('in_progress')}
              activeOpacity={0.8}
            >
              <Ionicons name="play" size={16} color="#fff" />
              <Text style={styles.actionBtnText}>Start Working</Text>
            </TouchableOpacity>
          )}
          {user?.role === 'freelancer' && project.status === 'in_progress' && (
            <TouchableOpacity
              style={[styles.actionBtn, { backgroundColor: colors.success }]}
              onPress={() => handleStatusUpdate('completed')}
              activeOpacity={0.8}
            >
              <Ionicons name="checkmark" size={16} color="#fff" />
              <Text style={styles.actionBtnText}>Mark Complete</Text>
            </TouchableOpacity>
          )}
          <TouchableOpacity
            style={[styles.actionBtn, { backgroundColor: colors.secondary }]}
            onPress={async () => {
              try {
                const conv = await chatApi.findOrCreateByProject(project.id);
                navigation.navigate('Messages', { screen: 'Chat', params: { conversationId: conv.id, title: project.title } });
              } catch (e) {
                navigation.navigate('Messages', { screen: 'Conversations' });
              }
            }}
            activeOpacity={0.8}
          >
            <Ionicons name="chatbubbles" size={16} color="#fff" />
            <Text style={styles.actionBtnText}>Messages</Text>
          </TouchableOpacity>
        </View>

        {/* ── Tabs ───────────────────────────────────────── */}
        <View style={[styles.tabBar, { borderBottomColor: colors.border }]}>
          {TABS.map(tab => (
            <TouchableOpacity
              key={tab}
              style={[styles.tab, activeTab === tab && { borderBottomColor: colors.primary, borderBottomWidth: 2 }]}
              onPress={() => setActiveTab(tab)}
              activeOpacity={0.7}
            >
              <Text style={[
                styles.tabText,
                { color: activeTab === tab ? colors.primary : colors.textLight },
                activeTab === tab && { fontWeight: '700' },
              ]}>
                {tab}
              </Text>
            </TouchableOpacity>
          ))}
        </View>

        {/* ── Tab Content ───────────────────────────────── */}
        {activeTab === 'Overview' && (
          <View style={styles.tabContent}>
            {/* Description */}
            {project.description ? (
              <View style={styles.section}>
                <Text style={[styles.sectionTitle, { color: colors.text }]}>Description</Text>
                <Text style={[styles.description, { color: colors.textSecondary }]}>{project.description}</Text>
              </View>
            ) : null}

            {/* Categories */}
            {project.categories?.length > 0 && (
              <View style={styles.section}>
                <Text style={[styles.sectionTitle, { color: colors.text }]}>Categories</Text>
                <View style={styles.tagRow}>
                  {project.categories.map((cat: any) => (
                    <View key={cat.id} style={[styles.tag, { backgroundColor: colors.primary + '12' }]}>
                      <Text style={[styles.tagText, { color: colors.primary }]}>{cat.name}</Text>
                    </View>
                  ))}
                </View>
              </View>
            )}

            {/* Invoice */}
            {project.invoice && (
              <View style={styles.section}>
                <Text style={[styles.sectionTitle, { color: colors.text }]}>Invoice</Text>
                <View style={[styles.invoiceCard, { backgroundColor: colors.card }]}>
                  <View style={styles.invoiceRow}>
                    <Text style={[styles.invoiceLabel, { color: colors.textSecondary }]}>Total</Text>
                    <Text style={[styles.invoiceValue, { color: colors.text }]}>
                      ${parseFloat(project.invoice.total_amount).toLocaleString()}
                    </Text>
                  </View>
                  <View style={[styles.invoiceDivider, { backgroundColor: colors.border + '60' }]} />
                  <View style={styles.invoiceRow}>
                    <Text style={[styles.invoiceLabel, { color: colors.textSecondary }]}>Paid</Text>
                    <Text style={[styles.invoiceValue, { color: colors.success }]}>
                      ${parseFloat(project.invoice.paid_amount).toLocaleString()}
                    </Text>
                  </View>
                  <View style={[styles.invoiceDivider, { backgroundColor: colors.border + '60' }]} />
                  <View style={styles.invoiceRow}>
                    <Text style={[styles.invoiceLabel, { color: colors.textSecondary }]}>Remaining</Text>
                    <Text style={[styles.invoiceValue, { color: remaining > 0 ? colors.danger : colors.success }]}>
                      ${remaining.toLocaleString()}
                    </Text>
                  </View>
                  <View style={[styles.invoiceDivider, { backgroundColor: colors.border + '60' }]} />
                  <View style={styles.invoiceRow}>
                    <Text style={[styles.invoiceLabel, { color: colors.textSecondary }]}>Status</Text>
                    <View style={[styles.invoiceStatusPill, { backgroundColor: (statusColors[project.invoice.status] || '#94a3b8') + '15' }]}>
                      <Text style={{ fontSize: 11, fontWeight: '700', color: statusColors[project.invoice.status] || '#94a3b8', textTransform: 'capitalize' }}>
                        {project.invoice.status}
                      </Text>
                    </View>
                  </View>
                </View>
              </View>
            )}
          </View>
        )}

        {activeTab === 'Files' && (
          <View style={styles.tabContent}>
            {project.files?.length > 0 ? (
              project.files.map((file: any) => (
                <View key={file.id} style={[styles.fileRow, { backgroundColor: colors.card }]}>
                  <View style={[styles.fileIconBg, { backgroundColor: colors.primary + '12' }]}>
                    <Ionicons name="document-attach" size={18} color={colors.primary} />
                  </View>
                  <Text style={[styles.fileName, { color: colors.text }]} numberOfLines={1}>
                    {file.file_path.split('/').pop()}
                  </Text>
                  <Ionicons name="download-outline" size={18} color={colors.textLight} />
                </View>
              ))
            ) : (
              <View style={styles.tabEmpty}>
                <Ionicons name="document-outline" size={40} color={colors.textLight} />
                <Text style={[styles.tabEmptyText, { color: colors.textLight }]}>No files uploaded yet</Text>
              </View>
            )}
          </View>
        )}

        {activeTab === 'Activity' && (
          <View style={styles.tabContent}>
            <View style={styles.tabEmpty}>
              <Ionicons name="time-outline" size={40} color={colors.textLight} />
              <Text style={[styles.tabEmptyText, { color: colors.textLight }]}>Activity log coming soon</Text>
            </View>
          </View>
        )}
      </ScrollView>

      {/* ── Assign Freelancer Modal ───────────────────── */}
      <Modal visible={showAssignModal} transparent animationType="slide">
        <View style={styles.modalOverlay}>
          <View style={[styles.modalCard, { backgroundColor: colors.card }]}>
            <View style={styles.modalHeader}>
              <Text style={[styles.modalTitle, { color: colors.text }]}>Assign Freelancer</Text>
              <TouchableOpacity onPress={() => setShowAssignModal(false)}>
                <Ionicons name="close" size={24} color={colors.textLight} />
              </TouchableOpacity>
            </View>
            {loadingFreelancers ? (
              <ActivityIndicator size="large" color={colors.primary} style={{ marginVertical: 40 }} />
            ) : freelancers.length === 0 ? (
              <Text style={[styles.modalEmpty, { color: colors.textSecondary }]}>No freelancers found</Text>
            ) : (
              <FlatList
                data={freelancers}
                keyExtractor={(item) => item.id.toString()}
                renderItem={({ item }) => (
                  <TouchableOpacity
                    style={[styles.freelancerRow, { borderBottomColor: colors.border + '50' }]}
                    onPress={() => handleAssign(item.id)}
                    activeOpacity={0.7}
                  >
                    <View style={[styles.freelancerAvatar, { backgroundColor: '#8b5cf6' + '20' }]}>
                      <Text style={[styles.freelancerInitial, { color: '#8b5cf6' }]}>
                        {item.name?.[0]?.toUpperCase() || '?'}
                      </Text>
                    </View>
                    <View style={{ flex: 1 }}>
                      <Text style={[styles.freelancerName, { color: colors.text }]}>{item.name}</Text>
                      <Text style={[styles.freelancerEmail, { color: colors.textSecondary }]}>{item.email}</Text>
                    </View>
                    <Ionicons name="chevron-forward" size={18} color={colors.textLight} />
                  </TouchableOpacity>
                )}
                style={{ maxHeight: 320 }}
              />
            )}
          </View>
        </View>
      </Modal>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1 },
  center: { flex: 1 },

  // Empty state
  emptyIcon: { width: 96, height: 96, borderRadius: 48, justifyContent: 'center', alignItems: 'center', marginBottom: spacing.md },
  emptyTitle: { fontSize: fontSize.lg, fontWeight: '700', marginBottom: 4 },
  emptyDesc: { fontSize: fontSize.sm, textAlign: 'center', lineHeight: 20, marginBottom: spacing.lg },
  emptyBtn: { paddingHorizontal: 24, paddingVertical: 12, borderRadius: 12 },
  emptyBtnText: { color: '#fff', fontWeight: '700', fontSize: fontSize.md },

  // Hero
  heroCard: {
    marginHorizontal: spacing.md, marginTop: spacing.md, borderRadius: 20, overflow: 'hidden',
    shadowColor: '#000', shadowOffset: { width: 0, height: 3 }, shadowOpacity: 0.08, shadowRadius: 12, elevation: 4,
  },
  heroHeader: {
    flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between',
    paddingHorizontal: spacing.md, paddingVertical: 12,
  },
  heroStatusIcon: { width: 40, height: 40, borderRadius: 12, justifyContent: 'center', alignItems: 'center' },
  heroStatusBadge: { flexDirection: 'row', alignItems: 'center', paddingHorizontal: 12, paddingVertical: 5, borderRadius: 12 },
  heroDot: { width: 6, height: 6, borderRadius: 3, marginRight: 6 },
  heroStatusText: { fontSize: 12, fontWeight: '700', textTransform: 'capitalize' },
  heroBody: { padding: spacing.md, paddingTop: 4 },
  heroTitle: { fontSize: 22, fontWeight: '800', lineHeight: 28, marginBottom: spacing.md },

  // Hero progress
  heroProgressWrap: { marginBottom: spacing.md },
  heroProgressInfo: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 6 },
  heroProgressLabel: { fontSize: 12, fontWeight: '600' },
  heroProgressValue: { fontSize: 13, fontWeight: '800' },
  heroProgressTrack: { height: 6, borderRadius: 3, overflow: 'hidden' },
  heroProgressFill: { height: 6, borderRadius: 3 },

  // Hero info grid
  heroInfoGrid: { gap: 10 },
  heroInfoItem: { flexDirection: 'row', alignItems: 'center', gap: 10 },
  heroInfoIcon: { width: 32, height: 32, borderRadius: 10, justifyContent: 'center', alignItems: 'center' },
  heroInfoLabel: { fontSize: 11 },
  heroInfoValue: { fontSize: 14, fontWeight: '600' },

  // Actions
  actionRow: { flexDirection: 'row', paddingHorizontal: spacing.md, marginTop: spacing.md, gap: 10 },
  actionBtn: {
    flex: 1, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 6,
    paddingVertical: 13, borderRadius: 14,
    shadowColor: '#000', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.1, shadowRadius: 4, elevation: 2,
  },
  actionBtnText: { color: '#fff', fontWeight: '700', fontSize: 14 },

  // Tabs
  tabBar: {
    flexDirection: 'row', marginTop: spacing.lg, marginHorizontal: spacing.md,
    borderBottomWidth: 1,
  },
  tab: { flex: 1, alignItems: 'center', paddingVertical: 12, borderBottomWidth: 2, borderBottomColor: 'transparent' },
  tabText: { fontSize: 14, fontWeight: '600' },

  // Tab content
  tabContent: { paddingHorizontal: spacing.md, paddingTop: spacing.md },
  tabEmpty: { alignItems: 'center', paddingVertical: 40 },
  tabEmptyText: { fontSize: 14, marginTop: 8 },

  // Sections
  section: { marginBottom: spacing.lg },
  sectionTitle: { fontSize: 16, fontWeight: '700', marginBottom: spacing.sm },
  description: { fontSize: 14, lineHeight: 22 },

  // Tags
  tagRow: { flexDirection: 'row', flexWrap: 'wrap', gap: spacing.xs },
  tag: { paddingHorizontal: 14, paddingVertical: 7, borderRadius: 20 },
  tagText: { fontSize: 12, fontWeight: '600' },

  // Invoice
  invoiceCard: {
    borderRadius: 16, padding: spacing.md,
    shadowColor: '#000', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.03, shadowRadius: 3, elevation: 1,
  },
  invoiceRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', paddingVertical: 10 },
  invoiceLabel: { fontSize: 14 },
  invoiceValue: { fontSize: 16, fontWeight: '700' },
  invoiceDivider: { height: 1 },
  invoiceStatusPill: { paddingHorizontal: 10, paddingVertical: 3, borderRadius: 8 },

  // Files
  fileRow: {
    flexDirection: 'row', alignItems: 'center', gap: spacing.sm,
    padding: 12, borderRadius: 12, marginBottom: spacing.xs,
    shadowColor: '#000', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.02, shadowRadius: 2, elevation: 1,
  },
  fileIconBg: { width: 36, height: 36, borderRadius: 10, justifyContent: 'center', alignItems: 'center' },
  fileName: { fontSize: 14, flex: 1 },

  // Assign Modal
  modalOverlay: {
    flex: 1, backgroundColor: 'rgba(0,0,0,0.5)', justifyContent: 'flex-end',
  },
  modalCard: {
    borderTopLeftRadius: 24, borderTopRightRadius: 24, padding: spacing.lg,
    paddingBottom: 40, maxHeight: '60%',
  },
  modalHeader: {
    flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center',
    marginBottom: spacing.md,
  },
  modalTitle: { fontSize: 18, fontWeight: '700' },
  modalEmpty: { textAlign: 'center', paddingVertical: 40, fontSize: 14 },
  freelancerRow: {
    flexDirection: 'row', alignItems: 'center', paddingVertical: 14,
    borderBottomWidth: 1, gap: 12,
  },
  freelancerAvatar: {
    width: 44, height: 44, borderRadius: 22, justifyContent: 'center', alignItems: 'center',
  },
  freelancerInitial: { fontSize: 18, fontWeight: '700' },
  freelancerName: { fontSize: 15, fontWeight: '600' },
  freelancerEmail: { fontSize: 12, marginTop: 2 },
});

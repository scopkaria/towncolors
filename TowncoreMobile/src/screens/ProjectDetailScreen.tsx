import React, { useEffect, useState } from 'react';
import {
  View, Text, StyleSheet, ScrollView, TouchableOpacity,
  ActivityIndicator, Alert,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useAuth } from '../contexts/AuthContext';
import { projectsApi } from '../api';
import { colors, spacing, fontSize, statusColors } from '../theme';

export default function ProjectDetailScreen({ route, navigation }: any) {
  const { id } = route.params;
  const { user } = useAuth();
  const [project, setProject] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadProject();
  }, [id]);

  async function loadProject() {
    try {
      const data = await projectsApi.get(id);
      setProject(data);
    } catch (err: any) {
      Alert.alert('Error', err.message);
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

  if (loading) {
    return <View style={styles.center}><ActivityIndicator size="large" color={colors.primary} /></View>;
  }

  if (!project) {
    return <View style={styles.center}><Text>Project not found</Text></View>;
  }

  return (
    <ScrollView style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.title}>{project.title}</Text>
        <View style={[styles.badge, { backgroundColor: (statusColors[project.status] || '#94a3b8') + '20' }]}>
          <Text style={[styles.badgeText, { color: statusColors[project.status] || '#94a3b8' }]}>
            {project.status?.replace('_', ' ')}
          </Text>
        </View>
      </View>

      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Description</Text>
        <Text style={styles.description}>{project.description}</Text>
      </View>

      {/* People */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>People</Text>
        {project.client && (
          <View style={styles.personRow}>
            <Ionicons name="person" size={18} color={colors.primary} />
            <View style={styles.personInfo}>
              <Text style={styles.personLabel}>Client</Text>
              <Text style={styles.personName}>{project.client.name}</Text>
            </View>
          </View>
        )}
        {project.freelancer && (
          <View style={styles.personRow}>
            <Ionicons name="code-working" size={18} color={colors.success} />
            <View style={styles.personInfo}>
              <Text style={styles.personLabel}>Freelancer</Text>
              <Text style={styles.personName}>{project.freelancer.name}</Text>
            </View>
          </View>
        )}
      </View>

      {/* Categories */}
      {project.categories?.length > 0 && (
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Categories</Text>
          <View style={styles.tagRow}>
            {project.categories.map((cat: any) => (
              <View key={cat.id} style={styles.tag}>
                <Text style={styles.tagText}>{cat.name}</Text>
              </View>
            ))}
          </View>
        </View>
      )}

      {/* Invoice */}
      {project.invoice && (
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Invoice</Text>
          <View style={styles.infoCard}>
            <View style={styles.infoRow}>
              <Text style={styles.infoLabel}>Total</Text>
              <Text style={styles.infoValue}>${project.invoice.total_amount}</Text>
            </View>
            <View style={styles.infoRow}>
              <Text style={styles.infoLabel}>Paid</Text>
              <Text style={[styles.infoValue, { color: colors.success }]}>${project.invoice.paid_amount}</Text>
            </View>
            <View style={styles.infoRow}>
              <Text style={styles.infoLabel}>Status</Text>
              <Text style={styles.infoValue}>{project.invoice.status}</Text>
            </View>
          </View>
        </View>
      )}

      {/* Files */}
      {project.files?.length > 0 && (
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Files ({project.files.length})</Text>
          {project.files.map((file: any) => (
            <View key={file.id} style={styles.fileRow}>
              <Ionicons name="document-attach" size={18} color={colors.primary} />
              <Text style={styles.fileName} numberOfLines={1}>{file.file_path.split('/').pop()}</Text>
            </View>
          ))}
        </View>
      )}

      {/* Actions */}
      <View style={styles.actions}>
        {user?.role === 'freelancer' && project.status === 'assigned' && (
          <TouchableOpacity style={styles.actionBtn} onPress={() => handleStatusUpdate('in_progress')}>
            <Ionicons name="play" size={18} color={colors.white} />
            <Text style={styles.actionBtnText}>Start Working</Text>
          </TouchableOpacity>
        )}
        {user?.role === 'freelancer' && project.status === 'in_progress' && (
          <TouchableOpacity style={[styles.actionBtn, { backgroundColor: colors.success }]} onPress={() => handleStatusUpdate('completed')}>
            <Ionicons name="checkmark" size={18} color={colors.white} />
            <Text style={styles.actionBtnText}>Mark Complete</Text>
          </TouchableOpacity>
        )}
        <TouchableOpacity
          style={[styles.actionBtn, { backgroundColor: colors.secondary }]}
          onPress={() => navigation.navigate('Messages', { screen: 'Chat', params: { projectId: project.id } })}
        >
          <Ionicons name="chatbubbles" size={18} color={colors.white} />
          <Text style={styles.actionBtnText}>Messages</Text>
        </TouchableOpacity>
      </View>

      <View style={{ height: 40 }} />
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: colors.background },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  header: { padding: spacing.lg, paddingBottom: spacing.sm },
  title: { fontSize: fontSize.xl, fontWeight: '800', color: colors.text, marginBottom: spacing.sm },
  badge: { alignSelf: 'flex-start', paddingHorizontal: 12, paddingVertical: 5, borderRadius: 8 },
  badgeText: { fontSize: fontSize.sm, fontWeight: '700', textTransform: 'capitalize' },
  section: { paddingHorizontal: spacing.lg, marginTop: spacing.md },
  sectionTitle: { fontSize: fontSize.md, fontWeight: '700', color: colors.text, marginBottom: spacing.sm },
  description: { fontSize: fontSize.sm, color: colors.textSecondary, lineHeight: 22 },
  personRow: { flexDirection: 'row', alignItems: 'center', gap: spacing.sm, marginBottom: spacing.sm, backgroundColor: colors.card, padding: spacing.md, borderRadius: 12 },
  personInfo: { flex: 1 },
  personLabel: { fontSize: fontSize.xs, color: colors.textLight },
  personName: { fontSize: fontSize.md, fontWeight: '600', color: colors.text },
  tagRow: { flexDirection: 'row', flexWrap: 'wrap', gap: spacing.xs },
  tag: { backgroundColor: colors.primary + '15', paddingHorizontal: 12, paddingVertical: 6, borderRadius: 16 },
  tagText: { fontSize: fontSize.xs, color: colors.primary, fontWeight: '600' },
  infoCard: { backgroundColor: colors.card, borderRadius: 12, padding: spacing.md },
  infoRow: { flexDirection: 'row', justifyContent: 'space-between', paddingVertical: spacing.xs },
  infoLabel: { fontSize: fontSize.sm, color: colors.textSecondary },
  infoValue: { fontSize: fontSize.sm, fontWeight: '700', color: colors.text },
  fileRow: { flexDirection: 'row', alignItems: 'center', gap: spacing.sm, backgroundColor: colors.card, padding: spacing.sm, borderRadius: 8, marginBottom: spacing.xs },
  fileName: { fontSize: fontSize.sm, color: colors.text, flex: 1 },
  actions: { padding: spacing.lg, gap: spacing.sm },
  actionBtn: { flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: spacing.sm, backgroundColor: colors.primary, padding: 14, borderRadius: 12 },
  actionBtnText: { color: colors.white, fontWeight: '700', fontSize: fontSize.md },
});

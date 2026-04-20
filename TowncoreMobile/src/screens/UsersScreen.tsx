import React, { useEffect, useState, useCallback } from 'react';
import {
  View, Text, StyleSheet, FlatList, TouchableOpacity,
  ActivityIndicator, TextInput, Alert, Modal, ScrollView,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { useTheme } from '../contexts/ThemeContext';
import { adminUsersApi } from '../api';
import { spacing, fontSize } from '../theme';
import { TAB_BAR_TOTAL_HEIGHT } from '../constants/layout';
import ScreenHeader from '../components/ScreenHeader';

const ROLE_COLORS: Record<string, string> = {
  admin: '#ef4444',
  client: '#3b82f6',
  freelancer: '#8b5cf6',
};

const ROLE_FILTERS = ['all', 'admin', 'client', 'freelancer'] as const;

export default function UsersScreen({ navigation }: any) {
  const { colors } = useTheme();
  const insets = useSafeAreaInsets();
  const [users, setUsers] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);
  const [hasMore, setHasMore] = useState(true);
  const [roleFilter, setRoleFilter] = useState<string>('all');
  const [search, setSearch] = useState('');
  const [showCreateModal, setShowCreateModal] = useState(false);

  // Create form state
  const [formName, setFormName] = useState('');
  const [formEmail, setFormEmail] = useState('');
  const [formPassword, setFormPassword] = useState('');
  const [formRole, setFormRole] = useState('client');
  const [creating, setCreating] = useState(false);

  const loadUsers = useCallback(async (p = 1, append = false) => {
    try {
      if (p === 1) setLoading(true);
      const role = roleFilter === 'all' ? undefined : roleFilter;
      const data = await adminUsersApi.list(p, role, search || undefined);
      const items = data.data || data;
      if (append) {
        setUsers(prev => [...prev, ...items]);
      } else {
        setUsers(items);
      }
      setHasMore(data.next_page_url !== null);
      setPage(p);
    } catch (err: any) {
      Alert.alert('Error', err.message);
    } finally {
      setLoading(false);
    }
  }, [roleFilter, search]);

  useEffect(() => {
    loadUsers(1);
  }, [roleFilter]);

  function handleSearch() {
    loadUsers(1);
  }

  function loadMore() {
    if (hasMore && !loading) {
      loadUsers(page + 1, true);
    }
  }

  async function handleCreate() {
    if (!formName.trim() || !formEmail.trim() || !formPassword.trim()) {
      Alert.alert('Error', 'Fill in all fields');
      return;
    }
    setCreating(true);
    try {
      await adminUsersApi.create({
        name: formName.trim(),
        email: formEmail.trim(),
        password: formPassword,
        password_confirmation: formPassword,
        role: formRole,
      });
      setShowCreateModal(false);
      setFormName('');
      setFormEmail('');
      setFormPassword('');
      setFormRole('client');
      Alert.alert('Success', 'User created');
      loadUsers(1);
    } catch (err: any) {
      Alert.alert('Error', err.message);
    } finally {
      setCreating(false);
    }
  }

  async function handleDelete(user: any) {
    Alert.alert('Delete User', `Are you sure you want to delete ${user.name}?`, [
      { text: 'Cancel' },
      {
        text: 'Delete', style: 'destructive', onPress: async () => {
          try {
            await adminUsersApi.delete(user.id);
            setUsers(prev => prev.filter(u => u.id !== user.id));
          } catch (err: any) {
            Alert.alert('Error', err.message);
          }
        }
      },
    ]);
  }

  function renderUser({ item }: { item: any }) {
    const roleColor = ROLE_COLORS[item.role] || '#94a3b8';
    return (
      <View style={[styles.userCard, { backgroundColor: colors.card }]}>
        <View style={[styles.avatar, { backgroundColor: roleColor + '20' }]}>
          <Text style={[styles.avatarText, { color: roleColor }]}>
            {item.name?.[0]?.toUpperCase() || '?'}
          </Text>
        </View>
        <View style={styles.userInfo}>
          <Text style={[styles.userName, { color: colors.text }]} numberOfLines={1}>{item.name}</Text>
          <Text style={[styles.userEmail, { color: colors.textSecondary }]} numberOfLines={1}>{item.email}</Text>
          <View style={styles.metaRow}>
            <View style={[styles.rolePill, { backgroundColor: roleColor + '15' }]}>
              <Text style={[styles.roleText, { color: roleColor }]}>{item.role}</Text>
            </View>
            {item.email_verified_at && (
              <Ionicons name="checkmark-circle" size={14} color={colors.success} style={{ marginLeft: 6 }} />
            )}
          </View>
        </View>
        <TouchableOpacity onPress={() => handleDelete(item)} hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}>
          <Ionicons name="trash-outline" size={20} color={colors.danger} />
        </TouchableOpacity>
      </View>
    );
  }

  return (
    <View style={[styles.container, { backgroundColor: colors.background }]}>
      <ScreenHeader
        title="Users"
        onBack={() => navigation.goBack()}
        rightIcon="add-circle-outline"
        onRight={() => setShowCreateModal(true)}
      />

      {/* Search */}
      <View style={[styles.searchRow, { backgroundColor: colors.card }]}>
        <Ionicons name="search" size={18} color={colors.textLight} />
        <TextInput
          style={[styles.searchInput, { color: colors.text }]}
          placeholder="Search users..."
          placeholderTextColor={colors.textLight}
          value={search}
          onChangeText={setSearch}
          onSubmitEditing={handleSearch}
          returnKeyType="search"
        />
      </View>

      {/* Role Filters */}
      <ScrollView horizontal showsHorizontalScrollIndicator={false} style={styles.filterRow} contentContainerStyle={styles.filterContent}>
        {ROLE_FILTERS.map(role => (
          <TouchableOpacity
            key={role}
            style={[
              styles.filterPill,
              { backgroundColor: roleFilter === role ? colors.primary : colors.card },
            ]}
            onPress={() => setRoleFilter(role)}
          >
            <Text style={[
              styles.filterText,
              { color: roleFilter === role ? '#fff' : colors.textSecondary },
            ]}>
              {role.charAt(0).toUpperCase() + role.slice(1)}
            </Text>
          </TouchableOpacity>
        ))}
      </ScrollView>

      {loading && page === 1 ? (
        <ActivityIndicator size="large" color={colors.primary} style={{ marginTop: 40 }} />
      ) : (
        <FlatList
          data={users}
          keyExtractor={(item) => item.id.toString()}
          renderItem={renderUser}
          contentContainerStyle={{ paddingHorizontal: spacing.md, paddingBottom: TAB_BAR_TOTAL_HEIGHT + insets.bottom + 20 }}
          onEndReached={loadMore}
          onEndReachedThreshold={0.3}
          ListEmptyComponent={
            <View style={styles.empty}>
              <Ionicons name="people-outline" size={48} color={colors.textLight} />
              <Text style={[styles.emptyText, { color: colors.textLight }]}>No users found</Text>
            </View>
          }
        />
      )}

      {/* Create User Modal */}
      <Modal visible={showCreateModal} transparent animationType="slide">
        <View style={styles.modalOverlay}>
          <View style={[styles.modalCard, { backgroundColor: colors.card }]}>
            <View style={styles.modalHeader}>
              <Text style={[styles.modalTitle, { color: colors.text }]}>Create User</Text>
              <TouchableOpacity onPress={() => setShowCreateModal(false)}>
                <Ionicons name="close" size={24} color={colors.textLight} />
              </TouchableOpacity>
            </View>
            <ScrollView keyboardShouldPersistTaps="handled">
              <Text style={[styles.label, { color: colors.textSecondary }]}>Name</Text>
              <TextInput
                style={[styles.input, { backgroundColor: colors.inputBg, borderColor: colors.border, color: colors.text }]}
                value={formName}
                onChangeText={setFormName}
                placeholder="Full name"
                placeholderTextColor={colors.textLight}
              />
              <Text style={[styles.label, { color: colors.textSecondary }]}>Email</Text>
              <TextInput
                style={[styles.input, { backgroundColor: colors.inputBg, borderColor: colors.border, color: colors.text }]}
                value={formEmail}
                onChangeText={setFormEmail}
                placeholder="Email address"
                placeholderTextColor={colors.textLight}
                keyboardType="email-address"
                autoCapitalize="none"
              />
              <Text style={[styles.label, { color: colors.textSecondary }]}>Password</Text>
              <TextInput
                style={[styles.input, { backgroundColor: colors.inputBg, borderColor: colors.border, color: colors.text }]}
                value={formPassword}
                onChangeText={setFormPassword}
                placeholder="Password"
                placeholderTextColor={colors.textLight}
                secureTextEntry
              />
              <Text style={[styles.label, { color: colors.textSecondary }]}>Role</Text>
              <View style={styles.roleSelector}>
                {['client', 'freelancer', 'admin'].map(r => (
                  <TouchableOpacity
                    key={r}
                    style={[
                      styles.roleSelectorBtn,
                      { borderColor: formRole === r ? ROLE_COLORS[r] : colors.border },
                      formRole === r && { backgroundColor: ROLE_COLORS[r] + '15' },
                    ]}
                    onPress={() => setFormRole(r)}
                  >
                    <Text style={[
                      styles.roleSelectorText,
                      { color: formRole === r ? ROLE_COLORS[r] : colors.textSecondary },
                    ]}>
                      {r.charAt(0).toUpperCase() + r.slice(1)}
                    </Text>
                  </TouchableOpacity>
                ))}
              </View>
              <TouchableOpacity
                style={[styles.createBtn, { backgroundColor: colors.primary }, creating && { opacity: 0.6 }]}
                onPress={handleCreate}
                disabled={creating}
              >
                {creating ? <ActivityIndicator color="#fff" /> : <Text style={styles.createBtnText}>Create User</Text>}
              </TouchableOpacity>
            </ScrollView>
          </View>
        </View>
      </Modal>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1 },

  searchRow: {
    flexDirection: 'row', alignItems: 'center', marginHorizontal: spacing.md,
    marginTop: spacing.sm, paddingHorizontal: 14, paddingVertical: 10, borderRadius: 14, gap: 8,
  },
  searchInput: { flex: 1, fontSize: 15 },

  filterRow: { marginTop: spacing.sm, maxHeight: 44 },
  filterContent: { paddingHorizontal: spacing.md, gap: 8 },
  filterPill: { paddingHorizontal: 16, paddingVertical: 8, borderRadius: 20 },
  filterText: { fontSize: 13, fontWeight: '600' },

  userCard: {
    flexDirection: 'row', alignItems: 'center', padding: 14, borderRadius: 16,
    marginTop: spacing.sm, gap: 12,
    shadowColor: '#000', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.04, shadowRadius: 4, elevation: 2,
  },
  avatar: { width: 48, height: 48, borderRadius: 24, justifyContent: 'center', alignItems: 'center' },
  avatarText: { fontSize: 20, fontWeight: '700' },
  userInfo: { flex: 1 },
  userName: { fontSize: 15, fontWeight: '600' },
  userEmail: { fontSize: 12, marginTop: 2 },
  metaRow: { flexDirection: 'row', alignItems: 'center', marginTop: 4 },
  rolePill: { paddingHorizontal: 10, paddingVertical: 2, borderRadius: 8 },
  roleText: { fontSize: 11, fontWeight: '700', textTransform: 'capitalize' },

  empty: { alignItems: 'center', paddingTop: 60 },
  emptyText: { fontSize: 15, marginTop: 8 },

  modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.5)', justifyContent: 'flex-end' },
  modalCard: { borderTopLeftRadius: 24, borderTopRightRadius: 24, padding: spacing.lg, paddingBottom: 40, maxHeight: '75%' },
  modalHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: spacing.md },
  modalTitle: { fontSize: 18, fontWeight: '700' },
  label: { fontSize: 13, fontWeight: '600', marginTop: spacing.sm, marginBottom: 4 },
  input: { borderWidth: 1, borderRadius: 12, paddingHorizontal: 14, paddingVertical: 12, fontSize: 15 },
  roleSelector: { flexDirection: 'row', gap: 8, marginTop: 4 },
  roleSelectorBtn: { flex: 1, paddingVertical: 10, borderRadius: 12, borderWidth: 1.5, alignItems: 'center' },
  roleSelectorText: { fontSize: 13, fontWeight: '700' },
  createBtn: { marginTop: spacing.lg, paddingVertical: 14, borderRadius: 14, alignItems: 'center' },
  createBtnText: { color: '#fff', fontWeight: '700', fontSize: 15 },
});

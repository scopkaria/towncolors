import React, { useEffect, useState, useCallback } from 'react';
import {
  View, Text, StyleSheet, FlatList, TouchableOpacity,
  RefreshControl, ActivityIndicator, TextInput, Alert,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { chatApi } from '../api';
import { useAuth } from '../contexts/AuthContext';
import { useTheme } from '../contexts/ThemeContext';
import { spacing, fontSize } from '../theme';
import { TAB_BAR_TOTAL_HEIGHT } from '../constants/layout';

type TabType = 'chats' | 'contacts';

export default function ConversationsScreen({ navigation }: any) {
  const { user } = useAuth();
  const { colors } = useTheme();
  const insets = useSafeAreaInsets();
  const [tab, setTab] = useState<TabType>('chats');
  const [conversations, setConversations] = useState<any[]>([]);
  const [contacts, setContacts] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [contactsLoading, setContactsLoading] = useState(false);
  const [search, setSearch] = useState('');

  const loadConversations = useCallback(async () => {
    try {
      const result = await chatApi.conversations();
      setConversations(result.data);
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  const loadContacts = useCallback(async () => {
    setContactsLoading(true);
    try {
      const result = await chatApi.contacts();
      setContacts(result.data);
    } catch (err) {
      console.error(err);
    } finally {
      setContactsLoading(false);
    }
  }, []);

  useEffect(() => {
    loadConversations();
    loadContacts();
    const interval = setInterval(loadConversations, 10000);
    return () => clearInterval(interval);
  }, [loadConversations, loadContacts]);

  const onRefresh = () => {
    setRefreshing(true);
    loadConversations();
    loadContacts();
  };

  function getConversationTitle(conv: any) {
    if (conv.project) return conv.project.title;
    const other = conv.users?.find((u: any) => u.id !== user?.id);
    return other?.name || 'Conversation';
  }

  function getConversationAvatar(conv: any) {
    if (conv.project) return conv.project.title?.charAt(0).toUpperCase() || 'P';
    const other = conv.users?.find((u: any) => u.id !== user?.id);
    return other?.name?.charAt(0).toUpperCase() || '?';
  }

  async function startConversation(contact: any) {
    try {
      const conv = await chatApi.createConversation(contact.id);
      navigation.navigate('Chat', { conversationId: conv.id, title: contact.name });
    } catch (err: any) {
      Alert.alert('Error', err.message || 'Could not start conversation');
    }
  }

  const filteredConversations = search.trim()
    ? conversations.filter(c => getConversationTitle(c).toLowerCase().includes(search.toLowerCase()))
    : conversations;

  const filteredContacts = search.trim()
    ? contacts.filter(c => c.name.toLowerCase().includes(search.toLowerCase()) || c.role.toLowerCase().includes(search.toLowerCase()))
    : contacts;

  function getRoleBadgeColor(role: string) {
    switch (role) {
      case 'admin': return '#f97316';
      case 'client': return '#3b82f6';
      case 'freelancer': return '#8b5cf6';
      default: return '#94a3b8';
    }
  }

  const renderConversation = ({ item }: any) => (
    <TouchableOpacity
      style={[styles.card, { backgroundColor: colors.card, borderBottomColor: colors.border }]}
      onPress={() => navigation.navigate('Chat', { conversationId: item.id, title: getConversationTitle(item) })}
      activeOpacity={0.7}
    >
      <View style={[styles.avatar, { backgroundColor: colors.primary + '15' }]}>
        <Text style={[styles.avatarText, { color: colors.primary }]}>{getConversationAvatar(item)}</Text>
      </View>
      <View style={styles.content}>
        <View style={styles.headerRow}>
          <Text style={[styles.title, { color: colors.text }]} numberOfLines={1}>{getConversationTitle(item)}</Text>
          {item.latest_message && (
            <Text style={[styles.time, { color: colors.textLight }]}>
              {new Date(item.latest_message.created_at).toLocaleDateString()}
            </Text>
          )}
        </View>
        <View style={styles.messageRow}>
          <Text style={[styles.lastMessage, { color: colors.textSecondary }]} numberOfLines={1}>
            {item.latest_message
              ? `${item.latest_message.sender?.name}: ${item.latest_message.message || '[attachment]'}`
              : 'No messages yet'}
          </Text>
          {item.unread_count > 0 && (
            <View style={[styles.unreadBadge, { backgroundColor: colors.primary }]}>
              <Text style={styles.unreadText}>{item.unread_count}</Text>
            </View>
          )}
        </View>
      </View>
    </TouchableOpacity>
  );

  const renderContact = ({ item }: any) => {
    const badgeColor = getRoleBadgeColor(item.role);
    return (
      <TouchableOpacity
        style={[styles.card, { backgroundColor: colors.card, borderBottomColor: colors.border }]}
        onPress={() => startConversation(item)}
        activeOpacity={0.7}
      >
        <View style={[styles.avatar, { backgroundColor: badgeColor + '15' }]}>
          <Text style={[styles.avatarText, { color: badgeColor }]}>{item.name?.charAt(0).toUpperCase()}</Text>
        </View>
        <View style={styles.content}>
          <Text style={[styles.title, { color: colors.text }]}>{item.name}</Text>
          <View style={styles.contactMeta}>
            <View style={[styles.rolePill, { backgroundColor: badgeColor + '18' }]}>
              <Text style={[styles.roleText, { color: badgeColor }]}>{item.role}</Text>
            </View>
            <Text style={[styles.contactEmail, { color: colors.textLight }]}>{item.email}</Text>
          </View>
        </View>
        <Ionicons name="chatbubble-outline" size={20} color={colors.primary} />
      </TouchableOpacity>
    );
  };

  if (loading) {
    return <View style={[styles.center, { backgroundColor: colors.background }]}><ActivityIndicator size="large" color={colors.primary} /></View>;
  }

  return (
    <View style={[styles.container, { backgroundColor: colors.background }]}>
      {/* Tab Switcher */}
      <View style={[styles.tabRow, { backgroundColor: colors.card, borderBottomColor: colors.border }]}>
        <TouchableOpacity
          style={[styles.tabBtn, tab === 'chats' && { borderBottomColor: colors.primary, borderBottomWidth: 2 }]}
          onPress={() => setTab('chats')}
        >
          <Ionicons name="chatbubbles" size={18} color={tab === 'chats' ? colors.primary : colors.textLight} />
          <Text style={[styles.tabText, { color: tab === 'chats' ? colors.primary : colors.textLight }]}>Chats</Text>
        </TouchableOpacity>
        <TouchableOpacity
          style={[styles.tabBtn, tab === 'contacts' && { borderBottomColor: colors.primary, borderBottomWidth: 2 }]}
          onPress={() => setTab('contacts')}
        >
          <Ionicons name="people" size={18} color={tab === 'contacts' ? colors.primary : colors.textLight} />
          <Text style={[styles.tabText, { color: tab === 'contacts' ? colors.primary : colors.textLight }]}>Contacts</Text>
        </TouchableOpacity>
      </View>

      {/* Search */}
      <View style={{ paddingHorizontal: spacing.md, paddingVertical: spacing.sm }}>
        <View style={[styles.searchBar, { backgroundColor: colors.card, borderColor: colors.border }]}>
          <Ionicons name="search" size={18} color={colors.textLight} />
          <TextInput
            style={[styles.searchInput, { color: colors.text }]}
            placeholder={tab === 'chats' ? 'Search conversations...' : 'Search contacts...'}
            placeholderTextColor={colors.textLight}
            value={search}
            onChangeText={setSearch}
          />
          {search.length > 0 && (
            <TouchableOpacity onPress={() => setSearch('')}>
              <Ionicons name="close-circle" size={18} color={colors.textLight} />
            </TouchableOpacity>
          )}
        </View>
      </View>

      {tab === 'chats' ? (
        <FlatList
          data={filteredConversations}
          keyExtractor={item => item.id.toString()}
          renderItem={renderConversation}
          refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor={colors.primary} />}
          contentContainerStyle={[styles.list, { paddingBottom: TAB_BAR_TOTAL_HEIGHT + insets.bottom }]}
          ListEmptyComponent={
            <View style={styles.emptyWrap}>
              <Ionicons name="chatbubbles-outline" size={48} color={colors.textLight} />
              <Text style={[styles.emptyText, { color: colors.textLight }]}>No conversations yet</Text>
              <Text style={[styles.emptyDesc, { color: colors.textSecondary }]}>Tap Contacts to start a conversation</Text>
            </View>
          }
        />
      ) : (
        contactsLoading ? (
          <View style={styles.center}><ActivityIndicator size="large" color={colors.primary} /></View>
        ) : (
          <FlatList
            data={filteredContacts}
            keyExtractor={item => item.id.toString()}
            renderItem={renderContact}
            refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor={colors.primary} />}
            contentContainerStyle={[styles.list, { paddingBottom: TAB_BAR_TOTAL_HEIGHT + insets.bottom }]}
            ListEmptyComponent={
              <View style={styles.emptyWrap}>
                <Ionicons name="people-outline" size={48} color={colors.textLight} />
                <Text style={[styles.emptyText, { color: colors.textLight }]}>No contacts yet</Text>
              </View>
            }
          />
        )
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1 },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center', paddingTop: 80 },
  list: { },

  // Tabs
  tabRow: { flexDirection: 'row', borderBottomWidth: 1 },
  tabBtn: { flex: 1, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', paddingVertical: 14, gap: 6 },
  tabText: { fontSize: fontSize.sm, fontWeight: '700' },

  // Search
  searchBar: {
    flexDirection: 'row', alignItems: 'center', borderRadius: 12,
    paddingHorizontal: spacing.md, height: 40, gap: spacing.sm, borderWidth: 1,
  },
  searchInput: { flex: 1, fontSize: fontSize.sm, paddingVertical: 0 },

  // Cards
  card: { flexDirection: 'row', alignItems: 'center', padding: spacing.md, borderBottomWidth: 1 },
  avatar: { width: 48, height: 48, borderRadius: 24, justifyContent: 'center', alignItems: 'center', marginRight: spacing.md },
  avatarText: { fontSize: 18, fontWeight: '700' },
  content: { flex: 1 },
  headerRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' },
  title: { fontSize: fontSize.md, fontWeight: '700', flex: 1, marginRight: spacing.sm },
  time: { fontSize: fontSize.xs },
  messageRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginTop: 2 },
  lastMessage: { fontSize: fontSize.sm, flex: 1, marginRight: spacing.sm },
  unreadBadge: { borderRadius: 12, minWidth: 22, height: 22, justifyContent: 'center', alignItems: 'center', paddingHorizontal: 6 },
  unreadText: { color: '#fff', fontSize: 11, fontWeight: '700' },

  // Contacts
  contactMeta: { flexDirection: 'row', alignItems: 'center', gap: spacing.sm, marginTop: 3 },
  rolePill: { paddingHorizontal: 8, paddingVertical: 2, borderRadius: 6 },
  roleText: { fontSize: 10, fontWeight: '700', textTransform: 'capitalize' },
  contactEmail: { fontSize: fontSize.xs },

  // Empty
  emptyWrap: { alignItems: 'center', paddingTop: 80 },
  emptyText: { fontSize: fontSize.md, marginTop: spacing.sm, fontWeight: '600' },
  emptyDesc: { fontSize: fontSize.sm, marginTop: 4 },
});

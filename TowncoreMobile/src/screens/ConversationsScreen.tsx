import React, { useEffect, useState, useCallback } from 'react';
import {
  View, Text, StyleSheet, FlatList, TouchableOpacity,
  RefreshControl, ActivityIndicator,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { chatApi } from '../api';
import { useAuth } from '../contexts/AuthContext';
import { colors, spacing, fontSize } from '../theme';

export default function ConversationsScreen({ navigation }: any) {
  const { user } = useAuth();
  const [conversations, setConversations] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

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

  useEffect(() => {
    loadConversations();
    const interval = setInterval(loadConversations, 10000); // Poll every 10s
    return () => clearInterval(interval);
  }, [loadConversations]);

  const onRefresh = () => { setRefreshing(true); loadConversations(); };

  function getConversationTitle(conv: any) {
    if (conv.project) return conv.project.title;
    const other = conv.users?.find((u: any) => u.id !== user?.id);
    return other?.name || 'Conversation';
  }

  const renderConversation = ({ item }: any) => (
    <TouchableOpacity
      style={styles.card}
      onPress={() => navigation.navigate('Chat', { conversationId: item.id, title: getConversationTitle(item) })}
    >
      <View style={styles.avatar}>
        <Ionicons name={item.project ? 'folder' : 'person'} size={22} color={colors.primary} />
      </View>
      <View style={styles.content}>
        <View style={styles.headerRow}>
          <Text style={styles.title} numberOfLines={1}>{getConversationTitle(item)}</Text>
          {item.latest_message && (
            <Text style={styles.time}>
              {new Date(item.latest_message.created_at).toLocaleDateString()}
            </Text>
          )}
        </View>
        <View style={styles.messageRow}>
          <Text style={styles.lastMessage} numberOfLines={1}>
            {item.latest_message
              ? `${item.latest_message.sender?.name}: ${item.latest_message.message || '[attachment]'}`
              : 'No messages yet'}
          </Text>
          {item.unread_count > 0 && (
            <View style={styles.unreadBadge}>
              <Text style={styles.unreadText}>{item.unread_count}</Text>
            </View>
          )}
        </View>
      </View>
    </TouchableOpacity>
  );

  if (loading) {
    return <View style={styles.center}><ActivityIndicator size="large" color={colors.primary} /></View>;
  }

  return (
    <View style={styles.container}>
      <FlatList
        data={conversations}
        keyExtractor={item => item.id.toString()}
        renderItem={renderConversation}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
        contentContainerStyle={styles.list}
        ListEmptyComponent={
          <View style={styles.center}>
            <Ionicons name="chatbubbles-outline" size={48} color={colors.textLight} />
            <Text style={styles.emptyText}>No conversations yet</Text>
          </View>
        }
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: colors.background },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center', paddingTop: 80 },
  list: { paddingBottom: 20 },
  card: { flexDirection: 'row', alignItems: 'center', padding: spacing.md, backgroundColor: colors.card, borderBottomWidth: 1, borderBottomColor: colors.border },
  avatar: { width: 48, height: 48, borderRadius: 24, backgroundColor: colors.primary + '15', justifyContent: 'center', alignItems: 'center', marginRight: spacing.md },
  content: { flex: 1 },
  headerRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' },
  title: { fontSize: fontSize.md, fontWeight: '700', color: colors.text, flex: 1, marginRight: spacing.sm },
  time: { fontSize: fontSize.xs, color: colors.textLight },
  messageRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginTop: 2 },
  lastMessage: { fontSize: fontSize.sm, color: colors.textSecondary, flex: 1, marginRight: spacing.sm },
  unreadBadge: { backgroundColor: colors.primary, borderRadius: 12, minWidth: 22, height: 22, justifyContent: 'center', alignItems: 'center', paddingHorizontal: 6 },
  unreadText: { color: colors.white, fontSize: 11, fontWeight: '700' },
  emptyText: { fontSize: fontSize.md, color: colors.textLight, marginTop: spacing.sm },
});

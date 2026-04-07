import React, { useEffect, useState, useCallback } from 'react';
import {
  View, Text, StyleSheet, FlatList, TouchableOpacity,
  RefreshControl, ActivityIndicator,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { notificationsApi } from '../api';
import { colors, spacing, fontSize } from '../theme';

export default function NotificationsScreen() {
  const [notifications, setNotifications] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const loadNotifications = useCallback(async () => {
    try {
      const result = await notificationsApi.list();
      setNotifications(result.data);
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => { loadNotifications(); }, [loadNotifications]);

  const onRefresh = () => { setRefreshing(true); loadNotifications(); };

  async function handleMarkAsRead(id: string) {
    try {
      await notificationsApi.markAsRead(id);
      setNotifications(prev =>
        prev.map(n => n.id === id ? { ...n, read_at: new Date().toISOString() } : n)
      );
    } catch (err) {
      console.error(err);
    }
  }

  async function handleMarkAllAsRead() {
    try {
      await notificationsApi.markAllAsRead();
      setNotifications(prev =>
        prev.map(n => ({ ...n, read_at: n.read_at || new Date().toISOString() }))
      );
    } catch (err) {
      console.error(err);
    }
  }

  function getNotificationIcon(type: string) {
    if (type.includes('Project')) return 'folder';
    if (type.includes('Invoice') || type.includes('Payment')) return 'receipt';
    if (type.includes('Message')) return 'chatbubble';
    if (type.includes('Portfolio')) return 'images';
    return 'notifications';
  }

  function getNotificationMessage(notification: any) {
    const data = notification.data;
    return data?.message || data?.title || notification.type.split('\\').pop() || 'Notification';
  }

  const renderNotification = ({ item }: any) => {
    const isUnread = !item.read_at;
    return (
      <TouchableOpacity
        style={[styles.card, isUnread && styles.cardUnread]}
        onPress={() => isUnread && handleMarkAsRead(item.id)}
      >
        <View style={[styles.iconWrap, isUnread && styles.iconUnread]}>
          <Ionicons
            name={getNotificationIcon(item.type) as any}
            size={20}
            color={isUnread ? colors.primary : colors.textLight}
          />
        </View>
        <View style={styles.content}>
          <Text style={[styles.message, isUnread && styles.messageUnread]} numberOfLines={2}>
            {getNotificationMessage(item)}
          </Text>
          <Text style={styles.time}>
            {new Date(item.created_at).toLocaleDateString()} · {new Date(item.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
          </Text>
        </View>
        {isUnread && <View style={styles.dot} />}
      </TouchableOpacity>
    );
  };

  if (loading) {
    return <View style={styles.center}><ActivityIndicator size="large" color={colors.primary} /></View>;
  }

  const unreadCount = notifications.filter(n => !n.read_at).length;

  return (
    <View style={styles.container}>
      {unreadCount > 0 && (
        <TouchableOpacity style={styles.markAllBtn} onPress={handleMarkAllAsRead}>
          <Text style={styles.markAllText}>Mark all as read ({unreadCount})</Text>
        </TouchableOpacity>
      )}

      <FlatList
        data={notifications}
        keyExtractor={item => item.id}
        renderItem={renderNotification}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
        contentContainerStyle={styles.list}
        ListEmptyComponent={
          <View style={styles.center}>
            <Ionicons name="notifications-off-outline" size={48} color={colors.textLight} />
            <Text style={styles.emptyText}>No notifications</Text>
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
  markAllBtn: { padding: spacing.md, alignItems: 'flex-end' },
  markAllText: { color: colors.primary, fontWeight: '700', fontSize: fontSize.sm },
  card: { flexDirection: 'row', alignItems: 'center', padding: spacing.md, backgroundColor: colors.card, borderBottomWidth: 1, borderBottomColor: colors.border },
  cardUnread: { backgroundColor: colors.primary + '08' },
  iconWrap: { width: 40, height: 40, borderRadius: 20, backgroundColor: colors.inputBg, justifyContent: 'center', alignItems: 'center', marginRight: spacing.md },
  iconUnread: { backgroundColor: colors.primary + '15' },
  content: { flex: 1 },
  message: { fontSize: fontSize.sm, color: colors.textSecondary, lineHeight: 20 },
  messageUnread: { color: colors.text, fontWeight: '600' },
  time: { fontSize: fontSize.xs, color: colors.textLight, marginTop: 2 },
  dot: { width: 8, height: 8, borderRadius: 4, backgroundColor: colors.primary, marginLeft: spacing.sm },
  emptyText: { fontSize: fontSize.md, color: colors.textLight, marginTop: spacing.sm },
});

import React, { useEffect, useState, useCallback, useRef } from 'react';
import {
  View, Text, TextInput, TouchableOpacity, StyleSheet,
  FlatList, KeyboardAvoidingView, Platform, ActivityIndicator,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { chatApi } from '../api';
import { useAuth } from '../contexts/AuthContext';
import { colors, spacing, fontSize } from '../theme';

export default function ChatScreen({ route }: any) {
  const { conversationId } = route.params;
  const { user } = useAuth();
  const [messages, setMessages] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [text, setText] = useState('');
  const [sending, setSending] = useState(false);
  const flatListRef = useRef<FlatList>(null);

  const loadMessages = useCallback(async () => {
    try {
      const result = await chatApi.messages(conversationId);
      setMessages(result.data.reverse());
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
    }
  }, [conversationId]);

  useEffect(() => {
    loadMessages();
    const interval = setInterval(loadMessages, 5000);
    return () => clearInterval(interval);
  }, [loadMessages]);

  async function handleSend() {
    if (!text.trim()) return;
    setSending(true);
    try {
      const msg = await chatApi.sendTextMessage(conversationId, text.trim());
      setMessages(prev => [...prev, msg]);
      setText('');
      setTimeout(() => flatListRef.current?.scrollToEnd(), 100);
    } catch (err: any) {
      console.error(err);
    } finally {
      setSending(false);
    }
  }

  const renderMessage = ({ item }: any) => {
    const isMe = item.sender_id === user?.id;
    return (
      <View style={[styles.messageBubble, isMe ? styles.myMessage : styles.otherMessage]}>
        {!isMe && <Text style={styles.senderName}>{item.sender?.name}</Text>}
        {item.message_type === 'location' ? (
          <Text style={[styles.messageText, isMe && styles.myText]}>
            <Ionicons name="location" size={14} /> Location shared
          </Text>
        ) : item.message_type === 'image' ? (
          <Text style={[styles.messageText, isMe && styles.myText]}>
            <Ionicons name="image" size={14} /> Image
          </Text>
        ) : (
          <Text style={[styles.messageText, isMe && styles.myText]}>{item.message}</Text>
        )}
        <Text style={[styles.messageTime, isMe && styles.myTime]}>
          {new Date(item.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
        </Text>
      </View>
    );
  };

  if (loading) {
    return <View style={styles.center}><ActivityIndicator size="large" color={colors.primary} /></View>;
  }

  return (
    <KeyboardAvoidingView
      style={styles.container}
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
      keyboardVerticalOffset={90}
    >
      <FlatList
        ref={flatListRef}
        data={messages}
        keyExtractor={item => item.id.toString()}
        renderItem={renderMessage}
        contentContainerStyle={styles.messagesList}
        onContentSizeChange={() => flatListRef.current?.scrollToEnd({ animated: false })}
        ListEmptyComponent={
          <View style={styles.center}>
            <Text style={styles.emptyText}>No messages yet. Say hi!</Text>
          </View>
        }
      />
      <View style={styles.inputBar}>
        <TextInput
          style={styles.textInput}
          value={text}
          onChangeText={setText}
          placeholder="Type a message..."
          multiline
          maxLength={5000}
        />
        <TouchableOpacity
          style={[styles.sendBtn, (!text.trim() || sending) && styles.sendBtnDisabled]}
          onPress={handleSend}
          disabled={!text.trim() || sending}
        >
          <Ionicons name="send" size={20} color={colors.white} />
        </TouchableOpacity>
      </View>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: colors.background },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center', paddingTop: 40 },
  messagesList: { padding: spacing.md, paddingBottom: spacing.sm },
  messageBubble: { maxWidth: '78%', borderRadius: 16, padding: spacing.sm, paddingHorizontal: spacing.md, marginBottom: spacing.xs },
  myMessage: { alignSelf: 'flex-end', backgroundColor: colors.primary, borderBottomRightRadius: 4 },
  otherMessage: { alignSelf: 'flex-start', backgroundColor: colors.card, borderBottomLeftRadius: 4, borderWidth: 1, borderColor: colors.border },
  senderName: { fontSize: 11, fontWeight: '700', color: colors.primary, marginBottom: 2 },
  messageText: { fontSize: fontSize.md, color: colors.text, lineHeight: 22 },
  myText: { color: colors.white },
  messageTime: { fontSize: 10, color: colors.textLight, marginTop: 2, alignSelf: 'flex-end' },
  myTime: { color: 'rgba(255,255,255,0.7)' },
  emptyText: { color: colors.textLight, fontSize: fontSize.sm },
  inputBar: { flexDirection: 'row', alignItems: 'flex-end', padding: spacing.sm, borderTopWidth: 1, borderTopColor: colors.border, backgroundColor: colors.card },
  textInput: { flex: 1, backgroundColor: colors.inputBg, borderRadius: 20, paddingHorizontal: 16, paddingVertical: 10, fontSize: fontSize.md, maxHeight: 100, marginRight: spacing.sm },
  sendBtn: { width: 44, height: 44, borderRadius: 22, backgroundColor: colors.primary, justifyContent: 'center', alignItems: 'center' },
  sendBtnDisabled: { opacity: 0.4 },
});

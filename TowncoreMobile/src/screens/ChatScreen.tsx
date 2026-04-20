import React, { useEffect, useState, useCallback, useRef } from 'react';
import {
  View, Text, TextInput, TouchableOpacity, StyleSheet,
  FlatList, KeyboardAvoidingView, Platform, ActivityIndicator,
  Image, Linking, Keyboard, ScrollView, Alert,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import * as ImagePicker from 'expo-image-picker';
import { chatApi } from '../api';
import { useAuth } from '../contexts/AuthContext';
import { useTheme } from '../contexts/ThemeContext';
import { spacing, fontSize } from '../theme';
import { MEDIA_BASE_URL } from '../config';
import ScreenHeader from '../components/ScreenHeader';
import { TAB_BAR_TOTAL_HEIGHT } from '../constants/layout';

const QUICK_EMOJIS = ['😊', '👍', '❤️', '😂', '🔥', '👏', '🎉', '💯', '✅', '🙏', '😍', '🤔', '😎', '💪', '⭐', '🚀'];

export default function ChatScreen({ route, navigation }: any) {
  const { conversationId, title: chatTitle } = route.params;
  const { user } = useAuth();
  const { colors } = useTheme();
  const insets = useSafeAreaInsets();
  const [messages, setMessages] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [text, setText] = useState('');
  const [sending, setSending] = useState(false);
  const [showEmoji, setShowEmoji] = useState(false);
  const flatListRef = useRef<FlatList>(null);
  const [keyboardVisible, setKeyboardVisible] = useState(false);

  useEffect(() => {
    const showSub = Keyboard.addListener(Platform.OS === 'ios' ? 'keyboardWillShow' : 'keyboardDidShow', () => setKeyboardVisible(true));
    const hideSub = Keyboard.addListener(Platform.OS === 'ios' ? 'keyboardWillHide' : 'keyboardDidHide', () => setKeyboardVisible(false));
    return () => { showSub.remove(); hideSub.remove(); };
  }, []);

  // Guard: if no conversationId, show error instead of polling undefined
  if (!conversationId) {
    return (
      <View style={{ flex: 1, backgroundColor: colors.background }}>
        <ScreenHeader title={chatTitle || 'Chat'} onBack={() => navigation.goBack()} />
        <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center', padding: 32 }}>
          <Ionicons name="chatbubble-ellipses-outline" size={48} color={colors.textLight} />
          <Text style={{ color: colors.textLight, fontSize: 16, marginTop: 12, textAlign: 'center' }}>
            Could not open this conversation.{"\n"}Please try again from Messages.
          </Text>
        </View>
      </View>
    );
  }

  const loadMessages = useCallback(async () => {
    try {
      const result = await chatApi.messages(conversationId);
      const sorted = (result.data || []).slice().reverse();
      setMessages(prev => {
        // Keep temp messages that haven't appeared from server yet
        const tempMsgs = prev.filter(m => String(m.id).startsWith('temp_'));
        // Check if any temp message's text already exists in latest server messages
        const serverTexts = new Set(sorted.map((m: any) => `${m.sender_id}_${m.message}`));
        const remainingTemp = tempMsgs.filter(m => !serverTexts.has(`${m.sender_id}_${m.message}`));
        return [...sorted, ...remainingTemp];
      });
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
    }
  }, [conversationId]);

  useEffect(() => {
    loadMessages();
    const interval = setInterval(loadMessages, 4000);
    return () => clearInterval(interval);
  }, [loadMessages]);

  async function handleSend() {
    const trimmed = text.trim();
    if (!trimmed || sending) return;

    const tempId = `temp_${Date.now()}`;
    const optimisticMsg = {
      id: tempId,
      sender_id: user?.id,
      sender: { id: user?.id, name: user?.name },
      message: trimmed,
      message_type: 'text',
      created_at: new Date().toISOString(),
      _sending: true,
    };

    setSending(true);
    setText('');
    setMessages(prev => [...prev, optimisticMsg]);
    setTimeout(() => flatListRef.current?.scrollToEnd({ animated: true }), 50);

    try {
      const msg = await chatApi.sendTextMessage(conversationId, trimmed);
      setMessages(prev => prev.map(m => m.id === tempId ? { ...msg, _sending: false } : m));
    } catch {
      setMessages(prev => prev.map(m => m.id === tempId ? { ...m, _failed: true, _sending: false } : m));
    } finally {
      setSending(false);
    }
  }

  async function handlePickImage() {
    const result = await ImagePicker.launchImageLibraryAsync({
      mediaTypes: ['images'],
      quality: 0.7,
      allowsEditing: false,
    });
    if (result.canceled || !result.assets[0]) return;

    const asset = result.assets[0];
    const filename = asset.uri.split('/').pop() || 'image.jpg';
    const ext = filename.split('.').pop()?.toLowerCase() || 'jpg';
    const formData = new FormData();
    formData.append('file', {
      uri: asset.uri,
      name: filename,
      type: `image/${ext === 'jpg' ? 'jpeg' : ext}`,
    } as any);
    formData.append('message_type', 'image');

    setSending(true);
    try {
      await chatApi.sendMessage(conversationId, formData);
      await loadMessages();
      setTimeout(() => flatListRef.current?.scrollToEnd({ animated: true }), 100);
    } catch {
      Alert.alert('Error', 'Failed to send image');
    } finally {
      setSending(false);
    }
  }

  function formatTime(dateStr: string) {
    return new Date(dateStr).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  }

  function formatDateHeader(dateStr: string) {
    const d = new Date(dateStr);
    const today = new Date();
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);
    if (d.toDateString() === today.toDateString()) return 'Today';
    if (d.toDateString() === yesterday.toDateString()) return 'Yesterday';
    return d.toLocaleDateString(undefined, { weekday: 'short', month: 'short', day: 'numeric' });
  }

  function shouldShowDateHeader(index: number) {
    if (index === 0) return true;
    return new Date(messages[index].created_at).toDateString() !==
           new Date(messages[index - 1].created_at).toDateString();
  }

  const renderMessage = ({ item, index }: any) => {
    const isMe = Number(item.sender_id) === Number(user?.id);
    const showDate = shouldShowDateHeader(index);
    const showTail = index === messages.length - 1 ||
      messages[index + 1]?.sender_id !== item.sender_id;

    return (
      <View>
        {showDate && (
          <View style={styles.dateHeaderWrap}>
            <View style={[styles.dateHeaderPill, { backgroundColor: colors.border + '60' }]}>
              <Text style={[styles.dateHeaderText, { color: colors.textSecondary }]}>
                {formatDateHeader(item.created_at)}
              </Text>
            </View>
          </View>
        )}
        <View style={[styles.bubbleRow, isMe ? styles.bubbleRowRight : styles.bubbleRowLeft]}>
          <View style={[
            styles.bubble,
            isMe
              ? { backgroundColor: colors.primary, ...(showTail ? { borderBottomRightRadius: 4 } : {}) }
              : { backgroundColor: colors.card, borderWidth: 1, borderColor: colors.border, ...(showTail ? { borderBottomLeftRadius: 4 } : {}) },
          ]}>
            {!isMe && (
              <Text style={[styles.senderLabel, { color: colors.primary }]}>{item.sender?.name}</Text>
            )}

            {item.message_type === 'image' && item.file_path ? (
              <Image
                source={{ uri: `${MEDIA_BASE_URL}/storage/${item.file_path}` }}
                style={styles.imageMsg}
                resizeMode="cover"
              />
            ) : item.message_type === 'location' ? (
              <TouchableOpacity onPress={() => {
                if (item.latitude && item.longitude)
                  Linking.openURL(`https://maps.google.com/?q=${item.latitude},${item.longitude}`);
              }}>
                <View style={styles.attachRow}>
                  <Ionicons name="location" size={18} color={isMe ? '#fff' : colors.primary} />
                  <Text style={{ color: isMe ? '#fff' : colors.primary, fontWeight: '600', marginLeft: 6 }}>
                    View Location
                  </Text>
                </View>
              </TouchableOpacity>
            ) : item.message_type === 'document' && item.file_path ? (
              <TouchableOpacity onPress={() => Linking.openURL(`${MEDIA_BASE_URL}/storage/${item.file_path}`)}>
                <View style={styles.attachRow}>
                  <Ionicons name="document-attach" size={18} color={isMe ? '#fff' : colors.primary} />
                  <Text style={{ color: isMe ? '#fff' : colors.primary, fontWeight: '600', marginLeft: 6 }}>
                    Open File
                  </Text>
                </View>
              </TouchableOpacity>
            ) : (
              <Text style={[styles.msgText, { color: isMe ? '#ffffff' : colors.text }]}>
                {item.message}
              </Text>
            )}

            <View style={styles.metaRow}>
              <Text style={[styles.timeText, { color: isMe ? 'rgba(255,255,255,0.65)' : colors.textLight }]}>
                {formatTime(item.created_at)}
              </Text>
              {isMe && (
                <Ionicons
                  name={item._sending ? 'time-outline' : item._failed ? 'alert-circle' : 'checkmark-done'}
                  size={14}
                  color={item._failed ? '#ef4444' : 'rgba(255,255,255,0.65)'}
                  style={{ marginLeft: 4 }}
                />
              )}
            </View>
          </View>
        </View>
      </View>
    );
  };

  if (loading) {
    return <View style={[styles.center, { backgroundColor: colors.background }]}>
      <ActivityIndicator size="large" color={colors.primary} />
    </View>;
  }

  return (
    <KeyboardAvoidingView
      style={[styles.container, { backgroundColor: colors.background }]}
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
      keyboardVerticalOffset={Platform.OS === 'ios' ? 0 : 0}
    >
      <ScreenHeader title={chatTitle || 'Chat'} onBack={() => navigation.goBack()} />
      <FlatList
        ref={flatListRef}
        data={messages}
        keyExtractor={item => item.id.toString()}
        renderItem={renderMessage}
        contentContainerStyle={[styles.messagesList, messages.length === 0 && { flex: 1 }]}
        onContentSizeChange={() => flatListRef.current?.scrollToEnd({ animated: false })}
        keyboardDismissMode="interactive"
        keyboardShouldPersistTaps="handled"
        ListEmptyComponent={
          <View style={styles.emptyWrap}>
            <View style={[styles.emptyIcon, { backgroundColor: colors.primary + '12' }]}>
              <Ionicons name="chatbubble-ellipses-outline" size={40} color={colors.primary} />
            </View>
            <Text style={[styles.emptyTitle, { color: colors.text }]}>Start the conversation</Text>
            <Text style={[styles.emptyDesc, { color: colors.textLight }]}>Messages are end-to-end secured</Text>
          </View>
        }
      />

      {/* Emoji Quick Row */}
      {showEmoji && (
        <View style={[styles.emojiRow, { backgroundColor: colors.card, borderTopColor: colors.border }]}>
          <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.emojiRowContent}>
            {QUICK_EMOJIS.map(emoji => (
              <TouchableOpacity key={emoji} style={styles.emojiBtn} onPress={() => setText(prev => prev + emoji)}>
                <Text style={styles.emojiText}>{emoji}</Text>
              </TouchableOpacity>
            ))}
          </ScrollView>
        </View>
      )}

      <View style={[styles.inputBar, { backgroundColor: colors.card, borderTopColor: colors.border, paddingBottom: keyboardVisible ? Math.max(insets.bottom, 4) : TAB_BAR_TOTAL_HEIGHT + insets.bottom + 4 }]}>
        <TouchableOpacity
          style={styles.attachBtn}
          onPress={() => setShowEmoji(!showEmoji)}
          activeOpacity={0.7}
        >
          <Ionicons name={showEmoji ? 'close-circle-outline' : 'happy-outline'} size={24} color={colors.textLight} />
        </TouchableOpacity>
        <TouchableOpacity
          style={styles.attachBtn}
          onPress={handlePickImage}
          activeOpacity={0.7}
        >
          <Ionicons name="image-outline" size={24} color={colors.textLight} />
        </TouchableOpacity>
        <View style={[styles.inputWrap, { backgroundColor: colors.inputBg }]}>
          <TextInput
            style={[styles.textInput, { color: colors.text }]}
            value={text}
            onChangeText={setText}
            placeholder="Type a message..."
            placeholderTextColor={colors.textLight}
            multiline
            maxLength={5000}
            onFocus={() => setShowEmoji(false)}
          />
        </View>
        <TouchableOpacity
          style={[styles.sendBtn, { backgroundColor: colors.primary }, !text.trim() && { opacity: 0.4 }]}
          onPress={handleSend}
          disabled={!text.trim() || sending}
          activeOpacity={0.7}
        >
          <Ionicons name="send" size={18} color="#fff" />
        </TouchableOpacity>
      </View>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1 },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  messagesList: { paddingHorizontal: spacing.sm, paddingTop: spacing.sm, paddingBottom: spacing.md },

  dateHeaderWrap: { alignItems: 'center', marginVertical: spacing.md },
  dateHeaderPill: { paddingHorizontal: 14, paddingVertical: 4, borderRadius: 12 },
  dateHeaderText: { fontSize: 11, fontWeight: '600' },

  bubbleRow: { marginBottom: 3, paddingHorizontal: spacing.xs },
  bubbleRowRight: { alignItems: 'flex-end' },
  bubbleRowLeft: { alignItems: 'flex-start' },

  bubble: { maxWidth: '78%', borderRadius: 18, paddingHorizontal: 14, paddingVertical: 8 },
  senderLabel: { fontSize: 11, fontWeight: '700', marginBottom: 2 },
  msgText: { fontSize: 15, lineHeight: 21 },

  imageMsg: { width: 200, height: 200, borderRadius: 12, marginVertical: 4 },
  attachRow: { flexDirection: 'row', alignItems: 'center', paddingVertical: 4 },

  metaRow: { flexDirection: 'row', alignItems: 'center', justifyContent: 'flex-end', marginTop: 2 },
  timeText: { fontSize: 10 },

  emptyWrap: { flex: 1, justifyContent: 'center', alignItems: 'center', paddingBottom: 40 },
  emptyIcon: { width: 72, height: 72, borderRadius: 36, justifyContent: 'center', alignItems: 'center', marginBottom: spacing.md },
  emptyTitle: { fontSize: fontSize.md, fontWeight: '700' },
  emptyDesc: { fontSize: fontSize.sm, marginTop: 4 },

  inputBar: { flexDirection: 'row', alignItems: 'flex-end', padding: spacing.sm, borderTopWidth: 1 },
  attachBtn: { width: 36, height: 44, justifyContent: 'center', alignItems: 'center' },
  inputWrap: { flex: 1, borderRadius: 24, marginRight: spacing.sm },
  textInput: { paddingHorizontal: 16, paddingTop: 10, paddingBottom: 10, fontSize: 15, maxHeight: 100 },
  sendBtn: { width: 44, height: 44, borderRadius: 22, justifyContent: 'center', alignItems: 'center' },

  emojiRow: { borderTopWidth: 1, paddingVertical: 6 },
  emojiRowContent: { paddingHorizontal: 8, gap: 2 },
  emojiBtn: { width: 40, height: 40, justifyContent: 'center', alignItems: 'center' },
  emojiText: { fontSize: 22 },
});

import React, { useState, useEffect, useRef, useCallback } from 'react';
import {
  View, Text, TextInput, TouchableOpacity, StyleSheet,
  FlatList, KeyboardAvoidingView, Platform, ActivityIndicator,
  Alert, SafeAreaView,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { liveChatApi } from '../api';
import { useAuth } from '../contexts/AuthContext';
import { colors, spacing, fontSize } from '../theme';

const STORAGE_KEY = 'live_chat_session_key';
const POLL_INTERVAL = 4000;

type Message = {
  id: number;
  sender_type: 'visitor' | 'agent';
  body: string;
  created_at: string;
  agent_id?: number;
};

export default function LiveChatScreen({ navigation }: any) {
  const { user } = useAuth();

  // Session state
  const [sessionKey, setSessionKey] = useState<string | null>(null);
  const [sessionStatus, setSessionStatus] = useState<string>('');
  const [starting, setStarting] = useState(false);

  // Form state (pre-chat)
  const [name, setName] = useState(user?.name || '');
  const [email, setEmail] = useState(user?.email || '');

  // Chat state
  const [messages, setMessages] = useState<Message[]>([]);
  const [input, setInput] = useState('');
  const [sending, setSending] = useState(false);
  const [loading, setLoading] = useState(true);

  const flatListRef = useRef<FlatList>(null);
  const pollRef = useRef<ReturnType<typeof setInterval> | null>(null);
  const lastMsgIdRef = useRef(0);

  // Restore existing session on mount
  useEffect(() => {
    (async () => {
      try {
        const stored = await AsyncStorage.getItem(STORAGE_KEY);
        if (stored) {
          setSessionKey(stored);
        }
      } catch {}
      setLoading(false);
    })();
  }, []);

  // Start polling when we have a session
  useEffect(() => {
    if (!sessionKey) return;

    fetchMessages();
    pollRef.current = setInterval(fetchMessages, POLL_INTERVAL);

    return () => {
      if (pollRef.current) clearInterval(pollRef.current);
    };
  }, [sessionKey]);

  const fetchMessages = useCallback(async () => {
    if (!sessionKey) return;
    try {
      const data = await liveChatApi.getMessages(sessionKey, lastMsgIdRef.current || undefined);
      setSessionStatus(data.status);

      if (data.messages && data.messages.length > 0) {
        setMessages(prev => {
          const existingIds = new Set(prev.map(m => m.id));
          const newMessages = data.messages.filter((m: Message) => !existingIds.has(m.id));
          if (newMessages.length === 0) return prev;
          return [...prev, ...newMessages];
        });
        lastMsgIdRef.current = data.messages[data.messages.length - 1].id;
      }
    } catch (err) {
      // Session may have been deleted — clear it
      if (String(err).includes('404') || String(err).includes('Not Found')) {
        await AsyncStorage.removeItem(STORAGE_KEY);
        setSessionKey(null);
        setMessages([]);
        lastMsgIdRef.current = 0;
      }
    }
  }, [sessionKey]);

  // Start a new session
  async function handleStartSession() {
    if (!name.trim() || !email.trim()) {
      Alert.alert('Required', 'Please enter your name and email.');
      return;
    }
    setStarting(true);
    try {
      const data = await liveChatApi.startSession(name.trim(), email.trim());
      await AsyncStorage.setItem(STORAGE_KEY, data.session_key);
      setSessionKey(data.session_key);
      setSessionStatus(data.status);
    } catch (err: any) {
      Alert.alert('Error', err.message || 'Could not start chat session.');
    } finally {
      setStarting(false);
    }
  }

  // Send message
  async function handleSend() {
    const text = input.trim();
    if (!text || !sessionKey || sending) return;

    setSending(true);
    setInput('');
    try {
      const msg = await liveChatApi.sendMessage(sessionKey, text);
      setMessages(prev => [...prev, msg]);
      lastMsgIdRef.current = msg.id;
    } catch (err: any) {
      Alert.alert('Error', err.message || 'Failed to send message.');
      setInput(text);
    } finally {
      setSending(false);
    }
  }

  // End session
  async function handleEndChat() {
    await AsyncStorage.removeItem(STORAGE_KEY);
    if (pollRef.current) clearInterval(pollRef.current);
    setSessionKey(null);
    setMessages([]);
    lastMsgIdRef.current = 0;
    setSessionStatus('');
  }

  function formatTime(dateStr: string) {
    const d = new Date(dateStr);
    return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  }

  if (loading) {
    return (
      <View style={styles.centered}>
        <ActivityIndicator size="large" color={colors.primary} />
      </View>
    );
  }

  // Pre-chat form
  if (!sessionKey) {
    return (
      <SafeAreaView style={styles.container}>
        <View style={styles.preChatContainer}>
          <View style={styles.preChatHeader}>
            <View style={styles.chatIconCircle}>
              <Ionicons name="chatbubble-ellipses" size={36} color="#fff" />
            </View>
            <Text style={styles.preChatTitle}>Live Support</Text>
            <Text style={styles.preChatSubtitle}>
              Chat with our team in real-time.{'\n'}We typically reply within minutes.
            </Text>
          </View>

          <View style={styles.preChatForm}>
            <Text style={styles.inputLabel}>Your Name</Text>
            <TextInput
              style={styles.formInput}
              value={name}
              onChangeText={setName}
              placeholder="John Doe"
              placeholderTextColor={colors.textLight}
              autoCapitalize="words"
            />

            <Text style={styles.inputLabel}>Email Address</Text>
            <TextInput
              style={styles.formInput}
              value={email}
              onChangeText={setEmail}
              placeholder="you@example.com"
              placeholderTextColor={colors.textLight}
              keyboardType="email-address"
              autoCapitalize="none"
            />

            <TouchableOpacity
              style={[styles.startButton, starting && styles.buttonDisabled]}
              onPress={handleStartSession}
              disabled={starting}
              activeOpacity={0.8}
            >
              {starting ? (
                <ActivityIndicator color="#fff" size="small" />
              ) : (
                <>
                  <Ionicons name="chatbubbles" size={20} color="#fff" style={{ marginRight: 8 }} />
                  <Text style={styles.startButtonText}>Start Chat</Text>
                </>
              )}
            </TouchableOpacity>
          </View>
        </View>
      </SafeAreaView>
    );
  }

  // Chat interface
  return (
    <SafeAreaView style={styles.container}>
      <KeyboardAvoidingView
        style={styles.chatContainer}
        behavior={Platform.OS === 'ios' ? 'padding' : undefined}
        keyboardVerticalOffset={Platform.OS === 'ios' ? 90 : 0}
      >
        {/* Status bar */}
        <View style={styles.statusBar}>
          <View style={[styles.statusDot, sessionStatus === 'active' ? styles.dotActive : styles.dotWaiting]} />
          <Text style={styles.statusText}>
            {sessionStatus === 'active' ? 'Agent connected' :
              sessionStatus === 'closed' ? 'Chat ended' : 'Waiting for agent...'}
          </Text>
          {sessionStatus !== 'closed' && (
            <TouchableOpacity onPress={handleEndChat} hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}>
              <Text style={styles.endChatText}>End Chat</Text>
            </TouchableOpacity>
          )}
          {sessionStatus === 'closed' && (
            <TouchableOpacity onPress={handleEndChat} hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}>
              <Text style={[styles.endChatText, { color: colors.primary }]}>New Chat</Text>
            </TouchableOpacity>
          )}
        </View>

        {/* Messages */}
        <FlatList
          ref={flatListRef}
          data={messages}
          keyExtractor={(item) => item.id.toString()}
          contentContainerStyle={styles.messagesList}
          onContentSizeChange={() => flatListRef.current?.scrollToEnd({ animated: true })}
          onLayout={() => flatListRef.current?.scrollToEnd({ animated: false })}
          ListEmptyComponent={
            <View style={styles.emptyContainer}>
              <Ionicons name="chatbubble-outline" size={48} color={colors.border} />
              <Text style={styles.emptyText}>
                {sessionStatus === 'waiting'
                  ? "We've received your message.\nAn agent will be with you shortly."
                  : 'Send a message to start the conversation.'}
              </Text>
            </View>
          }
          renderItem={({ item }) => {
            const isVisitor = item.sender_type === 'visitor';
            return (
              <View style={[styles.messageBubbleRow, isVisitor ? styles.bubbleRight : styles.bubbleLeft]}>
                {!isVisitor && (
                  <View style={styles.agentAvatar}>
                    <Ionicons name="headset" size={16} color="#fff" />
                  </View>
                )}
                <View style={[styles.messageBubble, isVisitor ? styles.visitorBubble : styles.agentBubble]}>
                  <Text style={[styles.messageText, isVisitor ? styles.visitorText : styles.agentText]}>
                    {item.body}
                  </Text>
                  <Text style={[styles.messageTime, isVisitor ? styles.visitorTime : styles.agentTime]}>
                    {formatTime(item.created_at)}
                  </Text>
                </View>
              </View>
            );
          }}
        />

        {/* Input bar */}
        {sessionStatus !== 'closed' && (
          <View style={styles.inputBar}>
            <TextInput
              style={styles.chatInput}
              value={input}
              onChangeText={setInput}
              placeholder="Type a message..."
              placeholderTextColor={colors.textLight}
              multiline
              maxLength={2000}
            />
            <TouchableOpacity
              style={[styles.sendButton, (!input.trim() || sending) && styles.sendButtonDisabled]}
              onPress={handleSend}
              disabled={!input.trim() || sending}
              activeOpacity={0.7}
            >
              {sending ? (
                <ActivityIndicator color="#fff" size="small" />
              ) : (
                <Ionicons name="send" size={18} color="#fff" />
              )}
            </TouchableOpacity>
          </View>
        )}
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: colors.background },
  centered: { flex: 1, justifyContent: 'center', alignItems: 'center', backgroundColor: colors.background },

  // Pre-chat
  preChatContainer: { flex: 1, justifyContent: 'center', padding: spacing.lg },
  preChatHeader: { alignItems: 'center', marginBottom: spacing.xl },
  chatIconCircle: {
    width: 72, height: 72, borderRadius: 36,
    backgroundColor: colors.primary, justifyContent: 'center', alignItems: 'center',
    marginBottom: spacing.md,
    shadowColor: colors.primary, shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3, shadowRadius: 12, elevation: 6,
  },
  preChatTitle: { fontSize: fontSize.xxl, fontWeight: '800', color: colors.text, marginBottom: spacing.xs },
  preChatSubtitle: { fontSize: fontSize.sm, color: colors.textSecondary, textAlign: 'center', lineHeight: 20 },

  preChatForm: {
    backgroundColor: colors.card, borderRadius: 20, padding: spacing.lg,
    shadowColor: colors.shadow, shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.08, shadowRadius: 16, elevation: 4,
  },
  inputLabel: { fontSize: fontSize.sm, fontWeight: '600', color: colors.text, marginBottom: spacing.xs, marginTop: spacing.md },
  formInput: {
    backgroundColor: colors.inputBg, borderRadius: 12, padding: 14,
    fontSize: fontSize.md, borderWidth: 1, borderColor: colors.border, color: colors.text,
  },
  startButton: {
    backgroundColor: colors.primary, borderRadius: 14, padding: 16,
    alignItems: 'center', justifyContent: 'center', marginTop: spacing.lg,
    flexDirection: 'row',
    shadowColor: colors.primary, shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.25, shadowRadius: 8, elevation: 4,
  },
  startButtonText: { color: '#fff', fontSize: fontSize.md, fontWeight: '700' },
  buttonDisabled: { opacity: 0.6 },

  // Chat
  chatContainer: { flex: 1 },

  statusBar: {
    flexDirection: 'row', alignItems: 'center', paddingHorizontal: spacing.md,
    paddingVertical: spacing.sm + 2, backgroundColor: colors.card,
    borderBottomWidth: 1, borderBottomColor: colors.border,
  },
  statusDot: { width: 8, height: 8, borderRadius: 4, marginRight: spacing.sm },
  dotActive: { backgroundColor: colors.success },
  dotWaiting: { backgroundColor: colors.warning },
  statusText: { flex: 1, fontSize: fontSize.sm, color: colors.textSecondary, fontWeight: '500' },
  endChatText: { fontSize: fontSize.sm, color: colors.danger, fontWeight: '600' },

  messagesList: { padding: spacing.md, paddingBottom: spacing.xl, flexGrow: 1 },

  emptyContainer: { flex: 1, justifyContent: 'center', alignItems: 'center', paddingTop: 80 },
  emptyText: { fontSize: fontSize.sm, color: colors.textLight, textAlign: 'center', marginTop: spacing.md, lineHeight: 20 },

  messageBubbleRow: { flexDirection: 'row', marginBottom: spacing.sm, alignItems: 'flex-end' },
  bubbleRight: { justifyContent: 'flex-end' },
  bubbleLeft: { justifyContent: 'flex-start' },

  agentAvatar: {
    width: 28, height: 28, borderRadius: 14, backgroundColor: colors.primary,
    justifyContent: 'center', alignItems: 'center', marginRight: spacing.xs,
  },

  messageBubble: { maxWidth: '75%', borderRadius: 16, paddingHorizontal: 14, paddingVertical: 10 },
  visitorBubble: { backgroundColor: colors.primary, borderBottomRightRadius: 4 },
  agentBubble: { backgroundColor: colors.card, borderBottomLeftRadius: 4, borderWidth: 1, borderColor: colors.border },

  messageText: { fontSize: fontSize.md, lineHeight: 21 },
  visitorText: { color: '#fff' },
  agentText: { color: colors.text },

  messageTime: { fontSize: 10, marginTop: 4 },
  visitorTime: { color: 'rgba(255,255,255,0.7)', textAlign: 'right' },
  agentTime: { color: colors.textLight },

  inputBar: {
    flexDirection: 'row', alignItems: 'flex-end', padding: spacing.sm,
    paddingHorizontal: spacing.md, backgroundColor: colors.card,
    borderTopWidth: 1, borderTopColor: colors.border,
  },
  chatInput: {
    flex: 1, backgroundColor: colors.inputBg, borderRadius: 20,
    paddingHorizontal: 16, paddingTop: 10, paddingBottom: 10,
    fontSize: fontSize.md, maxHeight: 100, color: colors.text,
    marginRight: spacing.sm,
  },
  sendButton: {
    width: 40, height: 40, borderRadius: 20, backgroundColor: colors.primary,
    justifyContent: 'center', alignItems: 'center',
  },
  sendButtonDisabled: { backgroundColor: colors.textLight },
});

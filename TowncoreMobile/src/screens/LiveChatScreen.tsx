import React, { useState, useEffect, useRef, useCallback } from 'react';
import {
  View, Text, TextInput, TouchableOpacity,
  FlatList, KeyboardAvoidingView, Platform, ActivityIndicator,
  Alert, SafeAreaView, RefreshControl,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { liveChatAgentApi } from '../api';
import { useAuth } from '../contexts/AuthContext';
import { useTheme } from '../contexts/ThemeContext';
import { spacing, fontSize } from '../theme';

const POLL_INTERVAL = 4000;

type Session = {
  id: number;
  visitor_name: string;
  visitor_email: string;
  status: 'waiting' | 'active' | 'closed';
  agent?: { id: number; name: string } | null;
  messages_count: number;
  created_at: string;
  updated_at: string;
};

type Message = {
  id: number;
  sender_type: 'visitor' | 'agent';
  body: string;
  created_at: string;
  agent?: { id: number; name: string } | null;
};

export default function LiveChatScreen({ navigation }: any) {
  const { user } = useAuth();
  const { colors } = useTheme();

  const [view, setView] = useState<'list' | 'chat'>('list');
  const [activeSession, setActiveSession] = useState<Session | null>(null);

  const [sessions, setSessions] = useState<Session[]>([]);
  const [loadingSessions, setLoadingSessions] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [filter, setFilter] = useState<'all' | 'waiting' | 'active' | 'closed'>('all');

  const [messages, setMessages] = useState<Message[]>([]);
  const [input, setInput] = useState('');
  const [sending, setSending] = useState(false);
  const [sessionStatus, setSessionStatus] = useState('');
  const [joining, setJoining] = useState(false);

  const flatListRef = useRef<FlatList>(null);
  const pollRef = useRef<ReturnType<typeof setInterval> | null>(null);
  const lastMsgIdRef = useRef(0);
  const listPollRef = useRef<ReturnType<typeof setInterval> | null>(null);

  // --- Session List ---
  const loadSessions = useCallback(async (silent = false) => {
    try {
      if (!silent) setLoadingSessions(true);
      const data = await liveChatAgentApi.sessions(filter === 'all' ? undefined : filter);
      setSessions(data.data || []);
    } catch (err) {
      console.error('Failed to load sessions', err);
    } finally {
      setLoadingSessions(false);
      setRefreshing(false);
    }
  }, [filter]);

  useEffect(() => {
    if (view === 'list') {
      loadSessions();
      listPollRef.current = setInterval(() => loadSessions(true), 6000);
      return () => { if (listPollRef.current) clearInterval(listPollRef.current); };
    }
  }, [view, loadSessions]);

  // --- Chat Messages ---
  const fetchMessages = useCallback(async () => {
    if (!activeSession) return;
    try {
      const data = await liveChatAgentApi.getMessages(activeSession.id, lastMsgIdRef.current || undefined);
      setSessionStatus(data.status);
      if (data.messages && data.messages.length > 0) {
        setMessages(prev => {
          const existingIds = new Set(prev.filter(m => !String(m.id).startsWith('temp_')).map(m => m.id));
          const newMsgs = data.messages.filter((m: Message) => !existingIds.has(m.id));
          const serverTexts = new Set(data.messages.map((m: Message) => `${m.sender_type}_${m.body}`));
          const remainingTemp = prev.filter(m => String(m.id).startsWith('temp_') && !serverTexts.has(`agent_${m.body}`));
          const realMsgs = prev.filter(m => !String(m.id).startsWith('temp_'));
          if (newMsgs.length === 0) return [...realMsgs, ...remainingTemp];
          return [...realMsgs, ...newMsgs, ...remainingTemp];
        });
        lastMsgIdRef.current = data.messages[data.messages.length - 1].id;
      }
    } catch (err) {
      console.error('Failed to fetch messages', err);
    }
  }, [activeSession]);

  useEffect(() => {
    if (view === 'chat' && activeSession) {
      lastMsgIdRef.current = 0;
      setMessages([]);
      fetchMessages();
      pollRef.current = setInterval(fetchMessages, POLL_INTERVAL);
      return () => { if (pollRef.current) clearInterval(pollRef.current); };
    }
  }, [view, activeSession, fetchMessages]);

  // --- Actions ---
  function openSession(session: Session) {
    setActiveSession(session);
    setSessionStatus(session.status);
    setView('chat');
  }

  function goBack() {
    if (pollRef.current) clearInterval(pollRef.current);
    setView('list');
    setActiveSession(null);
    setMessages([]);
    lastMsgIdRef.current = 0;
  }

  async function handleJoin() {
    if (!activeSession) return;
    setJoining(true);
    try {
      await liveChatAgentApi.joinSession(activeSession.id);
      setSessionStatus('active');
      setActiveSession(prev => prev ? { ...prev, status: 'active', agent: { id: user!.id, name: user!.name } } : prev);
    } catch (err: any) {
      Alert.alert('Error', err.message || 'Could not join session.');
    } finally {
      setJoining(false);
    }
  }

  async function handleSend() {
    const text = input.trim();
    if (!text || !activeSession || sending) return;

    const tempId = `temp_${Date.now()}`;
    const optimisticMsg: Message = {
      id: tempId as any,
      sender_type: 'agent',
      body: text,
      created_at: new Date().toISOString(),
      agent: { id: user!.id, name: user!.name },
    };

    setSending(true);
    setInput('');
    setMessages(prev => [...prev, optimisticMsg]);
    setTimeout(() => flatListRef.current?.scrollToEnd({ animated: true }), 50);

    try {
      const msg = await liveChatAgentApi.sendMessage(activeSession.id, text);
      setMessages(prev => prev.map(m => m.id === tempId ? msg : m));
      lastMsgIdRef.current = Math.max(lastMsgIdRef.current, msg.id);
      if (sessionStatus !== 'active') setSessionStatus('active');
    } catch (err: any) {
      Alert.alert('Error', err.message || 'Failed to send message.');
      setMessages(prev => prev.filter(m => m.id !== tempId));
      setInput(text);
    } finally {
      setSending(false);
    }
  }

  async function handleClose() {
    if (!activeSession) return;
    Alert.alert('Close Session', 'Are you sure you want to close this chat session?', [
      { text: 'Cancel', style: 'cancel' },
      {
        text: 'Close', style: 'destructive', onPress: async () => {
          try {
            await liveChatAgentApi.closeSession(activeSession.id);
            setSessionStatus('closed');
          } catch (err: any) {
            Alert.alert('Error', err.message || 'Failed to close session.');
          }
        },
      },
    ]);
  }

  function formatTime(dateStr: string) {
    return new Date(dateStr).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  }

  function formatDate(dateStr: string) {
    const d = new Date(dateStr);
    const now = new Date();
    const yesterday = new Date(now);
    yesterday.setDate(yesterday.getDate() - 1);
    if (d.toDateString() === now.toDateString()) return formatTime(dateStr);
    if (d.toDateString() === yesterday.toDateString()) return 'Yesterday';
    return d.toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
  }

  // --- SESSION LIST VIEW ---
  if (view === 'list') {
    const filters: Array<{ key: typeof filter; label: string }> = [
      { key: 'all', label: 'All' },
      { key: 'waiting', label: 'Waiting' },
      { key: 'active', label: 'Active' },
      { key: 'closed', label: 'Closed' },
    ];

    const statusColor = (s: string) =>
      s === 'waiting' ? colors.warning : s === 'active' ? colors.success : colors.textLight;

    return (
      <SafeAreaView style={{ flex: 1, backgroundColor: colors.background }}>
        <View style={{ backgroundColor: colors.primary, paddingTop: Platform.OS === 'ios' ? 0 : 12, paddingBottom: 16, paddingHorizontal: spacing.md }}>
          <View style={{ flexDirection: 'row', alignItems: 'center', marginBottom: 14 }}>
            <TouchableOpacity onPress={() => navigation.goBack()} style={{ marginRight: 12 }}>
              <Ionicons name="arrow-back" size={24} color="#fff" />
            </TouchableOpacity>
            <View style={{ flex: 1 }}>
              <Text style={{ color: '#fff', fontSize: fontSize.lg, fontWeight: '800' }}>Live Chat</Text>
              <Text style={{ color: 'rgba(255,255,255,0.75)', fontSize: 12 }}>
                {sessions.filter(s => s.status === 'waiting').length} waiting
              </Text>
            </View>
            <TouchableOpacity onPress={() => loadSessions()}>
              <Ionicons name="refresh" size={22} color="#fff" />
            </TouchableOpacity>
          </View>
          <View style={{ flexDirection: 'row', gap: 8 }}>
            {filters.map(f => (
              <TouchableOpacity
                key={f.key}
                onPress={() => setFilter(f.key)}
                style={{
                  paddingHorizontal: 14, paddingVertical: 6, borderRadius: 16,
                  backgroundColor: filter === f.key ? '#fff' : 'rgba(255,255,255,0.2)',
                }}
              >
                <Text style={{
                  fontSize: 12, fontWeight: '700',
                  color: filter === f.key ? colors.primary : '#fff',
                }}>
                  {f.label}
                </Text>
              </TouchableOpacity>
            ))}
          </View>
        </View>

        {loadingSessions && sessions.length === 0 ? (
          <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
            <ActivityIndicator size="large" color={colors.primary} />
          </View>
        ) : (
          <FlatList
            data={sessions}
            keyExtractor={item => item.id.toString()}
            contentContainerStyle={{ paddingVertical: spacing.sm }}
            refreshControl={
              <RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); loadSessions(); }} colors={[colors.primary]} />
            }
            ListEmptyComponent={
              <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center', paddingTop: 80 }}>
                <Ionicons name="chatbubbles-outline" size={56} color={colors.border} />
                <Text style={{ color: colors.textSecondary, marginTop: spacing.md, fontSize: fontSize.md }}>
                  No live chat sessions
                </Text>
              </View>
            }
            renderItem={({ item }) => (
              <TouchableOpacity
                onPress={() => openSession(item)}
                activeOpacity={0.7}
                style={{
                  flexDirection: 'row', alignItems: 'center',
                  paddingHorizontal: spacing.md, paddingVertical: 14,
                  backgroundColor: colors.card,
                  borderBottomWidth: 1, borderBottomColor: colors.border,
                }}
              >
                <View style={{
                  width: 44, height: 44, borderRadius: 22,
                  backgroundColor: statusColor(item.status) + '20',
                  justifyContent: 'center', alignItems: 'center', marginRight: 12,
                }}>
                  <Ionicons
                    name={item.status === 'waiting' ? 'time' : item.status === 'active' ? 'chatbubbles' : 'checkmark-circle'}
                    size={22}
                    color={statusColor(item.status)}
                  />
                </View>
                <View style={{ flex: 1 }}>
                  <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' }}>
                    <Text style={{ fontSize: 15, fontWeight: '700', color: colors.text }} numberOfLines={1}>
                      {item.visitor_name}
                    </Text>
                    <Text style={{ fontSize: 11, color: colors.textLight }}>{formatDate(item.created_at)}</Text>
                  </View>
                  <View style={{ flexDirection: 'row', alignItems: 'center', marginTop: 3 }}>
                    <Text style={{ fontSize: 12, color: colors.textSecondary, flex: 1 }} numberOfLines={1}>
                      {item.visitor_email}
                    </Text>
                    <View style={{
                      flexDirection: 'row', alignItems: 'center',
                      paddingHorizontal: 8, paddingVertical: 2,
                      borderRadius: 10, backgroundColor: statusColor(item.status) + '18',
                    }}>
                      <View style={{ width: 6, height: 6, borderRadius: 3, backgroundColor: statusColor(item.status), marginRight: 4 }} />
                      <Text style={{ fontSize: 10, fontWeight: '700', color: statusColor(item.status), textTransform: 'capitalize' }}>
                        {item.status}
                      </Text>
                    </View>
                  </View>
                  {item.agent && (
                    <Text style={{ fontSize: 11, color: colors.textLight, marginTop: 2 }}>
                      Agent: {item.agent.name}
                    </Text>
                  )}
                </View>
                {item.messages_count > 0 && (
                  <View style={{
                    minWidth: 22, height: 22, borderRadius: 11,
                    backgroundColor: colors.primary, justifyContent: 'center', alignItems: 'center',
                    marginLeft: 8, paddingHorizontal: 6,
                  }}>
                    <Text style={{ color: '#fff', fontSize: 11, fontWeight: '700' }}>{item.messages_count}</Text>
                  </View>
                )}
              </TouchableOpacity>
            )}
          />
        )}
      </SafeAreaView>
    );
  }

  // --- CHAT VIEW ---
  return (
    <SafeAreaView style={{ flex: 1, backgroundColor: colors.background }}>
      <View style={{
        flexDirection: 'row', alignItems: 'center',
        paddingHorizontal: spacing.md, paddingVertical: 12,
        backgroundColor: colors.card, borderBottomWidth: 1, borderBottomColor: colors.border,
      }}>
        <TouchableOpacity onPress={goBack} style={{ marginRight: 12 }}>
          <Ionicons name="arrow-back" size={24} color={colors.text} />
        </TouchableOpacity>
        <View style={{ flex: 1 }}>
          <Text style={{ fontSize: 16, fontWeight: '700', color: colors.text }}>
            {activeSession?.visitor_name}
          </Text>
          <Text style={{ fontSize: 12, color: colors.textSecondary }}>
            {activeSession?.visitor_email}
          </Text>
        </View>
        {sessionStatus === 'waiting' && (
          <TouchableOpacity
            onPress={handleJoin}
            disabled={joining}
            style={{
              flexDirection: 'row', alignItems: 'center',
              backgroundColor: colors.success, paddingHorizontal: 14, paddingVertical: 7,
              borderRadius: 16,
            }}
          >
            {joining ? <ActivityIndicator size="small" color="#fff" /> : (
              <>
                <Ionicons name="enter-outline" size={16} color="#fff" />
                <Text style={{ color: '#fff', fontWeight: '700', fontSize: 13, marginLeft: 4 }}>Join</Text>
              </>
            )}
          </TouchableOpacity>
        )}
        {sessionStatus === 'active' && (
          <TouchableOpacity onPress={handleClose}>
            <Ionicons name="close-circle" size={26} color={colors.danger} />
          </TouchableOpacity>
        )}
      </View>

      <View style={{
        flexDirection: 'row', alignItems: 'center', justifyContent: 'center',
        paddingVertical: 6,
        backgroundColor: sessionStatus === 'waiting' ? colors.warning + '15' : sessionStatus === 'active' ? colors.success + '15' : colors.textLight + '15',
      }}>
        <View style={{
          width: 7, height: 7, borderRadius: 4, marginRight: 6,
          backgroundColor: sessionStatus === 'waiting' ? colors.warning : sessionStatus === 'active' ? colors.success : colors.textLight,
        }} />
        <Text style={{
          fontSize: 12, fontWeight: '600',
          color: sessionStatus === 'waiting' ? colors.warning : sessionStatus === 'active' ? colors.success : colors.textLight,
        }}>
          {sessionStatus === 'waiting' ? 'Waiting - visitor is online' : sessionStatus === 'active' ? 'Active - you are connected' : 'Session closed'}
        </Text>
      </View>

      <KeyboardAvoidingView
        style={{ flex: 1 }}
        behavior={Platform.OS === 'ios' ? 'padding' : undefined}
        keyboardVerticalOffset={Platform.OS === 'ios' ? 90 : 0}
      >
        <FlatList
          ref={flatListRef}
          data={messages}
          keyExtractor={item => String(item.id)}
          contentContainerStyle={{ padding: spacing.md, paddingBottom: spacing.xl, flexGrow: 1 }}
          onContentSizeChange={() => flatListRef.current?.scrollToEnd({ animated: true })}
          onLayout={() => flatListRef.current?.scrollToEnd({ animated: false })}
          ListEmptyComponent={
            <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center', paddingTop: 80 }}>
              <Ionicons name="chatbubble-outline" size={48} color={colors.border} />
              <Text style={{ fontSize: fontSize.sm, color: colors.textLight, textAlign: 'center', marginTop: spacing.md, lineHeight: 20 }}>
                {sessionStatus === 'waiting'
                  ? 'Visitor is waiting for help.\nJoin to start chatting.'
                  : 'No messages yet.'}
              </Text>
            </View>
          }
          renderItem={({ item }) => {
            const isAgent = item.sender_type === 'agent';
            return (
              <View style={{
                flexDirection: 'row', marginBottom: spacing.sm, alignItems: 'flex-end',
                justifyContent: isAgent ? 'flex-end' : 'flex-start',
              }}>
                {!isAgent && (
                  <View style={{
                    width: 28, height: 28, borderRadius: 14, backgroundColor: colors.primary + '20',
                    justifyContent: 'center', alignItems: 'center', marginRight: spacing.xs,
                  }}>
                    <Ionicons name="person" size={14} color={colors.primary} />
                  </View>
                )}
                <View style={{
                  maxWidth: '75%', borderRadius: 16, paddingHorizontal: 14, paddingVertical: 10,
                  ...(isAgent
                    ? { backgroundColor: colors.primary, borderBottomRightRadius: 4 }
                    : { backgroundColor: colors.card, borderBottomLeftRadius: 4, borderWidth: 1, borderColor: colors.border }),
                }}>
                  {!isAgent && (
                    <Text style={{ fontSize: 11, fontWeight: '700', color: colors.primary, marginBottom: 2 }}>
                      {activeSession?.visitor_name}
                    </Text>
                  )}
                  <Text style={{ fontSize: 15, lineHeight: 21, color: isAgent ? '#fff' : colors.text }}>
                    {item.body}
                  </Text>
                  <Text style={{
                    fontSize: 10, marginTop: 4,
                    color: isAgent ? 'rgba(255,255,255,0.7)' : colors.textLight,
                    textAlign: isAgent ? 'right' : 'left',
                  }}>
                    {formatTime(item.created_at)}
                  </Text>
                </View>
              </View>
            );
          }}
        />

        {sessionStatus !== 'closed' && (
          <View style={{
            flexDirection: 'row', alignItems: 'flex-end', padding: spacing.sm,
            paddingHorizontal: spacing.md, backgroundColor: colors.card,
            borderTopWidth: 1, borderTopColor: colors.border,
          }}>
            <TextInput
              style={{
                flex: 1, backgroundColor: colors.inputBg, borderRadius: 20,
                paddingHorizontal: 16, paddingTop: 10, paddingBottom: 10,
                fontSize: 15, maxHeight: 100, color: colors.text,
                marginRight: spacing.sm,
              }}
              value={input}
              onChangeText={setInput}
              placeholder="Type a reply..."
              placeholderTextColor={colors.textLight}
              multiline
              maxLength={2000}
            />
            <TouchableOpacity
              style={{
                width: 42, height: 42, borderRadius: 21,
                backgroundColor: !input.trim() || sending ? colors.textLight : colors.primary,
                justifyContent: 'center', alignItems: 'center',
              }}
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

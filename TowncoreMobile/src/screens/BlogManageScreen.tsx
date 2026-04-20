import React, { useEffect, useState, useCallback } from 'react';
import {
  View, Text, StyleSheet, FlatList, TouchableOpacity,
  ActivityIndicator, Alert, Modal, TextInput, ScrollView, Image,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import * as ImagePicker from 'expo-image-picker';
import { useTheme } from '../contexts/ThemeContext';
import { blogApi } from '../api';
import { spacing, fontSize } from '../theme';
import { TAB_BAR_TOTAL_HEIGHT } from '../constants/layout';
import ScreenHeader from '../components/ScreenHeader';
import API_BASE_URL from '../config';

const STATUS_FILTERS = ['all', 'published', 'draft'] as const;

export default function BlogManageScreen({ navigation }: any) {
  const { colors } = useTheme();
  const insets = useSafeAreaInsets();
  const [posts, setPosts] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);
  const [hasMore, setHasMore] = useState(true);
  const [statusFilter, setStatusFilter] = useState<string>('all');

  // Editor modal
  const [showEditor, setShowEditor] = useState(false);
  const [editingPost, setEditingPost] = useState<any>(null);
  const [title, setTitle] = useState('');
  const [content, setContent] = useState('');
  const [metaDesc, setMetaDesc] = useState('');
  const [status, setStatus] = useState<'draft' | 'published'>('draft');
  const [imageUri, setImageUri] = useState<string | null>(null);
  const [saving, setSaving] = useState(false);

  const loadPosts = useCallback(async (p = 1, append = false) => {
    try {
      if (p === 1) setLoading(true);
      const filterStatus = statusFilter === 'all' ? undefined : statusFilter;
      const data = await blogApi.adminList(p, filterStatus);
      const items = data.data || data;
      if (append) {
        setPosts(prev => [...prev, ...items]);
      } else {
        setPosts(items);
      }
      setHasMore(data.next_page_url !== null);
      setPage(p);
    } catch (err: any) {
      Alert.alert('Error', err.message);
    } finally {
      setLoading(false);
    }
  }, [statusFilter]);

  useEffect(() => {
    loadPosts(1);
  }, [statusFilter]);

  function openEditor(post?: any) {
    if (post) {
      setEditingPost(post);
      setTitle(post.title || '');
      setContent(post.content || '');
      setMetaDesc(post.meta_description || '');
      setStatus(post.status || 'draft');
      setImageUri(null);
    } else {
      setEditingPost(null);
      setTitle('');
      setContent('');
      setMetaDesc('');
      setStatus('draft');
      setImageUri(null);
    }
    setShowEditor(true);
  }

  async function pickImage() {
    const result = await ImagePicker.launchImageLibraryAsync({
      mediaTypes: ['images'],
      quality: 0.8,
      allowsEditing: true,
      aspect: [16, 9],
    });
    if (!result.canceled && result.assets[0]) {
      setImageUri(result.assets[0].uri);
    }
  }

  async function handleSave() {
    if (!title.trim() || !content.trim()) {
      Alert.alert('Error', 'Title and content are required');
      return;
    }
    setSaving(true);
    try {
      const formData = new FormData();
      formData.append('title', title.trim());
      formData.append('content', content.trim());
      formData.append('status', status);
      if (metaDesc.trim()) formData.append('meta_description', metaDesc.trim());
      if (imageUri) {
        const filename = imageUri.split('/').pop() || 'image.jpg';
        const ext = filename.split('.').pop()?.toLowerCase() || 'jpg';
        formData.append('featured_image', {
          uri: imageUri,
          name: filename,
          type: `image/${ext === 'jpg' ? 'jpeg' : ext}`,
        } as any);
      }

      if (editingPost) {
        await blogApi.update(editingPost.id, formData);
      } else {
        await blogApi.create(formData);
      }

      setShowEditor(false);
      Alert.alert('Success', editingPost ? 'Post updated' : 'Post created');
      loadPosts(1);
    } catch (err: any) {
      Alert.alert('Error', err.message);
    } finally {
      setSaving(false);
    }
  }

  async function handleDelete(post: any) {
    Alert.alert('Delete Post', `Delete "${post.title}"?`, [
      { text: 'Cancel' },
      {
        text: 'Delete', style: 'destructive', onPress: async () => {
          try {
            await blogApi.delete(post.id);
            setPosts(prev => prev.filter(p => p.id !== post.id));
          } catch (err: any) {
            Alert.alert('Error', err.message);
          }
        }
      },
    ]);
  }

  function getImageUrl(path: string | null) {
    if (!path) return null;
    if (path.startsWith('http')) return path;
    // Strip /api from API_BASE_URL to get the base server URL
    const baseUrl = API_BASE_URL.replace(/\/api$/, '');
    if (path.startsWith('/storage/')) return baseUrl + path;
    return baseUrl + '/storage/' + path;
  }

  function renderPost({ item }: { item: any }) {
    const imgUrl = getImageUrl(item.featured_image);
    return (
      <TouchableOpacity
        style={[styles.postCard, { backgroundColor: colors.card }]}
        onPress={() => openEditor(item)}
        activeOpacity={0.7}
      >
        {imgUrl ? (
          <Image source={{ uri: imgUrl }} style={styles.postImage} />
        ) : (
          <View style={[styles.postImagePlaceholder, { backgroundColor: colors.border + '50' }]}>
            <Ionicons name="image-outline" size={28} color={colors.textLight} />
          </View>
        )}
        <View style={styles.postInfo}>
          <Text style={[styles.postTitle, { color: colors.text }]} numberOfLines={2}>{item.title}</Text>
          <View style={styles.postMeta}>
            <View style={[
              styles.statusPill,
              { backgroundColor: item.status === 'published' ? colors.success + '15' : colors.warning + '15' },
            ]}>
              <Text style={[
                styles.statusText,
                { color: item.status === 'published' ? colors.success : colors.warning },
              ]}>
                {item.status}
              </Text>
            </View>
            {item.published_at && (
              <Text style={[styles.postDate, { color: colors.textLight }]}>
                {new Date(item.published_at).toLocaleDateString(undefined, { month: 'short', day: 'numeric' })}
              </Text>
            )}
          </View>
        </View>
        <TouchableOpacity onPress={() => handleDelete(item)} hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}>
          <Ionicons name="trash-outline" size={18} color={colors.danger} />
        </TouchableOpacity>
      </TouchableOpacity>
    );
  }

  return (
    <View style={[styles.container, { backgroundColor: colors.background }]}>
      <ScreenHeader
        title="Blog Manager"
        onBack={() => navigation.goBack()}
        rightIcon="add-circle-outline"
        onRight={() => openEditor()}
      />

      {/* Status Filters */}
      <ScrollView horizontal showsHorizontalScrollIndicator={false} style={styles.filterRow} contentContainerStyle={styles.filterContent}>
        {STATUS_FILTERS.map(s => (
          <TouchableOpacity
            key={s}
            style={[styles.filterPill, { backgroundColor: statusFilter === s ? colors.primary : colors.card }]}
            onPress={() => setStatusFilter(s)}
          >
            <Text style={[styles.filterText, { color: statusFilter === s ? '#fff' : colors.textSecondary }]}>
              {s.charAt(0).toUpperCase() + s.slice(1)}
            </Text>
          </TouchableOpacity>
        ))}
      </ScrollView>

      {loading && page === 1 ? (
        <ActivityIndicator size="large" color={colors.primary} style={{ marginTop: 40 }} />
      ) : (
        <FlatList
          data={posts}
          keyExtractor={(item) => item.id.toString()}
          renderItem={renderPost}
          contentContainerStyle={{ paddingHorizontal: spacing.md, paddingBottom: TAB_BAR_TOTAL_HEIGHT + insets.bottom + 20 }}
          onEndReached={() => hasMore && !loading && loadPosts(page + 1, true)}
          onEndReachedThreshold={0.3}
          ListEmptyComponent={
            <View style={styles.empty}>
              <Ionicons name="newspaper-outline" size={48} color={colors.textLight} />
              <Text style={[styles.emptyText, { color: colors.textLight }]}>No blog posts</Text>
            </View>
          }
        />
      )}

      {/* Post Editor Modal */}
      <Modal visible={showEditor} transparent animationType="slide">
        <View style={styles.modalOverlay}>
          <View style={[styles.modalCard, { backgroundColor: colors.card }]}>
            <View style={styles.modalHeader}>
              <Text style={[styles.modalTitle, { color: colors.text }]}>
                {editingPost ? 'Edit Post' : 'New Post'}
              </Text>
              <TouchableOpacity onPress={() => setShowEditor(false)}>
                <Ionicons name="close" size={24} color={colors.textLight} />
              </TouchableOpacity>
            </View>
            <ScrollView keyboardShouldPersistTaps="handled" showsVerticalScrollIndicator={false}>
              <Text style={[styles.label, { color: colors.textSecondary }]}>Title</Text>
              <TextInput
                style={[styles.input, { backgroundColor: colors.inputBg, borderColor: colors.border, color: colors.text }]}
                value={title}
                onChangeText={setTitle}
                placeholder="Post title"
                placeholderTextColor={colors.textLight}
              />

              <Text style={[styles.label, { color: colors.textSecondary }]}>Content</Text>
              <TextInput
                style={[styles.input, styles.textArea, { backgroundColor: colors.inputBg, borderColor: colors.border, color: colors.text }]}
                value={content}
                onChangeText={setContent}
                placeholder="Write your post..."
                placeholderTextColor={colors.textLight}
                multiline
                textAlignVertical="top"
              />

              <Text style={[styles.label, { color: colors.textSecondary }]}>Meta Description</Text>
              <TextInput
                style={[styles.input, { backgroundColor: colors.inputBg, borderColor: colors.border, color: colors.text }]}
                value={metaDesc}
                onChangeText={setMetaDesc}
                placeholder="Brief description for SEO"
                placeholderTextColor={colors.textLight}
              />

              {/* Featured Image */}
              <Text style={[styles.label, { color: colors.textSecondary }]}>Featured Image</Text>
              <TouchableOpacity style={[styles.imagePicker, { borderColor: colors.border }]} onPress={pickImage}>
                {imageUri ? (
                  <Image source={{ uri: imageUri }} style={styles.previewImage} />
                ) : editingPost?.featured_image ? (
                  <Image source={{ uri: getImageUrl(editingPost.featured_image)! }} style={styles.previewImage} />
                ) : (
                  <View style={styles.imagePickerEmpty}>
                    <Ionicons name="camera-outline" size={28} color={colors.textLight} />
                    <Text style={[styles.imagePickerText, { color: colors.textLight }]}>Tap to select image</Text>
                  </View>
                )}
              </TouchableOpacity>

              {/* Status */}
              <Text style={[styles.label, { color: colors.textSecondary }]}>Status</Text>
              <View style={styles.statusSelector}>
                {(['draft', 'published'] as const).map(s => (
                  <TouchableOpacity
                    key={s}
                    style={[
                      styles.statusSelectorBtn,
                      { borderColor: status === s ? colors.primary : colors.border },
                      status === s && { backgroundColor: colors.primary + '15' },
                    ]}
                    onPress={() => setStatus(s)}
                  >
                    <Text style={[
                      styles.statusSelectorText,
                      { color: status === s ? colors.primary : colors.textSecondary },
                    ]}>
                      {s.charAt(0).toUpperCase() + s.slice(1)}
                    </Text>
                  </TouchableOpacity>
                ))}
              </View>

              <TouchableOpacity
                style={[styles.saveBtn, { backgroundColor: colors.primary }, saving && { opacity: 0.6 }]}
                onPress={handleSave}
                disabled={saving}
              >
                {saving ? <ActivityIndicator color="#fff" /> : (
                  <Text style={styles.saveBtnText}>{editingPost ? 'Update Post' : 'Create Post'}</Text>
                )}
              </TouchableOpacity>

              <View style={{ height: 20 }} />
            </ScrollView>
          </View>
        </View>
      </Modal>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1 },

  filterRow: { marginTop: spacing.sm, maxHeight: 44 },
  filterContent: { paddingHorizontal: spacing.md, gap: 8 },
  filterPill: { paddingHorizontal: 16, paddingVertical: 8, borderRadius: 20 },
  filterText: { fontSize: 13, fontWeight: '600' },

  postCard: {
    flexDirection: 'row', alignItems: 'center', padding: 12, borderRadius: 16,
    marginTop: spacing.sm, gap: 12,
    shadowColor: '#000', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.04, shadowRadius: 4, elevation: 2,
  },
  postImage: { width: 60, height: 60, borderRadius: 12 },
  postImagePlaceholder: { width: 60, height: 60, borderRadius: 12, justifyContent: 'center', alignItems: 'center' },
  postInfo: { flex: 1 },
  postTitle: { fontSize: 15, fontWeight: '600' },
  postMeta: { flexDirection: 'row', alignItems: 'center', marginTop: 4, gap: 8 },
  statusPill: { paddingHorizontal: 8, paddingVertical: 2, borderRadius: 6 },
  statusText: { fontSize: 11, fontWeight: '700', textTransform: 'capitalize' },
  postDate: { fontSize: 11 },

  empty: { alignItems: 'center', paddingTop: 60 },
  emptyText: { fontSize: 15, marginTop: 8 },

  modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.5)', justifyContent: 'flex-end' },
  modalCard: { borderTopLeftRadius: 24, borderTopRightRadius: 24, padding: spacing.lg, paddingBottom: 40, maxHeight: '85%' },
  modalHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: spacing.md },
  modalTitle: { fontSize: 18, fontWeight: '700' },
  label: { fontSize: 13, fontWeight: '600', marginTop: spacing.sm, marginBottom: 4 },
  input: { borderWidth: 1, borderRadius: 12, paddingHorizontal: 14, paddingVertical: 12, fontSize: 15 },
  textArea: { minHeight: 120, maxHeight: 200 },
  imagePicker: { borderWidth: 1, borderStyle: 'dashed', borderRadius: 12, overflow: 'hidden', marginTop: 4 },
  previewImage: { width: '100%', height: 150, borderRadius: 12 },
  imagePickerEmpty: { alignItems: 'center', paddingVertical: 24 },
  imagePickerText: { fontSize: 13, marginTop: 4 },
  statusSelector: { flexDirection: 'row', gap: 8, marginTop: 4 },
  statusSelectorBtn: { flex: 1, paddingVertical: 10, borderRadius: 12, borderWidth: 1.5, alignItems: 'center' },
  statusSelectorText: { fontSize: 13, fontWeight: '700' },
  saveBtn: { marginTop: spacing.lg, paddingVertical: 14, borderRadius: 14, alignItems: 'center' },
  saveBtnText: { color: '#fff', fontWeight: '700', fontSize: 15 },
});

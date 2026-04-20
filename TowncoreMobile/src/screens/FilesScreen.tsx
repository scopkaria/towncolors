import React, { useEffect, useState, useCallback } from 'react';
import {
  View, Text, StyleSheet, ScrollView, RefreshControl,
  TouchableOpacity, ActivityIndicator, Alert, Modal,
  TextInput, StatusBar, Platform,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import * as DocumentPicker from 'expo-document-picker';
import { useTheme } from '../contexts/ThemeContext';
import { clientFilesApi } from '../api';
import { spacing, fontSize } from '../theme';
import { TAB_BAR_TOTAL_HEIGHT } from '../constants/layout';
import ScreenHeader from '../components/ScreenHeader';

function getFileIcon(mimeType?: string): { name: string; color: string } {
  if (!mimeType) return { name: 'document-outline', color: '#6366f1' };
  if (mimeType.startsWith('image/')) return { name: 'image-outline', color: '#ec4899' };
  if (mimeType.startsWith('video/')) return { name: 'videocam-outline', color: '#8b5cf6' };
  if (mimeType === 'application/pdf') return { name: 'document-text-outline', color: '#dc2626' };
  if (mimeType.includes('zip') || mimeType.includes('rar')) return { name: 'archive-outline', color: '#f59e0b' };
  return { name: 'document-outline', color: '#3b82f6' };
}

export default function FilesScreen({ navigation }: any) {
  const { colors, isDark } = useTheme();
  const insets = useSafeAreaInsets();

  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [data, setData] = useState<any>({ folders: [], files: [], breadcrumbs: [] });
  const [currentFolderId, setCurrentFolderId] = useState<number | null>(null);
  const [uploading, setUploading] = useState(false);

  // New folder modal
  const [folderModal, setFolderModal] = useState(false);
  const [newFolderName, setNewFolderName] = useState('');

  const loadFiles = useCallback(async (folderId?: number | null) => {
    try {
      const result = await clientFilesApi.list(folderId);
      setData(result);
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => { loadFiles(currentFolderId); }, [currentFolderId]);

  const onRefresh = () => { setRefreshing(true); loadFiles(currentFolderId); };

  const navigateToFolder = (folderId: number) => {
    setLoading(true);
    setCurrentFolderId(folderId);
  };

  const navigateBack = () => {
    const breadcrumbs = data.breadcrumbs || [];
    if (breadcrumbs.length > 1) {
      setLoading(true);
      setCurrentFolderId(breadcrumbs[breadcrumbs.length - 2].id);
    } else {
      setLoading(true);
      setCurrentFolderId(null);
    }
  };

  const handleUpload = async () => {
    try {
      const result = await DocumentPicker.getDocumentAsync({ type: '*/*', copyToCacheDirectory: true });
      if (result.canceled) return;

      const file = result.assets[0];
      setUploading(true);

      const formData = new FormData();
      formData.append('file', {
        uri: file.uri,
        name: file.name,
        type: file.mimeType || 'application/octet-stream',
      } as any);
      if (currentFolderId) formData.append('folder_id', String(currentFolderId));

      await clientFilesApi.upload(formData);
      loadFiles(currentFolderId);
    } catch (err: any) {
      Alert.alert('Upload Failed', err.message || 'Could not upload file');
    } finally {
      setUploading(false);
    }
  };

  const handleDeleteFile = (fileId: number, name: string) => {
    Alert.alert('Delete File', `Delete "${name}"?`, [
      { text: 'Cancel', style: 'cancel' },
      {
        text: 'Delete', style: 'destructive', onPress: async () => {
          try {
            await clientFilesApi.delete(fileId);
            loadFiles(currentFolderId);
          } catch (err: any) {
            Alert.alert('Error', err.message);
          }
        },
      },
    ]);
  };

  const handleDeleteFolder = (folderId: number, name: string) => {
    Alert.alert('Delete Folder', `Delete "${name}" and all its contents?`, [
      { text: 'Cancel', style: 'cancel' },
      {
        text: 'Delete', style: 'destructive', onPress: async () => {
          try {
            await clientFilesApi.deleteFolder(folderId);
            loadFiles(currentFolderId);
          } catch (err: any) {
            Alert.alert('Error', err.message);
          }
        },
      },
    ]);
  };

  const handleCreateFolder = async () => {
    if (!newFolderName.trim()) return;
    try {
      await clientFilesApi.createFolder(newFolderName.trim(), currentFolderId);
      setFolderModal(false);
      setNewFolderName('');
      loadFiles(currentFolderId);
    } catch (err: any) {
      Alert.alert('Error', err.message);
    }
  };

  if (loading && !refreshing) {
    return (
      <View style={[styles.center, { backgroundColor: colors.background }]}>
        <ActivityIndicator size="large" color={colors.primary} />
      </View>
    );
  }

  const breadcrumbs = data.breadcrumbs || [];
  const folders = data.folders || [];
  const files = data.files || [];

  return (
    <View style={[styles.container, { backgroundColor: colors.background }]}>
      <StatusBar barStyle="light-content" />

      <ScreenHeader
        title={breadcrumbs.length > 0 ? breadcrumbs[breadcrumbs.length - 1].name : 'My Files'}
        onBack={() => currentFolderId ? navigateBack() : navigation.goBack()}
        rightIcon="cloud-upload-outline"
        onRight={handleUpload}
        rightIcon2="folder-open-outline"
        onRight2={() => { setNewFolderName(''); setFolderModal(true); }}
      />

      {/* Breadcrumbs */}
      {breadcrumbs.length > 0 && (
        <ScrollView horizontal showsHorizontalScrollIndicator={false} style={styles.breadcrumbBar} contentContainerStyle={{ paddingHorizontal: spacing.md }}>
          <TouchableOpacity onPress={() => { setLoading(true); setCurrentFolderId(null); }}>
            <Text style={[styles.breadcrumbText, { color: colors.primary }]}>Home</Text>
          </TouchableOpacity>
          {breadcrumbs.map((b: any, i: number) => (
            <View key={b.id} style={styles.breadcrumbItem}>
              <Ionicons name="chevron-forward" size={14} color={colors.textLight} />
              <TouchableOpacity onPress={() => { if (i < breadcrumbs.length - 1) { setLoading(true); setCurrentFolderId(b.id); } }}>
                <Text style={[styles.breadcrumbText, { color: i === breadcrumbs.length - 1 ? colors.text : colors.primary }]}>
                  {b.name}
                </Text>
              </TouchableOpacity>
            </View>
          ))}
        </ScrollView>
      )}

      <ScrollView
        style={{ flex: 1 }}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor={colors.primary} />}
        showsVerticalScrollIndicator={false}
        contentContainerStyle={{ paddingHorizontal: spacing.md, paddingTop: spacing.sm }}
      >
        {/* Folders */}
        {folders.map((folder: any) => (
          <TouchableOpacity
            key={`folder-${folder.id}`}
            style={[styles.fileRow, { backgroundColor: colors.card }]}
            onPress={() => navigateToFolder(folder.id)}
            onLongPress={() => handleDeleteFolder(folder.id, folder.name)}
            activeOpacity={0.7}
          >
            <View style={[styles.fileIconBg, { backgroundColor: '#f59e0b15' }]}>
              <Ionicons name="folder" size={22} color="#f59e0b" />
            </View>
            <View style={styles.fileInfo}>
              <Text style={[styles.fileName, { color: colors.text }]} numberOfLines={1}>{folder.name}</Text>
              <Text style={[styles.fileMeta, { color: colors.textSecondary }]}>Folder</Text>
            </View>
            <Ionicons name="chevron-forward" size={18} color={colors.textLight} />
          </TouchableOpacity>
        ))}

        {/* Files */}
        {files.map((file: any) => {
          const icon = getFileIcon(file.mime_type);
          return (
            <TouchableOpacity
              key={`file-${file.id}`}
              style={[styles.fileRow, { backgroundColor: colors.card }]}
              onLongPress={() => handleDeleteFile(file.id, file.name)}
              activeOpacity={0.7}
            >
              <View style={[styles.fileIconBg, { backgroundColor: icon.color + '15' }]}>
                <Ionicons name={icon.name as any} size={22} color={icon.color} />
              </View>
              <View style={styles.fileInfo}>
                <Text style={[styles.fileName, { color: colors.text }]} numberOfLines={1}>{file.name}</Text>
                <Text style={[styles.fileMeta, { color: colors.textSecondary }]}>{file.size_formatted}</Text>
              </View>
              {file.is_image && (
                <Ionicons name="eye-outline" size={18} color={colors.textLight} />
              )}
            </TouchableOpacity>
          );
        })}

        {/* Empty state */}
        {folders.length === 0 && files.length === 0 && (
          <View style={styles.emptyState}>
            <Ionicons name="folder-open-outline" size={48} color={colors.textLight} />
            <Text style={[styles.emptyText, { color: colors.textLight }]}>This folder is empty</Text>
            <TouchableOpacity style={[styles.emptyBtn, { backgroundColor: colors.primary }]} onPress={handleUpload}>
              <Ionicons name="cloud-upload" size={18} color="#fff" />
              <Text style={styles.emptyBtnText}>Upload File</Text>
            </TouchableOpacity>
          </View>
        )}

        <View style={{ height: TAB_BAR_TOTAL_HEIGHT + insets.bottom + 20 }} />
      </ScrollView>

      {/* New Folder Modal */}
      <Modal visible={folderModal} transparent animationType="fade" onRequestClose={() => setFolderModal(false)}>
        <View style={styles.modalOverlay}>
          <View style={[styles.modalContent, { backgroundColor: colors.card }]}>
            <Text style={[styles.modalTitle, { color: colors.text }]}>New Folder</Text>
            <TextInput
              style={[styles.input, { backgroundColor: colors.inputBg, color: colors.text, borderColor: colors.border }]}
              value={newFolderName}
              onChangeText={setNewFolderName}
              placeholder="Folder name"
              placeholderTextColor={colors.textLight}
              autoFocus
            />
            <View style={styles.modalActions}>
              <TouchableOpacity style={[styles.modalBtn, { backgroundColor: colors.inputBg }]} onPress={() => setFolderModal(false)}>
                <Text style={[styles.modalBtnText, { color: colors.text }]}>Cancel</Text>
              </TouchableOpacity>
              <TouchableOpacity style={[styles.modalBtn, { backgroundColor: colors.primary }]} onPress={handleCreateFolder}>
                <Text style={[styles.modalBtnText, { color: '#fff' }]}>Create</Text>
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1 },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },

  header: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', paddingBottom: 14, paddingHorizontal: spacing.md },
  backBtn: { width: 40, height: 40, borderRadius: 20, backgroundColor: 'rgba(255,255,255,0.18)', justifyContent: 'center', alignItems: 'center' },
  headerTitle: { fontSize: 18, fontWeight: '800', color: '#fff', flex: 1, textAlign: 'center' },
  headerActions: { flexDirection: 'row', gap: 8 },
  headerActionBtn: { width: 36, height: 36, borderRadius: 18, backgroundColor: 'rgba(255,255,255,0.18)', justifyContent: 'center', alignItems: 'center' },

  breadcrumbBar: { paddingVertical: 10, flexGrow: 0 },
  breadcrumbItem: { flexDirection: 'row', alignItems: 'center', gap: 4 },
  breadcrumbText: { fontSize: 13, fontWeight: '600' },

  fileRow: {
    flexDirection: 'row', alignItems: 'center', borderRadius: 14, padding: 14, marginBottom: 8,
    shadowColor: '#000', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.03, shadowRadius: 3, elevation: 1,
  },
  fileIconBg: { width: 42, height: 42, borderRadius: 12, justifyContent: 'center', alignItems: 'center' },
  fileInfo: { flex: 1, marginLeft: 12 },
  fileName: { fontSize: 14, fontWeight: '600' },
  fileMeta: { fontSize: 11, marginTop: 2 },

  emptyState: { alignItems: 'center', paddingVertical: 60 },
  emptyText: { fontSize: 14, marginTop: 12, marginBottom: 20 },
  emptyBtn: { flexDirection: 'row', alignItems: 'center', borderRadius: 12, paddingHorizontal: 20, paddingVertical: 12, gap: 6 },
  emptyBtnText: { fontSize: 14, fontWeight: '700', color: '#fff' },

  modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.5)', justifyContent: 'center', paddingHorizontal: 30 },
  modalContent: { borderRadius: 20, padding: spacing.lg },
  modalTitle: { fontSize: 18, fontWeight: '700', marginBottom: spacing.md },
  input: { borderWidth: 1, borderRadius: 12, paddingHorizontal: 14, paddingVertical: 12, fontSize: 14 },
  modalActions: { flexDirection: 'row', gap: 10, marginTop: spacing.md },
  modalBtn: { flex: 1, borderRadius: 12, paddingVertical: 12, alignItems: 'center' },
  modalBtnText: { fontSize: 14, fontWeight: '700' },
});

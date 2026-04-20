import React, { useEffect, useState, useCallback } from 'react';
import {
  View, Text, StyleSheet, FlatList, Image,
  TouchableOpacity, RefreshControl, ActivityIndicator, Alert,
  Dimensions,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import * as ImagePicker from 'expo-image-picker';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { useAuth } from '../contexts/AuthContext';
import { useTheme } from '../contexts/ThemeContext';
import { portfolioApi } from '../api';
import { MEDIA_BASE_URL } from '../config';
import { spacing, fontSize } from '../theme';
import { TAB_BAR_TOTAL_HEIGHT } from '../constants/layout';
import ScreenHeader from '../components/ScreenHeader';

const SCREEN_WIDTH = Dimensions.get('window').width;
const ITEM_WIDTH = (SCREEN_WIDTH - spacing.md * 3) / 2;

export default function PortfolioScreen({ navigation }: any) {
  const { user } = useAuth();
  const { colors } = useTheme();
  const insets = useSafeAreaInsets();
  const isFreelancer = user?.role === 'freelancer';
  const [items, setItems] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const loadPortfolio = useCallback(async () => {
    try {
      const result = isFreelancer
        ? await portfolioApi.myPortfolio()
        : await portfolioApi.list();
      setItems(result.data);
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, [isFreelancer]);

  useEffect(() => { loadPortfolio(); }, [loadPortfolio]);

  const onRefresh = () => { setRefreshing(true); loadPortfolio(); };

  async function handleAdd() {
    const result = await ImagePicker.launchImageLibraryAsync({
      mediaTypes: ImagePicker.MediaTypeOptions.Images,
      quality: 0.8,
    });

    if (result.canceled) return;

    const asset = result.assets[0];
    const formData = new FormData();
    formData.append('title', 'New Portfolio Item');
    formData.append('image', {
      uri: asset.uri,
      type: 'image/jpeg',
      name: 'portfolio.jpg',
    } as any);

    try {
      await portfolioApi.create(formData);
      loadPortfolio();
      Alert.alert('Success', 'Portfolio item submitted for review');
    } catch (err: any) {
      Alert.alert('Error', err.message);
    }
  }

  async function handleDelete(id: number) {
    Alert.alert('Delete', 'Are you sure?', [
      { text: 'Cancel' },
      {
        text: 'Delete',
        style: 'destructive',
        onPress: async () => {
          try {
            await portfolioApi.delete(id);
            setItems(prev => prev.filter(i => i.id !== id));
          } catch (err: any) {
            Alert.alert('Error', err.message);
          }
        },
      },
    ]);
  }

  function getImageUrl(path: string) {
    return `${MEDIA_BASE_URL}/storage/${path}`;
  }

  const renderItem = ({ item }: any) => (
    <View style={[styles.card, { backgroundColor: colors.card }]}>
      <Image
        source={{ uri: getImageUrl(item.image_path) }}
        style={[styles.image, { backgroundColor: colors.inputBg }]}
        resizeMode="cover"
      />
      <View style={styles.cardContent}>
        <Text style={[styles.title, { color: colors.text }]} numberOfLines={1}>{item.title}</Text>
        {item.status && (
          <View style={[styles.badge, {
            backgroundColor: item.status === 'approved' ? colors.success + '20' :
              item.status === 'rejected' ? colors.danger + '20' : colors.warning + '20'
          }]}>
            <Text style={[styles.badgeText, {
              color: item.status === 'approved' ? colors.success :
                item.status === 'rejected' ? colors.danger : colors.warning
            }]}>
              {item.status}
            </Text>
          </View>
        )}
        {item.freelancer && (
          <Text style={[styles.freelancerName, { color: colors.textSecondary }]}>{item.freelancer.name}</Text>
        )}
      </View>
      {isFreelancer && (
        <TouchableOpacity style={styles.deleteBtn} onPress={() => handleDelete(item.id)}>
          <Ionicons name="trash-outline" size={16} color={colors.danger} />
        </TouchableOpacity>
      )}
    </View>
  );

  if (loading) {
    return <View style={styles.center}><ActivityIndicator size="large" color={colors.primary} /></View>;
  }

  return (
    <View style={[styles.container, { backgroundColor: colors.background }]}>
      <ScreenHeader title="Portfolio" onBack={() => navigation.goBack()} />
      <FlatList
        data={items}
        keyExtractor={item => item.id.toString()}
        renderItem={renderItem}
        numColumns={2}
        columnWrapperStyle={styles.row}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
        contentContainerStyle={[styles.list, { paddingBottom: TAB_BAR_TOTAL_HEIGHT + insets.bottom + 20 }]}
        ListEmptyComponent={
          <View style={styles.center}>
            <Ionicons name="images-outline" size={48} color={colors.textLight} />
            <Text style={[styles.emptyText, { color: colors.textLight }]}>No portfolio items</Text>
          </View>
        }
      />

      {isFreelancer && (
        <TouchableOpacity style={[styles.fab, { backgroundColor: colors.primary, bottom: TAB_BAR_TOTAL_HEIGHT + insets.bottom + 16 }]} onPress={handleAdd}>
          <Ionicons name="add" size={28} color={colors.white} />
        </TouchableOpacity>
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1 },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center', paddingTop: 80 },
  list: { padding: spacing.md, paddingBottom: 100 },
  row: { justifyContent: 'space-between' },
  card: { width: ITEM_WIDTH, borderRadius: 16, marginBottom: spacing.md, overflow: 'hidden', shadowColor: '#000', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.08, shadowRadius: 4, elevation: 2 },
  image: { width: '100%', height: ITEM_WIDTH },
  cardContent: { padding: spacing.sm },
  title: { fontSize: fontSize.sm, fontWeight: '700' },
  badge: { alignSelf: 'flex-start', paddingHorizontal: 8, paddingVertical: 2, borderRadius: 6, marginTop: 4 },
  badgeText: { fontSize: 10, fontWeight: '700', textTransform: 'capitalize' },
  freelancerName: { fontSize: fontSize.xs, marginTop: 2 },
  deleteBtn: { position: 'absolute', top: 8, right: 8, backgroundColor: 'rgba(255,255,255,0.9)', borderRadius: 16, padding: 6 },
  fab: { position: 'absolute', right: 24, width: 56, height: 56, borderRadius: 28, justifyContent: 'center', alignItems: 'center', elevation: 5 },
  emptyText: { fontSize: fontSize.md, marginTop: spacing.sm },
});

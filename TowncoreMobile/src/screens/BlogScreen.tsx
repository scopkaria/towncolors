import React, { useEffect, useState, useCallback } from 'react';
import {
  View, Text, StyleSheet, FlatList, Image,
  TouchableOpacity, RefreshControl, ActivityIndicator, Dimensions,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { blogApi } from '../api';
import { MEDIA_BASE_URL } from '../config';
import { useTheme } from '../contexts/ThemeContext';
import { spacing, fontSize } from '../theme';
import ScreenHeader from '../components/ScreenHeader';

export default function BlogScreen({ navigation }: any) {
  const { colors } = useTheme();
  const [posts, setPosts] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [page, setPage] = useState(1);
  const [hasMore, setHasMore] = useState(true);

  const loadPosts = useCallback(async (p = 1) => {
    try {
      const result = await blogApi.list(p);
      if (p === 1) setPosts(result.data);
      else setPosts(prev => [...prev, ...result.data]);
      setHasMore(result.current_page < result.last_page);
      setPage(result.current_page);
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => { loadPosts(); }, [loadPosts]);

  const onRefresh = () => { setRefreshing(true); loadPosts(1); };
  const loadMore = () => { if (hasMore) loadPosts(page + 1); };

  function getImageUrl(path: string) {
    if (!path) return null;
    return `${MEDIA_BASE_URL}/storage/${path}`;
  }

  const renderPost = ({ item, index }: any) => {
    const isFeatured = index === 0;
    const imageUrl = getImageUrl(item.featured_image);

    return (
      <TouchableOpacity
        style={[styles.card, { backgroundColor: colors.card }, isFeatured && styles.featuredCard]}
        onPress={() => navigation.navigate('BlogPost', { slug: item.slug })}
      >
        {imageUrl && (
          <Image
            source={{ uri: imageUrl }}
            style={[styles.image, { backgroundColor: colors.inputBg }, isFeatured && styles.featuredImage]}
            resizeMode="cover"
          />
        )}
        <View style={styles.content}>
          <Text style={[styles.title, { color: colors.text }, isFeatured && styles.featuredTitle]} numberOfLines={2}>
            {item.title}
          </Text>
          {item.meta_description && (
            <Text style={[styles.excerpt, { color: colors.textSecondary }]} numberOfLines={2}>{item.meta_description}</Text>
          )}
          <Text style={[styles.date, { color: colors.textLight }]}>
            {new Date(item.published_at).toLocaleDateString('en-US', {
              month: 'short', day: 'numeric', year: 'numeric',
            })}
          </Text>
        </View>
      </TouchableOpacity>
    );
  };

  if (loading) {
    return <View style={styles.center}><ActivityIndicator size="large" color={colors.primary} /></View>;
  }

  return (
    <View style={[styles.container, { backgroundColor: colors.background }]}>
    <ScreenHeader title="Blog" onBack={() => navigation.goBack()} />
    <FlatList
      style={{ flex: 1 }}
      data={posts}
      keyExtractor={item => item.id.toString()}
      renderItem={renderPost}
      refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
      onEndReached={loadMore}
      onEndReachedThreshold={0.3}
      contentContainerStyle={styles.list}
      ListEmptyComponent={
        <View style={styles.center}>
          <Ionicons name="newspaper-outline" size={48} color={colors.textLight} />
          <Text style={[styles.emptyText, { color: colors.textLight }]}>No blog posts yet</Text>
        </View>
      }
    />
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1 },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center', paddingTop: 80 },
  list: { padding: spacing.md, paddingBottom: 20 },
  card: { borderRadius: 16, marginBottom: spacing.md, overflow: 'hidden', shadowColor: '#000', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.06, shadowRadius: 4, elevation: 2 },
  featuredCard: {},
  image: { width: '100%', height: 160 },
  featuredImage: { height: 220 },
  content: { padding: spacing.md },
  title: { fontSize: fontSize.md, fontWeight: '700' },
  featuredTitle: { fontSize: fontSize.lg },
  excerpt: { fontSize: fontSize.sm, marginTop: spacing.xs, lineHeight: 20 },
  date: { fontSize: fontSize.xs, marginTop: spacing.sm },
  emptyText: { fontSize: fontSize.md, marginTop: spacing.sm },
});

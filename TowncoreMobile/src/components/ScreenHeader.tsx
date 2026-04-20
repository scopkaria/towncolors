import React from 'react';
import { View, Text, TouchableOpacity, StyleSheet, Platform } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { useTheme } from '../contexts/ThemeContext';
import { spacing, fontSize } from '../theme';

const HEADER_HEIGHT = 56;

type ScreenHeaderProps = {
  title: string;
  /** Show back arrow (navigates back). Pass navigation.goBack or a custom handler. */
  onBack?: () => void;
  /** Right-side action icon name (Ionicons). */
  rightIcon?: string;
  /** Right-side action handler. */
  onRight?: () => void;
  /** Badge count to show on right icon. */
  rightBadge?: number;
  /** Second right icon (e.g. notifications). */
  rightIcon2?: string;
  onRight2?: () => void;
  /** Use transparent background instead of primary color. */
  transparent?: boolean;
  /** Override left icon (defaults to 'arrow-back' when onBack is set). */
  leftIcon?: string;
};

export default function ScreenHeader({
  title,
  onBack,
  rightIcon,
  onRight,
  rightBadge,
  rightIcon2,
  onRight2,
  transparent = false,
  leftIcon,
}: ScreenHeaderProps) {
  const { colors } = useTheme();
  const insets = useSafeAreaInsets();

  const bg = transparent ? 'transparent' : colors.primary;
  const tint = transparent ? colors.text : '#fff';
  const subtleTint = transparent ? colors.textLight : 'rgba(255,255,255,0.6)';

  return (
    <View style={[styles.container, { backgroundColor: bg, paddingTop: insets.top }]}>
      <View style={styles.row}>
        {/* Left */}
        <View style={styles.side}>
          {onBack ? (
            <TouchableOpacity
              style={styles.iconBtn}
              onPress={onBack}
              activeOpacity={0.7}
              hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}
            >
              <Ionicons name={(leftIcon as any) || 'arrow-back'} size={22} color={tint} />
            </TouchableOpacity>
          ) : (
            <View style={styles.iconBtn} />
          )}
        </View>

        {/* Center */}
        <View style={styles.center}>
          <Text style={[styles.title, { color: tint }]} numberOfLines={1}>
            {title}
          </Text>
        </View>

        {/* Right */}
        <View style={[styles.side, styles.rightSide]}>
          {rightIcon2 && onRight2 && (
            <TouchableOpacity
              style={styles.iconBtn}
              onPress={onRight2}
              activeOpacity={0.7}
              hitSlop={{ top: 10, bottom: 10, left: 6, right: 6 }}
            >
              <Ionicons name={rightIcon2 as any} size={22} color={tint} />
            </TouchableOpacity>
          )}
          {rightIcon && onRight ? (
            <TouchableOpacity
              style={styles.iconBtn}
              onPress={onRight}
              activeOpacity={0.7}
              hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}
            >
              <Ionicons name={rightIcon as any} size={22} color={tint} />
              {rightBadge !== undefined && rightBadge > 0 && (
                <View style={styles.badge}>
                  <Text style={styles.badgeText}>
                    {rightBadge > 99 ? '99+' : rightBadge}
                  </Text>
                </View>
              )}
            </TouchableOpacity>
          ) : (
            !rightIcon2 && <View style={styles.iconBtn} />
          )}
        </View>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    zIndex: 10,
  },
  row: {
    height: HEADER_HEIGHT,
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: spacing.sm,
  },
  side: {
    width: 48,
    alignItems: 'center',
    justifyContent: 'center',
  },
  rightSide: {
    flexDirection: 'row',
    justifyContent: 'flex-end',
    width: 'auto',
    minWidth: 48,
  },
  center: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    paddingHorizontal: spacing.xs,
  },
  title: {
    fontSize: fontSize.lg,
    fontWeight: '800',
    letterSpacing: 0.3,
  },
  iconBtn: {
    width: 40,
    height: 40,
    borderRadius: 20,
    justifyContent: 'center',
    alignItems: 'center',
  },
  badge: {
    position: 'absolute',
    top: 4,
    right: 2,
    backgroundColor: '#ef4444',
    minWidth: 18,
    height: 18,
    borderRadius: 9,
    justifyContent: 'center',
    alignItems: 'center',
    paddingHorizontal: 4,
    borderWidth: 1.5,
    borderColor: '#fff',
  },
  badgeText: {
    color: '#fff',
    fontSize: 10,
    fontWeight: '800',
    lineHeight: 12,
  },
});

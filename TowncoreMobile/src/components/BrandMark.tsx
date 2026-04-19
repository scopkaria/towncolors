import React from 'react';
import { Image, Text, View, StyleSheet } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useBranding } from '../contexts/BrandingContext';
import { useTheme } from '../contexts/ThemeContext';

type BrandMarkProps = {
  size?: number;
  showName?: boolean;
  nameColor?: string;
};

export default function BrandMark({ size = 48, showName = true, nameColor }: BrandMarkProps) {
  const { colors } = useTheme();
  const branding = useBranding();
  const iconSize = Math.max(18, Math.round(size * 0.42));

  return (
    <View style={styles.row}>
      <View
        style={[
          styles.logoWrap,
          {
            width: size,
            height: size,
            borderRadius: Math.round(size / 2),
            backgroundColor: `${colors.primary}22`,
            borderColor: `${colors.primary}55`,
          },
        ]}
      >
        {branding.appIconUrl ? (
          <Image source={{ uri: branding.appIconUrl }} style={styles.logoImage} resizeMode="cover" />
        ) : branding.logoUrl ? (
          <Image source={{ uri: branding.logoUrl }} style={styles.logoImage} resizeMode="contain" />
        ) : (
          <Ionicons name="business" size={iconSize} color={colors.primary} />
        )}
      </View>
      {showName && (
        <Text style={[styles.appName, { color: nameColor || colors.text }]}>{branding.appName}</Text>
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  row: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 12,
  },
  logoWrap: {
    justifyContent: 'center',
    alignItems: 'center',
    borderWidth: 1,
    overflow: 'hidden',
  },
  logoImage: {
    width: '100%',
    height: '100%',
  },
  appName: {
    fontSize: 26,
    fontWeight: '800',
    letterSpacing: 0.3,
  },
});

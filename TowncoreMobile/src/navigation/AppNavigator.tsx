import React from 'react';
import { ActivityIndicator, View, Platform } from 'react-native';
import { NavigationContainer, createNavigationContainerRef } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { Ionicons } from '@expo/vector-icons';
import { useSafeAreaInsets } from 'react-native-safe-area-context';

import { useAuth } from '../contexts/AuthContext';
import { useTheme } from '../contexts/ThemeContext';

// Auth Screens
import LoginScreen from '../screens/LoginScreen';
import RegisterScreen from '../screens/RegisterScreen';

// Main Screens
import DashboardScreen from '../screens/DashboardScreen';
import ProjectsScreen from '../screens/ProjectsScreen';
import ProjectDetailScreen from '../screens/ProjectDetailScreen';
import CreateProjectScreen from '../screens/CreateProjectScreen';
import ConversationsScreen from '../screens/ConversationsScreen';
import ChatScreen from '../screens/ChatScreen';
import InvoicesScreen from '../screens/InvoicesScreen';
import InvoiceDetailScreen from '../screens/InvoiceDetailScreen';
import PortfolioScreen from '../screens/PortfolioScreen';
import BlogScreen from '../screens/BlogScreen';
import BlogPostScreen from '../screens/BlogPostScreen';
import NotificationsScreen from '../screens/NotificationsScreen';
import ProfileScreen from '../screens/ProfileScreen';
import LiveChatScreen from '../screens/LiveChatScreen';
import NotificationSettingsScreen from '../screens/NotificationSettingsScreen';

export const navigationRef = createNavigationContainerRef<any>();

/** Height of the floating tab bar + gap — use for bottom content padding in tab screens */
export const TAB_BAR_TOTAL_HEIGHT = 80;

const Stack = createNativeStackNavigator();
const Tab = createBottomTabNavigator();

// Auth Stack
function AuthStack() {
  return (
    <Stack.Navigator screenOptions={{ headerShown: false }}>
      <Stack.Screen name="Login" component={LoginScreen} />
      <Stack.Screen name="Register" component={RegisterScreen} />
    </Stack.Navigator>
  );
}

// Projects Stack
function ProjectsStack() {
  const { colors } = useTheme();
  const screenOptions = {
    headerStyle: { backgroundColor: colors.card },
    headerTintColor: colors.text,
    headerTitleStyle: { fontWeight: '700' as const },
    headerShadowVisible: false,
  };
  return (
    <Stack.Navigator screenOptions={screenOptions}>
      <Stack.Screen name="ProjectsList" component={ProjectsScreen} options={{ headerShown: false }} />
      <Stack.Screen name="ProjectDetail" component={ProjectDetailScreen} options={{ title: 'Project Details' }} />
      <Stack.Screen name="CreateProject" component={CreateProjectScreen} options={{ title: 'New Project' }} />
    </Stack.Navigator>
  );
}

// Messages Stack
function MessagesStack() {
  const { colors } = useTheme();
  const screenOptions = {
    headerStyle: { backgroundColor: colors.card },
    headerTintColor: colors.text,
    headerTitleStyle: { fontWeight: '700' as const },
    headerShadowVisible: false,
  };
  return (
    <Stack.Navigator screenOptions={screenOptions}>
      <Stack.Screen name="Conversations" component={ConversationsScreen} options={{ title: 'Messages' }} />
      <Stack.Screen
        name="Chat"
        component={ChatScreen}
        options={({ route }: any) => ({ title: route.params?.title || 'Chat' })}
      />
    </Stack.Navigator>
  );
}

// Main Tab Navigator (3 tabs — no More tab, drawer handles extras)
function MainTabs() {
  const { colors } = useTheme();
  const insets = useSafeAreaInsets();
  const bottomInset = Math.max(insets.bottom, 8);

  return (
    <Tab.Navigator
      screenOptions={({ route }) => ({
        headerShown: false,
        tabBarActiveTintColor: colors.primary,
        tabBarInactiveTintColor: colors.textLight,
        tabBarStyle: {
          backgroundColor: colors.card,
          borderTopColor: 'transparent',
          borderTopWidth: 0,
          position: 'absolute',
          left: 12,
          right: 12,
          bottom: bottomInset,
          borderRadius: 18,
          paddingTop: 6,
          paddingBottom: 6,
          height: 60,
          shadowColor: '#000',
          shadowOffset: { width: 0, height: 6 },
          shadowOpacity: 0.12,
          shadowRadius: 14,
          elevation: 10,
        },
        tabBarLabelStyle: { fontSize: 11, fontWeight: '700' },
        tabBarItemStyle: { borderRadius: 12, marginHorizontal: 2 },
        tabBarIcon: ({ focused, color }) => {
          let iconName: any;
          switch (route.name) {
            case 'Dashboard': iconName = focused ? 'grid' : 'grid-outline'; break;
            case 'Projects': iconName = focused ? 'folder' : 'folder-outline'; break;
            case 'Messages': iconName = focused ? 'chatbubbles' : 'chatbubbles-outline'; break;
          }
          return <Ionicons name={iconName} size={22} color={color} />;
        },
      })}
    >
      <Tab.Screen name="Dashboard" component={DashboardScreen} />
      <Tab.Screen name="Projects" component={ProjectsStack} />
      <Tab.Screen name="Messages" component={MessagesStack} />
    </Tab.Navigator>
  );
}

// App Main — tabs + overlay screens accessible from drawer
function AppMain() {
  const { colors } = useTheme();
  const screenOptions = {
    headerStyle: { backgroundColor: colors.card },
    headerTintColor: colors.text,
    headerTitleStyle: { fontWeight: '700' as const },
    headerShadowVisible: false,
  };
  return (
    <Stack.Navigator>
      <Stack.Screen name="Tabs" component={MainTabs} options={{ headerShown: false }} />
      <Stack.Screen name="Invoices" component={InvoicesScreen} options={{ ...screenOptions, title: 'Invoices' }} />
      <Stack.Screen name="InvoiceDetail" component={InvoiceDetailScreen} options={{ ...screenOptions, title: 'Invoice Details' }} />
      <Stack.Screen name="Portfolio" component={PortfolioScreen} options={{ ...screenOptions, title: 'Portfolio' }} />
      <Stack.Screen name="Blog" component={BlogScreen} options={{ ...screenOptions, title: 'Blog' }} />
      <Stack.Screen name="BlogPost" component={BlogPostScreen} options={{ ...screenOptions, title: 'Article' }} />
      <Stack.Screen name="Notifications" component={NotificationsScreen} options={{ ...screenOptions, title: 'Notifications' }} />
      <Stack.Screen name="Profile" component={ProfileScreen} options={{ ...screenOptions, title: 'Profile' }} />
      <Stack.Screen name="LiveChat" component={LiveChatScreen} options={{ ...screenOptions, title: 'Live Chat' }} />
      <Stack.Screen name="NotificationSettings" component={NotificationSettingsScreen} options={{ ...screenOptions, title: 'Notification Settings' }} />
    </Stack.Navigator>
  );
}

// Root Navigator
export default function AppNavigator() {
  const { user, isLoading } = useAuth();
  const { colors } = useTheme();

  if (isLoading) {
    return (
      <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center', backgroundColor: colors.background }}>
        <ActivityIndicator size="large" color={colors.primary} />
      </View>
    );
  }

  return (
    <NavigationContainer ref={navigationRef}>
      {user ? <AppMain /> : <AuthStack />}
    </NavigationContainer>
  );
}

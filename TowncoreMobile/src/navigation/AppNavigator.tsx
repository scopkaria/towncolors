import React from 'react';
import { ActivityIndicator, View } from 'react-native';
import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { Ionicons } from '@expo/vector-icons';

import { useAuth } from '../contexts/AuthContext';
import { colors } from '../theme';

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

const Stack = createNativeStackNavigator();
const Tab = createBottomTabNavigator();

const screenOptions = {
  headerStyle: { backgroundColor: colors.white },
  headerTintColor: colors.text,
  headerTitleStyle: { fontWeight: '700' as const },
  headerShadowVisible: false,
};

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
  return (
    <Stack.Navigator screenOptions={screenOptions}>
      <Stack.Screen name="ProjectsList" component={ProjectsScreen} options={{ title: 'Projects' }} />
      <Stack.Screen name="ProjectDetail" component={ProjectDetailScreen} options={{ title: 'Project Details' }} />
      <Stack.Screen name="CreateProject" component={CreateProjectScreen} options={{ title: 'New Project' }} />
    </Stack.Navigator>
  );
}

// Messages Stack
function MessagesStack() {
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

// More Stack (Invoices, Portfolio, Blog, Notifications, Profile)
function MoreStack() {
  const { user } = useAuth();

  return (
    <Stack.Navigator screenOptions={screenOptions}>
      <Stack.Screen name="MoreMenu" component={MoreMenuScreen} options={{ title: 'More' }} />
      <Stack.Screen name="Invoices" component={InvoicesScreen} options={{ title: 'Invoices' }} />
      <Stack.Screen name="InvoiceDetail" component={InvoiceDetailScreen} options={{ title: 'Invoice Details' }} />
      <Stack.Screen name="Portfolio" component={PortfolioScreen} options={{ title: 'Portfolio' }} />
      <Stack.Screen name="Blog" component={BlogScreen} options={{ title: 'Blog' }} />
      <Stack.Screen name="BlogPost" component={BlogPostScreen} options={{ title: 'Article' }} />
      <Stack.Screen name="Notifications" component={NotificationsScreen} options={{ title: 'Notifications' }} />
      <Stack.Screen name="Profile" component={ProfileScreen} options={{ title: 'Profile' }} />
    </Stack.Navigator>
  );
}

// More Menu Screen
import { TouchableOpacity, Text, StyleSheet, ScrollView } from 'react-native';
import { spacing, fontSize } from '../theme';

function MoreMenuScreen({ navigation }: any) {
  const { user } = useAuth();

  const menuItems = [
    { icon: 'receipt-outline', label: 'Invoices', screen: 'Invoices', roles: ['admin', 'client', 'freelancer'] },
    { icon: 'images-outline', label: 'Portfolio', screen: 'Portfolio', roles: ['admin', 'client', 'freelancer'] },
    { icon: 'newspaper-outline', label: 'Blog', screen: 'Blog', roles: ['admin', 'client', 'freelancer'] },
    { icon: 'notifications-outline', label: 'Notifications', screen: 'Notifications', roles: ['admin', 'client', 'freelancer'] },
    { icon: 'person-outline', label: 'Profile & Settings', screen: 'Profile', roles: ['admin', 'client', 'freelancer'] },
  ];

  return (
    <ScrollView style={menuStyles.container}>
      {menuItems
        .filter(item => item.roles.includes(user?.role || ''))
        .map((item) => (
          <TouchableOpacity
            key={item.screen}
            style={menuStyles.menuItem}
            onPress={() => navigation.navigate(item.screen)}
          >
            <View style={menuStyles.menuIcon}>
              <Ionicons name={item.icon as any} size={22} color={colors.primary} />
            </View>
            <Text style={menuStyles.menuLabel}>{item.label}</Text>
            <Ionicons name="chevron-forward" size={18} color={colors.textLight} />
          </TouchableOpacity>
        ))}
    </ScrollView>
  );
}

const menuStyles = StyleSheet.create({
  container: { flex: 1, backgroundColor: colors.background, paddingTop: spacing.sm },
  menuItem: {
    flexDirection: 'row', alignItems: 'center', backgroundColor: colors.card,
    padding: spacing.md, borderBottomWidth: 1, borderBottomColor: colors.border,
  },
  menuIcon: {
    width: 40, height: 40, borderRadius: 12, backgroundColor: colors.primary + '12',
    justifyContent: 'center', alignItems: 'center', marginRight: spacing.md,
  },
  menuLabel: { flex: 1, fontSize: fontSize.md, fontWeight: '600', color: colors.text },
});

// Main Tab Navigator
function MainTabs() {
  return (
    <Tab.Navigator
      screenOptions={({ route }) => ({
        headerShown: false,
        tabBarActiveTintColor: colors.primary,
        tabBarInactiveTintColor: colors.textLight,
        tabBarStyle: {
          backgroundColor: colors.white,
          borderTopColor: colors.border,
          paddingBottom: 4,
          height: 56,
        },
        tabBarLabelStyle: { fontSize: 11, fontWeight: '600' },
        tabBarIcon: ({ focused, color, size }) => {
          let iconName: any;
          switch (route.name) {
            case 'Dashboard': iconName = focused ? 'grid' : 'grid-outline'; break;
            case 'Projects': iconName = focused ? 'folder' : 'folder-outline'; break;
            case 'Messages': iconName = focused ? 'chatbubbles' : 'chatbubbles-outline'; break;
            case 'More': iconName = focused ? 'menu' : 'menu-outline'; break;
          }
          return <Ionicons name={iconName} size={22} color={color} />;
        },
      })}
    >
      <Tab.Screen name="Dashboard" component={DashboardScreen} />
      <Tab.Screen name="Projects" component={ProjectsStack} />
      <Tab.Screen name="Messages" component={MessagesStack} />
      <Tab.Screen name="More" component={MoreStack} />
    </Tab.Navigator>
  );
}

// Root Navigator
export default function AppNavigator() {
  const { user, isLoading } = useAuth();

  if (isLoading) {
    return (
      <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center', backgroundColor: colors.background }}>
        <ActivityIndicator size="large" color={colors.primary} />
      </View>
    );
  }

  return (
    <NavigationContainer>
      {user ? <MainTabs /> : <AuthStack />}
    </NavigationContainer>
  );
}

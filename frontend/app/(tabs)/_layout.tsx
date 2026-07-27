import React from "react";
import { View, Text, StyleSheet, TouchableOpacity } from "react-native";
import { Tabs, useRouter, useSegments } from "expo-router";
import { Ionicons, MaterialCommunityIcons } from "@expo/vector-icons";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import { colors } from "@/src/theme";

const TABS: {
  name: string;
  label: string;
  icon: any;
  family: "ion" | "mc";
}[] = [
  { name: "dashboard", label: "Dashboard", icon: "home", family: "ion" },
  { name: "dialer", label: "Dialer", icon: "keypad", family: "ion" },
  { name: "contacts", label: "Contacts", icon: "person-add", family: "ion" },
  { name: "call-logs", label: "Call Logs", icon: "time-outline", family: "ion" },
  { name: "more", label: "More", icon: "ellipsis-horizontal", family: "ion" },
];

export default function TabsLayout() {
  return (
    <Tabs
      screenOptions={{ headerShown: false }}
      tabBar={(props) => <CustomTabBar {...props} />}
    >
      {TABS.map((t) => (
        <Tabs.Screen key={t.name} name={t.name} />
      ))}
    </Tabs>
  );
}

function CustomTabBar({ state, navigation }: any) {
  const insets = useSafeAreaInsets();
  return (
    <View
      style={[styles.bar, { paddingBottom: Math.max(insets.bottom, 8) }]}
      testID="bottom-tabbar"
    >
      {state.routes.map((route: any, index: number) => {
        const focused = state.index === index;
        const config = TABS.find((t) => t.name === route.name);
        if (!config) return null;
        const color = focused ? colors.primary : colors.textMuted;
        return (
          <TouchableOpacity
            key={route.key}
            onPress={() => navigation.navigate(route.name)}
            style={styles.tab}
            testID={`tab-${config.name}`}
          >
            {config.family === "mc" ? (
              <MaterialCommunityIcons name={config.icon as any} size={22} color={color} />
            ) : (
              <Ionicons name={config.icon as any} size={22} color={color} />
            )}
            <Text style={[styles.tabLabel, { color }]}>{config.label}</Text>
          </TouchableOpacity>
        );
      })}
    </View>
  );
}

const styles = StyleSheet.create({
  bar: {
    flexDirection: "row",
    backgroundColor: colors.bgAlt,
    borderTopWidth: 1,
    borderTopColor: colors.border,
    paddingTop: 8,
  },
  tab: {
    flex: 1,
    alignItems: "center",
    justifyContent: "center",
    gap: 4,
    paddingVertical: 4,
  },
  tabLabel: { fontSize: 11, fontWeight: "600" },
});

import React from "react";
import { View, Text, TouchableOpacity, StyleSheet } from "react-native";
import { Ionicons } from "@expo/vector-icons";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import { useRouter } from "expo-router";
import { colors, spacing } from "@/src/theme";

type Props = {
  title: string;
  showMenu?: boolean;
  onMenu?: () => void;
  showBack?: boolean;
  right?: React.ReactNode;
  showBell?: boolean;
  showSip?: boolean;
  unread?: number;
};

export default function Header({
  title,
  showMenu = true,
  onMenu,
  showBack = false,
  right,
  showBell = true,
  showSip = true,
  unread = 3,
}: Props) {
  const insets = useSafeAreaInsets();
  const router = useRouter();
  return (
    <View style={[styles.header, { paddingTop: insets.top + 8 }]} testID="app-header">
      <View style={styles.row}>
        {showBack ? (
          <TouchableOpacity
            onPress={() => router.back()}
            testID="header-back-button"
            style={styles.iconBtn}
          >
            <Ionicons name="chevron-back" size={26} color={colors.text} />
          </TouchableOpacity>
        ) : showMenu ? (
          <TouchableOpacity onPress={onMenu} testID="header-menu-button" style={styles.iconBtn}>
            <Ionicons name="menu" size={26} color={colors.text} />
          </TouchableOpacity>
        ) : (
          <View style={styles.iconBtn} />
        )}
        <Text style={styles.title} testID="header-title" numberOfLines={1}>
          {title}
        </Text>
      </View>
      <View style={styles.rightRow}>
        {right}
        {showBell && (
          <TouchableOpacity
            style={styles.bell}
            onPress={() => router.push("/notifications")}
            testID="header-bell-button"
          >
            <Ionicons name="notifications-outline" size={22} color={colors.text} />
            {unread > 0 && (
              <View style={styles.badge}>
                <Text style={styles.badgeText}>{unread}</Text>
              </View>
            )}
          </TouchableOpacity>
        )}
        {showSip && (
          <View style={styles.sipPill} testID="header-sip-pill">
            <View style={styles.sipDot} />
            <Text style={styles.sipText}>SIP Registered</Text>
          </View>
        )}
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  header: {
    paddingHorizontal: spacing.lg,
    paddingBottom: spacing.md,
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    backgroundColor: colors.bg,
    gap: spacing.sm,
  },
  row: { flexDirection: "row", alignItems: "center", flex: 1, gap: spacing.md },
  iconBtn: { padding: 2 },
  title: { fontSize: 22, fontWeight: "700", color: colors.text, flexShrink: 1 },
  rightRow: { flexDirection: "row", alignItems: "center", gap: spacing.sm },
  bell: { padding: 4, position: "relative" },
  badge: {
    position: "absolute",
    top: -2,
    right: -2,
    minWidth: 18,
    height: 18,
    borderRadius: 9,
    backgroundColor: colors.red,
    alignItems: "center",
    justifyContent: "center",
    paddingHorizontal: 4,
  },
  badgeText: { color: "#fff", fontSize: 10, fontWeight: "700" },
  sipPill: {
    flexDirection: "row",
    alignItems: "center",
    gap: 6,
    paddingHorizontal: 10,
    paddingVertical: 6,
    backgroundColor: colors.card,
    borderRadius: 999,
  },
  sipDot: { width: 8, height: 8, borderRadius: 4, backgroundColor: colors.green },
  sipText: { color: colors.text, fontSize: 12, fontWeight: "600" },
});

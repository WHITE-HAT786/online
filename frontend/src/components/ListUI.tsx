import React, { useEffect, useState } from "react";
import { View, Text, StyleSheet, TouchableOpacity, TextInput, ActivityIndicator } from "react-native";
import { Ionicons } from "@expo/vector-icons";
import Screen from "@/src/components/Screen";
import { colors } from "@/src/theme";

type StatusColor = { bg: string; fg: string };
export const STATUS_COLORS: Record<string, StatusColor> = {
  Active: { bg: colors.greenDim, fg: colors.green },
  Inactive: { bg: colors.yellowDim, fg: colors.yellow },
  Disabled: { bg: colors.redDim, fg: colors.red },
  "In Use": { bg: colors.yellowDim, fg: colors.yellow },
  Paid: { bg: colors.greenDim, fg: colors.green },
  Unpaid: { bg: colors.yellowDim, fg: colors.yellow },
  Overdue: { bg: colors.redDim, fg: colors.red },
};

export function StatusPill({ status }: { status: string }) {
  const c = STATUS_COLORS[status] || { bg: colors.card, fg: colors.textMuted };
  return (
    <View style={{ paddingHorizontal: 8, paddingVertical: 3, borderRadius: 6, backgroundColor: c.bg, alignSelf: "flex-start", flexDirection: "row", alignItems: "center", gap: 4 }}>
      <View style={{ width: 6, height: 6, borderRadius: 3, backgroundColor: c.fg }} />
      <Text style={{ color: c.fg, fontSize: 10, fontWeight: "700" }}>{status}</Text>
    </View>
  );
}

export function FourStatCard({ stats }: { stats: { label: string; value: any; color: string; icon: string; sub?: string; percent?: string }[] }) {
  return (
    <View style={{ flexDirection: "row", gap: 8, marginTop: 8 }}>
      {stats.map((s, i) => (
        <View key={i} style={styles.statCard}>
          <View style={[styles.statIcon, { backgroundColor: s.color + "22" }]}>
            <Ionicons name={s.icon as any} size={16} color={s.color} />
          </View>
          <Text style={styles.statLabel}>{s.label}</Text>
          <Text style={styles.statValue}>{s.value}</Text>
          {s.percent && <Text style={[styles.statSub, { color: s.color }]}>{s.percent}</Text>}
          {s.sub && !s.percent && <Text style={styles.statSub}>{s.sub}</Text>}
        </View>
      ))}
    </View>
  );
}

export function SearchRow({ placeholder, value, onChange, right }: any) {
  return (
    <View style={{ flexDirection: "row", gap: 8, marginTop: 14 }}>
      <View style={styles.search}>
        <Ionicons name="search" size={16} color={colors.textMuted} />
        <TextInput style={styles.searchInput} placeholder={placeholder} placeholderTextColor={colors.textDim} value={value} onChangeText={onChange} />
      </View>
      {right}
    </View>
  );
}

const styles = StyleSheet.create({
  statCard: { flex: 1, padding: 10, backgroundColor: colors.card, borderRadius: 12, borderWidth: 1, borderColor: colors.border },
  statIcon: { width: 30, height: 30, borderRadius: 15, alignItems: "center", justifyContent: "center" },
  statLabel: { color: colors.textMuted, fontSize: 11, marginTop: 6 },
  statValue: { color: "#fff", fontSize: 20, fontWeight: "700" },
  statSub: { color: colors.textMuted, fontSize: 10, marginTop: 2 },
  search: { flex: 1, flexDirection: "row", alignItems: "center", gap: 8, backgroundColor: colors.card, borderRadius: 10, paddingHorizontal: 12, height: 42, borderWidth: 1, borderColor: colors.border },
  searchInput: { flex: 1, color: "#fff", fontSize: 13 },
});

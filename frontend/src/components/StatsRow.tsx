import React from "react";
import { View, Text, StyleSheet, ScrollView } from "react-native";
import { Ionicons, MaterialCommunityIcons } from "@expo/vector-icons";
import { colors } from "@/src/theme";

type Stat = {
  label: string;
  value: string | number;
  color: string;
  icon: string;
  family?: "ion" | "mc";
  sub?: string;
  change?: string;
  positive?: boolean;
};

export default function StatsRow({ stats, horizontal = true }: { stats: Stat[]; horizontal?: boolean }) {
  const Wrap = horizontal ? ScrollView : View;
  const wrapProps: any = horizontal
    ? { horizontal: true, showsHorizontalScrollIndicator: false, contentContainerStyle: { gap: 10, paddingRight: 8 } }
    : { style: { flexDirection: "row", flexWrap: "wrap", gap: 10 } };
  return (
    <Wrap {...wrapProps} style={{ marginTop: 12 }}>
      {stats.map((s, i) => (
        <View
          key={i}
          style={[styles.card, !horizontal && { flexBasis: "48%", flexGrow: 1 }]}
          testID={`stat-${i}`}
        >
          <View style={[styles.icon, { backgroundColor: s.color + "22" }]}>
            {s.family === "mc" ? (
              <MaterialCommunityIcons name={s.icon as any} size={18} color={s.color} />
            ) : (
              <Ionicons name={s.icon as any} size={18} color={s.color} />
            )}
          </View>
          <Text style={styles.label}>{s.label}</Text>
          <Text style={styles.value}>{s.value}</Text>
          {s.sub && (
            <Text style={[styles.sub, s.change ? { color: s.positive ? colors.green : colors.red } : undefined]}>
              {s.change ? `${s.positive ? "↑" : "↓"} ${s.change} ` : ""}
              {s.sub}
            </Text>
          )}
        </View>
      ))}
    </Wrap>
  );
}

const styles = StyleSheet.create({
  card: {
    padding: 12,
    backgroundColor: colors.card,
    borderRadius: 14,
    borderWidth: 1,
    borderColor: colors.border,
    minWidth: 140,
  },
  icon: {
    width: 36,
    height: 36,
    borderRadius: 18,
    alignItems: "center",
    justifyContent: "center",
  },
  label: { color: colors.textMuted, fontSize: 12, marginTop: 6 },
  value: { color: "#fff", fontSize: 22, fontWeight: "700", marginTop: 2 },
  sub: { color: colors.textMuted, fontSize: 11, marginTop: 2 },
});

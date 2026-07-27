import React, { useEffect, useState } from "react";
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  TextInput,
  ScrollView,
  ActivityIndicator,
} from "react-native";
import { Ionicons } from "@expo/vector-icons";
import Screen from "@/src/components/Screen";
import { colors, spacing } from "@/src/theme";
import { apiGet } from "@/src/api";

const TABS = ["All", "Incoming", "Outgoing", "Missed"];

const statIcon = (icon: string) => {
  if (icon === "outgoing") return { color: colors.green, bg: colors.greenDim, name: "arrow-up" };
  if (icon === "incoming") return { color: colors.primary, bg: colors.primaryDim, name: "arrow-down" };
  if (icon === "missed") return { color: colors.red, bg: colors.redDim, name: "close" };
  return { color: colors.primary, bg: colors.primaryDim, name: "call" };
};

export default function CallLogs() {
  const [data, setData] = useState<any>(null);
  const [active, setActive] = useState("All");
  const [q, setQ] = useState("");

  useEffect(() => {
    apiGet("/call-logs").then(setData).catch(() => {});
  }, []);

  const items = data
    ? data.items.filter((c: any) => {
        const matchTab =
          active === "All" ||
          (active === "Incoming" && c.type === "incoming") ||
          (active === "Outgoing" && c.type === "outgoing") ||
          (active === "Missed" && c.type === "missed");
        const matchQ = !q || c.name.toLowerCase().includes(q.toLowerCase()) || c.number.includes(q);
        return matchTab && matchQ;
      })
    : [];

  return (
    <Screen title="Call Logs" activeKey="call-logs">
      {/* Tabs */}
      <View style={styles.tabsRow}>
        {TABS.map((t) => (
          <TouchableOpacity
            key={t}
            style={styles.tab}
            onPress={() => setActive(t)}
            testID={`calllogs-tab-${t}`}
          >
            <Text style={[styles.tabLabel, active === t && styles.tabLabelActive]}>{t}</Text>
            {active === t && <View style={styles.tabUnderline} />}
          </TouchableOpacity>
        ))}
      </View>

      {/* Filter row */}
      <View style={{ flexDirection: "row", gap: 8, marginTop: 12 }}>
        <View style={styles.chip}>
          <Ionicons name="calendar-outline" size={16} color={colors.textMuted} />
          <Text style={styles.chipText}>May 20, 2025</Text>
          <Ionicons name="chevron-down" size={14} color={colors.textMuted} />
        </View>
        <View style={styles.chip}>
          <Text style={styles.chipText}>All SIP Accounts</Text>
          <Ionicons name="chevron-down" size={14} color={colors.textMuted} />
        </View>
        <View style={[styles.chip, { backgroundColor: "transparent", borderColor: colors.primary }]}>
          <Ionicons name="funnel-outline" size={14} color={colors.primary} />
          <Text style={[styles.chipText, { color: colors.primary }]}>Filter</Text>
        </View>
      </View>

      {/* Stats */}
      {data && (
        <View style={styles.statsRow}>
          {data.stats.map((s: any, i: number) => {
            const si = statIcon(s.icon);
            return (
              <View key={i} style={styles.statCard} testID={`calllog-stat-${i}`}>
                <View style={[styles.statIcon, { backgroundColor: si.bg }]}>
                  <Ionicons name={si.name as any} size={16} color={si.color} />
                </View>
                <Text style={styles.statLabel}>{s.label}</Text>
                <Text style={styles.statValue}>{s.value}</Text>
                <Text style={[styles.statChange, { color: s.positive ? colors.green : colors.red }]}>
                  {s.positive ? "↑" : "↓"} {s.change}
                </Text>
                <Text style={styles.statSub}>vs May 19</Text>
              </View>
            );
          })}
        </View>
      )}

      {/* Search */}
      <View style={{ flexDirection: "row", gap: 8, marginTop: spacing.md }}>
        <View style={styles.search}>
          <Ionicons name="search" size={16} color={colors.textMuted} />
          <TextInput
            style={styles.searchInput}
            placeholder="Search by number or contact..."
            placeholderTextColor={colors.textDim}
            value={q}
            onChangeText={setQ}
            testID="calllogs-search"
          />
        </View>
        <TouchableOpacity style={styles.iconBtn}>
          <Ionicons name="download-outline" size={20} color={colors.primary} />
        </TouchableOpacity>
      </View>

      {/* List */}
      {!data ? (
        <View style={{ alignItems: "center", marginTop: 40 }}>
          <ActivityIndicator color={colors.primary} />
        </View>
      ) : (
        <View style={styles.listCard}>
          {items.map((c: any, i: number) => {
            const si = statIcon(c.type);
            const durColor = c.type === "missed" ? colors.red : colors.green;
            const typeLabel =
              c.type === "outgoing"
                ? "Outgoing Call"
                : c.type === "incoming"
                ? "Incoming Call"
                : "Missed Call";
            return (
              <View
                key={c.id}
                style={[styles.callRow, i !== items.length - 1 && styles.callRowDivider]}
                testID={`calllog-row-${i}`}
              >
                <View style={[styles.callIcon, { backgroundColor: si.bg }]}>
                  <Ionicons name={si.name as any} size={16} color={si.color} />
                </View>
                <View style={{ flex: 1 }}>
                  <Text style={styles.callName}>{c.name}</Text>
                  <Text style={styles.callNumber}>{c.number}</Text>
                  <View style={{ flexDirection: "row", gap: 6, marginTop: 2 }}>
                    <Text style={[styles.callType, { color: si.color }]}>{typeLabel}</Text>
                    <Text style={{ color: colors.textMuted, fontSize: 12 }}>•</Text>
                    <Text style={styles.trunk}>{c.trunk}</Text>
                  </View>
                </View>
                <View style={{ alignItems: "flex-end" }}>
                  <Text style={styles.callTime}>{c.time}</Text>
                  <Text style={[styles.callDur, { color: durColor }]}>{c.duration}</Text>
                </View>
                <TouchableOpacity>
                  <Ionicons name="information-circle-outline" size={22} color={colors.primary} />
                </TouchableOpacity>
              </View>
            );
          })}
        </View>
      )}
    </Screen>
  );
}

const styles = StyleSheet.create({
  tabsRow: { flexDirection: "row", borderBottomWidth: 1, borderBottomColor: colors.border, marginTop: 4 },
  tab: { paddingVertical: 12, paddingHorizontal: 16, position: "relative" },
  tabLabel: { color: colors.textMuted, fontSize: 14, fontWeight: "500" },
  tabLabelActive: { color: colors.primary, fontWeight: "700" },
  tabUnderline: {
    position: "absolute",
    bottom: -1,
    left: 16,
    right: 16,
    height: 2,
    backgroundColor: colors.primary,
    borderRadius: 1,
  },
  chip: {
    flexDirection: "row",
    alignItems: "center",
    gap: 6,
    backgroundColor: colors.card,
    borderRadius: 10,
    paddingHorizontal: 12,
    paddingVertical: 10,
    borderWidth: 1,
    borderColor: colors.border,
  },
  chipText: { color: "#fff", fontSize: 12, fontWeight: "500" },
  statsRow: { flexDirection: "row", gap: 8, marginTop: 12 },
  statCard: {
    flex: 1,
    padding: 10,
    backgroundColor: colors.card,
    borderRadius: 12,
    alignItems: "center",
    borderWidth: 1,
    borderColor: colors.border,
  },
  statIcon: {
    width: 32,
    height: 32,
    borderRadius: 16,
    alignItems: "center",
    justifyContent: "center",
  },
  statLabel: { color: colors.textMuted, fontSize: 11, marginTop: 6 },
  statValue: { color: "#fff", fontSize: 20, fontWeight: "700" },
  statChange: { fontSize: 10, fontWeight: "700", marginTop: 2 },
  statSub: { color: colors.textDim, fontSize: 9 },
  search: {
    flex: 1,
    flexDirection: "row",
    alignItems: "center",
    gap: 8,
    backgroundColor: colors.card,
    borderRadius: 10,
    paddingHorizontal: 12,
    height: 42,
    borderWidth: 1,
    borderColor: colors.border,
  },
  searchInput: { flex: 1, color: "#fff", fontSize: 13 },
  iconBtn: {
    width: 42,
    height: 42,
    borderRadius: 10,
    backgroundColor: colors.card,
    alignItems: "center",
    justifyContent: "center",
    borderWidth: 1,
    borderColor: colors.border,
  },
  listCard: {
    marginTop: spacing.md,
    backgroundColor: colors.card,
    borderRadius: 14,
    borderWidth: 1,
    borderColor: colors.border,
    paddingHorizontal: 12,
  },
  callRow: {
    flexDirection: "row",
    alignItems: "center",
    gap: 12,
    paddingVertical: 12,
  },
  callRowDivider: { borderBottomWidth: 1, borderBottomColor: colors.borderSoft },
  callIcon: {
    width: 42,
    height: 42,
    borderRadius: 21,
    alignItems: "center",
    justifyContent: "center",
  },
  callName: { color: "#fff", fontSize: 14, fontWeight: "600" },
  callNumber: { color: colors.textMuted, fontSize: 12, marginTop: 2 },
  callType: { fontSize: 11, fontWeight: "500" },
  trunk: { color: colors.primary, fontSize: 11 },
  callTime: { color: colors.textMuted, fontSize: 11 },
  callDur: { fontSize: 12, fontWeight: "600" },
});

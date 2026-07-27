import React, { useEffect, useState } from "react";
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, ActivityIndicator } from "react-native";
import { Ionicons, MaterialCommunityIcons } from "@expo/vector-icons";
import Screen from "@/src/components/Screen";
import { colors } from "@/src/theme";
import { apiGet } from "@/src/api";
import { FourStatCard, SearchRow, StatusPill } from "@/src/components/ListUI";

const PLAN_ICON: Record<string, any> = {
  rocket: ["ion", "rocket"],
  star: ["ion", "star"],
  crown: ["mc", "crown"],
  building: ["mc", "office-building"],
  headset: ["ion", "headset"],
  handshake: ["mc", "handshake"],
  medal: ["mc", "medal"],
  ban: ["mc", "cancel"],
};

const CAT_COLORS: Record<string, { bg: string; fg: string }> = {
  Retail: { bg: colors.primaryDim, fg: colors.primary },
  Wholesale: { bg: colors.tealDim, fg: colors.teal },
  "Call Center": { bg: colors.purpleDim, fg: colors.purple },
  Other: { bg: colors.card, fg: colors.textMuted },
};

export default function Plans() {
  const [data, setData] = useState<any>(null);
  const [q, setQ] = useState("");
  const [active, setActive] = useState("All Plans");
  useEffect(() => { apiGet("/plans").then(setData); }, []);
  const items = data ? data.items.filter((x: any) =>
    (!q || x.name.toLowerCase().includes(q.toLowerCase())) &&
    (active === "All Plans" || x.category === active)
  ) : [];

  return (
    <Screen title="Plans" activeKey="plans" showSip={false} showBell={false}
      right={<>
        <TouchableOpacity><Ionicons name="search" size={22} color="#fff" /></TouchableOpacity>
        <TouchableOpacity><Ionicons name="funnel-outline" size={20} color="#fff" /></TouchableOpacity>
        <TouchableOpacity style={styles.addBtn}>
          <Ionicons name="add" size={14} color="#fff" />
          <Text style={{ color: "#fff", fontSize: 11, fontWeight: "700" }}>Add Plan</Text>
        </TouchableOpacity>
      </>}
    >
      {!data ? <ActivityIndicator color={colors.primary} style={{ marginTop: 40 }} /> : (
        <>
          <FourStatCard stats={[
            { label: "Total Plans", value: data.stats.total, color: colors.primary, icon: "list", sub: "All Plans" },
            { label: "Active Plans", value: data.stats.active, color: colors.green, icon: "checkmark-circle", percent: "71.4%" },
            { label: "Inactive Plans", value: data.stats.inactive, color: colors.yellow, icon: "pause-circle", percent: "19.0%" },
            { label: "Disabled Plans", value: data.stats.disabled, color: colors.red, icon: "close-circle", percent: "9.5%" },
          ]} />
          <SearchRow placeholder="Search by Plan Name or Type..." value={q} onChange={setQ}
            right={<View style={styles.sortBox}><Text style={{ color: colors.textMuted, fontSize: 11 }}>Sort By</Text><Text style={{ color: "#fff", fontWeight: "600" }}>Plan Name ▾</Text></View>}
          />
          <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={{ gap: 16, paddingVertical: 12 }}>
            {["All Plans", "Retail", "Wholesale", "Call Center", "Other"].map(t => (
              <TouchableOpacity key={t} onPress={() => setActive(t)}>
                <Text style={[styles.chip, active === t && { color: colors.primary, borderBottomWidth: 2, borderBottomColor: colors.primary }]}>{t}</Text>
              </TouchableOpacity>
            ))}
          </ScrollView>
          {items.map((p: any) => {
            const [family, iname] = PLAN_ICON[p.icon] || ["ion", "star"];
            const cat = CAT_COLORS[p.category] || CAT_COLORS.Other;
            return (
              <View key={p.id} style={styles.row}>
                <View style={[styles.icon, { backgroundColor: p.color + "30" }]}>
                  {family === "mc" ? <MaterialCommunityIcons name={iname} size={22} color={p.color} /> : <Ionicons name={iname} size={22} color={p.color} />}
                </View>
                <View style={{ flex: 1 }}>
                  <View style={{ flexDirection: "row", gap: 6, alignItems: "center", flexWrap: "wrap" }}>
                    <Text style={styles.name}>{p.name}</Text>
                    <View style={[styles.catPill, { backgroundColor: cat.bg }]}>
                      <Text style={{ color: cat.fg, fontSize: 10, fontWeight: "700" }}>{p.category}</Text>
                    </View>
                  </View>
                  <View style={{ flexDirection: "row", gap: 6, marginTop: 4, flexWrap: "wrap" }}>
                    <Text style={styles.meta}>{p.minutes}</Text>
                    <Text style={styles.meta}>•</Text>
                    <Text style={styles.meta}>{p.accounts}</Text>
                    <Text style={styles.meta}>•</Text>
                    <Text style={styles.meta}>{p.concurrent}</Text>
                  </View>
                  <Text style={styles.meta}>Created: {p.created}</Text>
                </View>
                <View style={{ alignItems: "flex-end", gap: 4 }}>
                  <StatusPill status={p.status} />
                  <Text style={{ color: "#fff", fontWeight: "700", fontSize: 16 }}>${p.price.toFixed(2)}</Text>
                  <Text style={styles.meta}>Monthly</Text>
                </View>
                <TouchableOpacity><Ionicons name="ellipsis-vertical" size={16} color={colors.textMuted} /></TouchableOpacity>
              </View>
            );
          })}
        </>
      )}
    </Screen>
  );
}

const styles = StyleSheet.create({
  addBtn: { flexDirection: "row", alignItems: "center", gap: 4, backgroundColor: colors.primary, paddingHorizontal: 10, paddingVertical: 7, borderRadius: 8 },
  sortBox: { paddingHorizontal: 10, justifyContent: "center", backgroundColor: colors.card, borderRadius: 10, borderWidth: 1, borderColor: colors.border },
  chip: { color: colors.textMuted, fontSize: 13, paddingBottom: 6 },
  row: { flexDirection: "row", alignItems: "flex-start", gap: 10, padding: 12, backgroundColor: colors.card, borderRadius: 12, marginTop: 10, borderWidth: 1, borderColor: colors.border },
  icon: { width: 42, height: 42, borderRadius: 21, alignItems: "center", justifyContent: "center" },
  name: { color: "#fff", fontWeight: "700", fontSize: 14 },
  meta: { color: colors.textMuted, fontSize: 11, marginTop: 2 },
  catPill: { paddingHorizontal: 6, paddingVertical: 2, borderRadius: 4 },
});

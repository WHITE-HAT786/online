import React, { useEffect, useState } from "react";
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, ActivityIndicator } from "react-native";
import { Ionicons, MaterialCommunityIcons } from "@expo/vector-icons";
import Screen from "@/src/components/Screen";
import { colors } from "@/src/theme";
import { apiGet } from "@/src/api";
import { FourStatCard, SearchRow, StatusPill } from "@/src/components/ListUI";

const ICONS: Record<string, any> = {
  cart: ["ion", "cart"],
  headset: ["ion", "headset"],
  cash: ["ion", "cash"],
  information: ["ion", "information-circle"],
  person: ["ion", "person"],
  megaphone: ["ion", "megaphone"],
  bank: ["mc", "bank"],
  airplane: ["ion", "airplane"],
};

export default function IVR() {
  const [data, setData] = useState<any>(null);
  const [q, setQ] = useState("");
  const [active, setActive] = useState("All IVRs");
  useEffect(() => { apiGet("/ivr").then(setData); }, []);
  const filtered = data ? data.items.filter((x: any) =>
    (!q || x.name.toLowerCase().includes(q.toLowerCase())) &&
    (active === "All IVRs" || x.status === active.replace(/^\w+ /, "").replace(/ \(\d+\)/, ""))
  ) : [];

  return (
    <Screen title="IVR" activeKey="ivr" showSip={false} showBell={false}
      right={<>
        <TouchableOpacity><Ionicons name="search" size={22} color="#fff" /></TouchableOpacity>
        <TouchableOpacity style={styles.createBtn}>
          <Ionicons name="add" size={16} color="#fff" />
          <Text style={{ color: "#fff", fontWeight: "700", fontSize: 12 }}>Create IVR</Text>
        </TouchableOpacity>
      </>}
    >
      {!data ? <ActivityIndicator color={colors.primary} style={{ marginTop: 40 }} /> : (
        <>
          <FourStatCard stats={[
            { label: "Total IVRs", value: data.stats.total, color: colors.primary, icon: "sync-outline", sub: "All IVRs" },
            { label: "Active", value: data.stats.active, color: colors.green, icon: "checkmark-circle", percent: "71.4%" },
            { label: "Inactive", value: data.stats.inactive, color: colors.yellow, icon: "pause-circle", percent: "21.4%" },
            { label: "Disabled", value: data.stats.disabled, color: colors.red, icon: "close-circle", percent: "7.1%" },
          ]} />
          <SearchRow placeholder="Search by IVR Name or Extension..." value={q} onChange={setQ}
            right={<TouchableOpacity style={styles.filterBtn}><Ionicons name="funnel-outline" size={14} color={colors.primary} /><Text style={{ color: colors.primary }}>Filter</Text><Ionicons name="chevron-down" size={12} color={colors.primary} /></TouchableOpacity>}
          />
          <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={{ gap: 12, paddingVertical: 12 }}>
            {[`All IVRs (${data.stats.total})`, `Active (${data.stats.active})`, `Inactive (${data.stats.inactive})`, `Disabled (${data.stats.disabled})`].map(t => (
              <TouchableOpacity key={t} onPress={() => setActive(t.split(" (")[0])}>
                <Text style={[styles.chip, active === t.split(" (")[0] && { color: colors.primary, borderBottomWidth: 2, borderBottomColor: colors.primary }]}>{t}</Text>
              </TouchableOpacity>
            ))}
          </ScrollView>
          {filtered.map((it: any) => {
            const [family, name] = ICONS[it.icon] || ["ion", "call"];
            return (
              <View key={it.id} style={styles.row}>
                <View style={[styles.icon, { backgroundColor: it.color + "30" }]}>
                  {family === "mc" ? <MaterialCommunityIcons name={name} size={22} color={it.color} /> : <Ionicons name={name} size={22} color={it.color} />}
                </View>
                <View style={{ flex: 1 }}>
                  <Text style={styles.name}>{it.name}</Text>
                  <Text style={styles.meta}>Extension: {it.ext}</Text>
                  <Text style={styles.meta}>Created: {it.created}</Text>
                </View>
                <View style={{ alignItems: "flex-end", gap: 4 }}>
                  <StatusPill status={it.status} />
                  <Text style={styles.meta}>Steps: {it.steps}</Text>
                  <Text style={styles.meta}>Calls Today: {it.calls_today}</Text>
                </View>
                <TouchableOpacity style={styles.callBtn}><Ionicons name="call" size={16} color={colors.primary} /></TouchableOpacity>
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
  createBtn: { flexDirection: "row", alignItems: "center", gap: 4, backgroundColor: colors.primary, paddingHorizontal: 12, paddingVertical: 8, borderRadius: 999 },
  filterBtn: { flexDirection: "row", alignItems: "center", gap: 4, paddingHorizontal: 12, backgroundColor: colors.card, borderRadius: 10, borderWidth: 1, borderColor: colors.border },
  chip: { color: colors.textMuted, fontSize: 13, paddingBottom: 6 },
  row: { flexDirection: "row", alignItems: "center", gap: 10, padding: 12, backgroundColor: colors.card, borderRadius: 12, marginTop: 10, borderWidth: 1, borderColor: colors.border },
  icon: { width: 42, height: 42, borderRadius: 21, alignItems: "center", justifyContent: "center" },
  name: { color: "#fff", fontWeight: "700", fontSize: 14 },
  meta: { color: colors.textMuted, fontSize: 11, marginTop: 2 },
  callBtn: { width: 34, height: 34, borderRadius: 8, backgroundColor: colors.primaryDim, alignItems: "center", justifyContent: "center" },
});

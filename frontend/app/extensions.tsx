import React, { useEffect, useState } from "react";
import { View, Text, StyleSheet, TouchableOpacity, ActivityIndicator } from "react-native";
import { Ionicons } from "@expo/vector-icons";
import Screen from "@/src/components/Screen";
import { colors } from "@/src/theme";
import { apiGet } from "@/src/api";
import { FourStatCard, SearchRow, StatusPill } from "@/src/components/ListUI";

export default function Extensions() {
  const [data, setData] = useState<any>(null);
  const [q, setQ] = useState("");
  useEffect(() => { apiGet("/extensions").then(setData); }, []);
  const items = data ? data.items.filter((x: any) => !q || x.name.toLowerCase().includes(q.toLowerCase()) || x.ext.includes(q)) : [];

  return (
    <Screen title="Extensions" activeKey="extensions" showSip={false} showBell={false}
      right={<>
        <TouchableOpacity><Ionicons name="search" size={22} color="#fff" /></TouchableOpacity>
        <TouchableOpacity><Ionicons name="funnel-outline" size={20} color="#fff" /></TouchableOpacity>
        <TouchableOpacity style={styles.addBtn}><Ionicons name="add" size={18} color="#fff" /></TouchableOpacity>
      </>}
    >
      {!data ? <ActivityIndicator color={colors.primary} style={{ marginTop: 40 }} /> : (
        <>
          <FourStatCard stats={[
            { label: "Total Extensions", value: data.stats.total, color: colors.primary, icon: "people", sub: "All Extensions" },
            { label: "Active", value: data.stats.active, color: colors.green, icon: "checkmark-circle", percent: "75.0%" },
            { label: "Inactive", value: data.stats.inactive, color: colors.yellow, icon: "pause-circle", percent: "17.9%" },
            { label: "Disabled", value: data.stats.disabled, color: colors.red, icon: "close-circle", percent: "7.1%" },
          ]} />
          <SearchRow placeholder="Search by Extension, Name or Caller ID..." value={q} onChange={setQ}
            right={<View style={styles.sortBox}><Text style={{ color: colors.textMuted, fontSize: 11 }}>Sort By</Text><Text style={{ color: "#fff", fontWeight: "600" }}>Extension ▾</Text></View>}
          />
          {items.map((s: any) => (
            <View key={s.id} style={styles.row}>
              <View style={[styles.avatar, { backgroundColor: s.color + "30" }]}>
                <Text style={{ color: s.color, fontWeight: "700", fontSize: 12 }}>{s.name.split(" ").map((x: string) => x[0]).slice(0, 2).join("")}</Text>
              </View>
              <View style={{ flex: 1 }}>
                <Text style={styles.ext}>{s.ext}</Text>
                <Text style={styles.name}>{s.name}</Text>
                <Text style={styles.meta}>{s.email}</Text>
              </View>
              <View style={{ alignItems: "flex-end", gap: 4 }}>
                <StatusPill status={s.status} />
                <Text style={styles.meta}>Device: {s.device}</Text>
                <Text style={styles.meta}>{s.caller_id}</Text>
              </View>
              <TouchableOpacity style={styles.callBtn}><Ionicons name="call" size={16} color={colors.primary} /></TouchableOpacity>
              <TouchableOpacity><Ionicons name="ellipsis-vertical" size={16} color={colors.textMuted} /></TouchableOpacity>
            </View>
          ))}
        </>
      )}
    </Screen>
  );
}

const styles = StyleSheet.create({
  addBtn: { width: 34, height: 34, borderRadius: 17, backgroundColor: colors.primary, alignItems: "center", justifyContent: "center" },
  sortBox: { paddingHorizontal: 10, justifyContent: "center", backgroundColor: colors.card, borderRadius: 10, borderWidth: 1, borderColor: colors.border },
  row: { flexDirection: "row", alignItems: "center", gap: 10, padding: 12, backgroundColor: colors.card, borderRadius: 12, marginTop: 10, borderWidth: 1, borderColor: colors.border },
  avatar: { width: 40, height: 40, borderRadius: 20, alignItems: "center", justifyContent: "center" },
  ext: { color: "#fff", fontWeight: "700", fontSize: 16 },
  name: { color: colors.textMuted, fontSize: 12, marginTop: 2 },
  meta: { color: colors.textMuted, fontSize: 11, marginTop: 2 },
  callBtn: { width: 34, height: 34, borderRadius: 8, backgroundColor: colors.primaryDim, alignItems: "center", justifyContent: "center" },
});

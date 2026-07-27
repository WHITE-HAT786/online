import React, { useEffect, useState } from "react";
import { View, Text, StyleSheet, TouchableOpacity, ActivityIndicator } from "react-native";
import { Ionicons } from "@expo/vector-icons";
import Screen from "@/src/components/Screen";
import { colors } from "@/src/theme";
import { apiGet } from "@/src/api";
import { FourStatCard, SearchRow, StatusPill } from "@/src/components/ListUI";

export default function SipAccounts() {
  const [data, setData] = useState<any>(null);
  const [q, setQ] = useState("");
  useEffect(() => { apiGet("/sip-accounts").then(setData); }, []);

  const items = data ? data.items.filter((x: any) => !q || x.name.toLowerCase().includes(q.toLowerCase())) : [];

  return (
    <Screen title="SIP Accounts" activeKey="sip" showSip={false} showBell={false}
      right={<>
        <TouchableOpacity><Ionicons name="search" size={22} color="#fff" /></TouchableOpacity>
        <TouchableOpacity><Ionicons name="funnel-outline" size={20} color="#fff" /></TouchableOpacity>
        <TouchableOpacity style={styles.addBtn}><Ionicons name="add" size={18} color="#fff" /></TouchableOpacity>
      </>}
    >
      {!data ? <ActivityIndicator color={colors.primary} style={{ marginTop: 40 }} /> : (
        <>
          <FourStatCard stats={[
            { label: "Total Accounts", value: data.stats.total, color: colors.primary, icon: "people", sub: "All SIP Accounts" },
            { label: "Active", value: data.stats.active, color: colors.green, icon: "checkmark-circle", percent: "75.0%" },
            { label: "Inactive", value: data.stats.inactive, color: colors.yellow, icon: "pause-circle", percent: "18.8%" },
            { label: "Disabled", value: data.stats.disabled, color: colors.red, icon: "close-circle", percent: "6.2%" },
          ]} />
          <SearchRow placeholder="Search by Name, Username or Domain..." value={q} onChange={setQ}
            right={<View style={styles.sortBox}><Text style={{ color: colors.textMuted, fontSize: 11 }}>Sort By</Text><Text style={{ color: "#fff", fontWeight: "600" }}>Name ▾</Text></View>}
          />
          {items.map((s: any) => (
            <View key={s.id} style={styles.row}>
              <View style={[styles.avatar, { backgroundColor: s.color + "30" }]}>
                <Text style={{ color: s.color, fontWeight: "700", fontSize: 12 }}>{s.name.split(" ").map((x: string) => x[0]).slice(0, 2).join("")}</Text>
              </View>
              <View style={{ flex: 1 }}>
                <Text style={styles.name}>{s.name}</Text>
                <Text style={styles.meta}>Username: {s.username}</Text>
                <Text style={styles.meta}>Domain: {s.domain}</Text>
              </View>
              <View style={{ alignItems: "flex-end", gap: 4 }}>
                <StatusPill status={s.status} />
                <Text style={styles.meta}>IP: {s.ip}</Text>
                <Text style={styles.meta}>Port: {s.port}</Text>
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
  name: { color: "#fff", fontWeight: "700", fontSize: 14 },
  meta: { color: colors.textMuted, fontSize: 11, marginTop: 2 },
  callBtn: { width: 34, height: 34, borderRadius: 8, backgroundColor: colors.primaryDim, alignItems: "center", justifyContent: "center" },
});

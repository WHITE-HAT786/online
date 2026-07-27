import React, { useEffect, useState } from "react";
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  ActivityIndicator,
  ScrollView,
} from "react-native";
import { Ionicons, MaterialCommunityIcons } from "@expo/vector-icons";
import { useRouter } from "expo-router";
import Screen from "@/src/components/Screen";
import { colors, spacing } from "@/src/theme";
import { apiGet } from "@/src/api";
import { useAuth } from "@/src/AuthContext";

const iconFor = (type: string) => {
  if (type === "outgoing") return { name: "call-sharp", rot: -45, color: colors.green, bg: colors.greenDim };
  if (type === "incoming") return { name: "call-sharp", rot: 135, color: colors.primary, bg: colors.primaryDim };
  return { name: "close-circle-outline", rot: 0, color: colors.red, bg: colors.redDim };
};

const statIcon = (icon: string) => {
  switch (icon) {
    case "outgoing":
      return { color: colors.green, bg: colors.greenDim, name: "arrow-forward-circle" };
    case "incoming":
      return { color: colors.primary, bg: colors.primaryDim, name: "arrow-down-circle" };
    case "missed":
      return { color: colors.red, bg: colors.redDim, name: "close-circle" };
    default:
      return { color: colors.purple, bg: colors.purpleDim, name: "call" };
  }
};

export default function Dashboard() {
  const router = useRouter();
  const { user } = useAuth();
  const [data, setData] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const load = async () => {
    try {
      const d = await apiGet("/dashboard");
      setData(d);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };
  useEffect(() => {
    load();
  }, []);

  const onRefresh = async () => {
    setRefreshing(true);
    await load();
  };

  if (loading || !data) {
    return (
      <Screen title="Depth Route" activeKey="dashboard" showBell={false} showSip={false}>
        <View style={{ marginTop: 60, alignItems: "center" }}>
          <ActivityIndicator color={colors.primary} />
        </View>
      </Screen>
    );
  }

  return (
    <Screen
      title=""
      activeKey="dashboard"
      onRefresh={onRefresh}
      refreshing={refreshing}
      right={
        <View style={{ flexDirection: "row", alignItems: "center", gap: 8 }}>
          <View style={styles.brandInline}>
            <View style={styles.brandLogoSmall}>
              <MaterialCommunityIcons name="waveform" size={16} color="#fff" />
            </View>
            <Text style={styles.brandInlineText}>Depth Route</Text>
          </View>
        </View>
      }
      showBell
      showSip
    >
      {/* Profile row */}
      <View style={styles.profileRow} testID="dashboard-profile-row">
        <View style={styles.avatar}>
          <Text style={styles.avatarText}>
            {(data.profile.name || "JD")
              .split(" ")
              .map((s: string) => s[0])
              .slice(0, 2)
              .join("")}
          </Text>
        </View>
        <View style={{ flex: 1 }}>
          <Text style={styles.profileName}>{data.profile.name}</Text>
          <Text style={styles.profileExt}>
            <Text style={{ color: colors.primary }}>{data.profile.ext}</Text>{" "}
            <Text style={{ color: colors.textMuted }}>({data.profile.name})</Text>
          </Text>
          <View style={styles.sipRow}>
            <View style={styles.sipDot} />
            <Text style={styles.sipText}>{data.profile.sip_status}</Text>
          </View>
        </View>
        <View style={styles.balanceBox}>
          <TouchableOpacity
            style={styles.balanceRow}
            onPress={() => router.push("/billing")}
            testID="dashboard-balance"
          >
            <View style={[styles.miniIcon, { backgroundColor: colors.greenDim }]}>
              <Ionicons name="wallet" size={16} color={colors.green} />
            </View>
            <View style={{ flex: 1 }}>
              <Text style={styles.tinyLabel}>Account Balance</Text>
              <Text style={styles.balanceValue}>
                ${data.balance.amount.toFixed(2)}
              </Text>
            </View>
            <Ionicons name="chevron-forward" size={16} color={colors.textMuted} />
          </TouchableOpacity>
          <View style={{ height: 1, backgroundColor: colors.border, marginVertical: 10 }} />
          <TouchableOpacity
            style={styles.balanceRow}
            onPress={() => router.push("/plans")}
            testID="dashboard-plan"
          >
            <View style={{ flex: 1 }}>
              <Text style={styles.tinyLabel}>Plan</Text>
              <Text style={styles.planName}>{data.plan.name}</Text>
              <Text style={styles.planDate}>Valid till: {data.plan.valid_till}</Text>
            </View>
            <Ionicons name="chevron-forward" size={16} color={colors.textMuted} />
          </TouchableOpacity>
        </View>
      </View>

      {/* Stats row */}
      <ScrollView
        horizontal
        showsHorizontalScrollIndicator={false}
        contentContainerStyle={{ gap: 10, paddingRight: 8 }}
        style={{ marginTop: spacing.md }}
      >
        {data.stats.map((s: any, i: number) => {
          const si = statIcon(s.icon);
          return (
            <View key={i} style={styles.statCard} testID={`dashboard-stat-${i}`}>
              <View style={[styles.statIcon, { backgroundColor: si.bg }]}>
                <Ionicons name={si.name as any} size={18} color={si.color} />
              </View>
              <Text style={styles.statLabel}>{s.label}</Text>
              <Text style={styles.statValue}>{s.value}</Text>
              <View style={{ flexDirection: "row", alignItems: "center", gap: 4, marginTop: 4 }}>
                <Ionicons
                  name={s.positive ? "arrow-up" : "arrow-down"}
                  size={11}
                  color={s.positive ? colors.green : colors.red}
                />
                <Text style={[styles.statChange, { color: s.positive ? colors.green : colors.red }]}>
                  {s.change}
                </Text>
                <Text style={styles.statSub}>vs last 7 days</Text>
              </View>
            </View>
          );
        })}
      </ScrollView>

      {/* Quick actions */}
      <View style={styles.actionsRow}>
        <QuickAction
          icon="person-add"
          color={colors.green}
          bg={colors.greenDim}
          label="Add Contact"
          onPress={() => router.push("/(tabs)/contacts")}
          testID="qa-add-contact"
        />
        <QuickAction
          icon="keypad"
          color={colors.primary}
          bg={colors.primaryDim}
          label="Dial Pad"
          onPress={() => router.push("/(tabs)/dialer")}
          testID="qa-dial-pad"
        />
        <QuickAction
          icon="people"
          color={colors.purple}
          bg={colors.purpleDim}
          label="Contacts"
          onPress={() => router.push("/(tabs)/contacts")}
          testID="qa-contacts"
        />
        <QuickAction
          icon="time"
          color={colors.yellow}
          bg={colors.yellowDim}
          label="Call Logs"
          onPress={() => router.push("/(tabs)/call-logs")}
          testID="qa-call-logs"
        />
        <QuickAction
          icon="mic"
          color={colors.teal}
          bg={colors.tealDim}
          label="Voicemails"
          onPress={() => router.push("/voicemails")}
          mci="voicemail"
          testID="qa-voicemails"
        />
      </View>

      {/* Recent calls */}
      <View style={styles.section}>
        <View style={styles.sectionHeader}>
          <Text style={styles.sectionTitle}>Recent Calls</Text>
          <TouchableOpacity
            style={{ flexDirection: "row", alignItems: "center", gap: 4 }}
            onPress={() => router.push("/(tabs)/call-logs")}
            testID="dashboard-view-all-calls"
          >
            <Text style={styles.viewAll}>View All</Text>
            <Ionicons name="chevron-forward" size={14} color={colors.textMuted} />
          </TouchableOpacity>
        </View>

        {data.recent_calls.map((c: any, i: number) => {
          const isMissed = c.type === "missed";
          const isOutgoing = c.type === "outgoing";
          const iconColor = isMissed ? colors.red : isOutgoing ? colors.green : colors.primary;
          const iconBg = isMissed ? colors.redDim : isOutgoing ? colors.greenDim : colors.primaryDim;
          const typeLabel = isMissed ? "Missed Call" : isOutgoing ? "Outgoing Call" : "Incoming Call";
          const durColor = isMissed ? colors.red : colors.green;
          return (
            <View
              key={c.id}
              style={[styles.callRow, i !== data.recent_calls.length - 1 && styles.callRowDivider]}
              testID={`recent-call-${i}`}
            >
              <View style={[styles.callIcon, { backgroundColor: iconBg }]}>
                {isMissed ? (
                  <Ionicons name="close-outline" size={18} color={iconColor} />
                ) : (
                  <Ionicons
                    name={isOutgoing ? "arrow-forward" : "arrow-down"}
                    size={16}
                    color={iconColor}
                  />
                )}
              </View>
              <View style={{ flex: 1 }}>
                <Text style={styles.callName} numberOfLines={1}>
                  {c.name}
                </Text>
                <Text style={[styles.callType, { color: iconColor }]}>
                  {c.ext ? `${c.ext} ` : ""}
                  {typeLabel}
                </Text>
              </View>
              <View style={{ alignItems: "flex-end" }}>
                <Text style={styles.callTime}>{c.time}</Text>
                <Text style={[styles.callDur, { color: durColor }]}>{c.duration}</Text>
              </View>
              <TouchableOpacity style={styles.infoBtn}>
                <Ionicons name="information-circle-outline" size={22} color={colors.primary} />
              </TouchableOpacity>
            </View>
          );
        })}
      </View>

      {/* Quick stats bottom */}
      <View style={styles.bottomStatsRow}>
        {data.quick_stats.map((q: any, i: number) => (
          <View key={i} style={styles.bottomStat} testID={`quick-stat-${i}`}>
            <View style={[styles.miniIcon, { backgroundColor: q.color + "20", marginBottom: 8 }]}>
              {q.icon === "voicemail" ? (
                <MaterialCommunityIcons name="voicemail" size={16} color={q.color} />
              ) : (
                <Ionicons name={q.icon} size={16} color={q.color} />
              )}
            </View>
            <Text style={styles.bottomStatLabel}>{q.label}</Text>
            <Text style={styles.bottomStatValue}>{q.value}</Text>
            <Text style={[styles.bottomStatSub, { color: q.color }]}>{q.sub}</Text>
          </View>
        ))}
      </View>
    </Screen>
  );
}

function QuickAction({ icon, color, bg, label, onPress, testID, mci }: any) {
  return (
    <TouchableOpacity style={styles.qa} onPress={onPress} testID={testID}>
      <View style={[styles.qaIcon, { backgroundColor: bg }]}>
        {mci ? (
          <MaterialCommunityIcons name={mci} size={22} color={color} />
        ) : (
          <Ionicons name={icon} size={22} color={color} />
        )}
      </View>
      <Text style={styles.qaLabel}>{label}</Text>
    </TouchableOpacity>
  );
}

const styles = StyleSheet.create({
  brandInline: { flexDirection: "row", alignItems: "center", gap: 6 },
  brandLogoSmall: {
    width: 24,
    height: 24,
    borderRadius: 6,
    backgroundColor: colors.primary,
    alignItems: "center",
    justifyContent: "center",
  },
  brandInlineText: { color: "#fff", fontWeight: "700", fontSize: 14 },
  profileRow: { flexDirection: "row", gap: 12, marginTop: 8 },
  avatar: {
    width: 64,
    height: 64,
    borderRadius: 32,
    backgroundColor: colors.primaryDim,
    alignItems: "center",
    justifyContent: "center",
  },
  avatarText: { color: "#fff", fontSize: 22, fontWeight: "700" },
  profileName: { color: "#fff", fontSize: 18, fontWeight: "700" },
  profileExt: { fontSize: 13, marginTop: 2 },
  sipRow: { flexDirection: "row", alignItems: "center", gap: 6, marginTop: 4 },
  sipDot: { width: 7, height: 7, borderRadius: 4, backgroundColor: colors.green },
  sipText: { color: colors.green, fontSize: 12, fontWeight: "600" },
  balanceBox: {
    flex: 1.1,
    backgroundColor: colors.card,
    borderRadius: 14,
    padding: 12,
    borderWidth: 1,
    borderColor: colors.border,
  },
  balanceRow: { flexDirection: "row", alignItems: "center", gap: 10 },
  miniIcon: {
    width: 34,
    height: 34,
    borderRadius: 17,
    alignItems: "center",
    justifyContent: "center",
  },
  tinyLabel: { color: colors.textMuted, fontSize: 11 },
  balanceValue: { color: colors.green, fontSize: 18, fontWeight: "700" },
  planName: { color: "#fff", fontSize: 14, fontWeight: "700" },
  planDate: { color: colors.textMuted, fontSize: 11, marginTop: 2 },
  statCard: {
    width: 130,
    padding: 12,
    backgroundColor: colors.card,
    borderRadius: 14,
    borderWidth: 1,
    borderColor: colors.border,
  },
  statIcon: {
    width: 36,
    height: 36,
    borderRadius: 18,
    alignItems: "center",
    justifyContent: "center",
  },
  statLabel: { color: colors.textMuted, fontSize: 12, marginTop: 8 },
  statValue: { color: "#fff", fontSize: 22, fontWeight: "700", marginTop: 2 },
  statChange: { fontSize: 11, fontWeight: "700" },
  statSub: { fontSize: 10, color: colors.textDim, marginLeft: 4 },
  actionsRow: {
    flexDirection: "row",
    backgroundColor: colors.card,
    borderRadius: 14,
    padding: 14,
    marginTop: spacing.md,
    borderWidth: 1,
    borderColor: colors.border,
    justifyContent: "space-between",
  },
  qa: { alignItems: "center", gap: 8, flex: 1 },
  qaIcon: {
    width: 46,
    height: 46,
    borderRadius: 23,
    alignItems: "center",
    justifyContent: "center",
  },
  qaLabel: { color: "#fff", fontSize: 11, textAlign: "center", fontWeight: "500" },
  section: {
    marginTop: spacing.md,
    backgroundColor: colors.card,
    borderRadius: 14,
    padding: 14,
    borderWidth: 1,
    borderColor: colors.border,
  },
  sectionHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: 6,
  },
  sectionTitle: { color: "#fff", fontSize: 17, fontWeight: "700" },
  viewAll: { color: colors.textMuted, fontSize: 13 },
  callRow: {
    flexDirection: "row",
    alignItems: "center",
    gap: 12,
    paddingVertical: 12,
  },
  callRowDivider: { borderBottomWidth: 1, borderBottomColor: colors.border },
  callIcon: {
    width: 42,
    height: 42,
    borderRadius: 21,
    alignItems: "center",
    justifyContent: "center",
  },
  callName: { color: "#fff", fontSize: 15, fontWeight: "600" },
  callType: { fontSize: 12, marginTop: 2, fontWeight: "500" },
  callTime: { color: colors.textMuted, fontSize: 12 },
  callDur: { fontSize: 13, fontWeight: "600", marginTop: 2 },
  infoBtn: { padding: 4 },
  bottomStatsRow: { flexDirection: "row", gap: 8, marginTop: spacing.md },
  bottomStat: {
    flex: 1,
    padding: 12,
    backgroundColor: colors.card,
    borderRadius: 14,
    borderWidth: 1,
    borderColor: colors.border,
  },
  bottomStatLabel: { color: colors.textMuted, fontSize: 12 },
  bottomStatValue: { color: "#fff", fontSize: 22, fontWeight: "700", marginTop: 2 },
  bottomStatSub: { fontSize: 11, marginTop: 2 },
});

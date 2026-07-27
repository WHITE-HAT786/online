// Depth Route Dialer - Design tokens
export const colors = {
  bg: "#050B1A",
  bgAlt: "#0A1224",
  card: "#0F1A30",
  cardAlt: "#111C33",
  border: "#1E2A45",
  borderSoft: "#172136",
  text: "#FFFFFF",
  textMuted: "#8891A6",
  textDim: "#5F6A82",
  primary: "#2F80ED",
  primaryDim: "#1F3A6B",
  green: "#22C55E",
  greenDim: "#0F3B22",
  red: "#EF4444",
  redDim: "#3B1518",
  yellow: "#F59E0B",
  yellowDim: "#3B2810",
  purple: "#A855F7",
  purpleDim: "#2A163F",
  teal: "#14B8A6",
  tealDim: "#0B3A36",
  orange: "#F97316",
  orangeDim: "#3A1E10",
  pink: "#EC4899",
};

export const spacing = { xs: 4, sm: 8, md: 12, lg: 16, xl: 20, xxl: 24, huge: 32 };

export const radius = { sm: 8, md: 12, lg: 16, xl: 20, pill: 999 };

export const typography = {
  h1: { fontSize: 28, fontWeight: "700" as const, color: colors.text },
  h2: { fontSize: 22, fontWeight: "700" as const, color: colors.text },
  h3: { fontSize: 18, fontWeight: "600" as const, color: colors.text },
  body: { fontSize: 15, color: colors.text },
  small: { fontSize: 13, color: colors.textMuted },
  tiny: { fontSize: 11, color: colors.textDim },
};

import "@/App.css";

function App() {
  return (
    <div className="App" data-testid="webdialer-info-root" style={{ minHeight: "100vh", background: "#0b0f1a", color: "#e2e8f0", fontFamily: "ui-sans-serif, system-ui" }}>
      <div style={{ maxWidth: 780, margin: "0 auto", padding: "64px 24px" }}>
        <div style={{ display: "inline-block", padding: "6px 14px", borderRadius: 999, background: "#1e293b", color: "#38bdf8", fontSize: 12, letterSpacing: 1, textTransform: "uppercase", marginBottom: 24 }} data-testid="webdialer-badge">
          PHP / MySQL / Asterisk Project
        </div>
        <h1 data-testid="webdialer-title" style={{ fontSize: 44, fontWeight: 700, lineHeight: 1.1, margin: 0 }}>
          WebDialer Source Code
        </h1>
        <p data-testid="webdialer-subtitle" style={{ marginTop: 16, fontSize: 18, color: "#94a3b8", lineHeight: 1.6 }}>
          The complete WebDialer application (PHP 8.2, MySQL, Asterisk PBX bridge) lives in the
          <code style={{ background: "#1e293b", padding: "2px 8px", borderRadius: 6, margin: "0 6px", color: "#f8fafc" }}>/app/web-dialer/</code>
          directory of this workspace.
        </p>

        <div style={{ marginTop: 40, padding: 24, background: "#111827", borderRadius: 12, border: "1px solid #1f2937" }} data-testid="webdialer-deploy-card">
          <h2 style={{ margin: 0, fontSize: 20, color: "#f8fafc" }}>Deploy to your Debian 12 server</h2>
          <p style={{ color: "#94a3b8", marginTop: 8, marginBottom: 16, fontSize: 14 }}>
            One-command installer sets up Asterisk, MySQL, PHP, Nginx, and the FastAPI bridge.
          </p>
          <pre style={{ background: "#020617", color: "#22d3ee", padding: 16, borderRadius: 8, overflowX: "auto", fontSize: 13, margin: 0 }} data-testid="webdialer-install-cmd">
{`# On a fresh Debian 12 host
sudo bash install.sh`}
          </pre>
        </div>

        <div style={{ marginTop: 32, display: "grid", gridTemplateColumns: "repeat(auto-fit, minmax(220px, 1fr))", gap: 16 }}>
          <div style={{ padding: 20, background: "#111827", borderRadius: 12, border: "1px solid #1f2937" }} data-testid="feature-frontend">
            <div style={{ color: "#38bdf8", fontSize: 12, textTransform: "uppercase" }}>Frontend</div>
            <div style={{ marginTop: 6, color: "#f8fafc", fontWeight: 600 }}>Vanilla HTML/CSS/JS</div>
            <div style={{ marginTop: 4, color: "#64748b", fontSize: 13 }}>Custom dark/light themes</div>
          </div>
          <div style={{ padding: 20, background: "#111827", borderRadius: 12, border: "1px solid #1f2937" }} data-testid="feature-backend">
            <div style={{ color: "#38bdf8", fontSize: 12, textTransform: "uppercase" }}>Backend</div>
            <div style={{ marginTop: 6, color: "#f8fafc", fontWeight: 600 }}>PHP 8.2 + MySQL</div>
            <div style={{ marginTop: 4, color: "#64748b", fontSize: 13 }}>Session auth, REST APIs</div>
          </div>
          <div style={{ padding: 20, background: "#111827", borderRadius: 12, border: "1px solid #1f2937" }} data-testid="feature-telephony">
            <div style={{ color: "#38bdf8", fontSize: 12, textTransform: "uppercase" }}>Telephony</div>
            <div style={{ marginTop: 6, color: "#f8fafc", fontWeight: 600 }}>Asterisk PBX</div>
            <div style={{ marginTop: 4, color: "#64748b", fontSize: 13 }}>Python FastAPI bridge</div>
          </div>
        </div>

        <p style={{ marginTop: 40, color: "#475569", fontSize: 13 }} data-testid="webdialer-footnote">
          Note: this preview shell is intentionally minimal &mdash; the WebDialer app itself runs on your Debian 12 server after install.
        </p>
      </div>
    </div>
  );
}

export default App;

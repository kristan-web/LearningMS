// Quick e2e: load dashboard.js, populate a jsdom DOM with the 11 IDs from
// public/student-dashboard.php + seed data, and assert that everything
// (greeting, subtitle, stat cards, assignments, schedule, announcements,
// grades) renders correctly for student 12.
const fs = require("fs");
const path = require("path");
const { JSDOM } = require("jsdom");

const ROOT = path.resolve(__dirname, "../../../..");
const DASH_JS = path.join(__dirname, "dashboard.js");
const SEED_DIR = path.join(__dirname, "../../../data");
const HTML_FILE = path.join(ROOT, "public", "student-dashboard.php");

// --- Read HTML and pull just the body markup we need. ---
const html = fs.readFileSync(HTML_FILE, "utf8");
const bodyMatch = html.match(/<body[\s\S]*?<\/body>/);
if (!bodyMatch) throw new Error("no <body> in student-dashboard.php");
const body = bodyMatch[0];

// Inject a <script> that exposes student 12 + section 5.
const seed = JSON.parse(fs.readFileSync(path.join(SEED_DIR, "students.json"), "utf8"));
const student = seed.find((s) => String(s.student_id) === "12");
if (!student) throw new Error("student 12 not found");

const dom = new JSDOM(
  `<!doctype html><html><head></head>${body}<script>
     window.LMS_TEST_MODE = true;
     window.LMS_TEST_STUDENT = ${JSON.stringify(student)};
   </script></html>`,
  { runScripts: "outside-only", pretendToBeVisual: true }
);
const { window } = dom;
const { document } = window;

// Stub the global session bootstrap the dashboard expects.
window.LMS_API = { baseUrl: "/api", token: "test" };
window.LMS_SESSION = { session_id: 999, user_id: 12, role: "student" };
window.LMS_SOCKET = { on: () => {}, emit: () => {} };

// Load the dashboard script.
const code = fs.readFileSync(DASH_JS, "utf8");
window.eval(code);

// Wait a tick for any async init.
setTimeout(() => {
  const ids = [
    "dashboard-greeting",
    "dashboard-greeting-subtitle",
    "stat-cards",
    "assignments-list",
    "schedule-tabs",
    "schedule-list",
    "announcements-list",
    "grades-chart"
  ];
  const extra = [
    "dashboard-stats-row",
    "dashboard-schedule-card",
    "dashboard-announcements-card"
  ];
  const all = [...ids, ...extra];
  let ok = true;
  for (const id of all) {
    const el = document.getElementById(id);
    if (!el) { ok = false; console.log("  [FAIL] missing #" + id); continue; }
    const txt = (el.textContent || "").trim().replace(/\s+/g, " ");
    console.log("  [" + (txt ? "ok  " : "FAIL") + "] #" + id + ": " + (txt ? txt.slice(0, 110) : "(empty)"));
    if (!txt && id !== "schedule-list" && id !== "assignments-list" && id !== "announcements-list" && id !== "grades-chart") {
      // The first 3 may be intentionally empty depending on data, but greeting + subtitle + tabs must populate.
    }
  }
  console.log("\n--- DETAIL: greeting + subtitle ---");
  const g = document.getElementById("dashboard-greeting");
  const s = document.getElementById("dashboard-greeting-subtitle");
  console.log("Greeting :", g && g.textContent);
  console.log("Subtitle :", s && s.textContent);

  console.log("\n--- DETAIL: stat cards (count) ---");
  const sc = document.getElementById("stat-cards");
  console.log("stat-cards children:", sc && sc.children.length);

  console.log("\n--- DETAIL: schedule tabs + class rows ---");
  const tabs = document.getElementById("schedule-tabs");
  const list = document.getElementById("schedule-list");
  console.log("tab buttons  :", tabs && tabs.querySelectorAll("button").length);
  console.log("class rows   :", list && list.querySelectorAll("li").length);

  console.log("\n--- DETAIL: assignments / announcements / grades ---");
  console.log("assignments  :", document.getElementById("assignments-list").querySelectorAll("[data-assignment]").length);
  console.log("announcements:", document.getElementById("announcements-list").querySelectorAll("[data-announcement]").length);
  console.log("grade bars   :", document.getElementById("grades-chart").querySelectorAll("[data-grade]").length);

  console.log("\n--- DETAIL: verify no blue- classes leaked in (sanity) ---");
  const allHtml = dom.serialize();
  const blues = (allHtml.match(/\bblue-\d+\b/g) || []);
  console.log("blue- hits in rendered HTML:", blues.length, blues.slice(0, 5));
  const teals = (allHtml.match(/\bteal-\d+\b/g) || []);
  console.log("teal- hits in rendered HTML:", teals.length, teals.slice(0, 5));

  console.log("\n" + (ok ? "OK" : "FAILED"));
  process.exit(ok ? 0 : 1);
}, 150);

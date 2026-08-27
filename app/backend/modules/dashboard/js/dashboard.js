/* =============================================================================
 * dashboard.js
 *
 * Front-end controller for the Student Dashboard (public/student-dashboard.php).
 *
 * Loads temporary JSON seed files under ./seeds/ that mirror the
 * production MySQL schema (database/schema.sql), identifies the currently
 * logged-in student from seeds/current_session.json, and renders:
 *   - Greeting ("Good morning, <First name>")
 *   - Stat cards (Assignments Due, Quizzes This Week, Current GPA,
 *     Announcements)
 *   - Assignments list (upcoming)
 *   - Schedule mini-calendar (Mon-Fri) + class list
 *   - Announcements list
 *   - Grades bar chart (per subject)
 *
 * Pure vanilla JS, no dependencies. Follows the conventions in
 * app/backend/modules/accounts/js/account-validation.js.
 * ========================================================================== */

(function () {
  "use strict";

  // Base URL of the dashboard's seed JSON directory.
  const SEEDS_BASE = "/LearningMS/app/backend/modules/dashboard/js/seeds";

  // Only the seeds actually rendered on the dashboard.
  const SEED_FILES = {
    currentSession: "current_session.json",
    students:       "students.json",
    enrollments:    "enrollments.json",
    classSections:  "class_sections.json",
    schedules:      "schedules.json",
    subjects:       "subjects.json",
    assignments:    "assignments.json",
    quizzes:        "quizzes.json",
    announcements:  "announcements.json",
    finalGrades:    "final_grades.json"
  };

  // Hour-based greeting, e.g. "Good morning" / "Good afternoon".
  function greetingForHour(hour) {
    if (hour < 5)  return "Good evening";
    if (hour < 12) return "Good morning";
    if (hour < 18) return "Good afternoon";
    return "Good evening";
  }

  // ---- HTTP / loading ----------------------------------------------------

  async function fetchJson(url) {
    const response = await fetch(url, { cache: "no-store" });
    if (!response.ok) {
      throw new Error("HTTP " + response.status + " for " + url);
    }
    return response.json();
  }

  // Fetch every seed in parallel. Missing files become [] (or null for
  // currentSession) so a partial seed set never breaks the page.
  async function loadAllSeeds() {
    const entries = Object.entries(SEED_FILES);
    const results = await Promise.allSettled(
      entries.map(([, file]) => fetchJson(SEEDS_BASE + "/" + file))
    );
    const data = {};
    entries.forEach(([key], index) => {
      const result = results[index];
      if (result.status === "fulfilled") {
        data[key] = result.value;
      } else {
        console.warn("[dashboard] could not load", SEED_FILES[key],
                     "-", result.reason && result.reason.message);
        data[key] = (key === "currentSession") ? null : [];
      }
    });
    return data;
  }

  // ---- Lookup helpers ----------------------------------------------------

  function findStudent(students, id) {
    if (!Array.isArray(students)) return null;
    return students.find((s) => String(s.student_id) === String(id)) || null;
  }

  function findEnrollmentForStudent(enrollments, id) {
    if (!Array.isArray(enrollments)) return null;
    return enrollments.find((e) => String(e.student_id) === String(id)) || null;
  }

  function findSection(sections, id) {
    if (!Array.isArray(sections) || id == null) return null;
    return sections.find((s) => String(s.section_id) === String(id)) || null;
  }

  function findSubject(subjects, id) {
    if (!Array.isArray(subjects) || id == null) return null;
    return subjects.find((s) => String(s.subject_id) === String(id)) || null;
  }

  // ---- Time helpers ------------------------------------------------------

  // "HH:MM" or "HH:MM:SS" -> {h, m}. Returns null when unparsable.
  function parseTime(value) {
    if (typeof value !== "string") return null;
    const parts = value.split(":");
    if (parts.length < 2) return null;
    const h = parseInt(parts[0], 10);
    const m = parseInt(parts[1], 10);
    if (Number.isNaN(h) || Number.isNaN(m)) return null;
    return { h: h, m: m };
  }

  // "13:30:00" -> "1:30 PM".
  function formatTime12h(value) {
    const t = parseTime(value);
    if (!t) return "";
    const period = t.h >= 12 ? "PM" : "AM";
    let hour = t.h % 12;
    if (hour === 0) hour = 12;
    const minutes = String(t.m).padStart(2, "0");
    return hour + ":" + minutes + " " + period;
  }

  // Relative due date string ("Due tomorrow", "Due in 3 days", "Overdue").
  function describeDueDate(dueDate, now) {
    if (!dueDate) return "";
    const due = new Date(dueDate.replace(" ", "T"));
    if (Number.isNaN(due.getTime())) return "Due " + dueDate;
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    const dueDay = new Date(due.getFullYear(), due.getMonth(), due.getDate());
    const diffDays = Math.round((dueDay - today) / (1000 * 60 * 60 * 24));
    if (diffDays < 0)   return "Overdue";
    if (diffDays === 0) return "Due today";
    if (diffDays === 1) return "Due tomorrow";
    if (diffDays < 7)   return "Due in " + diffDays + " days";
    return "Due " + due.toLocaleDateString(undefined, { month: "short", day: "numeric" });
  }

  // DepEd SHS 1.00-5.00 GPA scale. 70 cutoff mirrors the remarks column
  // in the final_grades table ("Passed" / "Failed").
  function gradeToGpa(rating) {
    if (rating == null) return null;
    const r = parseFloat(rating);
    if (Number.isNaN(r)) return null;
    if (r >= 90) return 1.0;
    if (r >= 85) return 1.5;
    if (r >= 80) return 2.0;
    if (r >= 75) return 2.5;
    if (r >= 70) return 3.0;
    return 5.0;
  }

  // ---- DOM helpers -------------------------------------------------------

  // Tiny createElement. `props` keys: class, text, html, attrs, or any DOM
  // property. `onXxx` keys register event listeners.
  function el(tag, props, children) {
    const node = document.createElement(tag);
    if (props) {
      Object.keys(props).forEach((key) => {
        const v = props[key];
        if (key === "class") node.className = v;
        else if (key === "text") node.textContent = v;
        else if (key === "html") node.innerHTML = v;
        else if (key.startsWith("on") && typeof v === "function") {
          node.addEventListener(key.slice(2).toLowerCase(), v);
        } else if (key === "attrs" && v) {
          Object.keys(v).forEach((a) => node.setAttribute(a, v[a]));
        } else {
          node[key] = v;
        }
      });
    }
    if (children != null) {
      const list = Array.isArray(children) ? children : [children];
      list.forEach((child) => {
        if (child == null) return;
        if (typeof child === "string") {
          node.appendChild(document.createTextNode(child));
        } else {
          node.appendChild(child);
        }
      });
    }
    return node;
  }

  function emptyState(message) {
    return el("li",
      { class: "text-xs text-gray-400 italic px-2 py-3" },
      message
    );
  }

  // ---- Section: Greeting --------------------------------------------------

  // Best-effort read of the "lms.studentSession" blob that the login
  // form stashed in sessionStorage right after a successful
  // authentication. Returns null when the user reached the dashboard
  // directly (no login) or when sessionStorage is unavailable; the
  // caller's fallback chain then uses the seed-driven student / session
  // objects instead. Wrapped in try/catch so a malformed JSON value
  // (e.g. a leftover blob from a different LMS version) can never break
  // page rendering.
  function readLoggedInStudent() {
    try {
      const raw = window.sessionStorage &&
                  window.sessionStorage.getItem("lms.studentSession");
      if (!raw) { return null; }
      const parsed = JSON.parse(raw);
      return (parsed && typeof parsed === "object") ? parsed : null;
    } catch (storageError) {
      return null;
    }
  }

  // Personalizes the H2 greeting (e.g. "Good morning, Kristan") and the
  // subtitle line (e.g. "ABM 11-A • Here's what's happening with your
  // courses today") using the logged-in student.
  function renderGreeting(student, session, section) {
    const greetingEl = document.getElementById("dashboard-greeting");
    if (!greetingEl) return;
    const hour = new Date().getHours();
    const greeting = greetingForHour(hour);

    // Choose the most specific name we can find. The freshly-logged-in
    // user's real first name (placed in sessionStorage by the login
    // form's onLoginSuccess handler) wins over the seed. Fall back
    // through: logged-in first_name -> student.first_name ->
    // student.last_name -> username -> "Student".
    const loggedIn = readLoggedInStudent();
    let displayName = "";
    if (loggedIn && loggedIn.first_name) {
      displayName = loggedIn.first_name;
    } else if (student && student.first_name) {
      displayName = student.first_name;
    } else if (student && student.last_name) {
      displayName = student.last_name;
    } else if (loggedIn && loggedIn.username) {
      displayName = loggedIn.username.split(/[._\-]/)[0] || loggedIn.username;
    } else if (session && session.username) {
      displayName = session.username.split(/[._\-]/)[0] || session.username;
    } else {
      displayName = "Student";
    }
    const capitalized = displayName.charAt(0).toUpperCase() + displayName.slice(1);

    greetingEl.textContent = greeting + ", " + capitalized + " \u{1F44B}";

    // Subtitle: section name + static line.
    const subtitleEl = document.getElementById("dashboard-greeting-subtitle");
    if (subtitleEl) {
      const sectionName = (section && section.section_name) || (student && student.section_name) || "";
      const intro = "Here's what's happening with your courses today";
      subtitleEl.textContent = sectionName
        ? sectionName + " \u2022 " + intro
        : intro;
    }
  }

  // ---- Section: Stat cards -----------------------------------------------

  function renderStatCards(data, ctx) {
    const now = ctx.now;

    // Assignments due = the student's section assignments whose due date
    // is now or in the future.
    const assignmentsDue = data.assignments.filter((a) => {
      if (String(a.section_id) !== String(ctx.sectionId)) return false;
      if (!a.due_date) return false;
      const due = new Date(a.due_date.replace(" ", "T"));
      return due >= now;
    }).length;

    // Quizzes this week: created within the current Sun-Sat week, scoped
    // to the student's section.
    const weekStart = new Date(now);
    weekStart.setHours(0, 0, 0, 0);
    weekStart.setDate(weekStart.getDate() - weekStart.getDay());
    const weekEnd = new Date(weekStart);
    weekEnd.setDate(weekEnd.getDate() + 7);
    const quizzesThisWeek = data.quizzes.filter((q) => {
      if (String(q.section_id) !== String(ctx.sectionId)) return false;
      if (!q.created_at) return false;
      const c = new Date(q.created_at.replace(" ", "T"));
      return c >= weekStart && c < weekEnd;
    }).length;

    // Current GPA = average of this student's final grades on the
    // 1.00-5.00 scale.
    const myGrades = data.finalGrades.filter((g) =>
      String(g.student_id) === String(ctx.studentId)
    );
    let gpaText = "\u2014";
    if (myGrades.length > 0) {
      const gpas = myGrades
        .map((g) => gradeToGpa(g.final_rating))
        .filter((n) => n != null);
      if (gpas.length > 0) {
        const avg = gpas.reduce((a, b) => a + b, 0) / gpas.length;
        gpaText = avg.toFixed(2);
      }
    }

    // Announcements: school-wide (section_id null) + the student's section.
    const announcements = data.announcements.filter((a) => {
      return a.section_id == null || String(a.section_id) === String(ctx.sectionId);
    }).length;

    setStat("stat-assignments-due", String(assignmentsDue));
    setStat("stat-quizzes-week",    String(quizzesThisWeek));
    setStat("stat-gpa",             gpaText);
    setStat("stat-announcements",   String(announcements));
  }

  function setStat(elementId, value) {
    const node = document.getElementById(elementId);
    if (node) node.textContent = value;
  }

  // ---- Section: Assignments list -----------------------------------------

  function renderAssignments(data, ctx) {
    const list = document.getElementById("dashboard-assignments-list");
    if (!list) return;
    list.innerHTML = "";

    const items = data.assignments
      .filter((a) => String(a.section_id) === String(ctx.sectionId))
      .slice()
      .sort((a, b) => new Date(a.due_date) - new Date(b.due_date))
      .slice(0, 5);

    if (items.length === 0) {
      list.appendChild(emptyState("No assignments yet."));
      return;
    }

    items.forEach((assignment) => {
      const subject = findSubject(data.subjects, assignment.subject_id);
      list.appendChild(
        el("li",
          { class: "flex items-center gap-2 p-2 bg-gray-50 rounded-md" },
          [
            el("span", { class: "w-1.5 h-1.5 bg-gray-400 rounded-full flex-shrink-0" }),
            el("div", { class: "flex-1 min-w-0" }, [
              el("p", { class: "text-xs font-medium text-gray-900 truncate" },
                assignment.title),
              el("p", { class: "text-[10px] text-gray-500 truncate" },
                (subject ? subject.subject_code : "\u2014") + " \u2022 " +
                describeDueDate(assignment.due_date, ctx.now))
            ]),
            el("span", {
              class: "text-[10px] font-semibold text-gray-500 bg-white border border-gray-200 rounded px-2 py-1"
            }, assignment.max_score != null ? assignment.max_score + " pts" : "\u2014")
          ]
        )
      );
    });
  }

  // ---- Section: Schedule (Mon-Fri tabs + list) ---------------------------

  // Map JS getDay() (0=Sun..6=Sat) to the day names we use for filtering.
  const SCHEDULE_DAYS = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];
  const SCHEDULE_SHORT = {
    Monday: "Mon", Tuesday: "Tue", Wednesday: "Wed",
    Thursday: "Thu", Friday: "Fri"
  };
  // JS getDay() (0=Sun..6=Sat) -> matching full day name.
  const JS_DAY_TO_NAME = [
    "Sunday", "Monday", "Tuesday", "Wednesday",
    "Thursday", "Friday", "Saturday"
  ];

  let scheduleState = { activeDay: "Monday" };

  function renderSchedule(data, ctx) {
    const daysContainer = document.getElementById("dashboard-schedule-days");
    const listContainer = document.getElementById("dashboard-schedule-list");
    if (!daysContainer || !listContainer) return;

    // Build the set of days that actually have classes for the student.
    const sectionSchedules = data.schedules.filter((s) =>
      String(s.section_id) === String(ctx.sectionId)
    );
    const daysWithClasses = new Set(sectionSchedules.map((s) => s.day_of_week));

    // Default to today if today has classes, otherwise the next weekday
    // that does, otherwise Mon.
    const todayKey = JS_DAY_TO_NAME[new Date().getDay()];
    if (daysWithClasses.has(todayKey)) {
      scheduleState.activeDay = todayKey;
    } else {
      const found = SCHEDULE_DAYS.find((d) => daysWithClasses.has(d));
      scheduleState.activeDay = found || "Monday";
    }

    // Render the day tab buttons.
    daysContainer.innerHTML = "";
    SCHEDULE_DAYS.forEach((day) => {
      const isActive = day === scheduleState.activeDay;
      const hasClass = daysWithClasses.has(day);
      const button = el("button", {
        type: "button",
        class: "flex-1 py-1 text-xs font-semibold rounded-md transition " +
               (isActive
                 ? "bg-teal-600 text-white"
                 : (hasClass
                     ? "text-gray-700 hover:bg-gray-100"
                     : "text-gray-300 cursor-default")),
        onClick: function () {
          if (!hasClass) return;
          scheduleState.activeDay = day;
          renderSchedule(data, ctx);
        }
      }, SCHEDULE_SHORT[day] || day);
      daysContainer.appendChild(button);
    });

    // Render the class list for the active day, sorted by start time.
    const todays = sectionSchedules
      .filter((s) => s.day_of_week === scheduleState.activeDay)
      .slice()
      .sort((a, b) => {
        const ta = parseTime(a.start_time);
        const tb = parseTime(b.start_time);
        if (!ta || !tb) return 0;
        return (ta.h * 60 + ta.m) - (tb.h * 60 + tb.m);
      });

    listContainer.innerHTML = "";
    if (todays.length === 0) {
      listContainer.appendChild(emptyState("No classes on " + scheduleState.activeDay + "."));
      return;
    }

    todays.forEach((sched) => {
      const subject = findSubject(data.subjects, sched.subject_id);
      // The schedules table has room_id; there's no rooms seed, so we
      // show "Room #N" as a stand-in.
      const room = sched.room_id != null
        ? "Room #" + sched.room_id
        : (sched.room || "TBA");
      const range = formatTime12h(sched.start_time) +
                    " \u2013 " +
                    formatTime12h(sched.end_time);
      const subjectName = subject
        ? (subject.subject_name || subject.subject_title || subject.subject_code)
        : "Class";
      const subjectCode = subject ? subject.subject_code : "\u2014";
      listContainer.appendChild(
        el("li",
          { class: "flex items-start gap-3 p-2 bg-gray-50 rounded-md" },
          [
            el("div", {
              class: "w-14 flex-shrink-0 text-center bg-teal-50 text-teal-700 rounded px-1 py-0.5"
            }, [
              el("p", { class: "text-[10px] font-semibold leading-tight" },
                formatTime12h(sched.start_time) || ""),
              el("p", { class: "text-[9px] leading-tight" },
                formatTime12h(sched.end_time) || "")
            ]),
            el("div", { class: "flex-1 min-w-0" }, [
              el("p", { class: "text-xs font-medium text-gray-900 truncate" },
                subjectName),
              el("p", { class: "text-[10px] text-gray-500 truncate" },
                subjectCode + " \u2022 " + room + " \u2022 " + range)
            ])
          ]
        )
      );
    });
  }

  // ---- Section: Announcements list ---------------------------------------

  function renderAnnouncements(data, ctx) {
    const list = document.getElementById("dashboard-announcements-list");
    if (!list) return;
    list.innerHTML = "";

    const items = data.announcements
      .filter((a) => a.section_id == null ||
                     String(a.section_id) === String(ctx.sectionId))
      .slice()
      .sort((a, b) => new Date(b.posted_at) - new Date(a.posted_at))
      .slice(0, 5);

    if (items.length === 0) {
      list.appendChild(emptyState("No announcements yet."));
      return;
    }

    items.forEach((ann) => {
      const posted = ann.posted_at
        ? new Date(ann.posted_at.replace(" ", "T"))
        : null;
      const dateText = posted && !Number.isNaN(posted.getTime())
        ? posted.toLocaleDateString(undefined, { month: "short", day: "numeric" })
        : "";
      const isSchoolWide = ann.section_id == null;
      list.appendChild(
        el("li",
          { class: "p-2 bg-gray-50 rounded-md" },
          [
            el("div", { class: "flex items-center gap-2" }, [
              el("p", { class: "text-xs font-semibold text-gray-900 flex-1 truncate" },
                ann.title),
              isSchoolWide
                ? el("span", {
                    class: "text-[9px] font-bold uppercase tracking-wide text-teal-700 bg-teal-50 px-1.5 py-0.5 rounded"
                  }, "All")
                : null,
              dateText
                ? el("span", { class: "text-[10px] text-gray-500" }, dateText)
                : null
            ]),
            ann.body
              ? el("p", { class: "text-[10px] text-gray-600 mt-1 line-clamp-2" }, ann.body)
              : null
          ]
        )
      );
    });
  }

  // ---- Section: Grades chart ---------------------------------------------

  // Renders a small horizontal bar chart of the student's final grades
  // per subject. Width of each bar is rating/100. Subject codes are
  // pulled from subjects.json. Container is the grid element on the
  // page; the chart is rendered as a series of stacked rows so it
  // works without any external charting library.
  function renderGrades(data, ctx) {
    const grid = document.getElementById("dashboard-grades-chart");
    if (!grid) return;
    grid.innerHTML = "";

    const myGrades = data.finalGrades.filter((g) =>
      String(g.student_id) === String(ctx.studentId)
    );

    if (myGrades.length === 0) {
      grid.appendChild(
        el("p",
          { class: "col-span-2 text-xs text-gray-400 italic px-2 py-3" },
          "No grades yet.")
      );
      return;
    }

    myGrades.forEach((grade) => {
      const subject = findSubject(data.subjects, grade.subject_id);
      const rating = parseFloat(grade.final_rating);
      const pct = Number.isNaN(rating) ? 0 : Math.max(0, Math.min(100, rating));
      const passed = (grade.remarks || "").toLowerCase() === "passed" || rating >= 70;
      const barColor = passed ? "bg-emerald-500" : "bg-red-500";
      const label = subject ? subject.subject_code : "Subject " + grade.subject_id;
      const title = subject ? subject.subject_title : "";

      grid.appendChild(
        el("div",
          { class: "flex flex-col gap-1" },
          [
            el("div", { class: "flex items-center justify-between" }, [
              el("span", {
                class: "text-[10px] font-semibold text-gray-700 truncate",
                attrs: { title: title }
              }, label),
              el("span", {
                class: "text-[10px] font-semibold " +
                       (passed ? "text-emerald-700" : "text-red-700")
              }, Number.isNaN(rating) ? "\u2014" : rating.toFixed(0))
            ]),
            el("div", { class: "w-full h-1.5 bg-gray-200 rounded-full overflow-hidden" }, [
              el("div",
                {
                  class: "h-full " + barColor + " rounded-full transition-all",
                  style: "width: " + pct + "%"
                })
            ])
          ]
        )
      );
    });
  }

  // ---- Boot --------------------------------------------------------------

  async function boot() {
    // Bail out early if the page doesn't actually have the dashboard
    // greeting element, so this script is safe to load on every page.
    if (!document.getElementById("dashboard-greeting")) return;

    const data = await loadAllSeeds();

    // 1. Identify the logged-in student.
    const session  = data.currentSession;
    const studentId = session && session.student_id;
    if (!studentId) {
      console.warn("[dashboard] no student_id in current_session.json; " +
                   "greeting will use a generic fallback.");
      renderGreeting(null, session, null);
      return;
    }

    const student     = findStudent(data.students, studentId);
    const enrollment  = findEnrollmentForStudent(data.enrollments, studentId);
    const sectionId   = enrollment ? enrollment.section_id : null;
    const section     = findSection(data.classSections, sectionId);

    // 2. Context shared with every renderer.
    const ctx = {
      now: new Date(),
      studentId: studentId,
      sectionId: sectionId
    };

    // 3. Render all sections.
    renderGreeting(student, session, section);
    renderStatCards(data, ctx);
    renderAssignments(data, ctx);
    renderSchedule(data, ctx);
    renderAnnouncements(data, ctx);
    renderGrades(data, ctx);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot);
  } else {
    boot();
  }
})();

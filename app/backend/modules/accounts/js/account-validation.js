/* =============================================================================
 * account-validation.js
 *
 * Shared front-end validation for the Accounts module. Drives both:
 *   - public/student-login.php  (login form: identifier + password)
 *   - public/student-signup.php (registration form: 12 personal + account fields)
 *
 * Pure vanilla JS, no dependencies. The same DOM-contract pattern is used on
 * every page; the script auto-detects which page it's on and wires only the
 * relevant form(s). All field styling (red/green borders, error text, summary
 * alert) is shared so login and signup look and feel identical.
 *
 * Element ID contract (every form exposes these per field):
 *   - <formId>Form        <form>
 *   - <formId>Alert       <div role="alert"> at the top of the form
 *   - <formId>Button      <button type="submit">
 *   - <fieldId>Input      <input/select> for the value
 *   - <fieldId>Error      <p>      error message under the field
 *   - <fieldId>Group      <div>    optional wrapper (used by select+label layout)
 *
 *   Examples for login:        loginForm, loginAlert, loginButton,
 *                              loginIdentifierInput, loginIdentifierError
 *
 *   Examples for signup:       signupForm, signupAlert, signupButton,
 *                              signupFirstNameInput, signupFirstNameError, ...
 * ========================================================================== */

(function () {
    "use strict";

    // ---- Constants ---------------------------------------------------------

    // Base URL of the Accounts module's JSON API. Both forms POST to the same
    // controller; only the `action` query parameter changes.
    const ACCOUNTS_API =
        "/LearningMS/app/backend/modules/accounts/controller/AccountController.php";

    const ENDPOINTS = {
        login:          ACCOUNTS_API + "?action=login",
        register:       ACCOUNTS_API + "?action=register",
        checkUsername:  ACCOUNTS_API + "?action=check_username"
    };

    // The same DOM-contract is used by every wiring helper:
    //   <formId>Form    - the <form> element
    //   <formId>Alert   - the top-of-form summary alert
    //   <formId>Button  - the submit button
    //   <fieldId>Input  - the input/select for a field
    //   <fieldId>Error  - the inline <p> under a field
    // Forms opt in by declaring a <formId> on its data attribute (see wirings).

    // ---- Validation rules ---------------------------------------------------
    // (Note: the actual password complexity policy is intentionally NOT shown
    // to the end user as a bullet list. We only report the specific rule a
    // user is currently violating, e.g. "Password must include a number.")

    const RULES = {
        // Identifier may be either a valid email or a 3-32 char username
        // using lowercase letters, digits, dot, underscore or dash.
        identifier: {
            minLength: 3,
            maxLength: 32,
            usernamePattern: /^[a-z0-9._-]+$/,
            // Pragmatic email regex — covers virtually all real-world cases
            // without trying to fully implement RFC 5322.
            emailPattern: /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/
        },
        // Password: 8-128 chars, at least one lower, upper, digit and symbol.
        password: {
            minLength: 8,
            maxLength: 128,
            hasLower:   /[a-z]/,
            hasUpper:   /[A-Z]/,
            hasDigit:   /[0-9]/,
            hasSymbol:  /[^A-Za-z0-9]/
        },
        // Personal-info fields, used by the signup form.
        name: {
            minLength: 1,
            maxLength: 50,
            // Allow letters (including accented), spaces, hyphen, apostrophe, period.
            pattern: /^[\p{L}\s.'-]+$/u
        },
        email: {
            maxLength: 100,
            pattern: /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/
        },
        phone: {
            // Accept digits, spaces, +, -, parentheses. 7-20 visible chars.
            minLength: 7,
            maxLength: 25,
            pattern: /^[+0-9()\-\s]+$/
        },
        address: {
            minLength: 5,
            maxLength: 255
        },
        // ISO date "YYYY-MM-DD". Real validity is also checked via Date.parse.
        birthdate: {
            minAge: 10,
            maxAge: 100
        }
    };


    // ---- Validation functions ----------------------------------------------
    // Each validator is a pure function that takes the raw value and a
    // `required` flag, and returns either an error message string or null.

    function validateIdentifierValue(raw, required) {
        const value = (raw || "").trim();
        if (value.length === 0) {
            return required
                ? "Please enter your email or username."
                : null;
        }
        if (value.length > RULES.identifier.maxLength) {
            return "Email or username is too long (max "
                 + RULES.identifier.maxLength + " characters).";
        }
        const looksLikeEmail = value.indexOf("@") !== -1;
        if (looksLikeEmail) {
            return RULES.identifier.emailPattern.test(value)
                ? null
                : "Please enter a valid email address.";
        }
        if (value.length < RULES.identifier.minLength) {
            return "Username must be at least "
                 + RULES.identifier.minLength + " characters.";
        }
        return RULES.identifier.usernamePattern.test(value)
            ? null
            : "Username may only contain letters, numbers, dot, underscore or dash.";
    }

    function validatePasswordValue(raw, required) {
        const value = raw || "";
        if (value.length === 0) {
            return required
                ? "Please enter your password."
                : null;
        }
        if (value.length < RULES.password.minLength) {
            return "Password must be at least "
                 + RULES.password.minLength + " characters.";
        }
        if (value.length > RULES.password.maxLength) {
            return "Password is too long (max "
                 + RULES.password.maxLength + " characters).";
        }
        if (!RULES.password.hasLower.test(value)) {
            return "Password must include a lowercase letter.";
        }
        if (!RULES.password.hasUpper.test(value)) {
            return "Password must include an uppercase letter.";
        }
        if (!RULES.password.hasDigit.test(value)) {
            return "Password must include a number.";
        }
        if (!RULES.password.hasSymbol.test(value)) {
            return "Password must include a symbol (e.g. !, @, #, ?).";
        }
        return null;
    }

    function validateNameValue(raw, required) {
        const value = (raw || "").trim();
        if (value.length === 0) {
            return required ? "This field is required." : null;
        }
        if (value.length > RULES.name.maxLength) {
            return "Name is too long (max "
                 + RULES.name.maxLength + " characters).";
        }
        return RULES.name.pattern.test(value)
            ? null
            : "Name may only contain letters, spaces, period, apostrophe or hyphen.";
    }

    function validateEmailValue(raw, required) {
        const value = (raw || "").trim();
        if (value.length === 0) {
            return required ? "Email is required." : null;
        }
        if (value.length > RULES.email.maxLength) {
            return "Email is too long (max "
                 + RULES.email.maxLength + " characters).";
        }
        return RULES.email.pattern.test(value)
            ? null
            : "Please enter a valid email address.";
    }

    function validatePhoneValue(raw, required) {
        const value = (raw || "").trim();
        if (value.length === 0) {
            return required ? "Phone number is required." : null;
        }
        if (value.length > RULES.phone.maxLength) {
            return "Phone number is too long (max "
                 + RULES.phone.maxLength + " characters).";
        }
        const digits = value.replace(/\D/g, "");
        if (digits.length < 7) {
            return "Phone number must contain at least 7 digits.";
        }
        return RULES.phone.pattern.test(value)
            ? null
            : "Phone number may only contain digits, spaces, +, -, or parentheses.";
    }

    function validateAddressValue(raw, required) {
        const value = (raw || "").trim();
        if (value.length === 0) {
            return required ? "Address is required." : null;
        }
        if (value.length < RULES.address.minLength) {
            return "Address is too short.";
        }
        if (value.length > RULES.address.maxLength) {
            return "Address is too long (max "
                 + RULES.address.maxLength + " characters).";
        }
        return null;
    }

    function validateSelectValue(raw, required, fieldLabel) {
        const value = (raw || "").trim();
        if (value.length === 0) {
            return required ? "Please select a " + fieldLabel + "." : null;
        }
        return null;
    }

    function validateBirthdateValue(raw, required) {
        const value = (raw || "").trim();
        if (value.length === 0) {
            return required ? "Birthdate is required." : null;
        }
        if (!/^\d{4}-\d{2}-\d{2}$/.test(value)) {
            return "Please enter a valid date.";
        }
        const d = new Date(value + "T00:00:00");
        if (isNaN(d.getTime())) {
            return "Please enter a valid date.";
        }
        const today = new Date();
        let age = today.getFullYear() - d.getFullYear();
        const m = today.getMonth() - d.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < d.getDate())) {
            age--;
        }
        if (age < RULES.birthdate.minAge) {
            return "You must be at least "
                 + RULES.birthdate.minAge + " years old to register.";
        }
        if (age > RULES.birthdate.maxAge) {
            return "Please enter a valid birthdate.";
        }
        return null;
    }

    function validateConfirmValue(raw, passwordRaw) {
        const value = raw || "";
        if (value.length === 0) {
            return "Please confirm your password.";
        }
        return value === passwordRaw
            ? null
            : "Passwords do not match.";
    }


    // ---- UI feedback helpers -----------------------------------------------

    // Tailwind class groups we add/remove to mark a field as invalid/valid.
    // Kept in arrays so we can re-apply the base style cleanly.
    const BASE_INPUT_CLASSES = [
        "bg-white", "border", "border-gray-300", "rounded",
        "px-4", "py-2", "mb-3", "text-sm", "text-gray-900",
        "placeholder-gray-400", "focus:outline-none",
        "focus:border-gray-500", "focus:ring-1", "focus:ring-gray-400",
        "transition"
    ];
    const INVALID_CLASSES = [
        "border-red-500", "ring-1", "ring-red-300",
        "focus:border-red-500", "focus:ring-red-400"
    ];
    const VALID_CLASSES = [
        "border-green-500",
        "focus:border-green-500", "focus:ring-green-400"
    ];

    function resetFieldStyles(input) {
        input.classList.remove(
            "border-red-500", "ring-1", "ring-red-300",
            "focus:border-red-500", "focus:ring-red-400",
            "border-green-500",
            "focus:border-green-500", "focus:ring-green-400"
        );
        // Ensure the base look is intact (e.g. if the HTML forgot a class).
        BASE_INPUT_CLASSES.forEach(function (c) {
            if (!input.classList.contains(c)) {
                input.classList.add(c);
            }
        });
    }

    function markInvalid(input, errorEl, message) {
        resetFieldStyles(input);
        input.classList.add.apply(input.classList, INVALID_CLASSES);
        input.setAttribute("aria-invalid", "true");
        input.setAttribute("aria-describedby", errorEl.id || "");
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.classList.remove("hidden");
        }
    }

    function markValid(input, errorEl) {
        resetFieldStyles(input);
        input.classList.add.apply(input.classList, VALID_CLASSES);
        input.setAttribute("aria-invalid", "false");
        if (errorEl) {
            errorEl.textContent = "";
            errorEl.classList.add("hidden");
        }
    }

    function markNeutral(input, errorEl) {
        // Field has a value but hasn't been validated yet, OR it's empty
        // in live mode — show no error, no green border.
        resetFieldStyles(input);
        input.removeAttribute("aria-invalid");
        if (errorEl) {
            errorEl.textContent = "";
            errorEl.classList.add("hidden");
        }
    }


    // ---- Top-of-form alert --------------------------------------------------

    function showAlert(alertBox, messages, options) {
        if (!alertBox) { return; }
        if (!messages || messages.length === 0) {
            alertBox.classList.add("hidden");
            alertBox.innerHTML = "";
            return;
        }
        const title = (options && options.title)
            || "Please fix the following before continuing:";
        const list = messages.map(function (m) {
            return "<li>" + escapeHtml(m) + "</li>";
        }).join("");
        alertBox.innerHTML =
            "<div class=\"flex items-start\">"
          +   "<span class=\"font-semibold mr-2\">!</span>"
          +   "<div class=\"flex-1\">"
          +     "<p class=\"font-semibold mb-1\">" + escapeHtml(title) + "</p>"
          +     "<ul class=\"list-disc list-inside space-y-0.5\">" + list + "</ul>"
          +   "</div>"
          + "</div>";
        alertBox.classList.remove("hidden");
        // Bring the alert into view for keyboard / screen-reader users.
        alertBox.scrollIntoView({ behavior: "smooth", block: "nearest" });
    }

    function escapeHtml(s) {
        return String(s)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
    }

    // Hide the summary alert as soon as there are no longer any visible
    // field-level errors, so it doesn't linger after the user has fixed things.
    function hideAlertIfNoErrors(alertBox, errorEls) {
        if (!alertBox) { return; }
        const anyVisible = (errorEls || []).some(function (el) {
            return el && !el.classList.contains("hidden") && el.textContent;
        });
        if (!anyVisible) {
            alertBox.classList.add("hidden");
            alertBox.innerHTML = "";
        }
    }

    // ---- Submit + live validation wiring -----------------------------------

    // Debounce: avoid validating on every keystroke, but feel responsive.
    function debounce(fn, delay) {
        let t = null;
        return function () {
            const args = arguments;
            const ctx  = this;
            clearTimeout(t);
            t = setTimeout(function () { fn.apply(ctx, args); }, delay);
        };
    }

    // ---- Per-form context --------------------------------------------------

    /**
     * Build a context object for a single form on the page. Returns null if
     * the form, alert, or submit button is missing (caller can short-circuit).
     *
     * @param {string} formId   e.g. "login" -> looks up #loginForm, #loginAlert, #loginButton
     * @param {Array}  fields   array of { name, validate(raw) } describing each field
     * @param {string} fieldPrefix  e.g. "login" -> looks up #<prefix><Name>Input, #<prefix><Name>Error
     */
    function createFormContext(formId, fields, fieldPrefix) {
        const form      = document.getElementById(formId + "Form");
        const alertBox  = document.getElementById(formId + "Alert");
        const submitBtn = document.getElementById(formId + "Button");
        if (!form || !submitBtn) { return null; }

        const bound = fields.map(function (f) {
            const input = document.getElementById(fieldPrefix + f.name + "Input");
            const err   = document.getElementById(fieldPrefix + f.name + "Error");
            return { name: f.name, input: input, err: err, validate: f.validate };
        });

        // Every field must have a DOM input; otherwise the form is half-rendered
        // and we'd silently skip checks. Bail out so the wiring doesn't bind.
        for (let i = 0; i < bound.length; i++) {
            if (!bound[i].input) { return null; }
        }

        return { form: form, alertBox: alertBox, submitBtn: submitBtn, fields: bound };
    }

    /**
     * Run all validators as if the user clicked submit. Returns true on
     * success, false on failure (and surfaces messages in the alert).
     */
    function runFullValidation(ctx, options) {
        const messages = [];
        for (let i = 0; i < ctx.fields.length; i++) {
            const f = ctx.fields[i];
            const msg = f.validate(f.input.value, true);
            if (msg) {
                markInvalid(f.input, f.err, msg);
                messages.push(msg);
            } else {
                markValid(f.input, f.err);
            }
        }
        if (messages.length > 0) {
            showAlert(ctx.alertBox, messages, options);
            return false;
        }
        showAlert(ctx.alertBox, [], options);
        return true;
    }

    /**
     * Validate a single field "live" (not required-empty) and update its
     * visual state. Used as the body of a debounced input listener.
     */
    function onFieldLive(field, ctx) {
        const raw  = field.input.value || "";
        const msg  = field.validate(raw, false);
        const empty = (raw.trim ? raw.trim().length === 0 : raw.length === 0);
        if (empty) {
            markNeutral(field.input, field.err);
        } else if (msg) {
            markInvalid(field.input, field.err, msg);
        } else {
            markValid(field.input, field.err);
        }
        hideAlertIfNoErrors(ctx.alertBox, ctx.fields.map(function (f) { return f.err; }));
    }

    /**
     * Clear an individual field's inline error as soon as the user focuses
     * it again, so the red styling doesn't feel "stuck" while they edit.
     */
    function bindFocusClear(input, err) {
        input.addEventListener("focus", function () {
            if (err) { err.classList.add("hidden"); }
        });
    }



    // ---- Loading state on the submit button --------------------------------

    /**
     * Toggle a "loading" state on a submit button. Stores the original label
     * the first time it goes into loading so it can be restored afterwards.
     */
    function setLoading(submitBtn, isLoading, loadingText) {
        if (!submitBtn) { return; }
        if (isLoading) {
            if (!submitBtn.dataset.originalLabel) {
                submitBtn.dataset.originalLabel = submitBtn.textContent;
            }
            submitBtn.disabled = true;
            submitBtn.classList.add("opacity-70", "cursor-not-allowed");
            submitBtn.textContent = loadingText || "Working...";
        } else {
            submitBtn.disabled = false;
            submitBtn.classList.remove("opacity-70", "cursor-not-allowed");
            submitBtn.textContent = submitBtn.dataset.originalLabel || "Submit";
        }
    }

    /**
     * POST a urlencoded payload to the API and return a normalised
     * {ok, status, data} result, even when the server returns a non-JSON
     * body or the network call throws.
     */
    function postJson(url, payload) {
        return fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
                "Accept":       "application/json"
            },
            body: payload
        })
        .then(function (response) {
            return response.json().catch(function () {
                return {
                    success: false,
                    reason:  "bad_response",
                    errors:  ["Unexpected server response (status " + response.status + ")."]
                };
            }).then(function (data) {
                return { ok: response.ok, status: response.status, data: data };
            });
        })
        .catch(function () {
            return {
                ok: false,
                status: 0,
                data: {
                    success: false,
                    reason:  "network_error",
                    errors:  ["Network error. Please check your connection and try again."]
                }
            };
        });
    }

    // ---- Page wirings ------------------------------------------------------

    // Reusable "find a field on a context by short name" helper.
    function findField(ctx, name) {
        for (let i = 0; i < ctx.fields.length; i++) {
            if (ctx.fields[i].name === name) { return ctx.fields[i]; }
        }
        return null;
    }

    /**
     * Wire the login form. The page must contain #loginForm, #loginAlert,
     * #loginButton, #loginIdentifierInput, #loginIdentifierError,
     * #loginPasswordInput, #loginPasswordError. Returns true on success.
     */
    function wireLoginForm() {
        const ctx = createFormContext("login", [
            { name: "Identifier", validate: validateIdentifierValue },
            { name: "Password",   validate: validatePasswordValue }
        ], "login");
        if (!ctx) { return false; }

        const options = { title: "Please fix the following before logging in:" };
        const identifierField = findField(ctx, "Identifier");
        const passwordField   = findField(ctx, "Password");

        // Render a successful login response.
        function onLoginSuccess(data) {
            const reason  = (data && data.reason) || "ok";
            const message = (data && data.message) || "Login successful.";
            showAlert(ctx.alertBox, [message], options);
            if (reason === "must_change_password") {
                markInvalid(passwordField.input, passwordField.err, "Please change your temporary password first.");
            } else if (reason === "pending_verification") {
                markInvalid(identifierField.input, identifierField.err, "Please verify your email first.");
            }

            // On a clean "ok" login we persist a small session blob so the
            // next page (student-dashboard.php) can greet the user by their
            // real first name. The dashboard already has a seed-driven
            // fallback for users who reach it without logging in, so this
            // is purely additive. We intentionally skip the redirect for
            // must_change_password / pending_verification / inactive —
            // those flows need the user to do something else first.
            if (reason === "ok" && data && data.account) {
                persistStudentSession(data.account);
                window.location.assign("/LearningMS/public/student-dashboard.php");
            }
        }

        // ---- Student session bridge --------------------------------------
        // Save the bare minimum the dashboard needs to personalize the
        // greeting: the student_id, the legal first name (preferred) and
        // last name, and the username as a final fallback. We use
        // sessionStorage so the data is scoped to the current tab and
        // disappears when the browser is closed, which is appropriate
        // for a placeholder client-side "session" until the back-end
        // gets real session-cookie support.
        function persistStudentSession(account) {
            try {
                const session = {
                    student_id:  account.student_id || null,
                    user_id:     account.user_id    || null,
                    username:    account.username   || null,
                    first_name:  account.first_name || null,
                    middle_name: account.middle_name || null,
                    last_name:   account.last_name  || null,
                    saved_at:    new Date().toISOString()
                };
                window.sessionStorage.setItem(
                    "lms.studentSession",
                    JSON.stringify(session)
                );
            } catch (storageError) {
                // sessionStorage may be unavailable (e.g. private mode on
                // some browsers). The redirect still works; the dashboard
                // simply falls back to its seed-driven greeting.
            }
        }

        // Render a failed login response. We show the server's `errors` array
        // in the summary alert, and for known reasons we also flag the
        // relevant field so the user sees the red border + inline message.
        function onLoginFailure(data) {
            const reason   = (data && data.reason) || "unknown";
            const messages = (data && Array.isArray(data.errors) && data.errors.length > 0)
                ? data.errors
                : [(data && data.message) || "Login failed. Please try again."];

            showAlert(ctx.alertBox, messages, options);

            if (reason === "wrong_password" || reason === "locked" || reason === "same_password") {
                markInvalid(passwordField.input, passwordField.err, messages[0]);
            } else if (reason === "no_user" || reason === "missing_fields" || reason === "pending_verification") {
                markInvalid(identifierField.input, identifierField.err, messages[0]);
            }

            if (
                reason === "wrong_password" &&
                data && typeof data.attempts_remaining === "number" &&
                data.attempts_remaining >= 0
            ) {
                const remainingNote =
                    data.attempts_remaining === 0
                        ? messages[0] + " No attempts remaining."
                        : messages[0] + " " + data.attempts_remaining + " attempt(s) remaining.";
                markInvalid(passwordField.input, passwordField.err, remainingNote);
            }
        }

        ctx.form.addEventListener("submit", function (event) {
            event.preventDefault();
            if (!runFullValidation(ctx, options)) { return; }

            setLoading(ctx.submitBtn, true, "Logging in...");

            const payload = new URLSearchParams();
            payload.set("identifier", identifierField.input.value.trim());
            payload.set("password",   passwordField.input.value);

            postJson(ENDPOINTS.login, payload)
                .then(function (res) {
                    if (res.data && res.data.success) { onLoginSuccess(res.data); }
                    else { onLoginFailure(res.data); }
                })
                .finally(function () {
                    setLoading(ctx.submitBtn, false);
                });
        });

        // Live validation as the user types.
        ctx.fields.forEach(function (field) {
            field.input.addEventListener("input", debounce(function () {
                onFieldLive(field, ctx);
            }, 200));
            bindFocusClear(field.input, field.err);
        });

        return true;
    }

    /**
     * Wire the signup form. The page must contain #signupForm, #signupAlert,
     * #signupButton plus one pair of <name>Input / <name>Error elements for
     * every field listed in the `fieldDefs` array below.
     */
    function wireSignupForm() {
        const fieldDefs = [
            { name: "FirstName",  validate: validateNameValue },
            { name: "MiddleName", validate: function (raw, required) {
                return validateNameValue(raw, false);
            } },
            { name: "LastName",   validate: validateNameValue },
            { name: "Email",      validate: validateEmailValue },
            { name: "Phone",      validate: validatePhoneValue },
            { name: "Gender",     validate: function (raw, required) {
                return validateSelectValue(raw, required, "gender");
            } },
            { name: "Birthdate",  validate: validateBirthdateValue },
            { name: "Address",    validate: validateAddressValue },
            { name: "GradeLevel", validate: function (raw, required) {
                return validateSelectValue(raw, required, "grade level");
            } },
            { name: "Username",   validate: validateIdentifierValue },
            { name: "Password",   validate: validatePasswordValue },
            { name: "Confirm",    validate: function (raw) {
                // The `ctx` reference below is captured when the IIFE for
                // this wiring runs, so it's safe to use it here even though
                // it's declared later in the same function.
                const pwd = findField(ctx, "Password");
                return validateConfirmValue(raw, pwd ? pwd.input.value : "");
            } }
        ];

        const ctx = createFormContext("signup", fieldDefs, "signup");
        if (!ctx) { return false; }

        const options = { title: "Please fix the following before signing up:" };
        const usernameField = findField(ctx, "Username");
        const passwordField = findField(ctx, "Password");
        const emailField    = findField(ctx, "Email");

        // Map a server-side reason code to the most likely offending field.
        function flagByReason(data) {
            const reason = (data && data.reason) || "unknown";
            const msg    = (data && Array.isArray(data.errors) && data.errors[0])
                || (data && data.message) || "";
            if (reason === "duplicate_username" || reason === "duplicate") {
                markInvalid(usernameField.input, usernameField.err, msg);
            } else if (reason === "duplicate_email") {
                markInvalid(emailField.input, emailField.err, msg);
            } else if (reason === "weak_password") {
                markInvalid(passwordField.input, passwordField.err, msg);
            } else if (reason === "invalid_email") {
                markInvalid(emailField.input, emailField.err, msg);
            } else if (reason === "missing_fields") {
                runFullValidation(ctx, options);
            }
        }

        function onSignupSuccess(data) {
            const message = (data && data.message) ||
                "Account created. You can now log in.";
            showAlert(ctx.alertBox, [message], options);
            ctx.fields.forEach(function (f) { f.input.disabled = true; });
            ctx.submitBtn.disabled = true;
            setLoading(ctx.submitBtn, true, "Created");
        }

        function onSignupFailure(data) {
            const messages = (data && Array.isArray(data.errors) && data.errors.length > 0)
                ? data.errors
                : [(data && data.message) || "Registration failed. Please try again."];
            showAlert(ctx.alertBox, messages, options);
            flagByReason(data);
        }

        // When the password changes after the user has already typed a
        // confirmation, re-validate the confirmation so the match state stays
        // in sync without waiting for the user to type in the confirm field.
        passwordField.input.addEventListener("input", debounce(function () {
            const confirmField = findField(ctx, "Confirm");
            if (confirmField && (confirmField.input.value || "").length > 0) {
                onFieldLive(confirmField, ctx);
            }
        }, 200));

        ctx.form.addEventListener("submit", function (event) {
            event.preventDefault();
            if (!runFullValidation(ctx, options)) { return; }

            setLoading(ctx.submitBtn, true, "Creating account...");

            const payload = new URLSearchParams();
            payload.set("first_name",  findField(ctx, "FirstName").input.value.trim());
            payload.set("middle_name", findField(ctx, "MiddleName").input.value.trim());
            payload.set("last_name",   findField(ctx, "LastName").input.value.trim());
            payload.set("email",       findField(ctx, "Email").input.value.trim());
            payload.set("phone",       findField(ctx, "Phone").input.value.trim());
            payload.set("gender",      findField(ctx, "Gender").input.value);
            payload.set("birthdate",   findField(ctx, "Birthdate").input.value);
            payload.set("address",     findField(ctx, "Address").input.value.trim());
            payload.set("grade_level", findField(ctx, "GradeLevel").input.value);
            payload.set("username",    findField(ctx, "Username").input.value.trim());
            payload.set("password",    findField(ctx, "Password").input.value);

            postJson(ENDPOINTS.register, payload)
                .then(function (res) {
                    if (res.data && res.data.success) { onSignupSuccess(res.data); }
                    else { onSignupFailure(res.data); }
                })
                .finally(function () {
                    // If the request succeeded the button is now disabled
                    // permanently; if it failed, restore the button so the
                    // user can correct their input and retry.
                    if (!ctx.submitBtn.disabled) { return; }
                    if (ctx.submitBtn.textContent === "Created") { return; }
                    setLoading(ctx.submitBtn, false);
                });
        });

        // Live validation as the user types.
        ctx.fields.forEach(function (field) {
            field.input.addEventListener("input", debounce(function () {
                onFieldLive(field, ctx);
            }, 200));
            bindFocusClear(field.input, field.err);
        });

        return true;
    }

    // ---- Page router -------------------------------------------------------
    // The script auto-detects which page it's on by probing for the form's
    // DOM id. Each wiring short-circuits if its required elements are missing,
    // so it's safe to include this file on any page in the module.

    wireLoginForm();
    wireSignupForm();
})();



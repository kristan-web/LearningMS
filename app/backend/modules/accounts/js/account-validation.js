/* =============================================================================
 * account-validation.js
 *
 * Shared front-end validation for the Accounts module. Drives both:
 *   - public/student-login.php  (login form: identifier + password)
 *   - public/student-signup.php (registration form: Student + Teacher tabs)
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
 *   Examples for teacher:      teacherSignupForm, teacherSignupAlert, teacherSignupButton,
 *                              teacherSignupFirstNameInput, teacherSignupFirstNameError, ...
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
        registerStudent: ACCOUNTS_API + "?action=register_student",
        registerTeacher: ACCOUNTS_API + "?action=register_teacher",
        checkUsername:  ACCOUNTS_API + "?action=check_username"
    };

    // ---- Validation rules ---------------------------------------------------
    const RULES = {
        identifier: {
            minLength: 3,
            maxLength: 50,
            usernamePattern: /^[a-z0-9._-]+$/,
            emailPattern: /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/
        },
        password: {
            minLength: 8,
            maxLength: 128,
            hasLower:   /[a-z]/,
            hasUpper:   /[A-Z]/,
            hasDigit:   /[0-9]/,
            hasSymbol:  /[^A-Za-z0-9]/
        },
        name: {
            minLength: 1,
            maxLength: 50,
            pattern: /^[\p{L}\s.'-]+$/u
        },
        email: {
            maxLength: 100,
            pattern: /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/
        },
        phone: {
            minLength: 7,
            maxLength: 25,
            pattern: /^[+0-9()\-\s]+$/
        },
        address: {
            minLength: 5,
            maxLength: 255
        },
        birthdate: {
            minAge: 10,
            maxAge: 100
        }
    };


    // ---- Validation functions ----------------------------------------------

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

        for (let i = 0; i < bound.length; i++) {
            if (!bound[i].input) { return null; }
        }

        return { form: form, alertBox: alertBox, submitBtn: submitBtn, fields: bound };
    }

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

    function bindFocusClear(input, err) {
        input.addEventListener("focus", function () {
            if (err) { err.classList.add("hidden"); }
        });
    }


    // ---- Loading state on the submit button --------------------------------

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

    // ---- Helper to find a field on a context ------------------------------

    function findField(ctx, name) {
        for (let i = 0; i < ctx.fields.length; i++) {
            if (ctx.fields[i].name === name) { return ctx.fields[i]; }
        }
        return null;
    }

    // ---- Tab switching -----------------------------------------------------

    function initTabs() {
        const studentTabBtn = document.getElementById("studentTabBtn");
        const teacherTabBtn = document.getElementById("teacherTabBtn");
        const studentForm = document.getElementById("signupForm");
        const teacherForm = document.getElementById("teacherSignupForm");

        if (!studentTabBtn || !teacherTabBtn || !studentForm || !teacherForm) {
            return;
        }

        function switchTab(tab) {
            // Reset both tabs
            studentTabBtn.classList.remove("active", "text-gray-700", "bg-gray-100", "border-gray-700");
            studentTabBtn.classList.add("text-gray-500", "bg-gray-50", "border-transparent");
            teacherTabBtn.classList.remove("active", "text-gray-700", "bg-gray-100", "border-gray-700");
            teacherTabBtn.classList.add("text-gray-500", "bg-gray-50", "border-transparent");

            if (tab === "student") {
                studentTabBtn.classList.remove("text-gray-500", "bg-gray-50", "border-transparent");
                studentTabBtn.classList.add("active", "text-gray-700", "bg-gray-100", "border-gray-700");
                studentForm.classList.remove("hidden");
                teacherForm.classList.add("hidden");
            } else {
                teacherTabBtn.classList.remove("text-gray-500", "bg-gray-50", "border-transparent");
                teacherTabBtn.classList.add("active", "text-gray-700", "bg-gray-100", "border-gray-700");
                teacherForm.classList.remove("hidden");
                studentForm.classList.add("hidden");
            }
        }

        studentTabBtn.addEventListener("click", function () { switchTab("student"); });
        teacherTabBtn.addEventListener("click", function () { switchTab("teacher"); });
    }

    // ---- Wire Login Form ---------------------------------------------------

    function wireLoginForm() {
        const ctx = createFormContext("login", [
            { name: "Identifier", validate: validateIdentifierValue },
            { name: "Password",   validate: validatePasswordValue }
        ], "login");
        if (!ctx) { return false; }

        const options = { title: "Please fix the following before logging in:" };
        const identifierField = findField(ctx, "Identifier");
        const passwordField   = findField(ctx, "Password");

        function onLoginSuccess(data) {
            const reason  = (data && data.reason) || "ok";
            const message = (data && data.message) || "Login successful.";
            showAlert(ctx.alertBox, [message], options);
            if (reason === "must_change_password") {
                markInvalid(passwordField.input, passwordField.err, "Please change your temporary password first.");
            } else if (reason === "pending_verification") {
                markInvalid(identifierField.input, identifierField.err, "Please verify your email first.");
            }

            if (reason === "ok" && data && data.account) {
                persistStudentSession(data.account);
                window.location.assign("/LearningMS/public/student-dashboard.php");
            }
        }

        function persistStudentSession(account) {
            try {
                const session = {
                    account_id:  account.account_id || null,
                    user_id:     account.user_id    || null,
                    entity_id:   account.entity_id  || null,
                    entity_type: account.entity_type || null,
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
            } catch (storageError) {}
        }

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

        ctx.fields.forEach(function (field) {
            field.input.addEventListener("input", debounce(function () {
                onFieldLive(field, ctx);
            }, 200));
            bindFocusClear(field.input, field.err);
        });

        return true;
    }

    // ---- Wire Student Signup Form ------------------------------------------

    function wireStudentSignupForm() {
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
                "Student account created. You can now log in.";
            showAlert(ctx.alertBox, [message], options);
            ctx.fields.forEach(function (f) { f.input.disabled = true; });
            ctx.submitBtn.disabled = true;
            setLoading(ctx.submitBtn, true, "Created");
            
            setTimeout(function() {
                window.location.assign("/LearningMS/public/student-login.php");
            }, 3000);
        }

        function onSignupFailure(data) {
            const messages = (data && Array.isArray(data.errors) && data.errors.length > 0)
                ? data.errors
                : [(data && data.message) || "Registration failed. Please try again."];
            showAlert(ctx.alertBox, messages, options);
            flagByReason(data);
        }

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

            postJson(ENDPOINTS.registerStudent, payload)
                .then(function (res) {
                    if (res.data && res.data.success) { onSignupSuccess(res.data); }
                    else { onSignupFailure(res.data); }
                })
                .finally(function () {
                    if (!ctx.submitBtn.disabled) { return; }
                    if (ctx.submitBtn.textContent === "Created") { return; }
                    setLoading(ctx.submitBtn, false);
                });
        });

        ctx.fields.forEach(function (field) {
            field.input.addEventListener("input", debounce(function () {
                onFieldLive(field, ctx);
            }, 200));
            bindFocusClear(field.input, field.err);
        });

        return true;
    }

    // ---- Wire Teacher Signup Form ------------------------------------------

    function wireTeacherSignupForm() {
        const fieldDefs = [
            { name: "FirstName",    validate: validateNameValue },
            { name: "LastName",     validate: validateNameValue },
            { name: "Email",        validate: validateEmailValue },
            { name: "Phone",        validate: validatePhoneValue },
            { name: "Specialization", validate: function (raw, required) {
                // Specialization is optional
                const value = (raw || "").trim();
                if (value.length === 0) { return null; }
                if (value.length > 150) {
                    return "Specialization is too long (max 150 characters).";
                }
                return null;
            } },
            { name: "Username",     validate: validateIdentifierValue },
            { name: "Password",     validate: validatePasswordValue },
            { name: "Confirm",      validate: function (raw) {
                const pwd = findField(ctx, "Password");
                return validateConfirmValue(raw, pwd ? pwd.input.value : "");
            } }
        ];

        const ctx = createFormContext("teacherSignup", fieldDefs, "teacherSignup");
        if (!ctx) { return false; }

        const options = { title: "Please fix the following before signing up:" };
        const usernameField = findField(ctx, "Username");
        const passwordField = findField(ctx, "Password");
        const emailField    = findField(ctx, "Email");

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
                "Teacher account created. You can now log in.";
            showAlert(ctx.alertBox, [message], options);
            ctx.fields.forEach(function (f) { f.input.disabled = true; });
            ctx.submitBtn.disabled = true;
            setLoading(ctx.submitBtn, true, "Created");
            
            setTimeout(function() {
                window.location.assign("/LearningMS/public/student-login.php");
            }, 3000);
        }

        function onSignupFailure(data) {
            const messages = (data && Array.isArray(data.errors) && data.errors.length > 0)
                ? data.errors
                : [(data && data.message) || "Registration failed. Please try again."];
            showAlert(ctx.alertBox, messages, options);
            flagByReason(data);
        }

        passwordField.input.addEventListener("input", debounce(function () {
            const confirmField = findField(ctx, "Confirm");
            if (confirmField && (confirmField.input.value || "").length > 0) {
                onFieldLive(confirmField, ctx);
            }
        }, 200));

        ctx.form.addEventListener("submit", function (event) {
            event.preventDefault();
            if (!runFullValidation(ctx, options)) { return; }

            setLoading(ctx.submitBtn, true, "Creating teacher account...");

            const payload = new URLSearchParams();
            payload.set("first_name",      findField(ctx, "FirstName").input.value.trim());
            payload.set("last_name",       findField(ctx, "LastName").input.value.trim());
            payload.set("email",           findField(ctx, "Email").input.value.trim());
            payload.set("phone",           findField(ctx, "Phone").input.value.trim());
            payload.set("specialization",  findField(ctx, "Specialization").input.value.trim());
            payload.set("username",        findField(ctx, "Username").input.value.trim());
            payload.set("password",        findField(ctx, "Password").input.value);

            postJson(ENDPOINTS.registerTeacher, payload)
                .then(function (res) {
                    if (res.data && res.data.success) { onSignupSuccess(res.data); }
                    else { onSignupFailure(res.data); }
                })
                .finally(function () {
                    if (!ctx.submitBtn.disabled) { return; }
                    if (ctx.submitBtn.textContent === "Created") { return; }
                    setLoading(ctx.submitBtn, false);
                });
        });

        ctx.fields.forEach(function (field) {
            field.input.addEventListener("input", debounce(function () {
                onFieldLive(field, ctx);
            }, 200));
            bindFocusClear(field.input, field.err);
        });

        return true;
    }

    // ---- Page router -------------------------------------------------------

    initTabs();
    wireLoginForm();
    wireStudentSignupForm();
    wireTeacherSignupForm();
})();
/* =============================================================================
 * profile-validation.js
 *
 * Location once applied:
 *   app/backend/modules/profile/js/profile-validation.js
 *
 * Shared front-end logic for the "My Account" Profile module. Drives:
 *   - public/student-profile.php  (view profile + edit address/contact/bio)
 *
 * Pure vanilla JS, no dependencies — same DOM-contract pattern used by
 * account-validation.js:
 *
 *   - <formId>Form        <form>
 *   - <formId>Alert       <div role="alert"> at the top of the form
 *   - <formId>Button      <button type="submit">
 *   - <fieldId>Input      <input/textarea> for the value
 *   - <fieldId>Error      <p>      error message under the field
 *
 *   For this page: profileForm, profileAlert, profileButton,
 *                  profileAddressInput       / profileAddressError,
 *                  profileContactNumberInput / profileContactNumberError,
 *                  profileBioInput           / profileBioError
 *
 * Field naming matches schema.sql's `students.contact_number` column
 * (not "phone") so the JSON payload keys line up 1:1 with ProfileController.
 *
 * Read-only display fields (name, LRN, student number, email, grade
 * level, status) are plain elements the script fills in after loading
 * the profile; they are not part of the form-field contract above
 * because they're never submitted back to the server.
 *
 * NOTE: postJson / debounce / markInvalid / showAlert / setLoading below
 * intentionally mirror the small utilities in account-validation.js.
 * They're duplicated here rather than imported because the codebase
 * doesn't have a shared front-end utility file yet — a good follow-up
 * would be to factor these into one, e.g. js/form-utils.js.
 * ========================================================================== */

(function () {
    "use strict";

    // ---- Constants -----------------------------------------------------------

    const PROFILE_API =
        "/LearningMS/app/backend/modules/profile/controller/ProfileController.php";

    const ENDPOINTS = {
        get:    PROFILE_API + "?action=get",
        update: PROFILE_API + "?action=update"
    };

    const RULES = {
        address: { minLength: 5, maxLength: 255 },
        contactNumber: {
            minDigits: 7,
            maxLength: 20,
            pattern: /^[+0-9()\-\s]+$/
        },
        bio: { maxLength: 500 }
    };

    // ---- Small utilities -------------------------------------------------------

    function debounce(fn, wait) {
        let timer = null;
        return function () {
            const args = arguments;
            const ctx  = this;
            clearTimeout(timer);
            timer = setTimeout(function () { fn.apply(ctx, args); }, wait);
        };
    }

    function byId(id) {
        return document.getElementById(id);
    }

    function markInvalid(input, errEl, message) {
        input.classList.remove("border-gray-300", "focus:border-gray-500", "focus:ring-gray-400");
        input.classList.add("border-red-400", "focus:border-red-500", "focus:ring-red-300");
        if (errEl) {
            errEl.textContent = message || "";
            errEl.classList.toggle("hidden", !message);
        }
    }

    function markValid(input, errEl) {
        input.classList.remove("border-red-400", "focus:border-red-500", "focus:ring-red-300");
        input.classList.add("border-gray-300", "focus:border-gray-500", "focus:ring-gray-400");
        if (errEl) {
            errEl.textContent = "";
            errEl.classList.add("hidden");
        }
    }

    function bindFocusClear(input, errEl) {
        input.addEventListener("focus", function () {
            if (!input.classList.contains("border-red-400")) { return; }
            markValid(input, errEl);
        });
    }

    function showAlert(alertBox, messages, options) {
        if (!alertBox) { return; }
        if (!messages || messages.length === 0) {
            alertBox.classList.add("hidden");
            alertBox.innerHTML = "";
            return;
        }
        const title = (options && options.title) || "";
        let html = "";
        if (title) { html += "<p class=\"font-semibold mb-1\">" + title + "</p>"; }
        html += "<ul class=\"list-disc list-inside space-y-0.5\">";
        messages.forEach(function (m) { html += "<li>" + m + "</li>"; });
        html += "</ul>";
        alertBox.innerHTML = html;
        alertBox.classList.remove("hidden");
    }

    function setLoading(button, isLoading, loadingText) {
        if (!button) { return; }
        if (isLoading) {
            button.dataset.originalText = button.dataset.originalText || button.textContent;
            button.textContent = loadingText || "Saving...";
            button.disabled = true;
        } else {
            button.textContent = button.dataset.originalText || button.textContent;
            button.disabled = false;
        }
    }

    function postJson(url, params) {
        return fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: params
        })
            .then(function (response) {
                return response.json().then(function (data) {
                    return { ok: response.ok, status: response.status, data: data };
                });
            });
    }

    function getJson(url) {
        return fetch(url, { method: "GET" })
            .then(function (response) {
                return response.json().then(function (data) {
                    return { ok: response.ok, status: response.status, data: data };
                });
            });
    }

    // ---- Field validators --------------------------------------------------

    function validateAddressValue(raw) {
        const value = (raw || "").trim();
        if (value.length === 0) { return "Address is required."; }
        if (value.length < RULES.address.minLength) {
            return "Address is too short.";
        }
        if (value.length > RULES.address.maxLength) {
            return "Address is too long (max " + RULES.address.maxLength + " characters).";
        }
        return null;
    }

    function validateContactNumberValue(raw) {
        const value = (raw || "").trim();
        if (value.length === 0) { return null; } // optional — empty clears it
        if (value.length > RULES.contactNumber.maxLength) {
            return "Contact number is too long (max " + RULES.contactNumber.maxLength + " characters).";
        }
        const digits = value.replace(/\D/g, "");
        if (digits.length < RULES.contactNumber.minDigits) {
            return "Contact number must contain at least " + RULES.contactNumber.minDigits + " digits.";
        }
        if (!RULES.contactNumber.pattern.test(value)) {
            return "Contact number may only contain digits, spaces, +, -, or parentheses.";
        }
        return null;
    }

    function validateBioValue(raw) {
        const value = raw || "";
        if (value.length > RULES.bio.maxLength) {
            return "Bio is too long (max " + RULES.bio.maxLength + " characters).";
        }
        return null; // bio is optional — empty is always fine
    }

    // ---- Profile page wiring -------------------------------------------------

    /**
     * Fill the read-only summary fields once the profile has loaded.
     * Each element is optional — the page only sets the ones present.
     */
    function renderReadOnlyFields(profile) {
        const map = {
            profileFullNameDisplay:      profile.full_name,
            profileLrnDisplay:           profile.lrn,
            profileStudentNumberDisplay: profile.student_number,
            profileEmailDisplay:         profile.email,
            profileGradeLevelDisplay:    profile.grade_level ? ("Grade " + profile.grade_level) : "",
            profileStatusDisplay:        profile.status
        };
        Object.keys(map).forEach(function (id) {
            const el = byId(id);
            if (el) { el.textContent = map[id] || "\u2014"; }
        });
    }

    function updateBioCounter(bioInput) {
        const counter = byId("profileBioCounter");
        if (!counter) { return; }
        const used = (bioInput.value || "").length;
        counter.textContent = used + " / " + RULES.bio.maxLength;
    }

    function wireProfileForm() {
        const form      = byId("profileForm");
        const alertBox  = byId("profileAlert");
        const submitBtn = byId("profileButton");
        if (!form || !submitBtn) { return false; }

        const addressInput = byId("profileAddressInput");
        const addressError = byId("profileAddressError");
        const contactInput = byId("profileContactNumberInput");
        const contactError = byId("profileContactNumberError");
        const bioInput     = byId("profileBioInput");
        const bioError     = byId("profileBioError");

        function validateField(input, errEl, validator) {
            const message = validator(input.value);
            if (message) { markInvalid(input, errEl, message); return false; }
            markValid(input, errEl);
            return true;
        }

        function runFullValidation() {
            let valid = true;
            if (addressInput && !validateField(addressInput, addressError, validateAddressValue)) { valid = false; }
            if (contactInput && !validateField(contactInput, contactError, validateContactNumberValue)) { valid = false; }
            if (bioInput && !validateField(bioInput, bioError, validateBioValue)) { valid = false; }
            if (!valid) {
                showAlert(alertBox, ["Please fix the highlighted fields before saving."]);
            }
            return valid;
        }

        // ---- Load the current profile on page open. --------------------------
        getJson(ENDPOINTS.get).then(function (res) {
            if (!res.data || !res.data.success) {
                showAlert(alertBox, [
                    (res.data && res.data.errors && res.data.errors[0]) ||
                    "Could not load your profile. Please refresh the page."
                ]);
                return;
            }
            const profile = res.data.profile;
            renderReadOnlyFields(profile);
            if (addressInput) { addressInput.value = profile.address || ""; }
            if (contactInput) { contactInput.value = profile.contact_number || ""; }
            if (bioInput)     { bioInput.value     = profile.bio || ""; updateBioCounter(bioInput); }
        });

        // ---- Live validation as the user types. -------------------------------
        if (addressInput) {
            addressInput.addEventListener("input", debounce(function () {
                validateField(addressInput, addressError, validateAddressValue);
            }, 200));
            bindFocusClear(addressInput, addressError);
        }
        if (contactInput) {
            contactInput.addEventListener("input", debounce(function () {
                validateField(contactInput, contactError, validateContactNumberValue);
            }, 200));
            bindFocusClear(contactInput, contactError);
        }
        if (bioInput) {
            bioInput.addEventListener("input", debounce(function () {
                updateBioCounter(bioInput);
                validateField(bioInput, bioError, validateBioValue);
            }, 200));
            bindFocusClear(bioInput, bioError);
        }

        // ---- Submit. -----------------------------------------------------------
        form.addEventListener("submit", function (event) {
            event.preventDefault();
            if (!runFullValidation()) { return; }

            setLoading(submitBtn, true, "Saving...");
            showAlert(alertBox, []); // clear any previous alert

            const payload = new URLSearchParams();
            if (addressInput) { payload.set("address", addressInput.value.trim()); }
            if (contactInput) { payload.set("contact_number", contactInput.value.trim()); }
            if (bioInput)     { payload.set("bio", bioInput.value.trim()); }

            postJson(ENDPOINTS.update, payload)
                .then(function (res) {
                    if (res.data && res.data.success) {
                        alertBox.classList.remove("bg-red-50", "border-red-300", "text-red-800");
                        alertBox.classList.add("bg-green-50", "border-green-300", "text-green-800");
                        showAlert(alertBox, [res.data.message || "Profile updated successfully."], {});
                        renderReadOnlyFields(res.data.profile);
                    } else {
                        const messages = (res.data && res.data.errors && res.data.errors.length)
                            ? res.data.errors
                            : [(res.data && res.data.message) || "Could not update your profile."];
                        alertBox.classList.remove("bg-green-50", "border-green-300", "text-green-800");
                        alertBox.classList.add("bg-red-50", "border-red-300", "text-red-800");
                        showAlert(alertBox, messages, { title: "Update failed:" });
                    }
                })
                .finally(function () {
                    setLoading(submitBtn, false);
                });
        });

        return true;
    }

    // ---- Page router -----------------------------------------------------------
    wireProfileForm();
})();

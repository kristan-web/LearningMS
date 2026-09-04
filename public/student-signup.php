<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Student or Teacher</title>
    <script src="/LearningMS/public/css/tailwind.js"></script>
</head>
<body class="bg-gray-200 min-h-screen w-screen flex flex-col p-4">
    <!-- Logo at upper left -->
    <div>
        <h1 class="text-xl font-semibold text-gray-900">LOGO</h1>
    </div>

    <!-- Signup forms centered (takes remaining space) -->
    <div class="flex-1 flex flex-col items-center justify-center py-6">
        <h2 class="text-base font-bold text-gray-900 mb-4">Create Your Account</h2>

        <p class="text-xs text-gray-900 mb-4">
            Already have an account?
            <a href="/LearningMS/public/student-login.php" class="underline">Log in</a>
        </p>

        <!-- Tab Navigation -->
        <div class="w-full max-w-md mb-4 border-b border-gray-300">
            <div class="flex">
                <button id="studentTabBtn" class="tab-btn active flex-1 py-2 px-4 text-sm font-medium text-center text-gray-700 bg-gray-100 border-b-2 border-gray-700 rounded-t-lg hover:bg-gray-200 transition">
                    Student
                </button>
                <button id="teacherTabBtn" class="tab-btn flex-1 py-2 px-4 text-sm font-medium text-center text-gray-500 bg-gray-50 border-b-2 border-transparent rounded-t-lg hover:bg-gray-200 hover:text-gray-700 transition">
                    Teacher
                </button>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- STUDENT SIGNUP FORM                                           -->
        <!-- ============================================================ -->
        <form id="signupForm" class="signup-form w-full max-w-md" novalidate>
            <!-- Summary alert -->
            <div id="signupAlert" role="alert" aria-live="polite"
                 class="hidden w-full mb-4 p-3 rounded border border-red-300 bg-red-50 text-red-800 text-xs">
            </div>

            <!-- Name row: First / Middle / Last -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                <div class="flex flex-col">
                    <input id="signupFirstNameInput" name="first_name" type="text"
                           placeholder="First name" autocomplete="given-name" required
                           class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                    <p id="signupFirstNameError" class="hidden w-full text-left text-xs text-red-600 mt-1" aria-live="polite"></p>
                </div>
                <div class="flex flex-col">
                    <input id="signupMiddleNameInput" name="middle_name" type="text"
                           placeholder="Middle name (optional)" autocomplete="additional-name"
                           class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                    <p id="signupMiddleNameError" class="hidden w-full text-left text-xs text-red-600 mt-1" aria-live="polite"></p>
                </div>
                <div class="flex flex-col">
                    <input id="signupLastNameInput" name="last_name" type="text"
                           placeholder="Last name" autocomplete="family-name" required
                           class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                    <p id="signupLastNameError" class="hidden w-full text-left text-xs text-red-600 mt-1" aria-live="polite"></p>
                </div>
            </div>

            <!-- Email (full width) -->
            <div class="flex flex-col mt-2">
                <input id="signupEmailInput" name="email" type="email"
                       placeholder="Email address" autocomplete="email" required
                       class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                <p id="signupEmailError" class="hidden w-full text-left text-xs text-red-600 mt-1" aria-live="polite"></p>
            </div>

            <!-- Phone + Gender + Birthdate row -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mt-2">
                <div class="flex flex-col">
                    <input id="signupPhoneInput" name="phone" type="tel"
                           placeholder="Phone number" autocomplete="tel" required
                           class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                    <p id="signupPhoneError" class="hidden w-full text-left text-xs text-red-600 mt-1" aria-live="polite"></p>
                </div>
                <div class="flex flex-col">
                    <select id="signupGenderInput" name="gender" required
                            class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm text-gray-900 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                        <option value="">Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                    <p id="signupGenderError" class="hidden w-full text-left text-xs text-red-600 mt-1" aria-live="polite"></p>
                </div>
                <div class="flex flex-col">
                    <input id="signupBirthdateInput" name="birthdate" type="date"
                           placeholder="Birthdate" autocomplete="bday" required
                           class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                    <p id="signupBirthdateError" class="hidden w-full text-left text-xs text-red-600 mt-1" aria-live="polite"></p>
                </div>
            </div>

            <!-- Address (full width textarea) -->
            <div class="flex flex-col mt-2">
                <textarea id="signupAddressInput" name="address" rows="2"
                          placeholder="Home address" required
                          class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition resize-y"></textarea>
                <p id="signupAddressError" class="hidden w-full text-left text-xs text-red-600 mt-1" aria-live="polite"></p>
            </div>

            <!-- Grade Level + Username row -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mt-2">
                <div class="flex flex-col">
                    <select id="signupGradeLevelInput" name="grade_level" required
                            class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm text-gray-900 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                        <option value="">Grade level</option>
                        <option value="11">Grade 11</option>
                        <option value="12">Grade 12</option>
                    </select>
                    <p id="signupGradeLevelError" class="hidden w-full text-left text-xs text-red-600 mt-1" aria-live="polite"></p>
                </div>
                <div class="flex flex-col sm:col-span-2">
                    <input id="signupUsernameInput" name="username" type="text"
                           placeholder="Username" autocomplete="username" required
                           class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                    <p id="signupUsernameError" class="hidden w-full text-left text-xs text-red-600 mt-1" aria-live="polite"></p>
                </div>
            </div>

            <!-- Password + Confirm row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
                <div class="flex flex-col">
                    <input id="signupPasswordInput" name="password" type="password"
                           placeholder="Password" autocomplete="new-password" required
                           class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                    <p id="signupPasswordError" class="hidden w-full text-left text-xs text-red-600 mt-1" aria-live="polite"></p>
                </div>
                <div class="flex flex-col">
                    <input id="signupConfirmInput" name="password_confirm" type="password"
                           placeholder="Confirm password" autocomplete="new-password" required
                           class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                    <p id="signupConfirmError" class="hidden w-full text-left text-xs text-red-600 mt-1" aria-live="polite"></p>
                </div>
            </div>

            <button id="signupButton" type="submit"
                    class="w-full bg-gray-800 text-white text-sm font-semibold py-2 rounded mt-4 hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                Create Student Account
            </button>
        </form>

        <!-- ============================================================ -->
        <!-- TEACHER SIGNUP FORM                                           -->
        <!-- ============================================================ -->
        <form id="teacherSignupForm" class="signup-form w-full max-w-md hidden" novalidate>
            <!-- Summary alert -->
            <div id="teacherSignupAlert" role="alert" aria-live="polite"
                 class="hidden w-full mb-4 p-3 rounded border border-red-300 bg-red-50 text-red-800 text-xs">
            </div>

            <!-- Name row: First / Last -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <div class="flex flex-col">
                    <input id="teacherSignupFirstNameInput" name="first_name" type="text"
                           placeholder="First name" autocomplete="given-name" required
                           class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                    <p id="teacherSignupFirstNameError" class="hidden w-full text-left text-xs text-red-600 mt-1" aria-live="polite"></p>
                </div>
                <div class="flex flex-col">
                    <input id="teacherSignupLastNameInput" name="last_name" type="text"
                           placeholder="Last name" autocomplete="family-name" required
                           class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                    <p id="teacherSignupLastNameError" class="hidden w-full text-left text-xs text-red-600 mt-1" aria-live="polite"></p>
                </div>
            </div>

            <!-- Email + Phone row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
                <div class="flex flex-col">
                    <input id="teacherSignupEmailInput" name="email" type="email"
                           placeholder="Email address" autocomplete="email" required
                           class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                    <p id="teacherSignupEmailError" class="hidden w-full text-left text-xs text-red-600 mt-1" aria-live="polite"></p>
                </div>
                <div class="flex flex-col">
                    <input id="teacherSignupPhoneInput" name="phone" type="tel"
                           placeholder="Phone number" autocomplete="tel" required
                           class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                    <p id="teacherSignupPhoneError" class="hidden w-full text-left text-xs text-red-600 mt-1" aria-live="polite"></p>
                </div>
            </div>

            <!-- Specialization (full width) -->
            <div class="flex flex-col mt-2">
                <input id="teacherSignupSpecializationInput" name="specialization" type="text"
                       placeholder="Specialization (e.g., Mathematics, English, Science)"
                       class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                <p id="teacherSignupSpecializationError" class="hidden w-full text-left text-xs text-red-600 mt-1" aria-live="polite"></p>
            </div>

            <!-- Username -->
            <div class="flex flex-col mt-2">
                <input id="teacherSignupUsernameInput" name="username" type="text"
                       placeholder="Username" autocomplete="username" required
                       class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                <p id="teacherSignupUsernameError" class="hidden w-full text-left text-xs text-red-600 mt-1" aria-live="polite"></p>
            </div>

            <!-- Password + Confirm row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
                <div class="flex flex-col">
                    <input id="teacherSignupPasswordInput" name="password" type="password"
                           placeholder="Password" autocomplete="new-password" required
                           class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                    <p id="teacherSignupPasswordError" class="hidden w-full text-left text-xs text-red-600 mt-1" aria-live="polite"></p>
                </div>
                <div class="flex flex-col">
                    <input id="teacherSignupConfirmInput" name="password_confirm" type="password"
                           placeholder="Confirm password" autocomplete="new-password" required
                           class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                    <p id="teacherSignupConfirmError" class="hidden w-full text-left text-xs text-red-600 mt-1" aria-live="polite"></p>
                </div>
            </div>

            <button id="teacherSignupButton" type="submit"
                    class="w-full bg-gray-800 text-white text-sm font-semibold py-2 rounded mt-4 hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                Create Teacher Account
            </button>
        </form>
    </div>

    <!-- Footer link -->
    <div class="flex justify-center pb-4">
        <a href="/LearningMS/public/student-login.php" class="text-xs text-gray-900 underline">
            Already have an account? Log in
        </a>
    </div>

    <!-- Front-end validation -->
    <script src="/LearningMS/app/backend/modules/accounts/js/account-validation.js"></script>
</body>
</html>
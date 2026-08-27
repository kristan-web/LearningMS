<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Sign Up</title>
    <script src="/LearningMS/public/css/tailwind.js"></script>
</head>
<body class="bg-gray-200 min-h-screen w-screen flex flex-col p-4">
    <!-- Logo at upper left -->
    <div>
        <h1 class="text-xl font-semibold text-gray-900">LOGO</h1>
    </div>

    <!-- Signup form centered (takes remaining space) -->
    <div class="flex-1 flex flex-col items-center justify-center py-6">
        <h2 class="text-base font-bold text-gray-900 mb-4">Create Your Student Account</h2>

        <p class="text-xs text-gray-900 mb-4">
            Already have an account?
            <a href="/LearningMS/public/student-login.php" class="underline">Log in</a>
        </p>

        <form id="signupForm" class="w-full max-w-md" novalidate>

            <!-- Summary alert (hidden by default; shown by account-validation.js) -->
            <div id="signupAlert" role="alert" aria-live="polite"
                 class="hidden w-full mb-4 p-3 rounded border border-red-300 bg-red-50 text-red-800 text-xs">
            </div>

            <!-- Name row: First / Middle / Last -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                <div class="flex flex-col">
                    <input id="signupFirstNameInput" name="first_name" type="text"
                           placeholder="First name" autocomplete="given-name"
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
                           placeholder="Last name" autocomplete="family-name"
                           class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                    <p id="signupLastNameError" class="hidden w-full text-left text-xs text-red-600 mt-1" aria-live="polite"></p>
                </div>
            </div>

            <!-- Email (full width) -->
            <div class="flex flex-col mt-2">
                <input id="signupEmailInput" name="email" type="email"
                       placeholder="Email address" autocomplete="email"
                       class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                <p id="signupEmailError" class="hidden w-full text-left text-xs text-red-600 mt-1" aria-live="polite"></p>
            </div>

            <!-- Phone + Gender + Birthdate row -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mt-2">
                <div class="flex flex-col">
                    <input id="signupPhoneInput" name="phone" type="tel"
                           placeholder="Phone number" autocomplete="tel"
                           class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                    <p id="signupPhoneError" class="hidden w-full text-left text-xs text-red-600 mt-1" aria-live="polite"></p>
                </div>
                <div class="flex flex-col">
                    <select id="signupGenderInput" name="gender"
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
                           placeholder="Birthdate" autocomplete="bday"
                           class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                    <p id="signupBirthdateError" class="hidden w-full text-left text-xs text-red-600 mt-1" aria-live="polite"></p>
                </div>
            </div>


            <!-- Address (full width textarea) -->
            <div class="flex flex-col mt-2">
                <textarea id="signupAddressInput" name="address" rows="2"
                          placeholder="Home address"
                          class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition resize-y"></textarea>
                <p id="signupAddressError" class="hidden w-full text-left text-xs text-red-600 mt-1" aria-live="polite"></p>
            </div>

            <!-- Grade Level + Username row -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mt-2">
                <div class="flex flex-col">
                    <select id="signupGradeLevelInput" name="grade_level"
                            class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm text-gray-900 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                        <option value="">Grade level</option>
                        <option value="11">Grade 11</option>
                        <option value="12">Grade 12</option>
                    </select>
                    <p id="signupGradeLevelError" class="hidden w-full text-left text-xs text-red-600 mt-1" aria-live="polite"></p>
                </div>
                <div class="flex flex-col sm:col-span-2">
                    <input id="signupUsernameInput" name="username" type="text"
                           placeholder="Username" autocomplete="username"
                           class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                    <p id="signupUsernameError" class="hidden w-full text-left text-xs text-red-600 mt-1" aria-live="polite"></p>
                </div>
            </div>

            <!-- Password + Confirm row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
                <div class="flex flex-col">
                    <input id="signupPasswordInput" name="password" type="password"
                           placeholder="Password" autocomplete="new-password"
                           class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                    <p id="signupPasswordError" class="hidden w-full text-left text-xs text-red-600 mt-1" aria-live="polite"></p>
                </div>
                <div class="flex flex-col">
                    <input id="signupConfirmInput" name="password_confirm" type="password"
                           placeholder="Confirm password" autocomplete="new-password"
                           class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                    <p id="signupConfirmError" class="hidden w-full text-left text-xs text-red-600 mt-1" aria-live="polite"></p>
                </div>
            </div>

            <button id="signupButton" type="submit"
                    class="w-full bg-gray-800 text-white text-sm font-semibold py-2 rounded mt-4 hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                Create Account
            </button>
        </form>
    </div>


    <!-- Footer link -->
    <div class="flex justify-center pb-4">
        <a href="/LearningMS/public/student-login.php" class="text-xs text-gray-900 underline">
            Already have an account? Log in
        </a>
    </div>

    <!-- Front-end validation for the signup form. Loaded as a classic script
         at the end of <body> so the DOM nodes above are already in place. The
         same script also wires the login form on student-login.php. -->
    <script src="/LearningMS/app/backend/modules/accounts/js/account-validation.js"></script>
</body>
</html>


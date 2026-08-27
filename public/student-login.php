<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <script src="/LearningMS/public/css/tailwind.js"></script>
</head>
<body class="bg-gray-200 min-h-screen w-screen flex flex-col p-4">
    <!-- Logo at upper left -->
    <div>
        <h1 class="text-xl font-semibold text-gray-900">LOGO</h1>
    </div>

    <!-- Login Form centered (takes remaining space) -->
    <div class="flex-1 flex flex-col items-center justify-center">
        <h2 class="text-base font-bold text-gray-900 mb-6">Login To Your Account</h2>

        <p class="text-xs text-gray-900 mb-4">
            You don't have an account? <a href="/LearningMS/public/student-signup.php" class="underline">Sign up</a>
        </p>

        <form id="loginForm" class="flex flex-col items-center w-full max-w-sm" novalidate>

            <!-- Summary alert (hidden by default; shown by account-validation.js) -->
            <div id="loginAlert" role="alert" aria-live="polite"
                 class="hidden w-full mb-4 p-3 rounded border border-red-300 bg-red-50 text-red-800 text-xs">
            </div>

            <input id="loginIdentifierInput" name="identifier" type="text" placeholder="Email or Username" autocomplete="username"
                class="w-full bg-white border border-gray-300 rounded px-4 py-2 mb-1 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
            <p id="loginIdentifierError" class="hidden w-full text-left text-xs text-red-600 mb-3" aria-live="polite"></p>

            <input id="loginPasswordInput" name="password" type="password" placeholder="Password" autocomplete="current-password"
                class="w-full bg-white border border-gray-300 rounded px-4 py-2 mb-1 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
            <p id="loginPasswordError" class="hidden w-full text-left text-xs text-red-600 mb-6" aria-live="polite"></p>

            <button id="loginButton" type="submit"
                class="w-full bg-gray-800 text-white text-sm font-semibold py-2 rounded hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                Login
            </button>
        </form>
    </div>

    <!-- Forgot Password at bottom -->
    <div class="flex justify-center pb-4">
        <a href="#" class="text-xs text-gray-900 underline">Forgot Password??</a>
    </div>

    <!-- Front-end validation for the login form. Loaded as a classic script
         at the end of <body> so the DOM nodes above are already in place.
         The shared engine expects IDs of the form <prefix><Name>Input /
         <prefix><Name>Error, so this page uses loginIdentifierInput,
         loginIdentifierError, loginPasswordInput, loginPasswordError. -->
    <script src="/LearningMS/app/backend/modules/accounts/js/account-validation.js"></script>
</body>
</html>
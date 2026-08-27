<!DOCTYPE html>
<html lang="en" class="overflow-x-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <script src="/LearningMS/public/css/tailwind.js"></script>
</head>
<body class="bg-gray-200 min-h-screen flex overflow-x-hidden">

    <!-- Left Sidebar -->
    <aside class="w-16 flex-shrink-0 flex flex-col items-center pt-3 pb-3 gap-2 bg-gray-900">
        <div class="flex flex-col items-center mb-4">
            <span class="text-[10px] font-bold text-white tracking-wider">LOGO</span>
            <div class="w-8 h-8 bg-gray-700 rounded-md mt-1 shadow-sm"></div>
        </div>
        <nav class="flex flex-col items-center gap-1 w-full">
            <a href="#" class="w-10 h-10 flex items-center justify-center bg-gray-700 rounded-md text-white shadow-sm" title="Dashboard">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
            </a>
            <a href="#" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-700 hover:text-white rounded-md transition" title="Courses">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </a>
            <a href="#" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-700 hover:text-white rounded-md transition" title="Grades">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </a>
            <a href="schedule.php" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-700 hover:text-white rounded-md transition" title="Calendar">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </a>
            <a href="student-profile.php" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-700 hover:text-white rounded-md transition" title="Profile">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </a>
            <a href="#" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-700 hover:text-white rounded-md transition" title="News">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2"/></svg>
            </a>
            <a href="#" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-700 hover:text-white rounded-md transition" title="Notifications">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </a>
            <a href="#" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-700 hover:text-white rounded-md transition" title="Analytics">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </a>
            <a href="/LearningMS/public/student-login.php" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-700 hover:text-white rounded-md transition mt-auto" title="Logout">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            </a>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 min-w-0 flex flex-col pl-3 pr-3 pb-3 overflow-auto">

        <!-- Top Header Bar -->
        <header class="flex items-center justify-between gap-3 mb-4 min-w-0 bg-white border-b border-gray-200 px-7 py-3 -ml-3 -mr-3">
            <div class="min-w-0">
                <h1 class="text-lg font-semibold text-gray-900">Dashboard</h1>
                <p class="text-xs text-gray-500">Overview of your academic activity</p>
            </div>
            <div class="flex items-center gap-3 min-w-0">
                <div class="relative flex-1 min-w-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" placeholder="Search..." class="w-full bg-white border border-gray-300 rounded-md pl-9 pr-3 py-1.5 text-xs text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                </div>
                <button class="w-9 h-9 flex items-center justify-center bg-white border border-gray-200 rounded-full text-gray-700 hover:bg-gray-50 transition" title="Notifications">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </button>
                <button class="w-9 h-9 flex items-center justify-center bg-white border border-gray-200 rounded-full text-gray-700 hover:bg-gray-50 transition" title="Messages">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </button>
                <div class="relative">
                    <div class="w-9 h-9 rounded-full bg-gray-300 border-2 border-gray-200 overflow-hidden flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                    </div>
                    <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-gray-200 rounded-full"></span>
                </div>
            </div>
        </header>

        <!-- Greeting -->
        <div class="mb-4">
            <h2 id="dashboard-greeting" class="text-xl font-semibold text-gray-900">Good morning, Student 👋</h2>
            <p id="dashboard-greeting-subtitle" class="text-xs text-gray-500 mt-1">Here's what's happening with your courses today.</p>
        </div>
        <!-- Stats Cards Row -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <p class="text-[10px] font-semibold tracking-wider text-gray-500 uppercase">Assignments Due</p>
                <p id="stat-assignments-due" class="text-2xl font-bold text-gray-900 mt-1">—</p>
                <div class="mt-3 h-1 w-full bg-gray-300 rounded-full"></div>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <p class="text-[10px] font-semibold tracking-wider text-gray-500 uppercase">Quizzes This Week</p>
                <p id="stat-quizzes-week" class="text-2xl font-bold text-gray-900 mt-1">—</p>
                <div class="mt-3 h-1 w-full bg-gray-300 rounded-full"></div>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <p class="text-[10px] font-semibold tracking-wider text-gray-500 uppercase">Current GPA</p>
                <p id="stat-gpa" class="text-2xl font-bold text-gray-900 mt-1">—</p>
                <div class="mt-3 h-1 w-full bg-gray-300 rounded-full"></div>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <p class="text-[10px] font-semibold tracking-wider text-gray-500 uppercase">Announcements</p>
                <p id="stat-announcements" class="text-2xl font-bold text-gray-900 mt-1">—</p>
                <div class="mt-3 h-1 w-full bg-gray-300 rounded-full"></div>
            </div>
        </section>
        <!-- Middle Row: Assignments, Schedule, Announcements -->
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-3 mb-4">

            <!-- Assignments Card -->
            <div class="bg-white rounded-lg border border-gray-200 p-4 flex flex-col">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-900">Assignments</h3>
                    <a href="student-assignments.php" class="text-[11px] text-gray-500 hover:text-gray-900">View all &rarr;</a>
                </div>
                <ul id="dashboard-assignments-list" class="space-y-2 flex-1">
                    <li class="flex items-center gap-2 p-2 bg-gray-50 rounded-md">
                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full flex-shrink-0"></span>
                        <div class="flex-1 h-3 bg-gray-200 rounded"></div>
                        <div class="w-12 h-5 bg-gray-200 rounded"></div>
                    </li>
                </ul>
                <button class="mt-4 w-full py-2 border border-gray-200 rounded-md text-xs text-gray-500 hover:bg-gray-50 transition">
                    + Add Assignment
                </button>
            </div>
            <!-- Schedule Card -->
            <div class="bg-white rounded-lg border border-gray-200 p-4 flex flex-col">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-900">Schedule</h3>
                    <a href="#" class="text-[11px] text-gray-500 hover:text-gray-900">Full view &rarr;</a>
                </div>
                <div id="dashboard-schedule-days" class="grid grid-cols-5 gap-2 mb-4">
                    <button class="flex flex-col items-center py-2 bg-gray-50 text-gray-700 rounded-md">
                        <span class="text-[10px] font-medium tracking-wider">MON</span>
                    </button>
                    <button class="flex flex-col items-center py-2 bg-gray-50 text-gray-700 rounded-md">
                        <span class="text-[10px] font-medium tracking-wider">TUE</span>
                    </button>
                    <button class="flex flex-col items-center py-2 bg-gray-50 text-gray-700 rounded-md">
                        <span class="text-[10px] font-medium tracking-wider">WED</span>
                    </button>
                    <button class="flex flex-col items-center py-2 bg-gray-50 text-gray-700 rounded-md">
                        <span class="text-[10px] font-medium tracking-wider">THU</span>
                    </button>
                    <button class="flex flex-col items-center py-2 bg-gray-50 text-gray-700 rounded-md">
                        <span class="text-[10px] font-medium tracking-wider">FRI</span>
                    </button>
                </div>
                <ul id="dashboard-schedule-list" class="space-y-2 flex-1">
                    <li class="grid grid-cols-[60px_1fr] items-center gap-3">
                        <span class="text-xs text-gray-400 text-right">9:00</span>
                        <div class="h-8 bg-gray-100 rounded"></div>
                    </li>
                </ul>
            </div>
            <!-- Announcements Card -->
            <div class="bg-white rounded-lg border border-gray-200 p-4 flex flex-col">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-900">Announcements</h3>
                    <a href="#" class="text-[11px] text-gray-500 hover:text-gray-900">All &rarr;</a>
                </div>
                <ul id="dashboard-announcements-list" class="space-y-3 flex-1">
                    <li>
                        <div class="flex items-start gap-2">
                            <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mt-1.5 flex-shrink-0"></span>
                            <div class="flex-1">
                                <div class="h-2.5 bg-gray-200 rounded w-full mb-1.5"></div>
                                <div class="h-2.5 bg-gray-200 rounded w-1/3"></div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

        </section>

        <!-- Bottom Row: Grades -->
        <section class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-900">Grades</h3>
                <a href="#" class="text-[11px] text-gray-500 hover:text-gray-900">Details &rarr;</a>
            </div>
            <div id="dashboard-grades-chart" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4 items-end h-40">
                <div class="flex flex-col items-center justify-end h-full">
                    <div class="w-full bg-gray-700 rounded h-24"></div>
                    <span class="text-[10px] text-gray-400 mt-2">Math</span>
                </div>
            </div>
        </section>

    </main>

    <script src="/LearningMS/app/backend/modules/dashboard/js/dashboard.js"></script>
</body>
</html>

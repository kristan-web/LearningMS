<?php
/**
 * student-courses.php
 *
 * Location once applied: public/student-courses.php
 *
 * Student Courses page — Static version with hardcoded data
 * for visualization purposes. Students can view their enrolled subjects
 * with subject details.
 */
// session_start();
// if (empty($_SESSION["student_id"]) && empty($_SESSION["user_id"])) {
//     header("Location: /LearningMS/public/student-login.php");
//     exit;
// }
?>
<!DOCTYPE html>
<html lang="en" class="overflow-x-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses</title>
    <script src="/LearningMS/public/css/tailwind.js"></script>
</head>
<body class="bg-gray-200 min-h-screen flex overflow-x-hidden">

    <!-- Left Sidebar - Fixed -->
    <aside class="fixed top-0 left-0 bottom-0 z-50 w-16 flex-shrink-0 flex flex-col items-center pt-3 pb-3 gap-2 bg-gray-900 overflow-y-auto">
        <div class="flex flex-col items-center mb-4">
            <span class="text-[10px] font-bold text-white tracking-wider">LOGO</span>
            <div class="w-8 h-8 bg-gray-700 rounded-md mt-1 shadow-sm"></div>
        </div>
        <nav class="flex flex-col items-center gap-1 w-full">
            <a href="student-dashboard.php" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-700 hover:text-white rounded-md transition" title="Dashboard">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
            </a>
            <a href="schedule.php" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-700 hover:text-white rounded-md transition" title="Schedule">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </a>
            <a href="student-courses.php" class="w-10 h-10 flex items-center justify-center bg-gray-700 rounded-md text-white shadow-sm transition" title="Courses">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </a>
            <a href="#" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-700 hover:text-white rounded-md transition" title="Grades">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </a>
            <a href="#" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-700 hover:text-white rounded-md transition" title="Calendar">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </a>
            <a href="student-profile.php" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-700 hover:text-white rounded-md transition" title="Profile">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </a>
            <a href="student-assignments.php" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-700 hover:text-white rounded-md transition" title="Assignments">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </a>
            <a href="#" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-700 hover:text-white rounded-md transition" title="News">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2"/></svg>
            </a>
            <a href="#" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-700 hover:text-white rounded-md transition" title="Notifications">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </a>
            <a href="/LearningMS/public/student-login.php" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-700 hover:text-white rounded-md transition mt-auto" title="Logout">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            </a>
        </nav>
    </aside>

    <!-- Top Header Bar - Fixed -->
    <header class="fixed top-0 left-16 right-0 z-40 bg-white border-b border-gray-200 px-7 py-3 flex items-center justify-between gap-3 min-w-0">
        <div class="min-w-0">
            <h1 class="text-lg font-semibold text-gray-900">My Courses</h1>
            <p class="text-xs text-gray-500">View all your enrolled subjects and course details</p>
        </div>
        <div class="flex items-center gap-3 min-w-0">
            <div class="relative flex-1 min-w-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" placeholder="Search courses..." class="w-full bg-white border border-gray-300 rounded-md pl-9 pr-3 py-1.5 text-xs text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
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

    <!-- Main Content Area -->
    <main class="mt-[68px] ml-16 px-6 pb-6 min-h-[calc(100vh-68px)]">

        <!-- Student Info Card -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-gray-300 border-2 border-gray-200 overflow-hidden flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Sybil Delaney</h2>
                    <p class="text-sm text-gray-500">Grade 11 · ABM 11-A · 1st Semester 2026-2027</p>
                </div>
            </div>
            <div class="flex items-center gap-3 text-sm">
                <span class="px-4 py-1.5 bg-blue-50 text-blue-700 rounded-full font-medium">18 Units</span>
                <span class="px-4 py-1.5 bg-green-50 text-green-700 rounded-full font-medium">9 Subjects</span>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
            <form class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[160px]">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Subject Type</label>
                    <select class="w-full bg-white border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-900 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                        <option value="">All Types</option>
                        <option value="Core">Core</option>
                        <option value="Applied">Applied</option>
                        <option value="Specialized">Specialized</option>
                    </select>
                </div>
                <div class="flex-1 min-w-[160px]">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Semester</label>
                    <select class="w-full bg-white border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-900 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                        <option value="">All Semesters</option>
                        <option value="1st Semester">1st Semester</option>
                        <option value="2nd Semester">2nd Semester</option>
                    </select>
                </div>
                <div class="flex-1 min-w-[160px]">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                    <select class="w-full bg-white border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-900 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                        <option value="">All Status</option>
                        <option value="Active">Active</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-5 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700 transition">Apply</button>
                    <button type="reset" class="px-5 py-2 bg-white text-gray-700 text-sm font-medium rounded-md border border-gray-300 hover:bg-gray-50 transition">Reset</button>
                </div>
            </form>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg border border-gray-200 p-4 text-center">
                <p class="text-2xl font-bold text-gray-900">9</p>
                <p class="text-xs text-gray-500">Total Subjects</p>
            </div>
            <div class="bg-blue-50 rounded-lg border border-blue-200 p-4 text-center">
                <p class="text-2xl font-bold text-blue-700">3</p>
                <p class="text-xs text-blue-600">Core</p>
            </div>
            <div class="bg-purple-50 rounded-lg border border-purple-200 p-4 text-center">
                <p class="text-2xl font-bold text-purple-700">2</p>
                <p class="text-xs text-purple-600">Applied</p>
            </div>
            <div class="bg-green-50 rounded-lg border border-green-200 p-4 text-center">
                <p class="text-2xl font-bold text-green-700">4</p>
                <p class="text-xs text-green-600">Specialized</p>
            </div>
        </div>

        <!-- Subjects Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">

            <!-- Subject 1: Oral Communication -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition hover:-translate-y-1">
                <div class="h-36 bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center relative">
                    <div class="absolute top-3 right-3">
                        <span class="px-2.5 py-0.5 text-[10px] font-medium rounded-full bg-white/20 text-white backdrop-blur-sm">Core</span>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="text-sm font-semibold text-gray-900">Oral Communication</h3>
                    <p class="text-xs text-gray-500">ORALCOM</p>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-xs text-gray-500">1.0 unit</span>
                        <span class="px-2 py-0.5 text-[10px] font-medium rounded-full bg-green-100 text-green-700">Active</span>
                    </div>
                </div>
            </div>

            <!-- Subject 2: Komunikasyon at Pananaliksik -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition hover:-translate-y-1">
                <div class="h-36 bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center relative">
                    <div class="absolute top-3 right-3">
                        <span class="px-2.5 py-0.5 text-[10px] font-medium rounded-full bg-white/20 text-white backdrop-blur-sm">Core</span>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="text-sm font-semibold text-gray-900">Komunikasyon at Pananaliksik</h3>
                    <p class="text-xs text-gray-500">KOMPAN</p>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-xs text-gray-500">1.0 unit</span>
                        <span class="px-2 py-0.5 text-[10px] font-medium rounded-full bg-green-100 text-green-700">Active</span>
                    </div>
                </div>
            </div>

            <!-- Subject 3: General Mathematics -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition hover:-translate-y-1">
                <div class="h-36 bg-gradient-to-r from-blue-500 to-cyan-600 flex items-center justify-center relative">
                    <div class="absolute top-3 right-3">
                        <span class="px-2.5 py-0.5 text-[10px] font-medium rounded-full bg-white/20 text-white backdrop-blur-sm">Core</span>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-6 3v-3m-6 3h18M5 10h14M5 14h14M5 18h14"/></svg>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="text-sm font-semibold text-gray-900">General Mathematics</h3>
                    <p class="text-xs text-gray-500">GENMATH</p>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-xs text-gray-500">1.0 unit</span>
                        <span class="px-2 py-0.5 text-[10px] font-medium rounded-full bg-green-100 text-green-700">Active</span>
                    </div>
                </div>
            </div>

            <!-- Subject 4: Earth and Life Science -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition hover:-translate-y-1">
                <div class="h-36 bg-gradient-to-r from-purple-500 to-purple-600 flex items-center justify-center relative">
                    <div class="absolute top-3 right-3">
                        <span class="px-2.5 py-0.5 text-[10px] font-medium rounded-full bg-white/20 text-white backdrop-blur-sm">Applied</span>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="text-sm font-semibold text-gray-900">Earth and Life Science</h3>
                    <p class="text-xs text-gray-500">EARTHLIFE</p>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-xs text-gray-500">1.0 unit</span>
                        <span class="px-2 py-0.5 text-[10px] font-medium rounded-full bg-green-100 text-green-700">Active</span>
                    </div>
                </div>
            </div>

            <!-- Subject 5: Personal Development -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition hover:-translate-y-1">
                <div class="h-36 bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center relative">
                    <div class="absolute top-3 right-3">
                        <span class="px-2.5 py-0.5 text-[10px] font-medium rounded-full bg-white/20 text-white backdrop-blur-sm">Applied</span>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="text-sm font-semibold text-gray-900">Personal Development</h3>
                    <p class="text-xs text-gray-500">PERSDEV</p>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-xs text-gray-500">1.0 unit</span>
                        <span class="px-2 py-0.5 text-[10px] font-medium rounded-full bg-green-100 text-green-700">Active</span>
                    </div>
                </div>
            </div>

            <!-- Subject 6: Fundamentals of ABM 1 -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition hover:-translate-y-1">
                <div class="h-36 bg-gradient-to-r from-green-500 to-emerald-600 flex items-center justify-center relative">
                    <div class="absolute top-3 right-3">
                        <span class="px-2.5 py-0.5 text-[10px] font-medium rounded-full bg-white/20 text-white backdrop-blur-sm">Specialized</span>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 1v1m0 1v1m0 1v1m0 1v1"/></svg>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="text-sm font-semibold text-gray-900">Fundamentals of ABM 1</h3>
                    <p class="text-xs text-gray-500">ABM1</p>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-xs text-gray-500">1.0 unit</span>
                        <span class="px-2 py-0.5 text-[10px] font-medium rounded-full bg-green-100 text-green-700">Active</span>
                    </div>
                </div>
            </div>

            <!-- Subject 7: Business Mathematics -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition hover:-translate-y-1">
                <div class="h-36 bg-gradient-to-r from-green-500 to-teal-600 flex items-center justify-center relative">
                    <div class="absolute top-3 right-3">
                        <span class="px-2.5 py-0.5 text-[10px] font-medium rounded-full bg-white/20 text-white backdrop-blur-sm">Specialized</span>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 1v1m0 1v1m0 1v1m0 1v1"/></svg>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="text-sm font-semibold text-gray-900">Business Mathematics</h3>
                    <p class="text-xs text-gray-500">BUSMATH</p>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-xs text-gray-500">1.0 unit</span>
                        <span class="px-2 py-0.5 text-[10px] font-medium rounded-full bg-green-100 text-green-700">Active</span>
                    </div>
                </div>
            </div>

            <!-- Subject 8: Organization and Management -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition hover:-translate-y-1">
                <div class="h-36 bg-gradient-to-r from-green-500 to-lime-600 flex items-center justify-center relative">
                    <div class="absolute top-3 right-3">
                        <span class="px-2.5 py-0.5 text-[10px] font-medium rounded-full bg-white/20 text-white backdrop-blur-sm">Specialized</span>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="text-sm font-semibold text-gray-900">Organization and Management</h3>
                    <p class="text-xs text-gray-500">ORGMAN</p>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-xs text-gray-500">1.0 unit</span>
                        <span class="px-2 py-0.5 text-[10px] font-medium rounded-full bg-green-100 text-green-700">Active</span>
                    </div>
                </div>
            </div>

            <!-- Subject 9: Introduction to Philosophy -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition hover:-translate-y-1">
                <div class="h-36 bg-gradient-to-r from-green-500 to-amber-600 flex items-center justify-center relative">
                    <div class="absolute top-3 right-3">
                        <span class="px-2.5 py-0.5 text-[10px] font-medium rounded-full bg-white/20 text-white backdrop-blur-sm">Specialized</span>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="text-sm font-semibold text-gray-900">Introduction to Philosophy</h3>
                    <p class="text-xs text-gray-500">PHILO</p>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-xs text-gray-500">1.0 unit</span>
                        <span class="px-2 py-0.5 text-[10px] font-medium rounded-full bg-green-100 text-green-700">Active</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Completed Subjects Section -->
        <div class="mt-8">
            <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <span>Completed Subjects</span>
                <span class="text-xs font-normal text-gray-400">(2nd Semester 2025-2026)</span>
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                
                <!-- Completed Subject 1 -->
                <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden opacity-75">
                    <div class="h-32 bg-gradient-to-r from-gray-400 to-gray-500 flex items-center justify-center relative">
                        <div class="absolute top-3 right-3">
                            <span class="px-2.5 py-0.5 text-[10px] font-medium rounded-full bg-white/20 text-white backdrop-blur-sm">Core</span>
                        </div>
                        <div class="text-center">
                            <div class="w-14 h-14 mx-auto bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="text-sm font-semibold text-gray-700">Reading and Writing Skills</h3>
                        <p class="text-xs text-gray-400">READWRITE</p>
                        <div class="mt-2 flex items-center justify-between">
                            <span class="text-xs text-gray-400">Grade: 92.5</span>
                            <span class="px-2 py-0.5 text-[10px] font-medium rounded-full bg-gray-200 text-gray-600">Completed</span>
                        </div>
                    </div>
                </div>

                <!-- Completed Subject 2 -->
                <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden opacity-75">
                    <div class="h-32 bg-gradient-to-r from-gray-400 to-gray-600 flex items-center justify-center relative">
                        <div class="absolute top-3 right-3">
                            <span class="px-2.5 py-0.5 text-[10px] font-medium rounded-full bg-white/20 text-white backdrop-blur-sm">Specialized</span>
                        </div>
                        <div class="text-center">
                            <div class="w-14 h-14 mx-auto bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 1v1m0 1v1m0 1v1m0 1v1"/></svg>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="text-sm font-semibold text-gray-700">Fundamentals of ABM 2</h3>
                        <p class="text-xs text-gray-400">ABM2</p>
                        <div class="mt-2 flex items-center justify-between">
                            <span class="text-xs text-gray-400">Grade: 88.0</span>
                            <span class="px-2 py-0.5 text-[10px] font-medium rounded-full bg-gray-200 text-gray-600">Completed</span>
                        </div>
                    </div>
                </div>

                <!-- Completed Subject 3 -->
                <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden opacity-75">
                    <div class="h-32 bg-gradient-to-r from-gray-400 to-gray-500 flex items-center justify-center relative">
                        <div class="absolute top-3 right-3">
                            <span class="px-2.5 py-0.5 text-[10px] font-medium rounded-full bg-white/20 text-white backdrop-blur-sm">Applied</span>
                        </div>
                        <div class="text-center">
                            <div class="w-14 h-14 mx-auto bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="text-sm font-semibold text-gray-700">Applied Economics</h3>
                        <p class="text-xs text-gray-400">APPECON</p>
                        <div class="mt-2 flex items-center justify-between">
                            <span class="text-xs text-gray-400">Grade: 90.0</span>
                            <span class="px-2 py-0.5 text-[10px] font-medium rounded-full bg-gray-200 text-gray-600">Completed</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center text-xs text-gray-400 border-t border-gray-200 pt-4">
            <p>Showing 9 active subjects · 3 completed subjects · Total units: 18</p>
        </div>

    </main>

    <script>
        console.log('Student Courses page loaded (Static Preview)');
    </script>

</body>
</html>
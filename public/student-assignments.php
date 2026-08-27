<?php
/**
 * student-assignments.php
 *
 * Location once applied: public/student-assignments.php
 *
 * Student Assignments page — Static version with hardcoded data
 * for visualization purposes. No database calls needed.
 */
// session_start();
// if (empty($_SESSION["student_id"]) && empty($_SESSION["user_id"])) {
//     header("Location: /LearningMS/public/student-login.php");
//     exit;
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Assignments</title>
    <script src="/LearningMS/public/css/tailwind.js"></script>
</head>
<body class="bg-gray-200 min-h-screen overflow-x-hidden">

    <!-- Left Sidebar - Fixed using Tailwind -->
    <aside class="fixed top-0 left-0 bottom-0 z-50 w-16 flex-shrink-0 flex flex-col items-center pt-3 pb-3 gap-2 bg-gray-900 overflow-y-auto scrollbar-thin scrollbar-thumb-white/20 scrollbar-track-transparent">
        <div class="flex flex-col items-center mb-4">
            <span class="text-[10px] font-bold text-white tracking-wider">LOGO</span>
            <div class="w-8 h-8 bg-gray-700 rounded-md mt-1 shadow-sm"></div>
        </div>
        <nav class="flex flex-col items-center gap-1 w-full">
            <a href="student-dashboard.php" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-700 hover:text-white rounded-md transition" title="Dashboard">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
            </a>
            <a href="#" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-700 hover:text-white rounded-md transition" title="Schedule">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </a>
            <a href="#" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-700 hover:text-white rounded-md transition" title="Courses">
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
            <a href="student-assignments.php" class="w-10 h-10 flex items-center justify-center bg-gray-700 rounded-md text-white shadow-sm transition" title="Assignments">
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

    <!-- Top Header Bar - Fixed using Tailwind -->
    <header class="fixed top-0 left-16 right-0 z-40 bg-white border-b border-gray-200 px-7 py-3 flex items-center justify-between gap-3 min-w-0">
        <div class="min-w-0">
            <h1 class="text-lg font-semibold text-gray-900">My Assignments</h1>
            <p class="text-xs text-gray-500">View and submit your assignments</p>
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

    <!-- Main Content Area -->
    <main class="mt-[68px] ml-16 px-3 pb-3 min-h-[calc(100vh-68px)]">

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
            <div class="bg-white rounded-lg border border-gray-200 p-3 text-center">
                <p class="text-2xl font-bold text-gray-900">18</p>
                <p class="text-xs text-gray-500">Total</p>
            </div>
            <div class="bg-yellow-50 rounded-lg border border-yellow-200 p-3 text-center">
                <p class="text-2xl font-bold text-yellow-700">5</p>
                <p class="text-xs text-yellow-600">Pending</p>
            </div>
            <div class="bg-blue-50 rounded-lg border border-blue-200 p-3 text-center">
                <p class="text-2xl font-bold text-blue-700">8</p>
                <p class="text-xs text-blue-600">Submitted</p>
            </div>
            <div class="bg-red-50 rounded-lg border border-red-200 p-3 text-center">
                <p class="text-2xl font-bold text-red-700">3</p>
                <p class="text-xs text-red-600">Past Due</p>
            </div>
        </div>

        <!-- Quick Filters -->
        <div class="flex flex-wrap gap-2 mb-4">
            <button class="quick-filter-btn active px-3 py-1.5 text-xs font-medium rounded-md bg-gray-800 text-white hover:bg-gray-700 transition">All</button>
            <button class="quick-filter-btn px-3 py-1.5 text-xs font-medium rounded-md bg-yellow-100 text-yellow-800 hover:bg-yellow-200 transition">Pending</button>
            <button class="quick-filter-btn px-3 py-1.5 text-xs font-medium rounded-md bg-blue-100 text-blue-800 hover:bg-blue-200 transition">Submitted</button>
            <button class="quick-filter-btn px-3 py-1.5 text-xs font-medium rounded-md bg-green-100 text-green-800 hover:bg-green-200 transition">Graded</button>
            <button class="quick-filter-btn px-3 py-1.5 text-xs font-medium rounded-md bg-red-100 text-red-800 hover:bg-red-200 transition">Past Due</button>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 mb-4">
            <form id="filterForm" class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Subject</label>
                    <select class="w-full bg-white border border-gray-300 rounded-md px-3 py-1.5 text-sm text-gray-900 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                        <option value="">All Subjects</option>
                        <option value="1">Oral Communication</option>
                        <option value="2">General Mathematics</option>
                        <option value="3">Earth and Life Science</option>
                        <option value="4">Personal Development</option>
                        <option value="5">Introduction to Philosophy</option>
                        <option value="6">PE and Health 1</option>
                        <option value="7">ABM 1</option>
                        <option value="8">Business Mathematics</option>
                        <option value="9">Organization and Management</option>
                    </select>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Semester</label>
                    <select class="w-full bg-white border border-gray-300 rounded-md px-3 py-1.5 text-sm text-gray-900 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                        <option value="">All Semesters</option>
                        <option value="1st Semester">1st Semester (2026-2027)</option>
                        <option value="2nd Semester">2nd Semester (2026-2027)</option>
                    </select>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                    <select class="w-full bg-white border border-gray-300 rounded-md px-3 py-1.5 text-sm text-gray-900 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="submitted">Submitted</option>
                        <option value="graded">Graded</option>
                        <option value="past_due">Past Due</option>
                    </select>
                </div>
                <div class="flex-1 min-w-[120px]">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Sort By</label>
                    <select class="w-full bg-white border border-gray-300 rounded-md px-3 py-1.5 text-sm text-gray-900 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                        <option value="due_date" selected>Due Date</option>
                        <option value="title">Title</option>
                        <option value="subject_name">Subject</option>
                        <option value="created_at">Created</option>
                        <option value="max_score">Max Score</option>
                    </select>
                </div>
                <div class="flex-1 min-w-[120px]">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Order</label>
                    <select class="w-full bg-white border border-gray-300 rounded-md px-3 py-1.5 text-sm text-gray-900 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                        <option value="ASC" selected>Ascending</option>
                        <option value="DESC">Descending</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-1.5 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700 transition">Apply</button>
                    <button type="reset" class="px-4 py-1.5 bg-white text-gray-700 text-sm font-medium rounded-md border border-gray-300 hover:bg-gray-50 transition">Reset</button>
                </div>
            </form>
        </div>

        <!-- Assignments List -->
        <div id="assignmentList" class="space-y-3">
            
            <!-- ============================================================ -->
            <!-- GRADED ASSIGNMENTS -->
            <!-- ============================================================ -->
            
            <!-- Assignment 1: Graded -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition cursor-pointer hover:-translate-y-0.5" onclick="openAssignment(1)">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-gray-900">Speech Delivery Practice</h4>
                        <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                Oral Communication
                            </span>
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Dec 15, 2026
                            </span>
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Mr. Rajah Palmer
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-semibold text-green-600">85.5 / 100</span>
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Graded</span>
                    </div>
                </div>
            </div>

            <!-- Assignment 2: Graded -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition cursor-pointer hover:-translate-y-0.5" onclick="openAssignment(2)">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-gray-900">Functions and Relations Worksheet</h4>
                        <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                General Mathematics
                            </span>
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Dec 10, 2026
                            </span>
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Mrs. Maria Santos
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-semibold text-green-600">42 / 50</span>
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Graded</span>
                    </div>
                </div>
            </div>

            <!-- Assignment 3: Graded -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition cursor-pointer hover:-translate-y-0.5" onclick="openAssignment(3)">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-gray-900">Cell Structure and Function Report</h4>
                        <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                Earth and Life Science
                            </span>
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Dec 08, 2026
                            </span>
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Mr. Jose Reyes
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-semibold text-green-600">72 / 80</span>
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Graded</span>
                    </div>
                </div>
            </div>

            <!-- Assignment 4: Graded -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition cursor-pointer hover:-translate-y-0.5" onclick="openAssignment(4)">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-gray-900">Self-Reflection Essay</h4>
                        <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                Personal Development
                            </span>
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Dec 05, 2026
                            </span>
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Mr. Rajah Palmer
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-semibold text-green-600">36 / 40</span>
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Graded</span>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- SUBMITTED ASSIGNMENTS -->
            <!-- ============================================================ -->

            <!-- Assignment 5: Submitted -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition cursor-pointer hover:-translate-y-0.5" onclick="openAssignment(5)">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-gray-900">Life Goals Timeline</h4>
                        <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                Personal Development
                            </span>
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Dec 20, 2026
                            </span>
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Mr. Rajah Palmer
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-400">60 pts</span>
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Submitted</span>
                    </div>
                </div>
            </div>

            <!-- Assignment 6: Submitted -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition cursor-pointer hover:-translate-y-0.5" onclick="openAssignment(6)">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-gray-900">Organizational Structure Analysis</h4>
                        <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                Organization and Management
                            </span>
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Dec 22, 2026
                            </span>
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Ms. Ana Cruz
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-400">65 pts</span>
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Submitted</span>
                    </div>
                </div>
            </div>

            <!-- Assignment 7: Submitted -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition cursor-pointer hover:-translate-y-0.5" onclick="openAssignment(7)">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-gray-900">Business Math Problem Set</h4>
                        <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                Business Mathematics
                            </span>
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Dec 18, 2026
                            </span>
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Mrs. Maria Santos
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-400">75 pts</span>
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Submitted</span>
                    </div>
                </div>
            </div>

            <!-- Assignment 13: Late -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition cursor-pointer hover:-translate-y-0.5" onclick="openAssignment(13)">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-gray-900">Accounting Cycle Exercise</h4>
                        <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                ABM 1
                            </span>
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Nov 30, 2026
                            </span>
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Mr. Rajah Palmer
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-400">100 pts</span>
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-800">Late</span>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- UPCOMING / FUTURE ASSIGNMENTS -->
            <!-- ============================================================ -->

            <!-- Assignment 14: Future -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition cursor-pointer hover:-translate-y-0.5" onclick="openAssignment(14)">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-gray-900">Group Presentation: Intercultural Communication</h4>
                        <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                Oral Communication
                            </span>
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Jan 15, 2027
                            </span>
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Mr. Rajah Palmer
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-400">150 pts</span>
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                    </div>
                </div>
            </div>

            <!-- Assignment 15: Future -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition cursor-pointer hover:-translate-y-0.5" onclick="openAssignment(15)">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-gray-900">Stress Management Techniques Video</h4>
                        <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                Personal Development
                            </span>
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Jan 10, 2027
                            </span>
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Mr. Rajah Palmer
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-400">100 pts</span>
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                    </div>
                </div>
            </div>


    <!-- Assignment Modal -->
    <div id="assignmentModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-xl">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between z-10">
                <h3 id="modalTitle" class="text-lg font-semibold text-gray-900">Assignment Details</h3>
                <button id="modalCloseBtn" class="text-gray-400 hover:text-gray-600 transition" onclick="closeModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="px-6 py-4">
                <!-- Assignment Info -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-xs font-medium text-gray-500">Subject</p>
                        <p id="modalSubject" class="text-sm text-gray-900">—</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500">Due Date</p>
                        <p id="modalDueDate" class="text-sm text-gray-900">—</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500">Status</p>
                        <span id="modalStatus" class="px-2.5 py-1 text-xs font-medium rounded-full">—</span>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500">Score</p>
                        <p id="modalScore" class="text-sm text-gray-900">—</p>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="mb-4">
                    <p class="text-xs font-medium text-gray-500 mb-1">Instructions</p>
                    <div id="modalInstructions" class="text-sm text-gray-700 bg-gray-50 p-3 rounded border border-gray-200 whitespace-pre-wrap">—</div>
                </div>

                <!-- Alert -->
                <div id="modalAlert" class="hidden"></div>

                <!-- Submit Form -->
                <div id="modalSubmitForm" class="border-t border-gray-200 pt-4 mt-4">
                    <p class="text-sm font-medium text-gray-700 mb-2">Submit Your Work</p>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input type="file" id="modalFileInput" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="flex-1 bg-white border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-900 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                        <button id="modalSubmitBtn" class="px-6 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700 transition disabled:opacity-50 disabled:cursor-not-allowed">Submit Assignment</button>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Accepted file types: PDF, DOC, DOCX, JPG, PNG (Max 10MB)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // ================================================================
        // ASSIGNMENT DATA (Hardcoded for visualization)
        // ================================================================
        const assignmentsData = {
            1: {
                id: 1,
                title: 'Speech Delivery Practice',
                subject: 'Oral Communication',
                subject_code: 'ORALCOM',
                due_date: 'Dec 15, 2026',
                status: 'Graded',
                status_class: 'bg-green-100 text-green-800',
                score: '85.5 / 100',
                instructions: 'Prepare and deliver a 3-minute speech on any topic of your choice. Focus on proper articulation, pacing, and audience engagement. Record yourself and submit the video file. Also include a written copy of your speech with annotations on where you applied specific speech techniques.',
                teacher: 'Mr. Rajah Palmer',
                can_submit: false,
                is_past_due: false
            },
            2: {
                id: 2,
                title: 'Functions and Relations Worksheet',
                subject: 'General Mathematics',
                subject_code: 'GENMATH',
                due_date: 'Dec 10, 2026',
                status: 'Graded',
                status_class: 'bg-green-100 text-green-800',
                score: '42 / 50',
                instructions: 'Complete the attached worksheet on functions and relations. Show your complete solutions for each problem. Include a graph for each function and identify its domain and range.',
                teacher: 'Mrs. Maria Santos',
                can_submit: false,
                is_past_due: false
            },
            3: {
                id: 3,
                title: 'Cell Structure and Function Report',
                subject: 'Earth and Life Science',
                subject_code: 'EARTHLIFE',
                due_date: 'Dec 08, 2026',
                status: 'Graded',
                status_class: 'bg-green-100 text-green-800',
                score: '72 / 80',
                instructions: 'Create a detailed report on the structure and function of plant and animal cells. Include labeled diagrams, discuss the functions of major organelles, and explain the differences between the two cell types. Include a section on how cell structures relate to the overall function of organisms.',
                teacher: 'Mr. Jose Reyes',
                can_submit: false,
                is_past_due: false
            },
            4: {
                id: 4,
                title: 'Self-Reflection Essay',
                subject: 'Personal Development',
                subject_code: 'PERSDEV',
                due_date: 'Dec 05, 2026',
                status: 'Graded',
                status_class: 'bg-green-100 text-green-800',
                score: '36 / 40',
                instructions: 'Write a 300-word essay reflecting on your personal strengths, weaknesses, values, and goals. Identify how these aspects have shaped who you are today and how they can influence your future career choices. Be honest and introspective.',
                teacher: 'Mr. Rajah Palmer',
                can_submit: false,
                is_past_due: false
            },
            5: {
                id: 5,
                title: 'Life Goals Timeline',
                subject: 'Personal Development',
                subject_code: 'PERSDEV',
                due_date: 'Dec 20, 2026',
                status: 'Submitted',
                status_class: 'bg-blue-100 text-blue-800',
                score: 'Not yet graded',
                instructions: 'Create a visual timeline of your life goals from now until age 30. Include both short-term and long-term goals. For each milestone, identify the steps needed to achieve it and potential obstacles you might face.',
                teacher: 'Mr. Rajah Palmer',
                can_submit: false,
                is_past_due: false
            },
            6: {
                id: 6,
                title: 'Organizational Structure Analysis',
                subject: 'Organization and Management',
                subject_code: 'ORGMAN',
                due_date: 'Dec 22, 2026',
                status: 'Submitted',
                status_class: 'bg-blue-100 text-blue-800',
                score: 'Not yet graded',
                instructions: 'Research and analyze the organizational structure of a company of your choice. Identify the type of organizational structure, the chain of command, span of control, and the advantages/disadvantages of this structure. Create an organizational chart and write a 300-word analysis.',
                teacher: 'Ms. Ana Cruz',
                can_submit: false,
                is_past_due: false
            },
            7: {
                id: 7,
                title: 'Business Math Problem Set',
                subject: 'Business Mathematics',
                subject_code: 'BUSMATH',
                due_date: 'Dec 18, 2026',
                status: 'Submitted',
                status_class: 'bg-blue-100 text-blue-800',
                score: 'Not yet graded',
                instructions: 'Solve the 25 business math problems involving markups, markdowns, discounts, and commissions. Show your complete solutions and identify the formulas used for each problem type. Also, create 5 original word problems based on real business scenarios.',
                teacher: 'Mrs. Maria Santos',
                can_submit: false,
                is_past_due: false
            },
            8: {
                id: 8,
                title: 'Communication Models Analysis',
                subject: 'Oral Communication',
                subject_code: 'ORALCOM',
                due_date: 'Dec 25, 2026',
                status: 'Pending',
                status_class: 'bg-yellow-100 text-yellow-800',
                score: 'Not submitted',
                instructions: 'Choose one communication model (Shannon-Weaver, Schramm, or Transactional) and write a 500-word essay analyzing it. Provide real-life examples of how this model applies to everyday communication scenarios. Include a diagram or illustration of the model.',
                teacher: 'Mr. Rajah Palmer',
                can_submit: true,
                is_past_due: false
            },
            9: {
                id: 9,
                title: 'Business Ethics Case Study',
                subject: 'ABM 1',
                subject_code: 'ABM1',
                due_date: 'Dec 28, 2026',
                status: 'Pending',
                status_class: 'bg-yellow-100 text-yellow-800',
                score: 'Not submitted',
                instructions: 'Analyze a real or hypothetical business ethics scenario. Identify the ethical dilemma, stakeholders involved, and potential solutions. Apply the Utilitarian, Deontological, and Virtue ethics frameworks to analyze the situation. Write a 400-word report with recommendations.',
                teacher: 'Mr. Rajah Palmer',
                can_submit: true,
                is_past_due: false
            },
            10: {
                id: 10,
                title: 'Ecosystem Research Project',
                subject: 'Earth and Life Science',
                subject_code: 'EARTHLIFE',
                due_date: 'Jan 02, 2027',
                status: 'Pending',
                status_class: 'bg-yellow-100 text-yellow-800',
                score: 'Not submitted',
                instructions: 'Choose one ecosystem type (tropical rainforest, coral reef, mangrove, grassland, or freshwater). Research and present its biodiversity, ecological relationships, and current threats. Include conservation recommendations. Submit a written report and a visual infographic.',
                teacher: 'Mr. Jose Reyes',
                can_submit: true,
                is_past_due: false
            },
            11: {
                id: 11,
                title: 'Philosophical Reflection Paper',
                subject: 'Introduction to Philosophy',
                subject_code: 'PHILO',
                due_date: 'Dec 01, 2026',
                status: 'Past Due',
                status_class: 'bg-red-100 text-red-800',
                score: 'Not submitted',
                instructions: 'Choose one philosophical question and write a 500-word reflection paper. Consider: "What is the meaning of life?", "Do we have free will?", or "What is the nature of reality?" Present your arguments and consider counterarguments.',
                teacher: 'Mr. Rajah Palmer',
                can_submit: false,
                is_past_due: true
            },
            12: {
                id: 12,
                title: 'Philosophy of Famous Thinkers Analysis',
                subject: 'Introduction to Philosophy',
                subject_code: 'PHILO',
                due_date: 'Dec 03, 2026',
                status: 'Past Due',
                status_class: 'bg-red-100 text-red-800',
                score: 'Not submitted',
                instructions: 'Choose two philosophers from different eras (e.g., Plato and Sartre, or Aristotle and Kant). Compare and contrast their views on human existence, ethics, and knowledge. Write a 400-word analysis and create a visual comparison chart.',
                teacher: 'Mr. Rajah Palmer',
                can_submit: false,
                is_past_due: true
            },
            13: {
                id: 13,
                title: 'Accounting Cycle Exercise',
                subject: 'ABM 1',
                subject_code: 'ABM1',
                due_date: 'Nov 30, 2026',
                status: 'Late',
                status_class: 'bg-orange-100 text-orange-800',
                score: 'Not yet graded',
                instructions: 'Complete the accounting cycle exercises provided. Include journal entries, posting to ledgers, trial balance preparation, adjusting entries, worksheet creation, financial statements (income statement, balance sheet), and closing entries. Use the given business transactions.',
                teacher: 'Mr. Rajah Palmer',
                can_submit: false,
                is_past_due: true
            },
            14: {
                id: 14,
                title: 'Group Presentation: Intercultural Communication',
                subject: 'Oral Communication',
                subject_code: 'ORALCOM',
                due_date: 'Jan 15, 2027',
                status: 'Pending',
                status_class: 'bg-yellow-100 text-yellow-800',
                score: 'Not submitted',
                instructions: 'In groups of 3-4, prepare a 10-minute presentation on intercultural communication. Discuss barriers, strategies for effective cross-cultural communication, and present a case study of a cross-cultural misunderstanding and how it could have been avoided. Submit your slides and a group reflection paper.',
                teacher: 'Mr. Rajah Palmer',
                can_submit: true,
                is_past_due: false
            },
            15: {
                id: 15,
                title: 'Stress Management Techniques Video',
                subject: 'Personal Development',
                subject_code: 'PERSDEV',
                due_date: 'Jan 10, 2027',
                status: 'Pending',
                status_class: 'bg-yellow-100 text-yellow-800',
                score: 'Not submitted',
                instructions: 'Create a 2-3 minute video presenting various stress management techniques. Demonstrate at least 3 techniques (deep breathing, meditation, exercise, journaling, etc.) and explain how they help in managing stress. Submit the video file with a brief reflection.',
                teacher: 'Mr. Rajah Palmer',
                can_submit: true,
                is_past_due: false
            },
            16: {
                id: 16,
                title: 'Leadership Style Assessment',
                subject: 'Organization and Management',
                subject_code: 'ORGMAN',
                due_date: 'Jan 20, 2027',
                status: 'Pending',
                status_class: 'bg-yellow-100 text-yellow-800',
                score: 'Not submitted',
                instructions: 'Take a leadership style assessment (provide a link or description). Based on your results, write a 300-word reflection on your leadership style, its strengths and weaknesses, and how you can develop your leadership skills. Include examples of when you exhibited leadership.',
                teacher: 'Ms. Ana Cruz',
                can_submit: true,
                is_past_due: false
            }
        };

        // ================================================================
        // MODAL FUNCTIONS
        // ================================================================

        function openAssignment(id) {
            const data = assignmentsData[id];
            if (!data) return;

            document.getElementById('modalTitle').textContent = data.title;
            document.getElementById('modalSubject').textContent = data.subject + ' (' + data.subject_code + ')';
            document.getElementById('modalDueDate').textContent = data.due_date;
            document.getElementById('modalInstructions').textContent = data.instructions;

            // Status
            const statusEl = document.getElementById('modalStatus');
            statusEl.textContent = data.status;
            statusEl.className = 'px-2.5 py-1 text-xs font-medium rounded-full ' + data.status_class;

            // Score
            const scoreEl = document.getElementById('modalScore');
            scoreEl.textContent = data.score;
            scoreEl.className = data.score.includes('Not') ? 'text-sm text-gray-500' : 'text-sm font-semibold text-gray-900';

            // Submit form
            const submitForm = document.getElementById('modalSubmitForm');
            if (data.can_submit) {
                submitForm.innerHTML = `
                    <p class="text-sm font-medium text-gray-700 mb-2">Submit Your Work</p>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" 
                               class="flex-1 bg-white border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-900 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                        <button onclick="handleSubmit()" 
                                class="px-6 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700 transition">
                            Submit Assignment
                        </button>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Accepted file types: PDF, DOC, DOCX, JPG, PNG (Max 10MB)</p>
                `;
            } else if (data.status === 'Graded') {
                submitForm.innerHTML = `
                    <div class="bg-green-50 border border-green-200 rounded p-3 text-center">
                        <p class="text-sm text-green-700">✓ This assignment has been graded.</p>
                    </div>
                `;
            } else if (data.is_past_due) {
                submitForm.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded p-3 text-center">
                        <p class="text-sm text-red-700">⏰ This assignment is past the due date.</p>
                    </div>
                `;
            } else {
                submitForm.innerHTML = `
                    <div class="bg-blue-50 border border-blue-200 rounded p-3 text-center">
                        <p class="text-sm text-blue-700">📎 You have already submitted this assignment.</p>
                    </div>
                `;
            }

            // Show modal
            document.getElementById('assignmentModal').classList.remove('hidden');
            document.getElementById('assignmentModal').classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeModal() {
            document.getElementById('assignmentModal').classList.add('hidden');
            document.getElementById('assignmentModal').classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        function handleSubmit() {
            const fileInput = document.querySelector('#modalSubmitForm input[type="file"]');
            if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                alert('Please select a file to upload.');
                return;
            }
            const file = fileInput.files[0];
            if (file.size > 10 * 1024 * 1024) {
                alert('File size must be less than 10MB.');
                return;
            }
            alert('Assignment submitted successfully! (This is a static preview)');
            closeModal();
        }

        // Close modal on overlay click
        document.getElementById('assignmentModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('assignmentModal');
                if (modal && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            }
        });

        // ================================================================
        // QUICK FILTERS
        // ================================================================

        document.querySelectorAll('.quick-filter-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.quick-filter-btn').forEach(function(b) {
                    b.classList.remove('bg-gray-800', 'text-white');
                    b.classList.add('bg-gray-100', 'text-gray-700');
                });
                this.classList.remove('bg-gray-100', 'text-gray-700');
                this.classList.add('bg-gray-800', 'text-white');
            });
        });

        // Set initial active state
        document.querySelector('.quick-filter-btn:first-child')?.classList.remove('bg-gray-100', 'text-gray-700');
        document.querySelector('.quick-filter-btn:first-child')?.classList.add('bg-gray-800', 'text-white');

        console.log('Student Assignments page loaded (Static Preview)');
    </script>

</body>
</html>
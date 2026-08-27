<?php
if (!isset($calendar_events)) {
    require dirname(__DIR__, 2) . "/backend/modules/schedule/controller/ScheduleController.php";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en" class="overflow-x-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Schedule</title>
    <script src="/LearningMS/public/css/tailwind.js"></script>
    <!-- FullCalendar CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
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
            <a href="schedule.php" class="w-10 h-10 flex items-center justify-center bg-gray-700 rounded-md text-white shadow-sm transition" title="Schedule">
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
            <h1 class="text-lg font-semibold text-gray-900">Schedule</h1>
            <p class="text-xs text-gray-500">Keep classes, commitments, and campus moments in one clear rhythm.</p>
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
            <?php if (isset($switch_role) && isset($switch_label)): ?>
                <a href="?role=<?= $switch_role ?>&date=<?= urlencode($selected_date ?? '') ?>" 
                   class="px-4 py-1.5 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700 transition whitespace-nowrap">
                    <?= $switch_label ?>
                </a>
            <?php endif; ?>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="mt-[68px] ml-16 px-3 pb-3 min-h-[calc(100vh-68px)]">

        <!-- Errors & Messages -->
        <?php if (!empty($errors)): ?>
            <div class="mb-4 p-3 rounded border border-red-300 bg-red-50 text-red-800 text-sm">
                <?= htmlspecialchars(implode(" ", $errors)) ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($database_error)): ?>
            <div class="mb-4 p-3 rounded border border-red-300 bg-red-50 text-red-800 text-sm">
                Database error: <?= htmlspecialchars($database_error) ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($success_message)): ?>
            <div class="mb-4 p-3 rounded border border-green-300 bg-green-50 text-green-800 text-sm">
                <?= htmlspecialchars($success_message) ?>
            </div>
        <?php endif; ?>

        <!-- Calendar and Events Grid -->
        <div class="grid gap-4 lg:grid-cols-[1fr_380px]">
            
            <!-- Calendar Card -->
            <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                <div id="calendar"></div>
                
                <!-- Event Editor -->
                <div id="event-editor" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-50 bg-white rounded-lg shadow-xl border border-gray-200 w-[480px] max-w-[95vw] max-h-[90vh] overflow-y-auto">
                    <form method="post" id="event-form">
                        <input type="hidden" name="action" id="event-action" value="create">
                        <input type="hidden" name="event_id" id="event-id">
                        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between z-10">
                            <h2 id="event-title" class="text-lg font-semibold text-gray-900">
                                <?= $role === "student" ? "Add Note" : "Add Schedule Event" ?>
                            </h2>
                            <button type="button" id="close-editor" class="text-gray-400 hover:text-gray-600 transition" aria-label="Close editor">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="px-6 py-4 space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1" for="title">Title *</label>
                                <input class="w-full bg-white border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-900 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition" id="title" name="title" maxlength="150" required>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1" for="subject_id">Subject</label>
                                <select class="w-full bg-white border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-900 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition" id="subject_id" name="subject_id" required>
                                    <option value="">Choose a subject</option>
                                    <?php foreach ($subjects as $subject): ?>
                                        <option value="<?= (int) $subject["subject_id"] ?>">
                                            <?= htmlspecialchars($subject["subject_code"] . " - " . $subject["subject_name"]) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php if ($role === "teacher"): ?>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1" for="event_type">Event Type</label>
                                    <select class="w-full bg-white border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-900 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition" id="event_type" name="event_type">
                                        <option>Personal</option>
                                        <option>Quiz</option>
                                        <option>Review</option>
                                        <option>Announcement</option>
                                    </select>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input class="w-4 h-4 text-gray-700 border-gray-300 rounded focus:ring-gray-400" type="checkbox" id="share_to_section" name="share_to_section" value="1">
                                    <label class="text-sm text-gray-700" for="share_to_section">Share to my section</label>
                                </div>
                            <?php else: ?>
                                <input type="hidden" name="event_type" value="Personal">
                            <?php endif; ?>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1" for="start_datetime">Starts *</label>
                                    <input class="w-full bg-white border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-900 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition" id="start_datetime" type="datetime-local" name="start_datetime" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1" for="end_datetime">Ends *</label>
                                    <input class="w-full bg-white border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-900 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition" id="end_datetime" type="datetime-local" name="end_datetime" required>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1" for="description">Description</label>
                                <textarea class="w-full bg-white border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-900 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition resize-none" id="description" name="description" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4 flex justify-end gap-2">
                            <button type="button" id="cancel-editor" class="px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-md border border-gray-300 hover:bg-gray-50 transition">Cancel</button>
                            <button type="submit" id="event-submit" class="px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700 transition">
                                <?= $role === "student" ? "Save Note" : "Save Event" ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Events Sidebar -->
            <aside class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Daily View</p>
                        <h2 class="text-lg font-semibold text-gray-900">Events</h2>
                        <p class="text-xs text-gray-500"><?= date("D, M j, Y", strtotime($selected_date ?? 'now')) ?></p>
                    </div>
                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-700">
                        <?= count($selected_day_events ?? []) ?>
                    </span>
                </div>
                <div class="space-y-3 max-h-[600px] overflow-y-auto pr-1">
                    <?php if (empty($selected_day_events)): ?>
                        <div class="border border-dashed border-gray-200 rounded-lg px-4 py-8 text-center text-sm text-gray-400">
                            No events for this day.
                        </div>
                    <?php else: ?>
                        <?php foreach ($selected_day_events as $event): ?>
                            <?php $is_owner = ($event["created_by_role"] === ($role === "student" ? "Student" : "Teacher") && (int) $event["created_by_id"] === 1); ?>
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 hover:shadow-sm transition">
                                <div class="flex items-start justify-between gap-3">
                                    <h3 class="text-sm font-semibold text-gray-900 truncate"><?= htmlspecialchars($event["title"]) ?></h3>
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-teal-50 text-teal-700 whitespace-nowrap"><?= htmlspecialchars($event["status"] ?? 'Scheduled') ?></span>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    <?= date("g:i A", strtotime($event["start_datetime"])) ?> - <?= date("g:i A", strtotime($event["end_datetime"])) ?>
                                    <?php if (!empty($event["subject_code"])): ?>
                                        · <?= htmlspecialchars($event["subject_code"]) ?>
                                    <?php endif; ?>
                                </p>
                                <?php if (!empty($event["description"])): ?>
                                    <p class="mt-2 text-xs text-gray-600 line-clamp-2"><?= htmlspecialchars($event["description"]) ?></p>
                                <?php endif; ?>
                                <?php if ($is_owner): ?>
                                    <div class="mt-3 flex gap-2">
                                        <button type="button" class="edit-event px-3 py-1 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 transition" data-event="<?= htmlspecialchars(json_encode($event), ENT_QUOTES) ?>">Edit</button>
                                        <button type="button" class="delete-event px-3 py-1 text-xs font-medium text-red-700 bg-white border border-red-300 rounded hover:bg-red-50 transition" data-event-id="<?= (int) $event["event_id"] ?>">Delete</button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </aside>
        </div>

    </main>

    <!-- Delete Dialog -->
    <div id="delete-dialog" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-md w-full shadow-xl" role="alertdialog" aria-modal="true">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 id="delete-dialog-title" class="text-lg font-semibold text-gray-900">Delete this item?</h2>
                <button type="button" id="close-delete-dialog" class="text-gray-400 hover:text-gray-600 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-6 py-4">
                <dl id="delete-details" class="grid grid-cols-[100px_1fr] gap-2 text-sm"></dl>
                <p class="mt-4 text-sm text-gray-500">This action cannot be undone.</p>
                <form method="post" id="delete-form" class="mt-4 flex justify-end gap-2">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="event_id" id="delete-event-id">
                    <button type="button" id="cancel-delete" class="px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-md border border-gray-300 hover:bg-gray-50 transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 transition">Delete</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Save Confirmation Dialog -->
    <div id="save-dialog" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-md w-full shadow-xl" role="alertdialog" aria-modal="true">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 id="save-dialog-title" class="text-lg font-semibold text-gray-900">Confirm changes</h2>
            </div>
            <div class="px-6 py-4">
                <p id="save-dialog-message" class="text-sm text-gray-500 mb-4"></p>
                <dl id="save-details" class="grid grid-cols-[100px_1fr] gap-2 text-sm"></dl>
                <div class="mt-4 flex justify-end gap-2">
                    <button type="button" id="cancel-save" class="px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-md border border-gray-300 hover:bg-gray-50 transition">Review</button>
                    <button type="button" id="confirm-save" class="px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700 transition">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        function initializeSchedule() {
            var selectedDate = <?= json_encode($selected_date ?? date('Y-m-d')) ?>;
            var editor = document.getElementById('event-editor');
            var form = document.getElementById('event-form');
            
            var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                initialView: 'dayGridMonth',
                initialDate: selectedDate,
                height: 'auto',
                dayMaxEvents: 3,
                headerToolbar: {
                    left: 'prev,next today addEvent',
                    center: 'title',
                    right: 'dayGridMonth,dayGridWeek'
                },
                customButtons: {
                    addEvent: {
                        text: '+ <?= $role === "student" ? "Add Note" : "Add Event" ?>',
                        click: function() {
                            openEditor(selectedDate, document.querySelector('.fc-day-today'));
                        }
                    }
                },
                events: <?= json_encode($calendar_events ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
                dateClick: function(info) {
                    selectedDate = info.dateStr;
                    window.location.href = '?role=<?= $role ?? 'student' ?>&date=' + encodeURIComponent(selectedDate);
                },
                eventClick: function(info) {
                    if (info.event.extendedProps.editable) {
                        openEditor(info.event.startStr.slice(0, 10), info.el, info.event.extendedProps.event);
                    } else if ('<?= $role ?? 'student' ?>' === 'student') {
                        openViewDialog(info.event);
                    }
                }
            });
            calendar.render();

            function openEditor(date, anchor, event) {
                selectedDate = date;
                form.reset();
                document.getElementById('event-action').value = event ? 'update' : 'create';
                document.getElementById('event-id').value = event ? event.event_id : '';
                document.getElementById('event-title').textContent = event ? 'Edit <?= $role === "student" ? "Note" : "Event" ?>' : '<?= $role === "student" ? "Add Note" : "Add Schedule Event" ?>';
                document.getElementById('event-submit').textContent = event ? 'Update' : '<?= $role === "student" ? "Save Note" : "Save Event" ?>';
                
                var startVal = event ? event.start_datetime.replace(' ', 'T').slice(0, 16) : date + 'T09:00';
                var endVal = event ? event.end_datetime.replace(' ', 'T').slice(0, 16) : date + 'T10:00';
                document.getElementById('start_datetime').value = startVal;
                document.getElementById('end_datetime').value = endVal;
                
                if (event) {
                    document.getElementById('title').value = event.title || '';
                    document.getElementById('subject_id').value = event.subject_id || '';
                    document.getElementById('description').value = event.description || '';
                    var type = document.getElementById('event_type');
                    if (type) type.value = event.event_type || 'Personal';
                    var share = document.getElementById('share_to_section');
                    if (share) share.checked = event.section_id !== null;
                }
                
                editor.classList.remove('hidden');
            }

            function closeEditor() { editor.classList.add('hidden'); }
            
            document.getElementById('close-editor').addEventListener('click', closeEditor);
            document.getElementById('cancel-editor').addEventListener('click', closeEditor);

            // Save confirmation dialog
            var saveDialog = document.getElementById('save-dialog');
            var saveDetails = document.getElementById('save-details');
            var saveMessage = document.getElementById('save-dialog-message');
            var confirmSave = document.getElementById('confirm-save');
            var pendingSave = false;

            function escapeHtml(value) {
                return String(value || '').replace(/[&<>'"]/g, function(character) {
                    return { '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' }[character];
                });
            }

            function formDetails() {
                var subject = document.getElementById('subject_id');
                var subjectText = subject.options[subject.selectedIndex] ? subject.options[subject.selectedIndex].text : 'Not selected';
                return [
                    ['Title', document.getElementById('title').value || 'Not provided'],
                    ['Description', document.getElementById('description').value || 'None'],
                    ['Subject', subjectText],
                    ['Starts', document.getElementById('start_datetime').value || 'Not provided'],
                    ['Ends', document.getElementById('end_datetime').value || 'Not provided']
                ];
            }

            function renderDetails(target, details) {
                target.innerHTML = details.map(function(item) {
                    return '<dt class="text-xs font-medium text-gray-500">' + escapeHtml(item[0]) + '</dt><dd class="text-sm text-gray-900">' + escapeHtml(item[1]) + '</dd>';
                }).join('');
            }

            form.addEventListener('submit', function(event) {
                if (pendingSave) return;
                event.preventDefault();
                renderDetails(saveDetails, formDetails());
                saveMessage.textContent = document.getElementById('event-action').value === 'update' ? 'Review the updated information before saving.' : 'Review the new item before adding it to your schedule.';
                saveDialog.classList.remove('hidden');
            });

            document.getElementById('cancel-save').addEventListener('click', function() { saveDialog.classList.add('hidden'); });
            confirmSave.addEventListener('click', function() {
                pendingSave = true;
                saveDialog.classList.add('hidden');
                form.submit();
            });

            // Edit/Delete handlers
            document.querySelectorAll('.edit-event').forEach(function(button) {
                button.addEventListener('click', function() {
                    var event = JSON.parse(button.dataset.event);
                    var calendarEvent = calendar.getEventById('event-' + event.event_id);
                    openEditor(event.start_datetime.slice(0, 10), calendarEvent ? calendarEvent.el : button, event);
                });
            });

            var deleteDialog = document.getElementById('delete-dialog');
            var deleteForm = document.getElementById('delete-form');
            
            document.querySelectorAll('.delete-event').forEach(function(button) {
                button.addEventListener('click', function() {
                    var event = JSON.parse(button.closest('.bg-gray-50').querySelector('.edit-event').dataset.event);
                    document.getElementById('delete-event-id').value = button.dataset.eventId;
                    renderDetails(document.getElementById('delete-details'), [
                        ['Title', event.title],
                        ['Description', event.description || 'None'],
                        ['Subject', event.subject_code || 'General'],
                        ['Starts', event.start_datetime],
                        ['Ends', event.end_datetime]
                    ]);
                    deleteDialog.classList.remove('hidden');
                });
            });

            document.getElementById('cancel-delete').addEventListener('click', function() { deleteDialog.classList.add('hidden'); });
            document.getElementById('close-delete-dialog').addEventListener('click', function() { deleteDialog.classList.add('hidden'); });

            // View dialog for students
            function openViewDialog(calendarEvent) {
                var event = calendarEvent.extendedProps.event || {};
                var props = calendarEvent.extendedProps;
                document.getElementById('delete-dialog-title').textContent = 'Event Details';
                var message = document.getElementById('delete-dialog-message');
                if (message) message.hidden = true;
                deleteForm.hidden = true;
                renderDetails(document.getElementById('delete-details'), [
                    ['Title', calendarEvent.title],
                    ['Description', event.description || 'None'],
                    ['Subject', event.subject_code || props.subject_code || 'General'],
                    ['Section', props.section || 'Not specified'],
                    ['Room', props.room_name || 'Not specified'],
                    ['Type', event.event_type || 'Class schedule'],
                    ['Status', event.status || 'Scheduled'],
                    ['Starts', formatEventDate(calendarEvent.start)],
                    ['Ends', formatEventDate(calendarEvent.end)]
                ]);
                deleteDialog.classList.remove('hidden');
            }

            function formatEventDate(date) {
                return date ? new Intl.DateTimeFormat(undefined, {
                    dateStyle: 'medium',
                    timeStyle: 'short'
                }).format(date) : 'Not specified';
            }

            window.addEventListener('resize', function() {
                if (!editor.classList.contains('hidden')) {
                    // Keep editor centered
                }
            });

            // Close dialogs on overlay click
            document.querySelectorAll('#delete-dialog, #save-dialog').forEach(function(dialog) {
                dialog.addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.classList.add('hidden');
                    }
                });
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeSchedule);
        } else {
            initializeSchedule();
        }
    </script>

    <!-- FullCalendar CSS overrides for Tailwind compatibility -->
    <style>
        /* FullCalendar overrides to match Tailwind design */
        .fc .fc-toolbar-title {
            font-size: 1.125rem !important;
            font-weight: 600 !important;
            color: #111827 !important;
        }
        .fc .fc-button {
            background: #f3f4f6 !important;
            border: 1px solid #d1d5db !important;
            color: #374151 !important;
            font-weight: 500 !important;
            font-size: 0.75rem !important;
            padding: 0.375rem 0.75rem !important;
            border-radius: 0.375rem !important;
            text-transform: none !important;
            transition: all 0.15s ease !important;
        }
        .fc .fc-button:hover {
            background: #e5e7eb !important;
            border-color: #9ca3af !important;
        }
        .fc .fc-button-primary:not(:disabled):active,
        .fc .fc-button-primary:not(:disabled).fc-button-active {
            background: #1f2937 !important;
            border-color: #1f2937 !important;
            color: #ffffff !important;
        }
        .fc .fc-button-primary:focus {
            box-shadow: none !important;
        }
        .fc .fc-daygrid-day {
            border-color: #e5e7eb !important;
        }
        .fc .fc-daygrid-day-number {
            font-size: 0.75rem !important;
            color: #374151 !important;
            font-weight: 500 !important;
        }
        .fc .fc-daygrid-day.fc-day-today {
            background: #f0fdf4 !important;
        }
        .fc .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
            color: #065f46 !important;
            font-weight: 700 !important;
        }
        .fc .fc-daygrid-event {
            font-size: 0.7rem !important;
            padding: 0.125rem 0.375rem !important;
            border-radius: 0.25rem !important;
            border: none !important;
            cursor: pointer !important;
        }
        .fc .fc-daygrid-event .fc-event-title {
            font-weight: 500 !important;
        }
        .fc .fc-daygrid-more-link {
            font-size: 0.7rem !important;
            color: #6b7280 !important;
            font-weight: 500 !important;
        }
        .fc .fc-daygrid-more-link:hover {
            color: #374151 !important;
        }
        .fc .fc-dayGridMonth-view .fc-daygrid-day {
            min-height: 80px !important;
        }
        .fc .fc-col-header-cell-cushion {
            font-size: 0.7rem !important;
            font-weight: 600 !important;
            color: #6b7280 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            padding: 0.5rem 0 !important;
        }
        .fc .fc-daygrid-day-events {
            padding: 0 0.25rem !important;
        }
        .fc .fc-daygrid-day-frame {
            padding: 0.25rem !important;
        }
        .fc .fc-daygrid-day-top {
            margin-bottom: 0.125rem !important;
        }
        .fc .fc-scrollgrid {
            border-radius: 0.5rem !important;
            overflow: hidden !important;
        }
        /* Event color variants - Tailwind style */
        .fc-event-teal {
            background-color: #0d9488 !important;
            color: #ffffff !important;
        }
        .fc-event-blue {
            background-color: #2563eb !important;
            color: #ffffff !important;
        }
        .fc-event-purple {
            background-color: #7c3aed !important;
            color: #ffffff !important;
        }
        .fc-event-amber {
            background-color: #d97706 !important;
            color: #ffffff !important;
        }
        .fc-event-rose {
            background-color: #e11d48 !important;
            color: #ffffff !important;
        }
        .fc-event-gray {
            background-color: #6b7280 !important;
            color: #ffffff !important;
        }
        .fc-event-green {
            background-color: #16a34a !important;
            color: #ffffff !important;
        }
        /* Line clamp for description */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

</body>
</html>
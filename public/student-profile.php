<?php
/**
 * student-profile.php
 *
 * Location once applied: public/student-profile.php
 *
 * "My Account" page — a logged-in student views their profile and
 * edits address, contact number and bio. Read-only fields (name, LRN,
 * student number, email, grade level, status) are filled in by
 * profile-validation.js after it fetches ?action=get from
 * ProfileController.php; this file only lays out the shell.
 *
 * Session guard: `students` has no auth of its own (see schema.sql —
 * login lives on `users`), so this checks for either a cached
 * student_id or the raw user_id your login flow already sets on
 * success. ProfileController resolves whichever is present.
 */
// session_start();
// if (empty($_SESSION["student_id"]) && empty($_SESSION["user_id"])) {
//   header("Location: /LearningMS/public/student-login.php");
//   exit;
// }
?>
<!DOCTYPE html>
<html lang="en" class="overflow-x-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account</title>
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
            <a href="student-dashboard.php" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-700 hover:text-white rounded-md transition" title="Dashboard">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
            </a>
            <a href="schedule.php" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-700 hover:text-white rounded-md transition" title="Schedule">
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
            <a href="student-profile.php" class="w-10 h-10 flex items-center justify-center bg-gray-700 rounded-md text-white shadow-sm transition" title="Profile">
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
                <h1 class="text-lg font-semibold text-gray-900">My Account</h1>
                <p class="text-xs text-gray-500">View and edit your profile information</p>
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

        <!-- Profile Content -->
        <div class="flex-1 flex flex-col items-center py-4">
            <div class="w-full max-w-2xl">

                <!-- Single Profile Card -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    
                    <!-- Profile Picture Section -->
                    <div class="flex flex-col items-center mb-6 pb-6 border-b border-gray-200">
                        <div class="relative">
                            <!-- Profile Picture Display -->
                            <div id="profilePictureContainer" class="relative w-32 h-32 rounded-full overflow-hidden bg-gray-200 border-4 border-gray-100 shadow-md">
                                <img id="profilePictureDisplay" 
                                     src="" 
                                     alt="Profile Picture"
                                     class="w-full h-full object-cover hidden">
                                <div id="profilePicturePlaceholder" class="w-full h-full flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                    </svg>
                                </div>
                            </div>
                            
                            <!-- Upload Button Overlay -->
                            <label for="profilePictureInput" 
                                   class="absolute bottom-0 right-0 bg-gray-800 hover:bg-gray-700 text-white rounded-full p-2 cursor-pointer shadow-lg transition hover:scale-105 active:scale-95">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <input type="file" id="profilePictureInput" accept="image/*" class="hidden">
                            </label>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">Click the camera icon to upload a photo</p>
                        <div id="profilePictureError" class="hidden text-xs text-red-600 mt-1" aria-live="polite"></div>
                    </div>

                    <!-- Student Information Section -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-semibold text-gray-900">Student Information</h3>
                            <span class="text-xs text-gray-400">Read-only</span>
                        </div>
                        <dl class="grid grid-cols-3 gap-y-3 text-sm text-gray-900">
                            <dt class="text-gray-500 text-xs font-medium">Full Name</dt>
                            <dd id="profileFullNameDisplay" class="col-span-2">&mdash;</dd>

                            <dt class="text-gray-500 text-xs font-medium">LRN</dt>
                            <dd id="profileLrnDisplay" class="col-span-2">&mdash;</dd>

                            <dt class="text-gray-500 text-xs font-medium">Student No.</dt>
                            <dd id="profileStudentNumberDisplay" class="col-span-2">&mdash;</dd>

                            <dt class="text-gray-500 text-xs font-medium">Email</dt>
                            <dd id="profileEmailDisplay" class="col-span-2">&mdash;</dd>

                            <dt class="text-gray-500 text-xs font-medium">Grade Level</dt>
                            <dd id="profileGradeLevelDisplay" class="col-span-2">&mdash;</dd>

                            <dt class="text-gray-500 text-xs font-medium">Status</dt>
                            <dd id="profileStatusDisplay" class="col-span-2">&mdash;</dd>
                        </dl>
                        <p class="text-xs text-gray-400 mt-4 pt-3 border-t border-gray-100">
                            Name, LRN, student number, grade level and status are managed by the registrar.
                            Contact the registrar's office to request a correction.
                        </p>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-200 my-6"></div>

                    <!-- Contact Details Section -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-semibold text-gray-900">Contact Details</h3>
                            <button id="editToggleBtn" type="button"
                                class="text-xs font-medium text-gray-600 hover:text-gray-900 transition flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                                <span id="editBtnText">Edit</span>
                            </button>
                        </div>

                        <form id="profileForm" class="flex flex-col" novalidate>
                            <!-- Summary alert -->
                            <div id="profileAlert" role="alert" aria-live="polite"
                                 class="hidden w-full mb-4 p-3 rounded border border-red-300 bg-red-50 text-red-800 text-xs">
                            </div>

                            <!-- Address -->
                            <div class="mb-3">
                                <div class="flex items-center justify-between">
                                    <label for="profileAddressInput" class="text-xs font-medium text-gray-700">Address</label>
                                    <span id="addressDisplay" class="text-sm text-gray-900"></span>
                                </div>
                                <input id="profileAddressInput" name="address" type="text" placeholder="House No., Street, Barangay, City"
                                    class="hidden w-full bg-white border border-gray-300 rounded-md px-4 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                                <p id="profileAddressError" class="hidden w-full text-left text-xs text-red-600 mt-1" aria-live="polite"></p>
                            </div>

                            <!-- Contact Number -->
                            <div class="mb-3">
                                <div class="flex items-center justify-between">
                                    <label for="profileContactNumberInput" class="text-xs font-medium text-gray-700">Contact Number</label>
                                    <span id="contactDisplay" class="text-sm text-gray-900"></span>
                                </div>
                                <input id="profileContactNumberInput" name="contact_number" type="text" placeholder="09xx xxx xxxx"
                                    class="hidden w-full bg-white border border-gray-300 rounded-md px-4 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition">
                                <p id="profileContactNumberError" class="hidden w-full text-left text-xs text-red-600 mt-1" aria-live="polite"></p>
                            </div>

                            <!-- Bio -->
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-1">
                                    <label for="profileBioInput" class="text-xs font-medium text-gray-700">Bio / Short Description</label>
                                    <span id="bioDisplay" class="text-sm text-gray-900"></span>
                                </div>
                                <div class="relative">
                                    <textarea id="profileBioInput" name="bio" rows="4" placeholder="Tell your teachers and classmates a bit about yourself..."
                                        class="hidden w-full bg-white border border-gray-300 rounded-md px-4 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition resize-none"></textarea>
                                    <span id="profileBioCounter" class="hidden text-xs text-gray-400 mt-1 block text-right">0 / 500</span>
                                </div>
                                <p id="profileBioError" class="hidden w-full text-left text-xs text-red-600 mt-1" aria-live="polite"></p>
                            </div>

                            <!-- Action Buttons -->
                            <div id="formActions" class="hidden flex gap-3">
                                <button id="profileButton" type="submit"
                                    class="flex-1 bg-gray-800 text-white text-sm font-semibold py-2.5 rounded-md hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                                    Save Changes
                                </button>
                                <button id="cancelEditBtn" type="button"
                                    class="flex-1 bg-white text-gray-700 text-sm font-semibold py-2.5 rounded-md border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

    </main>

    <!-- Front-end logic for this page -->
    <script>
        // ========== Profile Picture Handling ==========
        const profilePictureInput = document.getElementById('profilePictureInput');
        const profilePictureDisplay = document.getElementById('profilePictureDisplay');
        const profilePicturePlaceholder = document.getElementById('profilePicturePlaceholder');
        const profilePictureError = document.getElementById('profilePictureError');

        // Function to update profile picture display
        function updateProfilePicture(imageUrl) {
            if (imageUrl && imageUrl !== '') {
                profilePictureDisplay.src = imageUrl;
                profilePictureDisplay.classList.remove('hidden');
                profilePicturePlaceholder.classList.add('hidden');
            } else {
                profilePictureDisplay.classList.add('hidden');
                profilePicturePlaceholder.classList.remove('hidden');
            }
        }

        // Handle file upload
        profilePictureInput.addEventListener('change', function(e) {
            const file = this.files[0];
            if (!file) return;

            // Validate file type
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                profilePictureError.textContent = 'Please upload a valid image file (JPEG, PNG, GIF, or WEBP)';
                profilePictureError.classList.remove('hidden');
                this.value = '';
                return;
            }

            // Validate file size (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                profilePictureError.textContent = 'Image size must be less than 5MB';
                profilePictureError.classList.remove('hidden');
                this.value = '';
                return;
            }

            profilePictureError.classList.add('hidden');

            // Preview the image
            const reader = new FileReader();
            reader.onload = function(event) {
                updateProfilePicture(event.target.result);
            };
            reader.readAsDataURL(file);

            // Here you would typically upload the file to your server
            // For now, we'll just show the preview and let the form submission handle the actual upload
            // You can add an AJAX upload here or include it in the form submission
        });

        // ========== Toggle Edit Mode ==========
        const editToggleBtn = document.getElementById('editToggleBtn');
        const editBtnText = document.getElementById('editBtnText');
        const formActions = document.getElementById('formActions');
        const cancelEditBtn = document.getElementById('cancelEditBtn');
        
        // Display elements
        const addressDisplay = document.getElementById('addressDisplay');
        const contactDisplay = document.getElementById('contactDisplay');
        const bioDisplay = document.getElementById('bioDisplay');
        
        // Input elements
        const addressInput = document.getElementById('profileAddressInput');
        const contactInput = document.getElementById('profileContactNumberInput');
        const bioInput = document.getElementById('profileBioInput');
        const bioCounter = document.getElementById('profileBioCounter');
        
        // Error elements
        const addressError = document.getElementById('profileAddressError');
        const contactError = document.getElementById('profileContactNumberError');
        const bioError = document.getElementById('profileBioError');
        const alertDiv = document.getElementById('profileAlert');

        let isEditing = false;

        function toggleEditMode() {
            isEditing = !isEditing;
            
            if (isEditing) {
                // Show inputs, hide displays
                addressInput.classList.remove('hidden');
                contactInput.classList.remove('hidden');
                bioInput.classList.remove('hidden');
                bioCounter.classList.remove('hidden');
                formActions.classList.remove('hidden');
                
                addressDisplay.classList.add('hidden');
                contactDisplay.classList.add('hidden');
                bioDisplay.classList.add('hidden');
                
                editBtnText.textContent = 'Cancel';
                editToggleBtn.classList.add('text-red-600');
                editToggleBtn.classList.remove('text-gray-600');
                
                // Populate inputs with current values
                addressInput.value = addressDisplay.textContent !== '—' ? addressDisplay.textContent : '';
                contactInput.value = contactDisplay.textContent !== '—' ? contactDisplay.textContent : '';
                bioInput.value = bioDisplay.textContent !== '—' ? bioDisplay.textContent : '';
                updateBioCounter();
            } else {
                // Hide inputs, show displays
                addressInput.classList.add('hidden');
                contactInput.classList.add('hidden');
                bioInput.classList.add('hidden');
                bioCounter.classList.add('hidden');
                formActions.classList.add('hidden');
                
                addressDisplay.classList.remove('hidden');
                contactDisplay.classList.remove('hidden');
                bioDisplay.classList.remove('hidden');
                
                editBtnText.textContent = 'Edit';
                editToggleBtn.classList.remove('text-red-600');
                editToggleBtn.classList.add('text-gray-600');
                
                // Clear errors
                addressError.classList.add('hidden');
                contactError.classList.add('hidden');
                bioError.classList.add('hidden');
                alertDiv.classList.add('hidden');
            }
        }

        function updateBioCounter() {
            const count = bioInput.value.length;
            bioCounter.textContent = `${count} / 500`;
        }

        // Event listeners
        editToggleBtn.addEventListener('click', toggleEditMode);
        
        cancelEditBtn.addEventListener('click', function() {
            if (isEditing) {
                toggleEditMode();
                // Reset inputs to display values
                addressInput.value = addressDisplay.textContent !== '—' ? addressDisplay.textContent : '';
                contactInput.value = contactDisplay.textContent !== '—' ? contactDisplay.textContent : '';
                bioInput.value = bioDisplay.textContent !== '—' ? bioDisplay.textContent : '';
                updateBioCounter();
            }
        });

        bioInput.addEventListener('input', updateBioCounter);

        // ========== Update profile data from server ==========
        // This function will be called by profile-validation.js
        function updateProfileDisplay(data) {
            // Update contact details
            if (data.address) {
                addressDisplay.textContent = data.address;
                addressInput.value = data.address;
            }
            if (data.contact_number) {
                contactDisplay.textContent = data.contact_number;
                contactInput.value = data.contact_number;
            }
            if (data.bio) {
                bioDisplay.textContent = data.bio;
                bioInput.value = data.bio;
                updateBioCounter();
            }
            if (data.profile_picture) {
                updateProfilePicture(data.profile_picture);
            }
        }

        // Handle form submission
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            // The profile-validation.js will handle the actual submission
            // We'll listen for success and exit edit mode
            
            // If there's a profile picture selected, include it in the form data
            const fileInput = document.getElementById('profilePictureInput');
            if (fileInput.files.length > 0) {
                // You can add the file to FormData in your validation script
                // or handle it separately via AJAX
            }
            
            setTimeout(() => {
                if (isEditing) {
                    toggleEditMode();
                }
            }, 100);
        });

        // Function to handle profile picture upload via AJAX (optional)
        function uploadProfilePicture(file) {
            const formData = new FormData();
            formData.append('profile_picture', file);
            formData.append('action', 'upload_picture');

            fetch('/LearningMS/app/backend/modules/profile/ProfileController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateProfilePicture(data.image_url);
                    profilePictureError.classList.add('hidden');
                } else {
                    profilePictureError.textContent = data.message || 'Failed to upload image';
                    profilePictureError.classList.remove('hidden');
                }
            })
            .catch(error => {
                profilePictureError.textContent = 'An error occurred while uploading';
                profilePictureError.classList.remove('hidden');
            });
        }

        // Override file input change to use AJAX upload
        profilePictureInput.addEventListener('change', function(e) {
            const file = this.files[0];
            if (!file) return;

            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                profilePictureError.textContent = 'Please upload a valid image file (JPEG, PNG, GIF, or WEBP)';
                profilePictureError.classList.remove('hidden');
                this.value = '';
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                profilePictureError.textContent = 'Image size must be less than 5MB';
                profilePictureError.classList.remove('hidden');
                this.value = '';
                return;
            }

            profilePictureError.classList.add('hidden');

            // Preview the image
            const reader = new FileReader();
            reader.onload = function(event) {
                updateProfilePicture(event.target.result);
            };
            reader.readAsDataURL(file);

            // Upload the image to server
            uploadProfilePicture(file);
        });
    </script>
    <script src="/LearningMS/app/backend/modules/profile/js/profile-validation.js"></script>
</body>
</html>
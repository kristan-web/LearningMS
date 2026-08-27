/* =============================================================================
 * assignment.js
 *
 * Location once applied:
 *   app/backend/modules/assignment/js/assignment.js
 *
 * Front-end logic for the Student Assignments page. Handles:
 *   - Loading and displaying assignments
 *   - Filtering by subject, semester, and status
 *   - Sorting assignments
 *   - Submitting assignments with file upload
 *   - Viewing assignment details
 * ========================================================================== */

(function () {
    "use strict";

    // ---- Constants -----------------------------------------------------------

    const ASSIGNMENT_API =
        "/LearningMS/app/backend/modules/assignment/controller/AssignmentController.php";

    // ---- DOM References -----------------------------------------------------

    const assignmentList = document.getElementById('assignmentList');
    const statsContainer = document.getElementById('assignmentStats');
    const filterForm = document.getElementById('filterForm');
    const subjectFilter = document.getElementById('subjectFilter');
    const semesterFilter = document.getElementById('semesterFilter');
    const statusFilter = document.getElementById('statusFilter');
    const sortBySelect = document.getElementById('sortBy');
    const sortOrderSelect = document.getElementById('sortOrder');
    const applyFiltersBtn = document.getElementById('applyFilters');
    const resetFiltersBtn = document.getElementById('resetFilters');

    const modalOverlay = document.getElementById('assignmentModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalSubject = document.getElementById('modalSubject');
    const modalDueDate = document.getElementById('modalDueDate');
    const modalInstructions = document.getElementById('modalInstructions');
    const modalStatus = document.getElementById('modalStatus');
    const modalScore = document.getElementById('modalScore');
    const modalSubmitForm = document.getElementById('modalSubmitForm');
    const modalFileInput = document.getElementById('modalFileInput');
    const modalSubmitBtn = document.getElementById('modalSubmitBtn');
    const modalCloseBtn = document.getElementById('modalCloseBtn');
    const modalAlert = document.getElementById('modalAlert');

    // ---- State --------------------------------------------------------------

    let currentFilters = {
        subject_id: null,
        semester: null,
        status: null,
        sort_by: 'due_date',
        sort_order: 'ASC'
    };

    let currentAssignments = [];
    let currentAssignment = null;

    // ---- Utility Functions ---------------------------------------------------

    function byId(id) {
        return document.getElementById(id);
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function getStatusBadge(status, isPastDue) {
        const classes = {
            'Graded': 'bg-green-100 text-green-800',
            'Submitted': 'bg-blue-100 text-blue-800',
            'Late': 'bg-orange-100 text-orange-800',
            'Past Due': 'bg-red-100 text-red-800',
            'Pending': 'bg-yellow-100 text-yellow-800'
        };

        let key = status;
        if (status === 'Past Due' || (status === 'Pending' && isPastDue)) {
            key = 'Past Due';
        } else if (status === 'Submitted' || status === 'Late') {
            key = status === 'Late' ? 'Late' : 'Submitted';
        }

        return classes[key] || classes['Pending'];
    }

    function getStatusText(status, isPastDue) {
        if (status === 'Graded') return 'Graded';
        if (status === 'Submitted') return 'Submitted';
        if (status === 'Late') return 'Late';
        if (isPastDue || status === 'Past Due') return 'Past Due';
        return 'Pending';
    }

    // ---- API Calls -----------------------------------------------------------

    function loadAssignments() {
        const params = new URLSearchParams();
        params.set('action', 'list');

        if (currentFilters.subject_id) {
            params.set('subject_id', currentFilters.subject_id);
        }
        if (currentFilters.semester) {
            params.set('semester', currentFilters.semester);
        }
        if (currentFilters.status) {
            params.set('status', currentFilters.status);
        }
        params.set('sort_by', currentFilters.sort_by);
        params.set('sort_order', currentFilters.sort_order);

        fetch(ASSIGNMENT_API + '?' + params.toString())
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentAssignments = data.assignments;
                    renderAssignments(data.assignments);
                    renderStats(data.stats);
                    populateFilters(data.filters);
                } else {
                    showError('Failed to load assignments: ' + (data.errors || ['Unknown error']).join(', '));
                }
            })
            .catch(error => {
                showError('Error loading assignments: ' + error.message);
            });
    }

    function loadAssignmentDetails(assignmentId) {
        const params = new URLSearchParams();
        params.set('action', 'view');
        params.set('assignment_id', assignmentId);

        fetch(ASSIGNMENT_API + '?' + params.toString())
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentAssignment = data.assignment;
                    showAssignmentModal(data.assignment);
                } else {
                    showModalError('Failed to load assignment details: ' + (data.errors || ['Unknown error']).join(', '));
                }
            })
            .catch(error => {
                showModalError('Error loading assignment: ' + error.message);
            });
    }

    function submitAssignment(assignmentId, file) {
        const formData = new FormData();
        formData.set('action', 'submit');
        formData.set('assignment_id', assignmentId);
        formData.append('submission_file', file);

        setLoading(true);

        fetch(ASSIGNMENT_API, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                setLoading(false);
                if (data.success) {
                    showModalSuccess(data.message || 'Assignment submitted successfully!');
                    setTimeout(() => {
                        closeModal();
                        loadAssignments();
                    }, 1500);
                } else {
                    showModalError(data.errors ? data.errors.join(', ') : 'Failed to submit assignment.');
                }
            })
            .catch(error => {
                setLoading(false);
                showModalError('Error submitting assignment: ' + error.message);
            });
    }

    // ---- Rendering -----------------------------------------------------------

    function renderAssignments(assignments) {
        if (!assignmentList) return;

        if (assignments.length === 0) {
            assignmentList.innerHTML = `
                <div class="text-center py-12 text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-lg font-medium">No assignments found</p>
                    <p class="text-sm">Try adjusting your filters</p>
                </div>
            `;
            return;
        }

        let html = '';
        assignments.forEach(assignment => {
            const statusText = getStatusText(assignment.submission_status, assignment.is_past_due);
            const badgeClass = getStatusBadge(assignment.submission_status, assignment.is_past_due);

            html += `
                <div class="assignment-item bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition cursor-pointer"
                     data-assignment-id="${assignment.assignment_id}"
                     onclick="window.openAssignment(${assignment.assignment_id})">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-semibold text-gray-900 truncate">${escapeHtml(assignment.title)}</h4>
                            <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-gray-500">
                                <span class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    ${escapeHtml(assignment.subject_name)}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    ${formatDate(assignment.due_date)}
                                </span>
                                ${assignment.teacher_name ? `
                                <span class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    ${escapeHtml(assignment.teacher_name)}
                                </span>
                                ` : ''}
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            ${assignment.submitted_score !== null ? `
                            <span class="text-sm font-semibold text-gray-900">
                                ${assignment.submitted_score} / ${assignment.max_score}
                            </span>
                            ` : `
                            <span class="text-xs text-gray-400">${assignment.max_score} pts</span>
                            `}
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full ${badgeClass}">
                                ${statusText}
                            </span>
                        </div>
                    </div>
                </div>
            `;
        });

        assignmentList.innerHTML = html;
    }

    function renderStats(stats) {
        if (!statsContainer) return;

        statsContainer.innerHTML = `
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                <div class="bg-white rounded-lg border border-gray-200 p-3 text-center">
                    <p class="text-2xl font-bold text-gray-900">${stats.total || 0}</p>
                    <p class="text-xs text-gray-500">Total</p>
                </div>
                <div class="bg-yellow-50 rounded-lg border border-yellow-200 p-3 text-center">
                    <p class="text-2xl font-bold text-yellow-700">${stats.pending || 0}</p>
                    <p class="text-xs text-yellow-600">Pending</p>
                </div>
                <div class="bg-blue-50 rounded-lg border border-blue-200 p-3 text-center">
                    <p class="text-2xl font-bold text-blue-700">${stats.submitted || 0}</p>
                    <p class="text-xs text-blue-600">Submitted</p>
                </div>
                <div class="bg-red-50 rounded-lg border border-red-200 p-3 text-center">
                    <p class="text-2xl font-bold text-red-700">${stats.past_due || 0}</p>
                    <p class="text-xs text-red-600">Past Due</p>
                </div>
            </div>
        `;
    }

    function populateFilters(filters) {
        if (!filters) return;

        // Populate subject filter
        if (subjectFilter && filters.subjects) {
            const currentValue = subjectFilter.value;
            subjectFilter.innerHTML = `
                <option value="">All Subjects</option>
                ${filters.subjects.map(subject => `
                    <option value="${subject.subject_id}" ${currentValue == subject.subject_id ? 'selected' : ''}>
                        ${escapeHtml(subject.subject_name)}
                    </option>
                `).join('')}
            `;
        }

        // Populate semester filter
        if (semesterFilter && filters.semesters) {
            const currentValue = semesterFilter.value;
            semesterFilter.innerHTML = `
                <option value="">All Semesters</option>
                ${filters.semesters.map(sem => `
                    <option value="${sem.semester}" ${currentValue === sem.semester ? 'selected' : ''}>
                        ${sem.semester} (${sem.school_year})
                    </option>
                `).join('')}
            `;
        }
    }

    // ---- Modal Functions ----------------------------------------------------

    function showAssignmentModal(assignment) {
        if (!modalOverlay) return;

        modalTitle.textContent = assignment.title;
        modalSubject.textContent = assignment.subject_name + ' (' + assignment.subject_code + ')';
        modalDueDate.textContent = formatDate(assignment.due_date);
        modalInstructions.textContent = assignment.instructions || 'No instructions provided.';

        // Set status
        const statusText = getStatusText(assignment.submission_status, assignment.is_past_due);
        const badgeClass = getStatusBadge(assignment.submission_status, assignment.is_past_due);
        modalStatus.textContent = statusText;
        modalStatus.className = 'px-2.5 py-1 text-xs font-medium rounded-full ' + badgeClass;

        // Set score
        if (assignment.submitted_score !== null) {
            modalScore.textContent = assignment.submitted_score + ' / ' + assignment.max_score;
            modalScore.className = 'text-sm font-semibold text-gray-900';
        } else {
            modalScore.textContent = 'Not yet graded';
            modalScore.className = 'text-sm text-gray-500';
        }

        // Show/hide submit form based on status
        const canSubmit = !assignment.is_past_due && 
                         (assignment.submission_status === null || 
                          assignment.submission_status === 'Pending');
        
        modalSubmitForm.style.display = canSubmit ? 'block' : 'none';
        
        if (!canSubmit && assignment.submission_status === 'Graded') {
            modalSubmitForm.innerHTML = `
                <div class="bg-green-50 border border-green-200 rounded p-3 text-center">
                    <p class="text-sm text-green-700">✓ This assignment has been graded.</p>
                </div>
            `;
            modalSubmitForm.style.display = 'block';
        } else if (!canSubmit && assignment.is_past_due) {
            modalSubmitForm.innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded p-3 text-center">
                    <p class="text-sm text-red-700">⏰ This assignment is past the due date.</p>
                </div>
            `;
            modalSubmitForm.style.display = 'block';
        }

        modalOverlay.classList.remove('hidden');
        modalOverlay.classList.add('flex');
        document.body.classList.add('overflow-hidden');

        // Reset form
        modalFileInput.value = '';
        modalAlert.classList.add('hidden');
        modalAlert.textContent = '';
        setLoading(false);
    }

    function closeModal() {
        if (!modalOverlay) return;
        modalOverlay.classList.add('hidden');
        modalOverlay.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
        currentAssignment = null;
    }

    function showModalError(message) {
        if (!modalAlert) return;
        modalAlert.textContent = message;
        modalAlert.className = 'p-3 rounded border border-red-300 bg-red-50 text-red-800 text-sm mb-4';
        modalAlert.classList.remove('hidden');
    }

    function showModalSuccess(message) {
        if (!modalAlert) return;
        modalAlert.textContent = message;
        modalAlert.className = 'p-3 rounded border border-green-300 bg-green-50 text-green-800 text-sm mb-4';
        modalAlert.classList.remove('hidden');
    }

    function setLoading(isLoading) {
        if (!modalSubmitBtn) return;
        if (isLoading) {
            modalSubmitBtn.disabled = true;
            modalSubmitBtn.textContent = 'Submitting...';
        } else {
            modalSubmitBtn.disabled = false;
            modalSubmitBtn.textContent = 'Submit Assignment';
        }
    }

    // ---- Event Handlers ----------------------------------------------------

    function handleFilterSubmit(e) {
        if (e) e.preventDefault();

        currentFilters.subject_id = subjectFilter ? subjectFilter.value : null;
        currentFilters.semester = semesterFilter ? semesterFilter.value : null;
        currentFilters.status = statusFilter ? statusFilter.value : null;
        currentFilters.sort_by = sortBySelect ? sortBySelect.value : 'due_date';
        currentFilters.sort_order = sortOrderSelect ? sortOrderSelect.value : 'ASC';

        loadAssignments();
    }

    function handleResetFilters() {
        if (subjectFilter) subjectFilter.value = '';
        if (semesterFilter) semesterFilter.value = '';
        if (statusFilter) statusFilter.value = '';
        if (sortBySelect) sortBySelect.value = 'due_date';
        if (sortOrderSelect) sortOrderSelect.value = 'ASC';

        currentFilters = {
            subject_id: null,
            semester: null,
            status: null,
            sort_by: 'due_date',
            sort_order: 'ASC'
        };

        loadAssignments();
    }

    function handleModalSubmit(e) {
        e.preventDefault();

        if (!currentAssignment) return;

        const file = modalFileInput.files[0];
        if (!file) {
            showModalError('Please select a file to upload.');
            return;
        }

        // Validate file size (10MB max)
        if (file.size > 10 * 1024 * 1024) {
            showModalError('File size must be less than 10MB.');
            return;
        }

        // Validate file type
        const allowedTypes = ['application/pdf', 'application/msword', 
                             'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                             'image/jpeg', 'image/png', 'image/jpg'];
        if (!allowedTypes.includes(file.type)) {
            showModalError('Please upload a PDF, DOC, DOCX, or image file.');
            return;
        }

        showModalError(''); // Clear any previous error
        submitAssignment(currentAssignment.assignment_id, file);
    }

    // ---- Helpers -----------------------------------------------------------

    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function showError(message) {
        // Simple error handling - you can implement a toast notification system
        console.error(message);
        const errorContainer = document.getElementById('errorContainer');
        if (errorContainer) {
            errorContainer.textContent = message;
            errorContainer.className = 'p-3 rounded border border-red-300 bg-red-50 text-red-800 text-sm mb-4';
            errorContainer.classList.remove('hidden');
            setTimeout(() => {
                errorContainer.classList.add('hidden');
            }, 5000);
        }
    }

    // ---- Expose functions globally -----------------------------------------

    window.openAssignment = function(assignmentId) {
        loadAssignmentDetails(assignmentId);
    };

    // ---- Initialize --------------------------------------------------------

    function init() {
        // Load initial assignments
        loadAssignments();

        // Event listeners
        if (filterForm) {
            filterForm.addEventListener('submit', handleFilterSubmit);
        }

        if (applyFiltersBtn) {
            applyFiltersBtn.addEventListener('click', handleFilterSubmit);
        }

        if (resetFiltersBtn) {
            resetFiltersBtn.addEventListener('click', handleResetFilters);
        }

        if (modalCloseBtn) {
            modalCloseBtn.addEventListener('click', closeModal);
        }

        // Close modal on overlay click
        if (modalOverlay) {
            modalOverlay.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modalOverlay && !modalOverlay.classList.contains('hidden')) {
                closeModal();
            }
        });

        if (modalSubmitForm) {
            modalSubmitForm.addEventListener('submit', handleModalSubmit);
        }

        // Quick filter buttons
        document.querySelectorAll('[data-filter-status]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const status = this.dataset.filterStatus;
                if (statusFilter) {
                    statusFilter.value = status;
                }
                handleFilterSubmit();
            });
        });

        console.log('Assignment module initialized.');
    }

    // Run when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
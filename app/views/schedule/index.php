<?php
if (!isset($calendar_events)) {
    require dirname(__DIR__, 2) . "/backend/modules/schedule/controller/ScheduleController.php";
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Schedule</title>
    <link rel="stylesheet" href="/LearningMS/app/backend/modules/schedule/js/output.css">
    <link rel="stylesheet" href="/LearningMS/app/backend/modules/schedule/js/assets/app.bundle.css">
</head>
<body>
    <div id="react-alert-root"></div>
    <div class="schedule-page min-h-screen bg-[#f4f7fb] px-4 py-6 text-slate-900 sm:px-8 lg:px-12">
        <div class="mx-auto max-w-[1500px]">
            <header class="mb-8 flex flex-col gap-5 border-b border-slate-200 pb-6 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="mb-2 text-xs font-bold uppercase tracking-[0.24em] text-teal-600">
                        Academic planner / <?= htmlspecialchars($role_label) ?> view
                    </p>
                    <h1 class="text-4xl font-black tracking-tight text-slate-950">Schedule</h1>
                    <p class="mt-2 text-slate-500">Keep classes, commitments, and campus moments in one clear rhythm.</p>
                </div>
                                <a href="?role=<?= $switch_role ?>&date=<?= urlencode(
    $selected_date,
) ?>" class="rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 no-underline transition hover:border-teal-500 hover:text-teal-700">
                    <?= $switch_label ?>
                </a>
            </header>

            <?php if ($errors): ?>
                <div class="mb-4 rounded-xl bg-red-50 p-4 text-red-700"><?= htmlspecialchars(
                  implode(" ", $errors),
                ) ?></div>
            <?php endif; ?>
            <?php if ($database_error): ?>
                <div class="mb-4 rounded-xl bg-red-50 p-4 text-red-700">Database error: <?= htmlspecialchars(
                  $database_error,
                ) ?></div>
            <?php endif; ?>
            <?php if ($success_message): ?>
                <div class="mb-4 rounded-xl bg-emerald-50 p-4 text-emerald-700"><?= htmlspecialchars(
                  $success_message,
                ) ?></div>
            <?php endif; ?>

            <main class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
                <section class="relative rounded-2xl border border-slate-200 bg-white p-4 shadow-[0_18px_45px_rgba(15,23,42,0.06)] sm:p-6">
                    <div id="calendar"></div>
                    <div id="event-editor" class="schedule-editor" hidden>
                        <form method="post" id="event-form">
                            <input type="hidden" name="action" id="event-action" value="create">
                            <input type="hidden" name="event_id" id="event-id">
                            <div class="schedule-editor__header">
                                <h2 id="event-title" class="text-lg font-black"><?= $role ===
                                "student"
                                  ? "Add note"
                                  : "Add schedule event" ?></h2>
                                <button type="button" class="schedule-icon-button" id="close-editor" aria-label="Close editor">&times;</button>
                            </div>
                            <div class="schedule-editor__body">
                                <label class="form-label fw-semibold" for="title">Title</label>
                                <input class="form-control mb-3" id="title" name="title" maxlength="150" required>
                                <label class="form-label fw-semibold" for="subject_id">Subject</label>
                                <select class="form-select subject-select mb-3" id="subject_id" name="subject_id" required>
                                    <option value="">Choose a subject</option>
                                    <?php foreach ($subjects as $subject): ?>
                                        <option value="<?= (int) $subject["subject_id"] ?>">
                                            <?= htmlspecialchars(
                                              $subject["subject_code"] .
                                                " - " .
                                                $subject["subject_name"],
                                            ) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if ($role === "teacher"): ?>
                                    <label class="form-label fw-semibold" for="event_type">Event type</label>
                                    <select class="form-select mb-3" id="event_type" name="event_type">
                                        <option>Personal</option>
                                        <option>Quiz</option>
                                        <option>Review</option>
                                        <option>Announcement</option>
                                    </select>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="share_to_section" name="share_to_section" value="1">
                                        <label class="form-check-label" for="share_to_section">Share to my section</label>
                                    </div>
                                <?php else: ?>
                                    <input type="hidden" name="event_type" value="Personal">
                                <?php endif; ?>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold" for="start_datetime">Starts</label>
                                        <input class="form-control" id="start_datetime" type="datetime-local" name="start_datetime" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold" for="end_datetime">Ends</label>
                                        <input class="form-control" id="end_datetime" type="datetime-local" name="end_datetime" required>
                                    </div>
                                </div>
                                <label class="form-label fw-semibold mt-3" for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                            <div class="schedule-editor__footer">
                                <button type="button" class="btn btn-light" id="cancel-editor">Cancel</button>
                                <button class="btn btn-primary" id="event-submit"><?= $role ===
                                "student"
                                  ? "Save note"
                                  : "Save event" ?></button>
                            </div>
                        </form>
                    </div>
                </section>

                <aside class="rounded-2xl border border-slate-200 bg-white p-5 text-slate-900 shadow-[0_18px_45px_rgba(15,23,42,0.08)] sm:p-6">
                    <div class="mb-6 flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-teal-600">Daily view</p>
                            <h2 class="mt-2 text-2xl font-black">Events</h2>
                            <p class="mt-1 text-sm text-slate-500"><?= date(
                              "D, M j, Y",
                              strtotime($selected_date),
                            ) ?></p>
                        </div>
                        <span class="rounded-full bg-teal-50 px-3 py-1 text-xs font-bold text-teal-700"><?= count(
                          $selected_day_events,
                        ) ?></span>
                    </div>
                    <div class="space-y-3">
                        <?php if (!$selected_day_events): ?>
                            <div class="rounded-xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-400">No events for this day.</div>
                        <?php else: ?>
                            <?php foreach ($selected_day_events as $event): ?>
                                <?php $is_owner =
                                  $event["created_by_role"] ===
                                    ($role === "student" ? "Student" : "Teacher") &&
                                  (int) $event["created_by_id"] === 1; ?>
                                <article class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <h3 class="font-bold"><?= htmlspecialchars(
                                          $event["title"],
                                        ) ?></h3>
                                        <span class="rounded-full bg-teal-50 px-2 py-1 text-[10px] font-bold uppercase text-teal-700"><?= htmlspecialchars(
                                          $event["status"],
                                        ) ?></span>
                                    </div>
                                    <p class="mt-2 text-xs text-slate-500"><?= date(
                                      "g:i A",
                                      strtotime($event["start_datetime"]),
                                    ) ?> - <?= date(
   "g:i A",
   strtotime($event["end_datetime"]),
 ) ?> · <?= htmlspecialchars($event["subject_code"] ?? "General") ?></p>
                                    <?php if (
                                      $event["description"]
                                    ): ?><p class="mt-3 text-sm leading-6 text-slate-600"><?= htmlspecialchars(
  $event["description"],
) ?></p><?php endif; ?>
                                    <?php if ($is_owner): ?>
                                        <div class="mt-4 flex gap-2">
                                            <button type="button" class="edit-event btn btn-sm btn-outline-secondary" data-event="<?= htmlspecialchars(
                                              json_encode($event),
                                              ENT_QUOTES,
                                            ) ?>">Edit</button>
                                            <button type="button" class="delete-event btn btn-sm btn-outline-danger" data-event-id="<?= (int) $event[
                                              "event_id"
                                            ] ?>">Delete</button>
                                        </div>
                                    <?php endif; ?>
                                </article>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </aside>
            </main>
        </div>
    </div>

    <div id="delete-dialog" class="schedule-dialog" hidden>
        <div class="schedule-dialog__card" role="alertdialog" aria-modal="true" aria-labelledby="delete-dialog-title">
            <div class="flex items-start justify-between gap-4">
                <h2 id="delete-dialog-title" class="text-xl font-black">Delete this item?</h2>
                <button type="button" class="schedule-icon-button" id="close-delete-dialog" aria-label="Close dialog">&times;</button>
            </div>
            <dl id="delete-details" class="schedule-dialog__details"></dl>
            <p id="delete-dialog-message" class="mt-4 text-sm text-slate-500">This action cannot be undone.</p>
            <form method="post" id="delete-form" class="mt-6 flex justify-end gap-2">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="event_id" id="delete-event-id">
                <button type="button" class="btn btn-light" id="cancel-delete">Cancel</button>
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>

    <div id="save-dialog" class="schedule-dialog" hidden>
        <div class="schedule-dialog__card" role="alertdialog" aria-modal="true" aria-labelledby="save-dialog-title">
            <h2 id="save-dialog-title" class="text-xl font-black">Confirm changes</h2>
            <p id="save-dialog-message" class="mt-2 text-sm text-slate-500"></p>
            <dl id="save-details" class="schedule-dialog__details"></dl>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" class="btn btn-light" id="cancel-save">Review</button>
                <button type="button" class="btn btn-primary" id="confirm-save">Confirm</button>
            </div>
        </div>
    </div>

    <script src="/LearningMS/app/backend/modules/schedule/js/assets/app.bundle.js"></script>
    <script>
        function initializeSchedule() {
            var selectedDate = <?= json_encode($selected_date) ?>;
            var editor = document.getElementById('event-editor');
            var form = document.getElementById('event-form');
            var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                plugins: [FullCalendarPlugins.dayGridPlugin, FullCalendarPlugins.interactionPlugin],
                initialView: 'dayGridMonth',
                initialDate: selectedDate,
                height: 'auto',
                dayMaxEvents: 3,
                headerToolbar: { left: 'prev,next today addEvent', center: 'title', right: 'dayGridMonth,dayGridWeek' },
                customButtons: {
                    addEvent: {
                        text: '+ <?= $role === "student" ? "Add note" : "Add event" ?>',
                        click: function () { openEditor(selectedDate, dateTile(selectedDate)); }
                    }
                },
                events: <?= json_encode(
                  $calendar_events,
                  JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT,
                ) ?>,
                dateClick: function (info) {
                    selectedDate = info.dateStr;
                    window.location.href = '?role=<?= $role ?>&date=' + encodeURIComponent(selectedDate);
                },
                eventClick: function (info) {
                    if (info.event.extendedProps.editable) {
                        openEditor(info.event.startStr.slice(0, 10), info.el, info.event.extendedProps.event);
                    } else if ('<?= $role ?>' === 'student') {
                        openViewDialog(info.event);
                    }
                }
            });
            calendar.render();

            function dateTile(date) {
                return document.querySelector('.fc-daygrid-day[data-date="' + date + '"]');
            }

            function openEditor(date, anchor, event) {
                selectedDate = date;
                form.reset();
                document.getElementById('event-action').value = event ? 'update' : 'create';
                document.getElementById('event-id').value = event ? event.event_id : '';
                document.getElementById('event-title').textContent = event ? 'Edit <?= $role ===
                "student"
                  ? "note"
                  : "event" ?>' : '<?= $role === "student" ? "Add note" : "Add schedule event" ?>';
                document.getElementById('event-submit').textContent = event ? 'Update' : '<?= $role ===
                "student"
                  ? "Save note"
                  : "Save event" ?>';
                document.getElementById('start_datetime').value = event ? event.start_datetime.replace(' ', 'T').slice(0, 16) : date + 'T09:00';
                document.getElementById('end_datetime').value = event ? event.end_datetime.replace(' ', 'T').slice(0, 16) : date + 'T10:00';
                if (event) {
                    document.getElementById('title').value = event.title;
                    document.getElementById('subject_id').value = event.subject_id || '';
                    document.getElementById('description').value = event.description || '';
                    var type = document.getElementById('event_type');
                    if (type) type.value = event.event_type;
                    var share = document.getElementById('share_to_section');
                    if (share) share.checked = event.section_id !== null;
                }
                editor.hidden = false;
                positionEditor(anchor);
            }

            function positionEditor(anchor) {
                editor.style.left = '50%';
                editor.style.top = '72px';
                editor.style.transform = 'translateX(-50%)';
                if (!anchor) return;
                var calendarBox = document.getElementById('calendar').getBoundingClientRect();
                var anchorBox = anchor.getBoundingClientRect();
                var editorWidth = editor.offsetWidth;
                var left = anchorBox.left - calendarBox.left;
                left = Math.max(12, Math.min(left, calendarBox.width - editorWidth - 12));
                editor.style.left = left + 'px';
                editor.style.top = Math.max(60, anchorBox.bottom - calendarBox.top + 8) + 'px';
                editor.style.transform = 'none';
            }

            function closeEditor() { editor.hidden = true; }
            document.getElementById('close-editor').addEventListener('click', closeEditor);
            document.getElementById('cancel-editor').addEventListener('click', closeEditor);
            var saveDialog = document.getElementById('save-dialog');
            var saveDetails = document.getElementById('save-details');
            var saveMessage = document.getElementById('save-dialog-message');
            var confirmSave = document.getElementById('confirm-save');
            var pendingSave = false;

            function escapeHtml(value) {
                return String(value || '').replace(/[&<>'"]/g, function (character) {
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
                target.innerHTML = details.map(function (item) {
                    return '<div><dt>' + escapeHtml(item[0]) + '</dt><dd>' + escapeHtml(item[1]) + '</dd></div>';
                }).join('');
            }

            form.addEventListener('submit', function (event) {
                if (pendingSave) return;
                event.preventDefault();
                renderDetails(saveDetails, formDetails());
                saveMessage.textContent = document.getElementById('event-action').value === 'update' ? 'Review the updated information before saving.' : 'Review the new item before adding it to your schedule.';
                saveDialog.hidden = false;
            });
            document.getElementById('cancel-save').addEventListener('click', function () { saveDialog.hidden = true; });
            confirmSave.addEventListener('click', function () {
                pendingSave = true;
                saveDialog.hidden = true;
                form.submit();
            });
            document.querySelectorAll('.edit-event').forEach(function (button) {
                button.addEventListener('click', function () {
                    var event = JSON.parse(button.dataset.event);
                    var calendarEvent = calendar.getEventById('event-' + event.event_id);
                    openEditor(event.start_datetime.slice(0, 10), calendarEvent ? calendarEvent.el : button, event);
                });
            });

            var deleteDialog = document.getElementById('delete-dialog');
            var deleteDialogTitle = document.getElementById('delete-dialog-title');
            var deleteDialogMessage = document.getElementById('delete-dialog-message');
            var deleteForm = document.getElementById('delete-form');
            document.querySelectorAll('.delete-event').forEach(function (button) {
                button.addEventListener('click', function () {
                    var event = JSON.parse(button.closest('article').querySelector('.edit-event').dataset.event);
                    deleteDialogTitle.textContent = 'Delete this item?';
                    deleteDialogMessage.textContent = 'This action cannot be undone.';
                    deleteDialogMessage.hidden = false;
                    deleteForm.hidden = false;
                    document.getElementById('delete-event-id').value = button.dataset.eventId;
                    renderDetails(document.getElementById('delete-details'), [
                        ['Title', event.title],
                        ['Description', event.description || 'None'],
                        ['Subject', event.subject_code || 'General'],
                        ['Starts', event.start_datetime],
                        ['Ends', event.end_datetime]
                    ]);
                    deleteDialog.hidden = false;
                });
            });
            document.getElementById('cancel-delete').addEventListener('click', function () { deleteDialog.hidden = true; });

            function openViewDialog(calendarEvent) {
                var event = calendarEvent.extendedProps.event || {};
                var props = calendarEvent.extendedProps;
                deleteDialogTitle.textContent = 'Event details';
                deleteDialogMessage.hidden = true;
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
                deleteDialog.hidden = false;
            }

            function formatEventDate(date) {
                return date ? new Intl.DateTimeFormat(undefined, {
                    dateStyle: 'medium',
                    timeStyle: 'short'
                }).format(date) : 'Not specified';
            }

            document.getElementById('close-delete-dialog').addEventListener('click', function () { deleteDialog.hidden = true; });
            window.addEventListener('resize', function () { if (!editor.hidden) positionEditor(null); });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeSchedule);
        } else {
            initializeSchedule();
        }
    </script>
</body>
</html>

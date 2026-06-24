<?php
$currentPage = 'planner';
ob_start();
?>

<?php /* Page styles moved to public/assets/css/pages/admin-planner.css (auto-linked by layout.php) */ ?>

<!-- Quill Rich Text Editor (Free, no API key required) -->
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

<div class="planner-container">
    <div class="planner-tabs">
        <div class="tabs-header">
            <button class="tab-button active" onclick="switchTab('notes')">
                <span>📝</span> Notes
            </button>
            <button class="tab-button" onclick="switchTab('todos')">
                <span>✓</span> Tasks
            </button>
            <button class="tab-button" onclick="switchTab('documents')">
                <span>📁</span> Documents
            </button>
            <button class="tab-button" onclick="switchTab('activity')">
                <span>📊</span> Activity
            </button>
            <button class="tab-button" onclick="switchTab('templates')">
                <span>📄</span> Templates
            </button>
            <button class="tab-button" onclick="switchTab('meetings')">
                <span>📅</span> Meetings
            </button>
            <a href="<?= url('admin/planner/me') ?>" class="tab-button" style="text-decoration:none; margin-left:auto; background:linear-gradient(135deg,#00b207,#009906); color:white; border-radius:10px 10px 0 0; flex:0 0 auto; padding: 12px 20px;">
                <span>👤</span> My Dashboard
            </a>
        </div>

        <!-- Notes Tab -->
        <div id="notes-tab" class="tab-content active">
            <div class="section-header">
                <h2>Notes</h2>
            </div>

            <div class="add-note-form" id="addNoteFormWrapper" style="position: relative;">
                <div class="note-scope-toggle">
                    <button class="active team" id="scopeTeamBtn" onclick="setNoteScope('team', this)">Team</button>
                    <button class="personal" id="scopePersonalBtn" onclick="setNoteScope('personal', this)">Personal</button>
                </div>
                <div id="noteInput" class="note-editable" contenteditable="true" data-placeholder="Drop your ideas here..."></div>
                <button id="addNoteBtn" class="btn-planner primary" onclick="addNote()" style="margin-top: 10px;">Add Note</button>
            </div>

            <div class="note-filter-tabs">
                <button class="note-filter-tab active" onclick="filterNotes('team')" id="noteFilterTeam">Team <span id="teamNoteCount"></span></button>
                <button class="note-filter-tab" onclick="filterNotes('personal')" id="noteFilterPersonal">Personal <span id="personalNoteCount"></span></button>
                <button class="note-filter-tab" onclick="filterNotes('archived')" id="noteFilterArchived">Archived <span id="archivedNoteCount"></span></button>
            </div>

            <div class="notes-list" id="notesList"></div>
        </div>

        <!-- Todos Tab -->
        <div id="todos-tab" class="tab-content">
            <div class="section-header">
                <h2>Todo List</h2>
            </div>

            <div class="add-todo-form">
                <div class="todo-input-group">
                    <input type="text" id="todoInput" placeholder="Task title..."
                           onkeydown="if(event.key==='Enter' && !event.shiftKey && !document.getElementById('todoExpandedFields').style.display !== 'none'){event.preventDefault(); addTodo();}">
                    <select id="todoPriority" style="min-width:130px;">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                    <select id="assignSelect">
                        <option value="">Assign to...</option>
                    </select>
                </div>

                <div class="todo-expand-toggle">
                    <button type="button" class="btn-planner small" onclick="toggleTodoExpand()" id="todoExpandBtn">
                        + Description / Checklist
                    </button>
                </div>

                        <div id="todoExpandedFields" style="display: none;">
                            <div class="todo-description-group">
                                <textarea id="todoDescription" placeholder="Add description or notes (optional)..." rows="3"></textarea>
                            </div>
                            <div class="todo-checklist-group">
                                <label class="checklist-label">Checklist Items</label>
                                <div id="todoChecklistInputs"></div>
                                <button type="button" class="btn-planner small" onclick="addChecklistInput()" style="background: transparent; color: #64748b; border: 1px dashed #d1d5db; font-size: 13px; padding: 6px 14px;">+ Add Item</button>
                            </div>
                        </div>

                        <button class="btn-planner primary" onclick="addTodo()">Add Task</button>
                    </div>

                <div class="filter-tabs">
                    <button class="filter-tab active" onclick="filterTodos('all', this)">All</button>
                    <button class="filter-tab" onclick="filterTodos('active', this)">Active</button>
                    <button class="filter-tab" onclick="filterTodos('completed', this)">Completed</button>
                </div>
                <div class="filter-tabs" id="priorityFilterTabs" style="margin-top:-8px; padding-top:0; border-bottom:none;">
                    <button class="filter-tab active" onclick="filterByPriority('all', this)">All Priorities</button>
                    <button class="filter-tab" onclick="filterByPriority('urgent', this)" style="color:#b91c1c;">Urgent</button>
                    <button class="filter-tab" onclick="filterByPriority('high', this)" style="color:#c2410c;">High</button>
                    <button class="filter-tab" onclick="filterByPriority('medium', this)">Medium</button>
                    <button class="filter-tab" onclick="filterByPriority('low', this)">Low</button>
                </div>

                <div class="todos-list" id="todosList"></div>
            </div>
        </div>

        <!-- Documents Tab -->
        <div id="documents-tab" class="tab-content">
            <div class="section-header">
                <h2>Documents</h2>
            </div>

            <div class="upload-form">
                <form id="uploadForm" onsubmit="uploadDocument(event)">
                    <div class="file-input-wrapper">
                        <input type="file" id="fileInput" name="file" required onchange="handleFileSelect(event)">
                        <label for="fileInput" class="file-input-label" id="fileLabel">
                            <span style="font-size: 32px;">📁</span>
                            <span>Click to upload or drag & drop files here</span>
                        </label>
                    </div>
                    <div id="selectedFile" class="selected-file" style="display: none;"></div>
                    <button type="submit" class="btn-planner primary" id="uploadBtn" style="margin-top: 10px; display: none;">Upload Document</button>
                </form>
            </div>

            <div class="documents-grid" id="documentsList"></div>
        </div>

        <!-- Activity Tab -->
        <div id="activity-tab" class="tab-content">
            <div class="section-header">
                <h2>Team Activity</h2>
            </div>
            <div id="activityListFull" style="display: grid; gap: 10px;"></div>
        </div>

        <!-- Templates Tab - HTML Editor -->
        <div id="templates-tab" class="tab-content">
            <div class="html-editor-container">
                <!-- Sidebar with saved templates -->
                <div class="editor-sidebar">
                    <div class="sidebar-header">
                        <h4>Saved Documents</h4>
                        <div style="display:flex;align-items:center;gap:4px;">
                            <select id="templateCategoryFilter" onchange="loadTemplates()" style="padding: 4px 8px; font-size: 11px; border: 1px solid #ddd; border-radius: 4px;">
                                <option value="all">All</option>
                            </select>
                            <button onclick="openManageCategoriesModal()" title="Manage Categories" style="background:none;border:1px solid #ddd;border-radius:4px;padding:4px 6px;cursor:pointer;font-size:11px;color:#666;line-height:1;">
                                <i class="fa-solid fa-gear"></i>
                            </button>
                        </div>
                    </div>
                    <div class="sidebar-files" id="templatesSidebar">
                        <div class="empty-sidebar">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p>No documents yet</p>
                        </div>
                    </div>
                </div>

                <!-- Main editor area -->
                <div class="editor-main">
                    <!-- Sidebar Toggle Button -->
                    <button class="sidebar-toggle" id="sidebarToggle" onclick="toggleEditorSidebar()" title="Toggle Sidebar">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>

                    <div class="editor-toolbar">
                        <div class="toolbar-left">
                            <span class="toolbar-title" id="currentDocTitle">HTML Editor</span>
                            <div class="view-edit-toggle" id="viewEditToggle">
                                <button class="active" id="viewModeBtn" onclick="switchEditorMode('view')">
                                    <i class="fa-solid fa-eye"></i> View
                                </button>
                                <button id="editModeBtn" onclick="switchEditorMode('edit')">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </button>
                            </div>
                            <div class="edit-mode-indicator" id="editModeIndicator" style="display:none;">
                                <span class="status-dot"></span>
                                Edit Mode
                            </div>
                            <div class="view-mode-indicator" id="viewModeIndicator">
                                <i class="fa-solid fa-eye" style="font-size:10px;"></i>
                                View Mode
                            </div>
                            <div class="saved-indicator" id="htmlSavedIndicator">✓ Saved</div>
                        </div>
                        <div class="toolbar-actions">
                            <button class="btn-toolbar secondary" onclick="openLoadHtmlModal()">
                                <i class="fa-solid fa-upload"></i> Load HTML
                            </button>
                            <button class="btn-toolbar secondary" onclick="openNewTemplateModal()">
                                <i class="fa-solid fa-plus"></i> New
                            </button>
                            <button class="btn-toolbar secondary" onclick="saveCurrentTemplate()">
                                <i class="fa-solid fa-save"></i> Save
                            </button>
                            <button class="btn-toolbar secondary" onclick="exportHtmlContent()">
                                <i class="fa-solid fa-code"></i> Export
                            </button>
                            <button class="btn-toolbar secondary" onclick="downloadHtmlContent()">
                                <i class="fa-solid fa-file-code"></i> HTML
                            </button>
                            <button class="btn-toolbar primary" onclick="downloadPdfContent()">
                                <i class="fa-solid fa-file-pdf"></i> PDF
                            </button>
                            <button class="btn-toolbar secondary" onclick="openFullscreenView()" title="Full View" id="fullscreenBtn">
                                <i class="fa-solid fa-expand"></i>
                            </button>
                        </div>
                    </div>
                    <div class="editor-content-area">
                        <!-- View Mode: sandboxed iframe -->
                        <iframe id="htmlViewerFrame" style="display:block;"></iframe>
                        <!-- Edit Mode: editable content -->
                        <div class="editable-content" id="htmlEditorContent" style="display:none;">
                            <div class="welcome-state">
                                <h3>Welcome to HTML Editor</h3>
                                <p>Select a document from the sidebar or create a new one</p>
                                <button class="btn-planner primary" onclick="openNewTemplateModal()" style="margin-top: 20px;">
                                    <i class="fa-solid fa-plus"></i> Create New Document
                                </button>
                                <button class="btn-planner secondary" onclick="openLoadHtmlModal()" style="margin-left: 10px;">
                                    <i class="fa-solid fa-upload"></i> Load HTML
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Meetings Tab -->
        <div id="meetings-tab" class="tab-content">
            <div class="meetings-container">
                <!-- Meetings List View -->
                <div id="meetingsListView" class="meetings-list-view">
                    <div class="meetings-header">
                        <h2>Meeting Minutes</h2>
                        <button class="btn-planner primary" onclick="createNewMeeting()">
                            <i class="fa-solid fa-plus"></i> New Meeting
                        </button>
                    </div>

                    <div class="meetings-filters">
                        <select id="meetingStatusFilter" onchange="loadMeetings()">
                            <option value="all">All Status</option>
                            <option value="draft">Draft</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="sent">Sent</option>
                        </select>
                        <input type="month" id="meetingMonthFilter" onchange="loadMeetings()">
                    </div>

                    <div id="meetingsList" class="meetings-list">
                        <div class="empty-meetings">
                            <i class="fa-solid fa-calendar-days" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
                            <p>No meetings yet</p>
                            <button class="btn-planner primary" onclick="createNewMeeting()" style="margin-top: 15px;">
                                Create Your First Meeting
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Meeting Editor View -->
                <div id="meetingEditorView" class="meeting-editor-view" style="display: none;">
                    <div class="meeting-editor-header">
                        <button class="btn-planner secondary" onclick="backToMeetingsList()">
                            <i class="fa-solid fa-arrow-left"></i> Back
                        </button>
                        <div class="meeting-status-badge" id="meetingStatusBadge">Draft</div>
                        <div class="meeting-actions">
                            <button class="btn-planner secondary" onclick="saveMeeting()">
                                <i class="fa-solid fa-save"></i> Save
                            </button>
                            <button class="btn-planner secondary" onclick="generateMeetingEmail()">
                                <i class="fa-solid fa-envelope"></i> Generate Email
                            </button>
                            <button class="btn-planner primary" onclick="openSendEmailModal()" id="sendEmailBtn" style="display: none;">
                                <i class="fa-solid fa-paper-plane"></i> Send Email
                            </button>
                        </div>
                    </div>

                    <div class="meeting-editor-content">
                        <div class="meeting-form-section">
                            <h3>Meeting Details</h3>
                            <input type="hidden" id="meetingId">

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Meeting Title *</label>
                                    <input type="text" id="meetingTitle" placeholder="e.g., Weekly Team Sync">
                                </div>
                            </div>

                            <div class="form-row three-col">
                                <div class="form-group">
                                    <label>Date *</label>
                                    <input type="date" id="meetingDate">
                                </div>
                                <div class="form-group">
                                    <label>Time</label>
                                    <input type="time" id="meetingTime">
                                </div>
                                <div class="form-group">
                                    <label>Location</label>
                                    <input type="text" id="meetingLocation" placeholder="e.g., Office / Zoom">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Link to Previous Meeting</label>
                                    <select id="previousMeetingId">
                                        <option value="">-- None --</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="meeting-form-section">
                            <h3>Attendees</h3>
                            <div class="attendees-controls">
                                <select id="teamMemberSelect">
                                    <option value="">-- Select Team Member --</option>
                                </select>
                                <button class="btn-planner secondary" onclick="addTeamMemberAttendee()">
                                    <i class="fa-solid fa-plus"></i> Add
                                </button>
                            </div>
                            <div class="manual-attendee-row">
                                <input type="text" id="manualAttendeeName" placeholder="Name">
                                <input type="email" id="manualAttendeeEmail" placeholder="Email">
                                <button class="btn-planner secondary" onclick="addManualAttendee()">
                                    <i class="fa-solid fa-plus"></i> Add Manual
                                </button>
                            </div>
                            <div id="attendeesList" class="attendees-list"></div>
                        </div>

                        <div class="meeting-form-section">
                            <h3>Meeting Notes</h3>
                            <textarea id="meetingNotes" class="meeting-notes-textarea" placeholder="Take notes during the meeting..."></textarea>
                        </div>

                        <div class="meeting-form-section">
                            <h3>Agenda Items</h3>
                            <div class="items-add-row">
                                <input type="text" id="newAgendaItem" placeholder="Add agenda item...">
                                <button class="btn-planner secondary" onclick="addMeetingItem('agenda')">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                            <div id="agendaItemsList" class="meeting-items-list"></div>
                        </div>

                        <div class="meeting-form-section">
                            <h3>Discussion Points</h3>
                            <div class="items-add-row">
                                <input type="text" id="newDiscussionItem" placeholder="Add discussion point...">
                                <button class="btn-planner secondary" onclick="addMeetingItem('discussion')">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                            <div id="discussionItemsList" class="meeting-items-list"></div>
                        </div>

                        <div class="meeting-form-section">
                            <h3>Decisions Made</h3>
                            <div class="items-add-row">
                                <input type="text" id="newDecisionItem" placeholder="Add decision...">
                                <button class="btn-planner secondary" onclick="addMeetingItem('decision')">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                            <div id="decisionItemsList" class="meeting-items-list"></div>
                        </div>

                        <div class="meeting-form-section">
                            <h3>Action Items</h3>
                            <div class="action-add-row">
                                <input type="text" id="newActionDescription" placeholder="Action description...">
                                <select id="newActionAssignee">
                                    <option value="">Assign to...</option>
                                </select>
                                <input type="date" id="newActionDueDate">
                                <button class="btn-planner secondary" onclick="addMeetingAction()">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                            <div id="actionItemsList" class="action-items-list"></div>
                        </div>

                        <div class="meeting-form-section">
                            <h3>Next Meeting</h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Proposed Date</label>
                                    <input type="date" id="nextMeetingDate">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Topics for Next Meeting</label>
                                <textarea id="nextMeetingTopics" placeholder="Topics to discuss in the next meeting..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Meeting Email Modal -->
<div id="meetingEmailModal" class="html-editor-modal">
    <div class="html-modal-content" style="max-width: 800px;">
        <div class="html-modal-header">Meeting Minutes Email</div>
        <div class="html-form-group">
            <label>Email Subject</label>
            <input type="text" id="emailSubject" placeholder="Meeting Minutes - [Title]">
        </div>
        <div class="html-form-group">
            <label>Recipients (from attendees)</label>
            <div id="emailRecipientsList" class="email-recipients-list"></div>
        </div>
        <div class="html-form-group">
            <label>Email Preview</label>
            <iframe id="emailPreviewFrame" class="email-preview-frame" style="width:100%;min-height:400px;border:1px solid #e0e0e0;border-radius:6px;background:#fff;"></iframe>
        </div>
        <div class="html-modal-actions">
            <button class="btn-planner secondary" onclick="closeMeetingEmailModal()">Cancel</button>
            <button class="btn-planner primary" onclick="sendMeetingEmail()">
                <i class="fa-solid fa-paper-plane"></i> Send Email
            </button>
        </div>
    </div>
</div>

<!-- New Template Modal -->
<div id="newTemplateModal" class="html-editor-modal">
    <div class="html-modal-content">
        <div class="html-modal-header">Create New Document</div>
        <div class="html-form-group">
            <label for="newTemplateName">Document Name</label>
            <input type="text" id="newTemplateName" placeholder="e.g., Delivery Driver Job Description">
        </div>
        <div class="html-form-group">
            <label for="newTemplateCategory">Category</label>
            <select id="newTemplateCategory">
                <option value="general">Other</option>
            </select>
        </div>
        <div class="html-modal-actions">
            <button class="btn-planner secondary" onclick="closeNewTemplateModal()">Cancel</button>
            <button class="btn-planner primary" onclick="createNewTemplate()">Create Document</button>
        </div>
    </div>
</div>

<!-- Manage Categories Modal -->
<div id="manageCategoriesModal" class="html-editor-modal">
    <div class="html-modal-content" style="max-width:480px;">
        <div class="html-modal-header" style="display:flex;justify-content:space-between;align-items:center;">
            <span>Manage Categories</span>
            <button onclick="closeManageCategoriesModal()" style="background:none;border:none;font-size:18px;cursor:pointer;color:#666;">&times;</button>
        </div>

        <div id="manageCategoriesList" style="margin:16px 0;display:flex;flex-direction:column;gap:8px;max-height:320px;overflow-y:auto;"></div>

        <div style="border-top:1px solid #eee;padding-top:14px;margin-top:4px;">
            <label style="font-size:12px;font-weight:600;color:#555;display:block;margin-bottom:6px;">Add New Category</label>
            <div style="display:flex;gap:8px;">
                <input type="text" id="newCategoryName" placeholder="Category name" maxlength="100"
                       style="flex:1;padding:7px 10px;border:1px solid #ddd;border-radius:4px;font-size:13px;"
                       onkeydown="if(event.key==='Enter')addCategory()">
                <button onclick="addCategory()" class="btn-planner primary" style="white-space:nowrap;">Add</button>
            </div>
            <div id="manageCatError" style="color:#e74c3c;font-size:12px;margin-top:6px;display:none;"></div>
        </div>
    </div>
</div>

<!-- Load HTML Modal -->
<div id="loadHtmlModal" class="html-editor-modal">
    <div class="html-modal-content">
        <div class="html-modal-header">Load HTML Document</div>

        <div class="html-form-group">
            <label>Option 1: Upload File</label>
            <div style="display: flex; gap: 12px; align-items: center;">
                <input type="file" id="htmlFileInput" accept=".html,.htm" style="flex: 1;">
                <button class="btn-planner primary" onclick="loadHtmlFile()">Upload</button>
            </div>
        </div>

        <div class="html-form-group">
            <label>Option 2: Paste HTML Code</label>
            <textarea id="htmlPasteInput" placeholder="Paste your HTML code here..."></textarea>
        </div>

        <div class="html-modal-actions">
            <button class="btn-planner secondary" onclick="closeLoadHtmlModal()">Cancel</button>
            <button class="btn-planner primary" onclick="loadHtmlFromPaste()">Load HTML</button>
        </div>
    </div>
</div>

<!-- Export HTML Modal -->
<div id="exportHtmlModal" class="html-editor-modal">
    <div class="html-modal-content">
        <div class="html-modal-header">Exported HTML</div>
        <div class="code-output" id="exportCodeOutput"></div>
        <div class="html-modal-actions">
            <button class="btn-planner secondary" onclick="closeExportHtmlModal()">Close</button>
            <button class="btn-planner primary" onclick="copyExportedHtml()">Copy to Clipboard</button>
        </div>
    </div>
</div>

<!-- Save Template Modal (for naming when saving new) -->
<div id="saveTemplateModal" class="html-editor-modal">
    <div class="html-modal-content">
        <div class="html-modal-header">Save Document</div>
        <div class="html-form-group">
            <label for="saveTemplateName">Document Name</label>
            <input type="text" id="saveTemplateName" placeholder="Enter document name...">
        </div>
        <div class="html-form-group">
            <label for="saveTemplateCategory">Category</label>
            <select id="saveTemplateCategory">
                <option value="general">Other</option>
            </select>
        </div>
        <div class="html-form-group" id="saveChangeSummaryGroup" style="display: none;">
            <label for="saveChangeSummary">Change Summary</label>
            <input type="text" id="saveChangeSummary" placeholder="What did you change?">
        </div>
        <div class="html-modal-actions">
            <button class="btn-planner secondary" onclick="closeSaveTemplateModal()">Cancel</button>
            <button class="btn-planner primary" onclick="confirmSaveTemplate()">Save</button>
        </div>
    </div>
</div>

<!-- Template Editor Modal -->
<div id="templateEditorModal" class="template-modal" style="display: none;">
    <div class="template-modal-content">
        <div class="template-modal-header">
            <h3 id="templateEditorTitle">New Template</h3>
            <button onclick="closeTemplateEditor()" class="modal-close">&times;</button>
        </div>
        <div class="template-modal-body">
            <div class="template-form-row">
                <div class="template-form-group">
                    <label for="templateName">Template Name</label>
                    <input type="text" id="templateName" placeholder="e.g., Delivery Driver Job Description">
                </div>
                <div class="template-form-group">
                    <label for="templateCategory">Category</label>
                    <select id="templateCategory">
                        <option value="general">Other</option>
                    </select>
                </div>
            </div>
            <div class="editor-mode-toggle">
                <button type="button" class="mode-btn active" id="richTextModeBtn" onclick="switchCodeEditorMode('rich')">
                    Rich Text Editor
                </button>
                <button type="button" class="mode-btn" id="codeModeBtn" onclick="switchCodeEditorMode('code')">
                    HTML Code
                </button>
            </div>
            <div class="template-editor-container">
                <div class="template-editor-pane">
                    <div class="pane-header">
                        <span id="editorModeLabel">Rich Text Editor</span>
                        <button onclick="formatHtml()" class="btn-small" id="formatBtn" style="display: none;">Format</button>
                    </div>
                    <!-- Rich Text Editor (Quill) -->
                    <div id="richEditorContainer" style="height: calc(100% - 45px);">
                        <div id="quillEditor"></div>
                    </div>
                    <!-- Code Editor (textarea) -->
                    <div id="codeEditorContainer" style="display: none; height: 100%;">
                        <textarea id="templateContent" placeholder="Enter HTML content here..." style="height: 100%; width: 100%; border: none; padding: 15px; font-family: 'Consolas', 'Monaco', monospace; font-size: 13px; line-height: 1.5; resize: none;"></textarea>
                    </div>
                </div>
                <div class="template-preview-pane">
                    <div class="pane-header">
                        <span>Live Preview</span>
                        <button onclick="refreshPreview()" class="btn-small">Refresh</button>
                    </div>
                    <iframe id="templatePreview"></iframe>
                </div>
            </div>
            <div class="template-form-group" id="changeSummaryGroup" style="display: none;">
                <label for="changeSummary">Change Summary (required for updates)</label>
                <input type="text" id="changeSummary" placeholder="Briefly describe what you changed...">
            </div>
        </div>
        <div class="template-modal-footer">
            <button onclick="closeTemplateEditor()" class="btn-planner secondary">Cancel</button>
            <button onclick="saveTemplate()" class="btn-planner primary">Save Template</button>
        </div>
    </div>
</div>

<!-- Revision History Modal -->
<div id="revisionHistoryModal" class="template-modal" style="display: none;">
    <div class="template-modal-content" style="max-width: 900px;">
        <div class="template-modal-header">
            <h3 id="revisionHistoryTitle">Revision History</h3>
            <button onclick="closeRevisionHistory()" class="modal-close">&times;</button>
        </div>
        <div class="template-modal-body">
            <div class="revision-list" id="revisionList">
                <div style="text-align: center; padding: 40px; color: #666;">
                    Loading revisions...
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Revision View Modal -->
<div id="revisionViewModal" class="template-modal" style="display: none;">
    <div class="template-modal-content">
        <div class="template-modal-header">
            <h3 id="revisionViewTitle">View Revision</h3>
            <button onclick="closeRevisionView()" class="modal-close">&times;</button>
        </div>
        <div class="template-modal-body">
            <iframe id="revisionPreview" style="width: 100%; height: 500px; border: 1px solid #ddd; border-radius: 4px;"></iframe>
        </div>
        <div class="template-modal-footer">
            <button onclick="closeRevisionView()" class="btn-planner secondary">Close</button>
            <button id="restoreRevisionBtn" onclick="restoreRevision()" class="btn-planner primary">Restore This Version</button>
        </div>
    </div>
</div>

<!-- Template Preview Modal -->
<div id="templatePreviewModal" class="preview-modal">
    <div class="preview-modal-content">
        <div class="preview-modal-header">
            <h3 id="previewModalTitle">Template Preview</h3>
            <button onclick="closePreviewModal()" class="close-btn">&times;</button>
        </div>
        <div class="preview-modal-body">
            <iframe id="templatePreviewFrame"></iframe>
        </div>
        <div class="preview-modal-footer">
            <button onclick="downloadTemplate()" class="btn-download">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                    <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                </svg>
                Download PDF
            </button>
            <button onclick="openTemplateInNewTab()" class="btn-newtab">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5z"/>
                    <path fill-rule="evenodd" d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0v-5z"/>
                </svg>
                Open in New Tab
            </button>
            <button onclick="editFromPreview()" class="btn-edit-preview">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                </svg>
                Edit Template
            </button>
            <button onclick="closePreviewModal()" class="btn-close-preview">Close</button>
        </div>
    </div>
</div>

<!-- Fullscreen Document View Overlay -->
<div class="fullscreen-overlay" id="fullscreenOverlay">
    <iframe id="fullscreenViewerFrame"></iframe>
    <div class="fullscreen-controls" id="fullscreenControls">
        <button onclick="fullscreenZoom('out')" title="Zoom Out"><i class="fa-solid fa-minus"></i></button>
        <span class="fullscreen-zoom-label" id="fullscreenZoomLabel">100%</span>
        <button onclick="fullscreenZoom('in')" title="Zoom In"><i class="fa-solid fa-plus"></i></button>
        <button onclick="fullscreenZoom('reset')" title="Reset Zoom" style="font-size:12px;"><i class="fa-solid fa-rotate-left"></i></button>
    </div>
    <button class="fullscreen-exit-btn" id="fullscreenExitBtn" onclick="closeFullscreenView()" title="Exit Full View (Esc)">
        <i class="fa-solid fa-xmark"></i>
    </button>
</div>

<script>
    const API_URL = '<?= url('api/planner') ?>';
    let currentUser = {
        id: <?= (int)$_SESSION['user']['id'] ?>,
        name: <?= json_encode($_SESSION['user']['first_name'] . ' ' . $_SESSION['user']['last_name']) ?>,
        email: <?= json_encode($_SESSION['user']['email']) ?>
    };
    let expandedNotes = new Set();
    let expandedTodos = new Set();
    let refreshInterval = null;
    let todoFilter = 'all';
    let priorityFilter = 'all';
    let noteFilter = 'team';
    let noteNewScope = 'team';
    let allUsers = [];
    let currentTab = 'notes';

    // @Mention state
    let mentionDropdown = null;
    let mentionActiveInput = null;
    let mentionStartPos = -1;
    let mentionSelectedIndex = 0;
    let mentionFilteredUsers = [];

    // HTML Editor State
    let currentTemplateId = null;
    let currentTemplateName = null;
    let isEditorDirty = false;
    let currentDocumentStyles = ''; // Store original styles for saving

    function switchTab(tabName) {
        currentTab = tabName;

        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.remove('active');
        });

        // Remove active from all buttons
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('active');
        });

        // Show selected tab
        document.getElementById(tabName + '-tab').classList.add('active');
        event.target.classList.add('active');

        // Load data for the tab
        if (tabName === 'notes') {
            loadNotes();
        } else if (tabName === 'todos') {
            loadTodos();
        } else if (tabName === 'documents') {
            loadDocuments();
        } else if (tabName === 'activity') {
            loadActivityFull();
        } else if (tabName === 'templates') {
            loadTemplates();
        }

        loadActivity(); // Always update sidebar activity
    }

    function getInitials(name) {
        return name.split(' ').map(n => n[0]).join('').toUpperCase();
    }

    function timeAgo(timestamp) {
        const now = new Date();
        const time = new Date(timestamp);
        const diff = Math.floor((now - time) / 1000);

        if (diff < 60) return 'just now';
        if (diff < 3600) return Math.floor(diff / 60) + ' minutes ago';
        if (diff < 86400) return Math.floor(diff / 3600) + ' hours ago';
        return Math.floor(diff / 86400) + ' days ago';
    }

    // Initialize on page load
    window.addEventListener('DOMContentLoaded', () => {
        initMentionDropdown();
        setupMentionListeners();
        loadUsers();
        loadCategories();
        loadNotes();
        loadTodos();
        loadDocuments();

        // Start auto-refresh (10 seconds interval)
        refreshInterval = setInterval(() => {
            // Don't refresh if user is typing in note input, comment input, or inline checklist input
            const activeElement = document.activeElement;
            if (activeElement && (
                activeElement.id === 'noteInput' ||
                (activeElement.id && activeElement.id.startsWith('comment-input-')) ||
                (activeElement.id && activeElement.id.startsWith('inline-item-'))
            )) {
                return;
            }
            // Also check if active element is inside a contenteditable
            if (activeElement && activeElement.closest && activeElement.closest('[contenteditable="true"]')) {
                return;
            }
            // Skip if mention dropdown is open
            if (mentionDropdown && mentionDropdown.classList.contains('active')) {
                return;
            }

            if (currentTab === 'notes') {
                loadNotes();
            } else if (currentTab === 'todos') {
                loadTodos();
            } else if (currentTab === 'documents') {
                loadDocuments();
            } else if (currentTab === 'activity') {
                loadActivityFull();
            }
        }, 10000);
    });

    // Load all users for assignment dropdown
    async function loadUsers() {
        try {
            const response = await fetch(`${API_URL}/users`);
            allUsers = await response.json();

            const select = document.getElementById('assignSelect');
            select.innerHTML = '<option value="">Assign to...</option>';
            allUsers.forEach(user => {
                select.innerHTML += `<option value="${user.id}">${escapeHtml(user.name)}</option>`;
            });
        } catch (error) {
            console.error('Error loading users:', error);
        }
    }

    // ===== @Mention System =====

    function initMentionDropdown() {
        mentionDropdown = document.createElement('div');
        mentionDropdown.className = 'mention-dropdown';
        mentionDropdown.id = 'mentionDropdown';
        document.body.appendChild(mentionDropdown);

        document.addEventListener('click', (e) => {
            if (mentionDropdown && !mentionDropdown.contains(e.target) && e.target !== mentionActiveInput) {
                closeMentionDropdown();
            }
        });
    }

    function setupMentionListeners() {
        document.addEventListener('input', handleMentionInput);
        document.addEventListener('keydown', handleMentionKeydown);

        // Prevent rich text paste into contenteditable — paste as plain text only
        document.addEventListener('paste', function(e) {
            const el = e.target.closest ? e.target.closest('[contenteditable]') : e.target;
            if (!isMentionEditable(el)) return;

            e.preventDefault();
            const text = (e.clipboardData || window.clipboardData).getData('text/plain');
            document.execCommand('insertText', false, text);
        });
    }

    // Check if an element is a mention-enabled contenteditable
    function isMentionEditable(el) {
        if (!el) return false;
        return (el.id === 'noteInput' || (el.id && el.id.startsWith('comment-input-')) || (el.id && el.id.startsWith('todo-comment-input-'))) && el.isContentEditable;
    }

    // Get text before cursor in a contenteditable element
    function getTextBeforeCursor(el) {
        const sel = window.getSelection();
        if (!sel.rangeCount) return '';

        const range = sel.getRangeAt(0);
        // Walk backwards from cursor through text nodes to build text
        // We need only the text in the current text node before cursor
        if (range.startContainer.nodeType === Node.TEXT_NODE) {
            return range.startContainer.textContent.substring(0, range.startOffset);
        }
        return '';
    }

    function handleMentionInput(e) {
        const el = e.target.closest ? e.target.closest('[contenteditable]') : e.target;
        if (!isMentionEditable(el)) return;

        const textBefore = getTextBeforeCursor(el);
        const atIndex = textBefore.lastIndexOf('@');

        if (atIndex === -1) {
            closeMentionDropdown();
            return;
        }

        // Ensure @ is at start or preceded by whitespace
        if (atIndex > 0 && !/\s/.test(textBefore[atIndex - 1])) {
            closeMentionDropdown();
            return;
        }

        const query = textBefore.substring(atIndex + 1);
        if (query.length > 30 || query.includes('\n')) {
            closeMentionDropdown();
            return;
        }

        mentionActiveInput = el;
        mentionStartPos = atIndex;
        mentionSelectedIndex = 0;

        const lowerQuery = query.toLowerCase();
        mentionFilteredUsers = allUsers.filter(user =>
            user.name.toLowerCase().includes(lowerQuery) ||
            user.email.toLowerCase().includes(lowerQuery)
        );

        renderMentionDropdown(mentionFilteredUsers);
        showMentionDropdown(el);
    }

    function handleMentionKeydown(e) {
        const el = e.target.closest ? e.target.closest('[contenteditable]') : e.target;
        if (!isMentionEditable(el)) return;
        if (!mentionDropdown || !mentionDropdown.classList.contains('active')) return;
        if (mentionFilteredUsers.length === 0) return;

        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                mentionSelectedIndex = Math.min(mentionSelectedIndex + 1, mentionFilteredUsers.length - 1);
                renderMentionDropdown(mentionFilteredUsers);
                scrollMentionSelectedIntoView();
                break;
            case 'ArrowUp':
                e.preventDefault();
                mentionSelectedIndex = Math.max(mentionSelectedIndex - 1, 0);
                renderMentionDropdown(mentionFilteredUsers);
                scrollMentionSelectedIntoView();
                break;
            case 'Enter':
                e.preventDefault();
                e.stopPropagation();
                insertMention(mentionSelectedIndex);
                break;
            case 'Tab':
                e.preventDefault();
                insertMention(mentionSelectedIndex);
                break;
            case 'Escape':
                e.preventDefault();
                closeMentionDropdown();
                break;
        }
    }

    function renderMentionDropdown(users) {
        if (!mentionDropdown) return;

        if (users.length === 0) {
            mentionDropdown.innerHTML = '<div class="mention-dropdown-empty">No users found</div>';
            return;
        }

        mentionDropdown.innerHTML = users.map((user, index) => `
            <div class="mention-dropdown-item ${index === mentionSelectedIndex ? 'selected' : ''}"
                 data-index="${index}"
                 onmousedown="insertMention(${index})">
                <div class="mention-avatar">${getInitials(user.name)}</div>
                <div>
                    <div class="mention-name">${escapeHtml(user.name)}</div>
                    <div class="mention-email">${escapeHtml(user.email)}</div>
                </div>
            </div>
        `).join('');
    }

    function showMentionDropdown(element) {
        if (!mentionDropdown) return;
        const sel = window.getSelection();
        if (sel.rangeCount) {
            const range = sel.getRangeAt(0);
            const rect = range.getBoundingClientRect();
            const elRect = element.getBoundingClientRect();
            // Position below the cursor
            mentionDropdown.style.top = (rect.bottom + 4) + 'px';
            mentionDropdown.style.left = Math.max(rect.left, elRect.left) + 'px';
        } else {
            const rect = element.getBoundingClientRect();
            mentionDropdown.style.top = (rect.bottom + 4) + 'px';
            mentionDropdown.style.left = rect.left + 'px';
        }
        mentionDropdown.style.minWidth = '220px';
        mentionDropdown.classList.add('active');
    }

    function closeMentionDropdown() {
        if (mentionDropdown) {
            mentionDropdown.classList.remove('active');
        }
        mentionActiveInput = null;
        mentionStartPos = -1;
        mentionSelectedIndex = 0;
        mentionFilteredUsers = [];
    }

    function insertMention(index) {
        if (!mentionActiveInput || mentionStartPos === -1) return;
        if (!mentionFilteredUsers[index]) return;

        const user = mentionFilteredUsers[index];
        const el = mentionActiveInput;

        const sel = window.getSelection();
        if (!sel.rangeCount) return;

        const range = sel.getRangeAt(0);
        const textNode = range.startContainer;
        if (textNode.nodeType !== Node.TEXT_NODE) return;

        const cursorOffset = range.startOffset;
        const textContent = textNode.textContent;

        // Split the text node: before @, after cursor
        const beforeAt = textContent.substring(0, mentionStartPos);
        const afterCursor = textContent.substring(cursorOffset);

        // Create the mention span
        const mentionSpan = document.createElement('span');
        mentionSpan.className = 'mention-tag';
        mentionSpan.contentEditable = 'false';
        mentionSpan.dataset.userId = user.id;
        mentionSpan.dataset.userName = user.name;
        mentionSpan.textContent = '@' + user.name;

        // Create a trailing space text node (so cursor goes after the mention)
        const spaceNode = document.createTextNode('\u00A0');

        // Replace the text node with: beforeText + mentionSpan + space + afterText
        const parent = textNode.parentNode;
        const beforeNode = document.createTextNode(beforeAt);
        const afterNode = document.createTextNode(afterCursor);

        parent.insertBefore(beforeNode, textNode);
        parent.insertBefore(mentionSpan, textNode);
        parent.insertBefore(spaceNode, textNode);
        if (afterCursor) {
            parent.insertBefore(afterNode, textNode);
        }
        parent.removeChild(textNode);

        // Place cursor after the space
        const newRange = document.createRange();
        const targetNode = afterCursor ? afterNode : spaceNode;
        const targetOffset = afterCursor ? 0 : 1;
        newRange.setStart(targetNode, targetOffset);
        newRange.collapse(true);
        sel.removeAllRanges();
        sel.addRange(newRange);

        el.focus();
        closeMentionDropdown();
    }

    function scrollMentionSelectedIntoView() {
        const selected = mentionDropdown.querySelector('.mention-dropdown-item.selected');
        if (selected) {
            selected.scrollIntoView({ block: 'nearest' });
        }
    }

    // Extract plain text + mention markup from a contenteditable element
    function extractContentFromEditable(el) {
        let result = '';
        el.childNodes.forEach(node => {
            if (node.nodeType === Node.TEXT_NODE) {
                // Replace non-breaking spaces with regular spaces
                result += node.textContent.replace(/\u00A0/g, ' ');
            } else if (node.nodeType === Node.ELEMENT_NODE) {
                if (node.classList && node.classList.contains('mention-tag')) {
                    const userId = node.dataset.userId;
                    const userName = node.dataset.userName;
                    result += `@[${userName}](${userId})`;
                } else if (node.tagName === 'BR') {
                    result += '\n';
                } else if (node.tagName === 'DIV' || node.tagName === 'P') {
                    // Contenteditable sometimes wraps lines in divs
                    if (result.length > 0 && !result.endsWith('\n')) {
                        result += '\n';
                    }
                    node.childNodes.forEach(child => {
                        if (child.nodeType === Node.TEXT_NODE) {
                            result += child.textContent.replace(/\u00A0/g, ' ');
                        } else if (child.classList && child.classList.contains('mention-tag')) {
                            const userId = child.dataset.userId;
                            const userName = child.dataset.userName;
                            result += `@[${userName}](${userId})`;
                        } else if (child.tagName === 'BR') {
                            result += '\n';
                        } else {
                            result += child.textContent.replace(/\u00A0/g, ' ');
                        }
                    });
                } else {
                    result += node.textContent.replace(/\u00A0/g, ' ');
                }
            }
        });
        return result;
    }

    function renderMentions(text) {
        if (!text) return '';
        let safe = escapeHtml(text);
        safe = safe.replace(/\n/g, '<br>');
        safe = safe.replace(/@\[([^\]]+)\]\((\d+)\)/g, '<span class="mention-tag" data-user-id="$2">@$1</span>');
        return safe;
    }

    // ===== End @Mention System =====

    function filterNotes(filter) {
        noteFilter = filter;
        document.querySelectorAll('.note-filter-tab').forEach(t => t.classList.remove('active'));
        const tabId = { team: 'noteFilterTeam', personal: 'noteFilterPersonal', archived: 'noteFilterArchived' }[filter] || 'noteFilterTeam';
        document.getElementById(tabId).classList.add('active');

        const formWrapper = document.getElementById('addNoteFormWrapper');
        if (formWrapper) formWrapper.style.display = filter === 'archived' ? 'none' : '';

        loadNotes();
    }

    function setNoteScope(scope, btn) {
        noteNewScope = scope;
        document.querySelectorAll('.note-scope-toggle button').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const noteEl = document.getElementById('noteInput');
        if (noteEl) noteEl.dataset.placeholder = scope === 'personal' ? 'Private note — only you can see this...' : 'Drop your ideas here...';
    }

    async function loadNotes() {
        try {
            const archivedParam = noteFilter === 'archived' ? '1' : '0';
            const scopeParam = noteFilter === 'archived' ? '' : `&scope=${noteFilter}`;
            const response = await fetch(`${API_URL}/notes?archived=${archivedParam}${scopeParam}`);
            const data = await response.json();

            // Check if response is an error
            if (!response.ok || data.error) {
                console.error('API error:', data.error || 'Unknown error');
                document.getElementById('notesList').innerHTML = `
                    <div style="text-align: center; padding: 40px; color: #e74c3c;">
                        <p><strong>Error loading notes:</strong></p>
                        <p>${data.error || 'Failed to load notes'}</p>
                        ${data.error && data.error.includes('table') ? '<p style="font-size: 12px; color: #666; margin-top: 10px;">Run: php database/migrations/setup_planner_tables.php</p>' : ''}
                    </div>
                `;
                return;
            }

            // Handle new response format {notes, active_count, archived_count}
            const notes = data.notes || data;
            if (!Array.isArray(notes)) {
                console.error('Invalid response format:', data);
                return;
            }

            // Update tab counts
            if (data.team_count !== undefined) {
                document.getElementById('teamNoteCount').textContent     = `(${data.team_count})`;
                document.getElementById('personalNoteCount').textContent = `(${data.personal_count})`;
                document.getElementById('archivedNoteCount').textContent = `(${data.archived_count})`;
            }

            displayNotes(notes);
        } catch (error) {
            console.error('Error loading notes:', error);
            document.getElementById('notesList').innerHTML = `
                <div style="text-align: center; padding: 40px; color: #e74c3c;">
                    <p>Failed to load notes. Check console for details.</p>
                </div>
            `;
        }
    }

    async function addNote() {
        const noteEl = document.getElementById('noteInput');
        const content = extractContentFromEditable(noteEl).trim();
        if (!content) return;

        const btn = document.getElementById('addNoteBtn');
        const originalText = btn.textContent;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Posting...';

        try {
            const response = await fetch(`${API_URL}/notes`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    user_id: currentUser.id,
                    content: content,
                    scope: noteNewScope
                })
            });

            if (response.ok) {
                noteEl.innerHTML = '';
                btn.innerHTML = '<i class="fa-solid fa-check"></i> Done!';
                btn.style.background = '#16a34a';
                // Jump to the tab matching what we just posted
                if (noteFilter !== noteNewScope) filterNotes(noteNewScope);
                else loadNotes();
                loadActivity();
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.style.background = '';
                    btn.disabled = false;
                }, 1500);
            } else {
                btn.innerHTML = '<i class="fa-solid fa-xmark"></i> Failed';
                btn.style.background = '#dc2626';
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.style.background = '';
                    btn.disabled = false;
                }, 2000);
            }
        } catch (error) {
            console.error('Error adding note:', error);
            btn.innerHTML = '<i class="fa-solid fa-xmark"></i> Failed';
            btn.style.background = '#dc2626';
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.style.background = '';
                btn.disabled = false;
            }, 2000);
        }
    }

    async function archiveNote(id) {
        try {
            await fetch(`${API_URL}/notes/archive`, {
                method: 'PUT',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    id: id,
                    user_id: currentUser.id
                })
            });
            loadNotes();
            loadActivity();
        } catch (error) {
            console.error('Error archiving note:', error);
        }
    }

    async function deleteNote(id) {
        if (!confirm('Permanently delete this note? This cannot be undone.')) return;

        try {
            await fetch(`${API_URL}/notes`, {
                method: 'DELETE',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    id: id,
                    user_id: currentUser.id
                })
            });
            loadNotes();
            loadActivity();
        } catch (error) {
            console.error('Error deleting note:', error);
        }
    }

    async function toggleComments(noteId) {
        if (expandedNotes.has(noteId)) {
            expandedNotes.delete(noteId);
        } else {
            expandedNotes.add(noteId);
            await loadComments(noteId);
        }
        loadNotes();
    }

    async function loadComments(noteId) {
        try {
            const response = await fetch(`${API_URL}/notes/comments?note_id=${noteId}`);
            const comments = await response.json();
            return comments;
        } catch (error) {
            console.error('Error loading comments:', error);
            return [];
        }
    }

    async function addComment(noteId) {
        const input = document.getElementById(`comment-input-${noteId}`);
        if (!input) {
            console.error(`Input element not found for note ${noteId}`);
            alert('Error: Comment input not found. Please try again.');
            return;
        }

        const comment = extractContentFromEditable(input).trim();
        if (!comment) {
            alert('Please enter a comment');
            return;
        }

        // Disable input while submitting
        input.contentEditable = 'false';
        input.style.opacity = '0.6';
        const btn = input.nextElementSibling;
        if (btn) btn.disabled = true;

        try {
            const response = await fetch(`${API_URL}/notes/comments`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    note_id: noteId,
                    user_id: currentUser.id,
                    comment: comment
                })
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
            }

            const result = await response.json();

            if (result.success) {
                input.innerHTML = '';
                expandedNotes.add(noteId);
                await loadNotes();
                loadActivity();
            } else {
                throw new Error('Failed to add comment');
            }
        } catch (error) {
            console.error('Error adding comment:', error);
            alert('Failed to add comment: ' + error.message);
            // Re-enable input on error
            input.contentEditable = 'true';
            input.style.opacity = '1';
            if (btn) btn.disabled = false;
        }
    }

    async function displayNotes(notes) {
        const container = document.getElementById('notesList');

        // Preserve comment input HTML content and focus state before refresh
        const commentInputValues = {};
        let focusedInputId = null;

        document.querySelectorAll('[id^="comment-input-"]').forEach(input => {
            const noteId = input.id.replace('comment-input-', '');
            if (input.innerHTML) {
                commentInputValues[noteId] = input.innerHTML;
            }
            if (document.activeElement === input || input.contains(document.activeElement)) {
                focusedInputId = input.id;
            }
        });

        if (notes.length === 0) {
            const emptyMsg = noteFilter === 'archived'
                ? 'No archived notes'
                : noteFilter === 'personal'
                    ? 'No personal notes yet — write something only you can see!'
                    : 'No team notes yet — drop your first idea above!';
            container.innerHTML = `<p style="text-align: center; color: #94a3b8; padding: 40px;">${emptyMsg}</p>`;
            return;
        }

        const notesHTML = await Promise.all(notes.map(async note => {
            let commentsHTML = '';

            if (expandedNotes.has(note.id)) {
                const comments = await loadComments(note.id);
                // Restore preserved input HTML if exists
                const preservedHTML = commentInputValues[note.id] || '';
                commentsHTML = `
                    <div class="comments-section">
                        ${comments.map(c => `
                            <div class="comment">
                                <div class="comment-header">${escapeHtml(c.user_name)} • ${timeAgo(c.created_at)}</div>
                                <div>${renderMentions(c.comment)}</div>
                            </div>
                        `).join('')}
                        <div class="add-comment" style="position: relative;">
                            <div class="comment-editable" id="comment-input-${note.id}" contenteditable="true" data-placeholder="Add a comment..." data-note-id="${note.id}">${preservedHTML}</div>
                            <button class="btn-planner primary small" onclick="addComment(${note.id})">Comment</button>
                        </div>
                    </div>
                `;
            }

            const isArchived = noteFilter === 'archived';
            const isPersonal = note.scope === 'personal';
            const actionButtons = isArchived
                ? `<div style="display:flex; gap:6px;">
                       <button class="btn-planner primary small" onclick="archiveNote(${note.id})">Restore</button>
                       <button class="btn-planner danger small" onclick="deleteNote(${note.id})">Delete</button>
                   </div>`
                : `<button class="btn-planner small" onclick="archiveNote(${note.id})" style="background:transparent; color:#64748b; border:1px solid #e2e8f0;">Archive</button>`;

            const personalBadge = isPersonal ? `<span class="note-personal-badge">Private</span>` : '';

            return `
                <div class="note-card ${isArchived ? 'archived' : ''}" style="${isPersonal ? 'border-left: 3px solid #166534;' : ''}">
                    <div class="note-header">
                        <div class="note-author">
                            <div class="avatar" style="${isPersonal ? 'background: linear-gradient(135deg, #166534 0%, #14532d 100%);' : ''}">${getInitials(note.user_name)}</div>
                            <div class="author-info">
                                <div class="author-name">${escapeHtml(note.user_name)} ${personalBadge}</div>
                                <div class="note-time">${timeAgo(note.created_at)}</div>
                            </div>
                        </div>
                        ${actionButtons}
                    </div>
                    <div class="note-content">${renderMentions(note.content)}</div>
                    <div class="note-footer">
                        ${!isPersonal ? `<button class="btn-planner primary small" onclick="toggleComments(${note.id})">
                            ${expandedNotes.has(note.id) ? 'Hide' : 'Show'} Comments (${note.comment_count})
                        </button>` : ''}
                    </div>
                    ${commentsHTML}
                </div>
            `;
        }));

        container.innerHTML = notesHTML.join('');

        // Setup Enter key handler for comment inputs
        document.querySelectorAll('.comment-editable[data-note-id]').forEach(el => {
            el.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    // Only submit if mention dropdown is NOT open
                    if (!mentionDropdown || !mentionDropdown.classList.contains('active')) {
                        e.preventDefault();
                        addComment(parseInt(this.dataset.noteId));
                    }
                }
            });
        });

        // Restore focus after refresh
        if (focusedInputId) {
            const inputToFocus = document.getElementById(focusedInputId);
            if (inputToFocus) {
                inputToFocus.focus();
                // Place cursor at end
                const sel = window.getSelection();
                const range = document.createRange();
                range.selectNodeContents(inputToFocus);
                range.collapse(false);
                sel.removeAllRanges();
                sel.addRange(range);
            }
        }
    }

    async function loadActivity() {
        // Refresh the Activity tab if it's currently visible
        if (currentTab === 'activity') {
            loadActivityFull();
        }
    }

    async function loadActivityFull() {
        try {
            const response = await fetch(`${API_URL}/activity`);
            const data = await response.json();

            // Check if response is an error or not an array
            if (!response.ok || data.error || !Array.isArray(data)) {
                console.error('Activity API error:', data.error || 'Invalid response');
                document.getElementById('activityListFull').innerHTML = `
                    <div style="text-align: center; padding: 40px; color: #e74c3c;">
                        <p>Unable to load activity</p>
                    </div>
                `;
                return;
            }

            const activityHTML = data.map(a => `
                <div class="activity-item">
                    <span class="activity-user">${escapeHtml(a.user_name)}</span> ${escapeHtml(a.description)}
                    <div class="activity-time">${timeAgo(a.created_at)}</div>
                </div>
            `).join('') || '<p style="color: #999; text-align: center;">No activity yet</p>';

            document.getElementById('activityListFull').innerHTML = activityHTML;
        } catch (error) {
            console.error('Error loading activity:', error);
        }
    }

    // Todo Functions
    async function loadTodos() {
        try {
            const response = await fetch(`${API_URL}/todos`);
            const data = await response.json();

            // Check if response is an error
            if (!response.ok || data.error) {
                console.error('API error:', data.error || 'Unknown error');
                document.getElementById('todosList').innerHTML = `
                    <div style="text-align: center; padding: 40px; color: #e74c3c;">
                        <p><strong>Error loading tasks:</strong></p>
                        <p>${data.error || 'Failed to load tasks'}</p>
                        ${data.error && data.error.includes('table') ? '<p style="font-size: 12px; color: #666; margin-top: 10px;">Run: php database/migrations/setup_planner_tables.php</p>' : ''}
                    </div>
                `;
                return;
            }

            // Ensure data is an array
            if (!Array.isArray(data)) {
                console.error('Invalid response format:', data);
                return;
            }

            displayTodos(data);
        } catch (error) {
            console.error('Error loading todos:', error);
            document.getElementById('todosList').innerHTML = `
                <div style="text-align: center; padding: 40px; color: #e74c3c;">
                    <p>Failed to load tasks. Check console for details.</p>
                </div>
            `;
        }
    }

    function toggleTodoExpand() {
        const fields = document.getElementById('todoExpandedFields');
        const btn = document.getElementById('todoExpandBtn');
        if (fields.style.display === 'none') {
            fields.style.display = 'block';
            btn.textContent = '− Hide Description / Checklist';
            // Add one checklist input by default if empty
            if (document.getElementById('todoChecklistInputs').children.length === 0) {
                addChecklistInput();
            }
        } else {
            fields.style.display = 'none';
            btn.textContent = '+ Description / Checklist';
        }
    }

    function addChecklistInput() {
        const container = document.getElementById('todoChecklistInputs');
        const row = document.createElement('div');
        row.className = 'checklist-input-row';
        row.innerHTML = `
            <input type="text" placeholder="Checklist item..." onkeydown="if(event.key==='Enter'){event.preventDefault(); addChecklistInput(); this.parentElement.nextElementSibling?.querySelector('input')?.focus();}">
            <button type="button" class="remove-item" onclick="this.parentElement.remove()">&times;</button>
        `;
        container.appendChild(row);
        row.querySelector('input').focus();
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    async function addTodo() {
        const task = document.getElementById('todoInput').value.trim();
        if (!task) {
            alert('Please enter a task title');
            return;
        }

        const assignedTo = document.getElementById('assignSelect').value || null;
        const priority = document.getElementById('todoPriority').value || 'medium';
        const description = document.getElementById('todoDescription')?.value.trim() || '';
        const addBtn = document.querySelector('.add-todo-form .btn-planner.primary');

        // Gather checklist items (non-empty)
        const items = [];
        document.querySelectorAll('#todoChecklistInputs .checklist-input-row input').forEach(input => {
            const val = input.value.trim();
            if (val) items.push(val);
        });

        // Disable button while submitting
        if (addBtn) {
            addBtn.disabled = true;
            addBtn.textContent = 'Adding...';
        }

        try {
            const payload = {
                user_id: currentUser.id,
                task: task,
                priority: priority,
                assigned_to: assignedTo
            };
            if (description) payload.description = description;
            if (items.length > 0) payload.items = items;

            const response = await fetch(`${API_URL}/todos`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (response.ok && data.success) {
                document.getElementById('todoInput').value = '';
                document.getElementById('todoPriority').value = 'medium';
                document.getElementById('assignSelect').value = '';
                document.getElementById('todoDescription').value = '';
                document.getElementById('todoChecklistInputs').innerHTML = '';
                // Collapse expanded fields
                document.getElementById('todoExpandedFields').style.display = 'none';
                document.getElementById('todoExpandBtn').textContent = '+ Description / Checklist';
                loadTodos();
                loadActivity();
            } else {
                const errorMsg = data.error || 'Failed to add task';
                alert('Error: ' + errorMsg);
                console.error('Server error:', data);
            }
        } catch (error) {
            console.error('Error adding todo:', error);
            alert('Failed to add task. Please check console for details and ensure database tables are set up.');
        } finally {
            // Re-enable button
            if (addBtn) {
                addBtn.disabled = false;
                addBtn.textContent = 'Add Task';
            }
        }
    }

    async function toggleTodo(id) {
        try {
            await fetch(`${API_URL}/todos`, {
                method: 'PUT',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    id: id,
                    user_id: currentUser.id
                })
            });
            loadTodos();
            loadActivity();
        } catch (error) {
            console.error('Error toggling todo:', error);
        }
    }

    async function deleteTodo(id) {
        if (!confirm('Delete this task?')) return;

        try {
            await fetch(`${API_URL}/todos`, {
                method: 'DELETE',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    id: id,
                    user_id: currentUser.id
                })
            });
            loadTodos();
            loadActivity();
        } catch (error) {
            console.error('Error deleting todo:', error);
        }
    }

    async function toggleChecklistItem(itemId, todoId) {
        try {
            await fetch(`${API_URL}/todos/items`, {
                method: 'PUT',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    id: itemId,
                    user_id: currentUser.id
                })
            });
            loadTodos();
            loadActivity();
        } catch (error) {
            console.error('Error toggling checklist item:', error);
        }
    }

    async function deleteChecklistItem(itemId, todoId) {
        try {
            await fetch(`${API_URL}/todos/items`, {
                method: 'DELETE',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    id: itemId,
                    user_id: currentUser.id
                })
            });
            loadTodos();
        } catch (error) {
            console.error('Error deleting checklist item:', error);
        }
    }

    async function addInlineChecklistItem(todoId) {
        const input = document.getElementById(`inline-item-${todoId}`);
        const title = input?.value.trim();
        if (!title) return;

        input.disabled = true;
        try {
            const response = await fetch(`${API_URL}/todos/items`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    todo_id: todoId,
                    title: title,
                    user_id: currentUser.id
                })
            });
            const data = await response.json();
            if (data.success) {
                input.value = '';
                loadTodos();
            }
        } catch (error) {
            console.error('Error adding checklist item:', error);
        } finally {
            if (input) input.disabled = false;
        }
    }

    async function toggleTodoComments(todoId) {
        if (expandedTodos.has(todoId)) {
            expandedTodos.delete(todoId);
        } else {
            expandedTodos.add(todoId);
        }
        loadTodos();
    }

    async function loadTodoComments(todoId) {
        try {
            const response = await fetch(`${API_URL}/todos/comments?todo_id=${todoId}`);
            const comments = await response.json();
            return comments;
        } catch (error) {
            console.error('Error loading todo comments:', error);
            return [];
        }
    }

    async function addTodoComment(todoId) {
        const input = document.getElementById(`todo-comment-input-${todoId}`);
        if (!input) {
            console.error(`Input element not found for todo ${todoId}`);
            return;
        }

        const comment = extractContentFromEditable(input).trim();
        if (!comment) {
            alert('Please enter a comment');
            return;
        }

        input.contentEditable = 'false';
        input.style.opacity = '0.6';
        const btn = input.nextElementSibling;
        if (btn) btn.disabled = true;

        try {
            const response = await fetch(`${API_URL}/todos/comments`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    todo_id: todoId,
                    user_id: currentUser.id,
                    comment: comment
                })
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
            }

            const result = await response.json();

            if (result.success) {
                input.innerHTML = '';
                expandedTodos.add(todoId);
                await loadTodos();
                loadActivity();
            } else {
                throw new Error('Failed to add comment');
            }
        } catch (error) {
            console.error('Error adding todo comment:', error);
            alert('Failed to add comment: ' + error.message);
            input.contentEditable = 'true';
            input.style.opacity = '1';
            if (btn) btn.disabled = false;
        }
    }

    function filterTodos(filter, btn) {
        todoFilter = filter;
        const statusTabs = document.querySelectorAll('#todos-tab .filter-tabs:first-of-type .filter-tab');
        statusTabs.forEach(t => t.classList.remove('active'));
        if (btn) btn.classList.add('active');
        loadTodos();
    }

    function filterByPriority(priority, btn) {
        priorityFilter = priority;
        document.querySelectorAll('#priorityFilterTabs .filter-tab').forEach(t => t.classList.remove('active'));
        if (btn) btn.classList.add('active');
        loadTodos();
    }

    async function displayTodos(todos) {
        const container = document.getElementById('todosList');

        // Filter todos
        let filteredTodos = todos;
        if (todoFilter === 'active') {
            filteredTodos = todos.filter(t => !t.is_completed);
        } else if (todoFilter === 'completed') {
            filteredTodos = todos.filter(t => t.is_completed);
        }
        if (priorityFilter !== 'all') {
            filteredTodos = filteredTodos.filter(t => t.priority === priorityFilter);
        }

        if (filteredTodos.length === 0) {
            container.innerHTML = '<p style="text-align: center; color: #7f8c8d; padding: 40px;">No tasks yet</p>';
            return;
        }

        // Preserve todo comment input HTML and focus state before refresh
        const todoCommentValues = {};
        let focusedTodoInputId = null;

        document.querySelectorAll('[id^="todo-comment-input-"]').forEach(input => {
            const todoId = input.id.replace('todo-comment-input-', '');
            if (input.innerHTML) {
                todoCommentValues[todoId] = input.innerHTML;
            }
            if (document.activeElement === input || input.contains(document.activeElement)) {
                focusedTodoInputId = input.id;
            }
        });

        // Preserve inline checklist item input values and focus
        const inlineItemValues = {};
        let focusedInlineItemId = null;

        document.querySelectorAll('[id^="inline-item-"]').forEach(input => {
            if (input.value) {
                inlineItemValues[input.id] = input.value;
            }
            if (document.activeElement === input) {
                focusedInlineItemId = input.id;
            }
        });

        const todosHTML = await Promise.all(filteredTodos.map(async todo => {
            const assignedBadge = todo.assigned_name
                ? `<span class="todo-badge assigned">👤 ${escapeHtml(todo.assigned_name)}</span>`
                : '';

            const completedBadge = todo.is_completed
                ? `<span class="todo-badge completed">✓ ${escapeHtml(todo.completed_name || '')} • ${timeAgo(todo.completed_at)}</span>`
                : '';

            const priorityLabels = { urgent: '🔴 Urgent', high: '🟠 High', medium: '🔵 Medium', low: '⚪ Low' };
            const priority = todo.priority || 'medium';
            const priorityBadge = `<span class="priority-badge ${priority}">${priorityLabels[priority] || priority}</span>`;

            // Description block
            const descriptionHTML = todo.description
                ? `<div class="todo-description">${escapeHtml(todo.description)}</div>`
                : '';

            // Checklist items
            const items = todo.items || [];
            const itemsTotal = todo.items_total || 0;
            const itemsCompleted = todo.items_completed || 0;
            let checklistHTML = '';
            if (items.length > 0) {
                const pct = itemsTotal > 0 ? Math.round((itemsCompleted / itemsTotal) * 100) : 0;
                checklistHTML = `
                    <div class="todo-progress">
                        <div class="todo-progress-bar"><div class="todo-progress-fill" style="width:${pct}%"></div></div>
                        <span>${itemsCompleted}/${itemsTotal}</span>
                    </div>
                    <ul class="todo-checklist">
                        ${items.map(item => `
                            <li class="todo-checklist-item ${item.is_completed ? 'item-completed' : ''}">
                                <input type="checkbox" ${item.is_completed ? 'checked' : ''} onchange="toggleChecklistItem(${item.id}, ${todo.id})">
                                <span>${escapeHtml(item.title)}</span>
                                <button class="delete-item" onclick="deleteChecklistItem(${item.id}, ${todo.id})" title="Remove item">&times;</button>
                            </li>
                        `).join('')}
                    </ul>
                    <div class="todo-add-item-inline">
                        <input type="text" placeholder="Add item..." id="inline-item-${todo.id}" onkeydown="if(event.key==='Enter'){event.preventDefault(); addInlineChecklistItem(${todo.id});}">
                        <button class="btn-planner small" onclick="addInlineChecklistItem(${todo.id})" style="padding:6px 10px; font-size:12px;">+</button>
                    </div>
                `;
            }

            let commentsHTML = '';
            if (expandedTodos.has(todo.id)) {
                const comments = await loadTodoComments(todo.id);
                const preservedHTML = todoCommentValues[todo.id] || '';
                commentsHTML = `
                    <div class="comments-section">
                        ${comments.map(c => `
                            <div class="comment">
                                <div class="comment-header">${escapeHtml(c.user_name)} • ${timeAgo(c.created_at)}</div>
                                <div>${renderMentions(c.comment)}</div>
                            </div>
                        `).join('')}
                        <div class="add-comment" style="position: relative;">
                            <div class="comment-editable" id="todo-comment-input-${todo.id}" contenteditable="true" data-placeholder="Add a comment..." data-todo-id="${todo.id}">${preservedHTML}</div>
                            <button class="btn-planner primary small" onclick="addTodoComment(${todo.id})">Comment</button>
                        </div>
                    </div>
                `;
            }

            return `
                <div class="todo-item ${todo.is_completed ? 'completed' : ''}">
                    <div class="todo-main">
                        <input type="checkbox"
                               class="todo-checkbox"
                               ${todo.is_completed ? 'checked' : ''}
                               onchange="toggleTodo(${todo.id})">
                        <div class="todo-content">
                            <div class="todo-task ${todo.is_completed ? 'completed' : ''}">${renderMentions(todo.task)}</div>
                            <div class="todo-meta">
                                ${priorityBadge}
                                <span>by ${escapeHtml(todo.creator_name || '')}</span>
                                <span>• ${timeAgo(todo.created_at)}</span>
                                ${assignedBadge}
                                ${completedBadge}
                            </div>
                            ${descriptionHTML}
                            ${checklistHTML}
                        </div>
                    </div>
                    <div class="todo-actions">
                        <button class="btn-planner primary small" onclick="toggleTodoComments(${todo.id})">
                            ${expandedTodos.has(todo.id) ? 'Hide' : 'Show'} Comments (${todo.comment_count || 0})
                        </button>
                        <button class="btn-planner danger small" onclick="deleteTodo(${todo.id})">Delete</button>
                    </div>
                    ${commentsHTML}
                </div>
            `;
        }));

        container.innerHTML = todosHTML.join('');

        // Setup Enter key handler for todo comment inputs
        document.querySelectorAll('.comment-editable[data-todo-id]').forEach(el => {
            el.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    if (!mentionDropdown || !mentionDropdown.classList.contains('active')) {
                        e.preventDefault();
                        addTodoComment(parseInt(this.dataset.todoId));
                    }
                }
            });
        });

        // Restore focus after refresh (comment inputs)
        if (focusedTodoInputId) {
            const inputToFocus = document.getElementById(focusedTodoInputId);
            if (inputToFocus) {
                inputToFocus.focus();
                const sel = window.getSelection();
                const range = document.createRange();
                range.selectNodeContents(inputToFocus);
                range.collapse(false);
                sel.removeAllRanges();
                sel.addRange(range);
            }
        }

        // Restore inline checklist item input values
        Object.entries(inlineItemValues).forEach(([id, value]) => {
            const input = document.getElementById(id);
            if (input) input.value = value;
        });

        // Restore focus on inline checklist item input
        if (focusedInlineItemId) {
            const inlineInput = document.getElementById(focusedInlineItemId);
            if (inlineInput) {
                inlineInput.focus();
                // Place cursor at end of value
                const len = inlineInput.value.length;
                inlineInput.setSelectionRange(len, len);
            }
        }
    }

    // Document Functions
    function handleFileSelect(event) {
        const file = event.target.files[0];
        if (file) {
            document.getElementById('selectedFile').style.display = 'block';
            document.getElementById('selectedFile').innerHTML = `
                <strong>Selected:</strong> ${file.name} (${formatFileSize(file.size)})
            `;
            document.getElementById('uploadBtn').style.display = 'block';
        }
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    async function uploadDocument(event) {
        event.preventDefault();

        const formData = new FormData();
        const fileInput = document.getElementById('fileInput');
        const file = fileInput.files[0];

        if (!file) return;

        formData.append('file', file);
        formData.append('user_id', currentUser.id);

        try {
            const response = await fetch(`${API_URL}/documents`, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                document.getElementById('uploadForm').reset();
                document.getElementById('selectedFile').style.display = 'none';
                document.getElementById('uploadBtn').style.display = 'none';
                loadDocuments();
                loadActivity();
            }
        } catch (error) {
            console.error('Error uploading document:', error);
            alert('Failed to upload document');
        }
    }

    async function loadDocuments() {
        try {
            const response = await fetch(`${API_URL}/documents`);
            const data = await response.json();

            // Check if response is an error
            if (!response.ok || data.error) {
                console.error('Documents API error:', data.error || 'Unknown error');
                document.getElementById('documentsList').innerHTML = `
                    <div style="text-align: center; padding: 40px; color: #e74c3c;">
                        <p><strong>Error loading documents:</strong></p>
                        <p>${data.error || 'Failed to load documents'}</p>
                    </div>
                `;
                return;
            }

            // Ensure data is an array
            if (!Array.isArray(data)) {
                console.error('Invalid response format:', data);
                return;
            }

            displayDocuments(data);
        } catch (error) {
            console.error('Error loading documents:', error);
            document.getElementById('documentsList').innerHTML = `
                <div style="text-align: center; padding: 40px; color: #e74c3c;">
                    <p>Failed to load documents. Check console for details.</p>
                </div>
            `;
        }
    }

    function getFileIcon(filename, mimeType) {
        const ext = filename.split('.').pop().toLowerCase();

        if (mimeType.includes('pdf') || ext === 'pdf') {
            return { icon: '📄', class: 'pdf' };
        } else if (mimeType.includes('image')) {
            return { icon: '🖼️', class: 'image' };
        } else if (ext === 'doc' || ext === 'docx') {
            return { icon: '📝', class: 'word' };
        } else if (ext === 'xls' || ext === 'xlsx') {
            return { icon: '📊', class: 'excel' };
        } else {
            return { icon: '📎', class: 'other' };
        }
    }

    function displayDocuments(documents) {
        const container = document.getElementById('documentsList');

        if (documents.length === 0) {
            container.innerHTML = '<p style="text-align: center; color: #7f8c8d; padding: 40px;">No documents uploaded yet</p>';
            return;
        }

        const docsHTML = documents.map(doc => {
            const fileInfo = getFileIcon(doc.original_filename, doc.mime_type);

            return `
                <div class="document-card">
                    <div class="doc-icon ${fileInfo.class}">${fileInfo.icon}</div>
                    <div class="doc-info">
                        <div class="doc-name" title="${doc.original_filename}">${doc.original_filename}</div>
                        <div class="doc-meta">by ${doc.user_name}</div>
                        <div class="doc-meta">${formatFileSize(doc.file_size)}</div>
                        <div class="doc-meta">${timeAgo(doc.uploaded_at)}</div>
                    </div>
                    <div class="doc-actions">
                        <button class="btn-planner primary small" onclick="viewDocument(${doc.id}, '${doc.mime_type}', '${doc.original_filename}')">View</button>
                        <button class="btn-planner primary small" onclick="downloadDocument(${doc.id})">Download</button>
                        <button class="btn-planner danger small" onclick="deleteDocument(${doc.id})">Delete</button>
                    </div>
                </div>
            `;
        }).join('');

        container.innerHTML = docsHTML;
    }

    function viewDocument(id, mimeType, filename) {
        const modal = document.getElementById('docModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalBody = document.getElementById('modalBody');

        modalTitle.textContent = filename;
        modal.style.display = 'block';

        // Check file extension as well for better detection
        const ext = filename.toLowerCase().split('.').pop();

        // Images - direct preview
        if (mimeType.includes('image')) {
            modalBody.innerHTML = `<img src="${API_URL}/documents/view?id=${id}" alt="${filename}" style="width: 100%; height: 100%; object-fit: contain;">`;
        }
        // PDFs - iframe preview
        else if (mimeType.includes('pdf')) {
            modalBody.innerHTML = `
                <iframe src="${API_URL}/documents/view?id=${id}#toolbar=1" style="width: 100%; height: 100%; border: none;"></iframe>
                <div style="padding: 20px; text-align: center; background: #f8f9fa;">
                    <p style="color: #666; margin-bottom: 10px;">Not displaying correctly?</p>
                    <button class="btn-planner primary small" onclick="window.open('${API_URL}/documents/view?id=${id}', '_blank')">Open in New Tab</button>
                    <button class="btn-planner primary small" onclick="window.open('${API_URL}/documents/download?id=${id}', '_blank')">Download Instead</button>
                </div>
            `;
        }
        // Videos - video player
        else if (mimeType.includes('video')) {
            modalBody.innerHTML = `
                <video controls style="width: 100%; height: 100%; background: #000;">
                    <source src="${API_URL}/documents/view?id=${id}" type="${mimeType}">
                    Your browser doesn't support video playback.
                </video>
            `;
        }
        // Audio - audio player
        else if (mimeType.includes('audio')) {
            modalBody.innerHTML = `
                <div style="padding: 40px; text-align: center;">
                    <audio controls style="width: 100%; max-width: 500px;">
                        <source src="${API_URL}/documents/view?id=${id}" type="${mimeType}">
                        Your browser doesn't support audio playback.
                    </audio>
                </div>
            `;
        }
        // Office documents - use Google Docs Viewer (check BEFORE text files)
        else if (mimeType.includes('word') || mimeType.includes('officedocument') ||
                 mimeType.includes('msword') || mimeType.includes('excel') ||
                 mimeType.includes('spreadsheet') || mimeType.includes('powerpoint') ||
                 mimeType.includes('presentation') || mimeType.includes('ms-') ||
                 ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'].includes(ext)) {

            let fileTypeMsg = 'Office document';
            if (mimeType.includes('word') || ['doc', 'docx'].includes(ext)) {
                fileTypeMsg = 'Word document';
            } else if (mimeType.includes('excel') || mimeType.includes('spreadsheet') || ['xls', 'xlsx'].includes(ext)) {
                fileTypeMsg = 'Excel spreadsheet';
            } else if (mimeType.includes('powerpoint') || mimeType.includes('presentation') || ['ppt', 'pptx'].includes(ext)) {
                fileTypeMsg = 'PowerPoint presentation';
            }

            // Get full URL for Google Docs Viewer
            const currentUrl = window.location.origin;
            const fileUrl = encodeURIComponent(`${currentUrl}${API_URL}/documents/download?id=${id}`);
            const googleDocsUrl = `https://docs.google.com/gview?url=${fileUrl}&embedded=true`;

            modalBody.innerHTML = `
                <div style="position: relative; height: 100%;">
                    <div style="padding: 15px; background: #f8f9fa; text-align: center; border-bottom: 1px solid #ddd;">
                        <p style="margin: 0; color: #666; font-size: 14px;">
                            <i class="fas fa-info-circle"></i> Viewing ${fileTypeMsg} using Google Docs Viewer
                        </p>
                    </div>
                    <iframe src="${googleDocsUrl}" style="width: 100%; height: calc(100% - 100px); border: none;"></iframe>
                    <div style="padding: 15px; background: #f8f9fa; text-align: center; border-top: 1px solid #ddd;">
                        <p style="color: #666; margin-bottom: 10px; font-size: 13px;">Not displaying correctly?</p>
                        <button class="btn-planner primary small" onclick="window.open('${currentUrl}${API_URL}/documents/download?id=${id}', '_blank')">
                            <i class="fas fa-download"></i> Download Instead
                        </button>
                        <button class="btn-planner small" onclick="closeModal()" style="margin-left: 10px;">Close</button>
                    </div>
                </div>
            `;
        }
        // Text files - can preview (AFTER Office documents check)
        else if (mimeType.includes('text') || mimeType.includes('json') ||
                 (mimeType.includes('xml') && !mimeType.includes('officedocument'))) {
            fetch(`${API_URL}/documents/view?id=${id}`)
                .then(response => response.text())
                .then(text => {
                    modalBody.innerHTML = `
                        <div style="padding: 20px; font-family: monospace; white-space: pre-wrap; background: #f8f9fa; height: 100%; overflow: auto;">
                            ${text.replace(/</g, '&lt;').replace(/>/g, '&gt;')}
                        </div>
                    `;
                });
        }
        // Other files - download only
        else {
            modalBody.innerHTML = `
                <div style="padding: 60px 40px; text-align: center;">
                    <div style="font-size: 48px; margin-bottom: 20px;">📄</div>
                    <h3 style="margin-bottom: 10px;">Preview Not Available</h3>
                    <p style="color: #666; margin-bottom: 30px;">This file type cannot be previewed in the browser.</p>
                    <button class="btn-planner primary" onclick="window.open('${API_URL}/documents/download?id=${id}', '_blank'); closeModal();">
                        Download to View
                    </button>
                    <button class="btn-planner" onclick="closeModal()" style="margin-left: 10px;">Close</button>
                </div>
            `;
        }
    }

    function closeModal() {
        document.getElementById('docModal').style.display = 'none';
        document.getElementById('modalBody').innerHTML = '';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('docModal');
        if (event.target == modal) {
            closeModal();
        }
    }

    function downloadDocument(id) {
        window.open(`${API_URL}/documents/download?id=${id}`, '_blank');
    }

    async function deleteDocument(id) {
        if (!confirm('Delete this document?')) return;

        try {
            await fetch(`${API_URL}/documents`, {
                method: 'DELETE',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    id: id,
                    user_id: currentUser.id
                })
            });
            loadDocuments();
            loadActivity();
        } catch (error) {
            console.error('Error deleting document:', error);
        }
    }

    // ========================================
    // TEMPLATES FUNCTIONS
    // ========================================

    let editingTemplateId = null;
    let currentRevisionTemplateId = null;
    let selectedRevisionId = null;
    let currentEditorMode = 'rich'; // 'rich' or 'code'
    let quillEditor = null;
    let preservedHtmlWrapper = null; // Stores head/style when editing in rich mode
    let previewTemplateId = null; // Current template being previewed
    let previewTemplateContent = null; // Content for new tab opening

    let categoryLabels = {};
    let categoriesData = []; // full objects from API (id, slug, name, template_count)

    // Extract body content from full HTML document
    function extractBodyContent(html) {
        // Check if this is a full HTML document
        if (!html.includes('<body') && !html.includes('<html')) {
            // It's just content, no wrapper to preserve
            preservedHtmlWrapper = null;
            return html;
        }

        // Extract body content
        const bodyMatch = html.match(/<body[^>]*>([\s\S]*?)<\/body>/i);
        if (bodyMatch) {
            // Store everything except the body content
            const beforeBody = html.substring(0, html.indexOf(bodyMatch[0]));
            const afterBody = html.substring(html.indexOf(bodyMatch[0]) + bodyMatch[0].length);

            preservedHtmlWrapper = {
                before: beforeBody + '<body>',
                after: '</body>' + afterBody
            };

            return bodyMatch[1].trim();
        }

        // No body tag found, return as-is
        preservedHtmlWrapper = null;
        return html;
    }

    // Rebuild full HTML document with preserved wrapper
    function rebuildFullHtml(bodyContent) {
        if (!preservedHtmlWrapper) {
            // No wrapper preserved, return content as-is
            return bodyContent;
        }

        return preservedHtmlWrapper.before + '\n' + bodyContent + '\n' + preservedHtmlWrapper.after;
    }

    // Initialize Quill Editor
    function initQuillEditor() {
        // Destroy existing instance if any
        destroyQuillEditor();

        // Clear the container
        const container = document.getElementById('quillEditor');
        container.innerHTML = '';

        // Initialize Quill
        quillEditor = new Quill('#quillEditor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'align': [] }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                    ['link', 'image'],
                    ['blockquote', 'code-block'],
                    ['clean']
                ],
                clipboard: {
                    matchVisual: false // Better paste handling
                }
            },
            placeholder: 'Start typing your content here...'
        });

        // Custom paste handler for better formatting preservation
        quillEditor.root.addEventListener('paste', function(e) {
            // Get plain text from clipboard
            const text = e.clipboardData.getData('text/plain');

            // Check if it looks like structured text (has bullets or time patterns)
            if (text && (text.includes('●') || text.includes('•') || text.includes('○') || /^\d{1,2}:\d{2}(am|pm)?/im.test(text))) {
                e.preventDefault();
                e.stopPropagation();

                // Convert the text to formatted HTML
                const formattedHtml = formatPastedText(text);

                // Use setTimeout to ensure we're outside the paste event
                setTimeout(() => {
                    // Get current selection
                    const range = quillEditor.getSelection(true);
                    const index = range ? range.index : 0;

                    // Clear editor if at start, otherwise insert at position
                    if (index === 0 && quillEditor.getLength() <= 1) {
                        quillEditor.root.innerHTML = '';
                    }

                    // Insert formatted content
                    quillEditor.clipboard.dangerouslyPasteHTML(index, formattedHtml);
                }, 0);
            }
        }, true); // Use capture phase

        // Auto-refresh preview on content change
        quillEditor.on('text-change', function() {
            clearTimeout(previewDebounce);
            previewDebounce = setTimeout(refreshPreview, 500);
        });
    }

    // Properly destroy Quill editor and clean up
    function destroyQuillEditor() {
        if (quillEditor) {
            // Remove any existing toolbars in the rich editor container
            const richContainer = document.getElementById('richEditorContainer');
            if (richContainer) {
                const toolbars = richContainer.querySelectorAll('.ql-toolbar');
                toolbars.forEach(toolbar => toolbar.remove());
            }
            quillEditor = null;
        }
    }

    // Format pasted text to preserve structure
    function formatPastedText(text) {
        const lines = text.split('\n');
        let html = '';
        let inList = false;

        for (let i = 0; i < lines.length; i++) {
            let line = lines[i].trim();

            if (!line) {
                // Empty line - close any open list
                if (inList) {
                    html += '</ul>';
                    inList = false;
                }
                continue;
            }

            // Check if line starts with bullet point
            if (/^[●•○▪▸►-]\s*/.test(line)) {
                // It's a bullet point
                line = line.replace(/^[●•○▪▸►-]\s*/, '');

                if (!inList) {
                    html += '<ul>';
                    inList = true;
                }
                html += `<li>${escapeHtml(line)}</li>`;
            }
            // Check if it looks like a time (e.g., "5:00pm" or "10:00 AM")
            else if (/^\d{1,2}:\d{2}\s*(am|pm|AM|PM)?$/.test(line)) {
                if (inList) {
                    html += '</ul>';
                    inList = false;
                }
                html += `<h3>${escapeHtml(line)}</h3>`;
            }
            // Check if it looks like a header (ALL CAPS or short line)
            else if (line === line.toUpperCase() && line.length > 3 && line.length < 50 && !/\d/.test(line)) {
                if (inList) {
                    html += '</ul>';
                    inList = false;
                }
                html += `<h2>${escapeHtml(line)}</h2>`;
            }
            // Regular paragraph
            else {
                if (inList) {
                    html += '</ul>';
                    inList = false;
                }
                html += `<p>${escapeHtml(line)}</p>`;
            }
        }

        // Close any remaining open list
        if (inList) {
            html += '</ul>';
        }

        return html;
    }

    // Check if HTML has complex structures that Quill will simplify
    function hasComplexHtmlStructure(html) {
        // Check for div elements with class attributes
        if (/<div[^>]+class\s*=/i.test(html)) return true;
        // Check for span elements with class attributes
        if (/<span[^>]+class\s*=/i.test(html)) return true;
        // Check for common layout classes
        if (/class\s*=\s*["'][^"']*(?:grid|column|box|header|footer|container)/i.test(html)) return true;
        return false;
    }

    function switchCodeEditorMode(mode) {
        if (mode === currentEditorMode) return;

        const richContainer = document.getElementById('richEditorContainer');
        const codeContainer = document.getElementById('codeEditorContainer');
        const richBtn = document.getElementById('richTextModeBtn');
        const codeBtn = document.getElementById('codeModeBtn');
        const formatBtn = document.getElementById('formatBtn');
        const modeLabel = document.getElementById('editorModeLabel');

        // Sync content between editors
        if (mode === 'code' && currentEditorMode === 'rich') {
            // Going from rich to code - rebuild full HTML with preserved wrapper
            if (quillEditor) {
                const bodyContent = quillEditor.root.innerHTML;
                const fullHtml = rebuildFullHtml(bodyContent);
                document.getElementById('templateContent').value = fullHtml;
            }
        } else if (mode === 'rich' && currentEditorMode === 'code') {
            // Going from code to rich - check for complex HTML and warn user
            const fullContent = document.getElementById('templateContent').value;
            const bodyContent = extractBodyContent(fullContent);

            if (hasComplexHtmlStructure(bodyContent)) {
                if (!confirm('Warning: This template contains advanced HTML formatting (custom divs, CSS classes, grid layouts) that will be simplified in Rich Text mode.\n\nThe CSS styles will be preserved, but the HTML structure will be converted to basic elements.\n\nFor templates with complex layouts, it\'s recommended to use HTML Code mode.\n\nSwitch to Rich Text mode anyway?')) {
                    return; // User cancelled, don't switch
                }
            }

            if (quillEditor) {
                quillEditor.clipboard.dangerouslyPasteHTML(bodyContent);
            }
        }

        currentEditorMode = mode;

        if (mode === 'rich') {
            richContainer.style.display = 'flex';
            codeContainer.style.display = 'none';
            richBtn.classList.add('active');
            codeBtn.classList.remove('active');
            formatBtn.style.display = 'none';
            modeLabel.textContent = 'Rich Text Editor';
        } else {
            richContainer.style.display = 'none';
            codeContainer.style.display = 'block';
            richBtn.classList.remove('active');
            codeBtn.classList.add('active');
            formatBtn.style.display = 'inline-block';
            modeLabel.textContent = 'HTML Code';
        }

        refreshPreview();
    }

    function getEditorContent() {
        if (currentEditorMode === 'rich' && quillEditor) {
            // Rebuild full HTML with preserved wrapper (CSS, etc.)
            const bodyContent = quillEditor.root.innerHTML;
            return rebuildFullHtml(bodyContent);
        } else {
            return document.getElementById('templateContent').value;
        }
    }

    function setEditorContent(content) {
        document.getElementById('templateContent').value = content;
        if (quillEditor) {
            quillEditor.clipboard.dangerouslyPasteHTML(content);
        }
    }

    async function loadTemplates() {
        try {
            const category = document.getElementById('templateCategoryFilter')?.value || 'all';
            const url = category === 'all'
                ? `${API_URL}/templates`
                : `${API_URL}/templates?category=${category}`;

            const response = await fetch(url);
            const data = await response.json();

            if (!response.ok || data.error) {
                document.getElementById('templatesSidebar').innerHTML = `
                    <div class="empty-sidebar">
                        <p style="color: #e74c3c; font-size: 12px;">${data.error || 'Failed to load'}</p>
                    </div>
                `;
                return;
            }

            displayTemplatesInSidebar(data);
        } catch (error) {
            console.error('Error loading templates:', error);
            document.getElementById('templatesSidebar').innerHTML = `
                <div class="empty-sidebar">
                    <p style="color: #e74c3c;">Failed to load templates</p>
                </div>
            `;
        }
    }

    async function loadCategories() {
        try {
            const response = await fetch(`${API_URL}/templates/categories`);
            const data = await response.json();

            if (!response.ok || data.error) return;

            categoriesData = data;
            categoryLabels = {};
            data.forEach(c => { categoryLabels[c.slug] = c.name; });

            const filterEl = document.getElementById('templateCategoryFilter');
            const newCatEl = document.getElementById('newTemplateCategory');
            const editCatEl = document.getElementById('templateCategory');

            const prevFilter = filterEl ? filterEl.value : 'all';

            if (filterEl) {
                filterEl.innerHTML = '<option value="all">All</option>' +
                    data.map(c => `<option value="${escapeHtml(c.slug)}">${escapeHtml(c.name)}</option>`).join('');
                filterEl.value = data.find(c => c.slug === prevFilter) ? prevFilter : 'all';
            }

            const categoryOptions = data.map(c => `<option value="${escapeHtml(c.slug)}">${escapeHtml(c.name)}</option>`).join('');
            [newCatEl, editCatEl, document.getElementById('saveTemplateCategory')].forEach(el => {
                if (!el) return;
                const prev = el.value;
                el.innerHTML = categoryOptions;
                el.value = data.find(c => c.slug === prev) ? prev : 'general';
            });
        } catch (e) {
            console.error('Failed to load categories:', e);
        }
    }

    function displayTemplatesInSidebar(templates) {
        const container = document.getElementById('templatesSidebar');

        if (!templates || templates.length === 0) {
            container.innerHTML = `
                <div class="empty-sidebar">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p>No documents yet</p>
                    <p style="font-size: 11px; margin-top: 8px;">Create a new document to get started</p>
                </div>
            `;
            return;
        }

        container.innerHTML = templates.map(template => `
            <div class="file-item ${currentTemplateId == template.id ? 'active' : ''}"
                 onclick="loadTemplateInEditor(${template.id})"
                 data-template-id="${template.id}">
                <div class="file-name">${escapeHtml(template.name)}</div>
                <div class="file-meta">
                    <span class="file-category">${categoryLabels[template.category] || template.category}</span>
                    <span>${timeAgo(template.updated_at)}</span>
                </div>
                <div class="file-actions">
                    <button class="file-action-btn" onclick="event.stopPropagation(); showRevisions(${template.id}, '${escapeHtml(template.name).replace(/'/g, "\\'")}')">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </button>
                    <button class="file-action-btn danger" onclick="event.stopPropagation(); deleteTemplate(${template.id})">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');
    }

    // ========================================
    // VIEW / EDIT MODE TOGGLE
    // ========================================

    let currentViewMode = 'view'; // 'view' or 'edit'

    function switchEditorMode(mode) {
        currentViewMode = mode;
        const viewerFrame = document.getElementById('htmlViewerFrame');
        const editorContent = document.getElementById('htmlEditorContent');
        const editIndicator = document.getElementById('editModeIndicator');
        const viewIndicator = document.getElementById('viewModeIndicator');
        const viewBtn = document.getElementById('viewModeBtn');
        const editBtn = document.getElementById('editModeBtn');

        if (mode === 'view') {
            // Switch to view mode - render in sandboxed iframe
            editorContent.style.display = 'none';
            viewerFrame.style.display = 'block';
            editIndicator.style.display = 'none';
            viewIndicator.style.display = 'flex';
            viewBtn.classList.add('active');
            editBtn.classList.remove('active');

            // Build full HTML with styles and render in iframe
            renderViewMode();
        } else {
            // Switch to edit mode
            viewerFrame.style.display = 'none';
            editorContent.style.display = 'block';
            editIndicator.style.display = 'flex';
            viewIndicator.style.display = 'none';
            editBtn.classList.add('active');
            viewBtn.classList.remove('active');
        }
    }

    function renderViewMode() {
        const viewerFrame = document.getElementById('htmlViewerFrame');
        const editorContent = document.getElementById('htmlEditorContent');

        // Check if there's content loaded
        if (!editorContent.innerHTML || editorContent.querySelector('.welcome-state')) {
            const doc = viewerFrame.contentDocument || viewerFrame.contentWindow.document;
            doc.open();
            doc.write(`
                <html><body style="font-family: 'Segoe UI', sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; color: #666;">
                    <div style="text-align: center;">
                        <p style="font-size: 16px; margin-bottom: 8px;">No document loaded</p>
                        <p style="font-size: 13px; color: #999;">Select a document from the sidebar or create a new one</p>
                    </div>
                </body></html>
            `);
            doc.close();
            return;
        }

        // Build full HTML with preserved styles
        let fullHtml = '';
        if (currentDocumentStyles) {
            fullHtml = `<html><head><style>${currentDocumentStyles}</style></head><body>${editorContent.innerHTML}</body></html>`;
        } else {
            fullHtml = `<html><head><style>
                body { font-family: 'Segoe UI', -apple-system, sans-serif; line-height: 1.6; color: #333; padding: 40px; max-width: 900px; margin: 0 auto; }
                h1, h2, h3, h4 { color: #1a1a2e; }
                table { border-collapse: collapse; width: 100%; }
                th, td { padding: 8px 12px; border: 1px solid #e0e0e0; text-align: left; }
                th { background: #f5f5f5; font-weight: 600; }
            </style></head><body>${editorContent.innerHTML}</body></html>`;
        }

        // Remove contenteditable attributes for clean view
        fullHtml = fullHtml.replace(/\s*contenteditable="true"/g, '');

        const doc = viewerFrame.contentDocument || viewerFrame.contentWindow.document;
        doc.open();
        doc.write(fullHtml);
        doc.close();
    }

    // ========================================
    // FULLSCREEN VIEW
    // ========================================

    function openFullscreenView() {
        const editorContent = document.getElementById('htmlEditorContent');
        if (!editorContent.innerHTML || editorContent.querySelector('.welcome-state')) {
            return; // No document loaded
        }

        // Build full HTML (same logic as renderViewMode)
        let fullHtml = '';
        if (currentDocumentStyles) {
            fullHtml = `<html><head><style>${currentDocumentStyles}</style></head><body>${editorContent.innerHTML}</body></html>`;
        } else {
            fullHtml = `<html><head><style>
                body { font-family: 'Segoe UI', -apple-system, sans-serif; line-height: 1.6; color: #333; padding: 40px; max-width: 900px; margin: 0 auto; }
                h1, h2, h3, h4 { color: #1a1a2e; }
                table { border-collapse: collapse; width: 100%; }
                th, td { padding: 8px 12px; border: 1px solid #e0e0e0; text-align: left; }
                th { background: #f5f5f5; font-weight: 600; }
            </style></head><body>${editorContent.innerHTML}</body></html>`;
        }
        fullHtml = fullHtml.replace(/\s*contenteditable="true"/g, '');

        const overlay = document.getElementById('fullscreenOverlay');
        const iframe = document.getElementById('fullscreenViewerFrame');

        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
        fullscreenZoomLevel = 100;
        document.getElementById('fullscreenZoomLabel').textContent = '100%';

        const doc = iframe.contentDocument || iframe.contentWindow.document;
        doc.open();
        doc.write(fullHtml);
        doc.close();

        // Request browser fullscreen
        if (overlay.requestFullscreen) {
            overlay.requestFullscreen().catch(() => {});
        }
    }

    function closeFullscreenView() {
        const overlay = document.getElementById('fullscreenOverlay');
        overlay.classList.remove('active');
        document.body.style.overflow = '';

        if (document.fullscreenElement) {
            document.exitFullscreen().catch(() => {});
        }
    }

    let fullscreenZoomLevel = 100;

    function fullscreenZoom(action) {
        if (action === 'in') {
            fullscreenZoomLevel = Math.min(fullscreenZoomLevel + 10, 200);
        } else if (action === 'out') {
            fullscreenZoomLevel = Math.max(fullscreenZoomLevel - 10, 50);
        } else {
            fullscreenZoomLevel = 100;
        }

        document.getElementById('fullscreenZoomLabel').textContent = fullscreenZoomLevel + '%';

        const iframe = document.getElementById('fullscreenViewerFrame');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        if (iframeDoc && iframeDoc.body) {
            iframeDoc.body.style.zoom = (fullscreenZoomLevel / 100);
        }
    }

    // Close on Esc key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.getElementById('fullscreenOverlay').classList.contains('active')) {
            closeFullscreenView();
        }
    });

    // Close when exiting browser fullscreen (e.g. user presses Esc natively)
    document.addEventListener('fullscreenchange', function() {
        if (!document.fullscreenElement && document.getElementById('fullscreenOverlay').classList.contains('active')) {
            closeFullscreenView();
        }
    });

    // ========================================
    // HTML EDITOR FUNCTIONS
    // ========================================

    async function loadTemplateInEditor(templateId) {
        try {
            const response = await fetch(`${API_URL}/templates/show?id=${templateId}`);
            const template = await response.json();

            if (template.error) {
                alert('Failed to load template: ' + template.error);
                return;
            }

            currentTemplateId = templateId;
            currentTemplateName = template.name;
            isEditorDirty = false;

            // Update title
            document.getElementById('currentDocTitle').textContent = template.name;

            // Extract and process styles from saved content
            let content = template.content;
            let scopedStyles = '';
            let originalStyles = '';
            const styleMatches = content.match(/<style[^>]*>([\s\S]*?)<\/style>/gi);
            if (styleMatches) {
                styleMatches.forEach(match => {
                    let styleContent = match.replace(/<\/?style[^>]*>/gi, '');
                    originalStyles += styleContent + '\n';
                    scopedStyles += scopeStylesForEditor(styleContent);
                });
            }
            currentDocumentStyles = originalStyles.trim();

            // Remove style tags from content for display
            content = content.replace(/<style[^>]*>[\s\S]*?<\/style>/gi, '');

            // Remove any previous scoped styles
            const existingScopedStyle = document.getElementById('editor-scoped-styles');
            if (existingScopedStyle) {
                existingScopedStyle.remove();
            }

            // Add scoped styles to head
            if (scopedStyles) {
                const styleElement = document.createElement('style');
                styleElement.id = 'editor-scoped-styles';
                styleElement.textContent = scopedStyles;
                document.head.appendChild(styleElement);
            }

            // Load content into editor
            const editorContent = document.getElementById('htmlEditorContent');
            editorContent.innerHTML = content;

            // Make elements editable (for when user switches to edit mode)
            makeEditable(editorContent);

            // Update sidebar active state
            document.querySelectorAll('.file-item').forEach(item => {
                item.classList.remove('active');
            });
            document.querySelector(`.file-item[data-template-id="${templateId}"]`)?.classList.add('active');

            // Default to view mode when loading a document
            switchEditorMode('view');

            // Auto-collapse sidebar after loading to give more editing space
            collapseSidebar();

        } catch (error) {
            console.error('Error loading template:', error);
            alert('Failed to load template');
        }
    }

    function makeEditable(element) {
        const textElements = ['P', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6', 'LI', 'SPAN', 'DIV', 'TD', 'TH', 'STRONG', 'EM', 'B', 'I', 'A', 'LABEL'];

        Array.from(element.children).forEach(child => {
            if (textElements.includes(child.tagName) && child.textContent.trim()) {
                const hasOnlyInlineChildren = Array.from(child.children).every(c =>
                    ['STRONG', 'EM', 'B', 'I', 'SPAN', 'BR', 'SMALL', 'CODE', 'A'].includes(c.tagName)
                );

                if (hasOnlyInlineChildren || child.children.length === 0) {
                    child.setAttribute('contenteditable', 'true');
                    child.addEventListener('input', () => {
                        isEditorDirty = true;
                    });
                }
            }
            makeEditable(child);
        });
    }

    // Modal functions
    function openNewTemplateModal() {
        document.getElementById('newTemplateName').value = '';
        document.getElementById('newTemplateCategory').value = 'job-descriptions';
        document.getElementById('newTemplateModal').classList.add('active');
    }

    function closeNewTemplateModal() {
        document.getElementById('newTemplateModal').classList.remove('active');
    }

    // ---- Manage Categories ----

    function openManageCategoriesModal() {
        document.getElementById('newCategoryName').value = '';
        document.getElementById('manageCatError').style.display = 'none';
        renderCategoryList();
        document.getElementById('manageCategoriesModal').classList.add('active');
    }

    function closeManageCategoriesModal() {
        document.getElementById('manageCategoriesModal').classList.remove('active');
    }

    function renderCategoryList() {
        const list = document.getElementById('manageCategoriesList');
        if (!categoriesData.length) {
            list.innerHTML = '<p style="color:#999;font-size:13px;">No categories found.</p>';
            return;
        }
        list.innerHTML = categoriesData.map(c => {
            const isDefault = c.slug === 'general';
            const count = parseInt(c.template_count) || 0;
            return `
            <div id="cat-row-${c.id}" style="display:flex;align-items:center;gap:8px;padding:6px 8px;background:#f9f9f9;border-radius:4px;border:1px solid #eee;">
                <span id="cat-label-${c.id}" style="flex:1;font-size:13px;">${escapeHtml(c.name)}${isDefault ? ' <em style="color:#aaa;font-size:11px;">(default)</em>' : ''}</span>
                <span style="font-size:11px;color:#999;white-space:nowrap;">${count} doc${count !== 1 ? 's' : ''}</span>
                <button onclick="startRenameCategory(${c.id})" title="Rename" style="background:none;border:1px solid #ddd;border-radius:3px;padding:2px 7px;cursor:pointer;font-size:11px;color:#555;">Rename</button>
                ${!isDefault ? `<button onclick="confirmDeleteCategory(${c.id},'${escapeHtml(c.slug)}',${count})" title="Delete" style="background:none;border:1px solid #f5c6cb;border-radius:3px;padding:2px 7px;cursor:pointer;font-size:11px;color:#c0392b;">Delete</button>` : ''}
            </div>`;
        }).join('');
    }

    function startRenameCategory(id) {
        const cat = categoriesData.find(c => c.id == id);
        if (!cat) return;
        const row = document.getElementById(`cat-row-${id}`);
        row.innerHTML = `
            <input type="text" id="rename-input-${id}" value="${escapeHtml(cat.name)}" maxlength="100"
                   style="flex:1;padding:4px 8px;border:1px solid #ccc;border-radius:3px;font-size:13px;"
                   onkeydown="if(event.key==='Enter')saveRenameCategory(${id});if(event.key==='Escape')renderCategoryList();">
            <button onclick="saveRenameCategory(${id})" class="btn-planner primary" style="padding:4px 10px;font-size:12px;">Save</button>
            <button onclick="renderCategoryList()" style="background:none;border:1px solid #ddd;border-radius:3px;padding:4px 8px;cursor:pointer;font-size:12px;color:#555;">Cancel</button>
        `;
        document.getElementById(`rename-input-${id}`).focus();
    }

    async function saveRenameCategory(id) {
        const input = document.getElementById(`rename-input-${id}`);
        const name = input ? input.value.trim() : '';
        if (!name) { showManageCatError('Name cannot be empty'); return; }

        try {
            const resp = await fetch(`${API_URL}/templates/categories`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, name })
            });
            const data = await resp.json();
            if (!resp.ok || data.error) { showManageCatError(data.error || 'Failed to rename'); return; }
            await loadCategories();
            renderCategoryList();
        } catch (e) {
            showManageCatError('Network error');
        }
    }

    function confirmDeleteCategory(id, slug, count) {
        const msg = count > 0
            ? `This will move ${count} document${count !== 1 ? 's' : ''} to "Other". Delete this category?`
            : 'Delete this category?';
        if (!confirm(msg)) return;
        deleteCategory(id);
    }

    async function deleteCategory(id) {
        try {
            const resp = await fetch(`${API_URL}/templates/categories`, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            });
            const data = await resp.json();
            if (!resp.ok || data.error) { showManageCatError(data.error || 'Failed to delete'); return; }
            await loadCategories();
            renderCategoryList();
            loadTemplates();
        } catch (e) {
            showManageCatError('Network error');
        }
    }

    async function addCategory() {
        const name = document.getElementById('newCategoryName').value.trim();
        if (!name) { showManageCatError('Enter a category name'); return; }

        try {
            const resp = await fetch(`${API_URL}/templates/categories`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name })
            });
            const data = await resp.json();
            if (!resp.ok || data.error) { showManageCatError(data.error || 'Failed to add'); return; }
            document.getElementById('newCategoryName').value = '';
            document.getElementById('manageCatError').style.display = 'none';
            await loadCategories();
            renderCategoryList();
        } catch (e) {
            showManageCatError('Network error');
        }
    }

    function showManageCatError(msg) {
        const el = document.getElementById('manageCatError');
        el.textContent = msg;
        el.style.display = 'block';
        setTimeout(() => { el.style.display = 'none'; }, 4000);
    }

    // ---- End Manage Categories ----

    function createNewTemplate() {
        const name = document.getElementById('newTemplateName').value.trim();
        const category = document.getElementById('newTemplateCategory').value;

        if (!name) {
            alert('Please enter a document name');
            return;
        }

        currentTemplateId = null;
        currentTemplateName = name;
        isEditorDirty = false;
        currentDocumentStyles = ''; // Clear styles for new document

        // Remove any previous scoped styles
        const existingScopedStyle = document.getElementById('editor-scoped-styles');
        if (existingScopedStyle) {
            existingScopedStyle.remove();
        }

        document.getElementById('currentDocTitle').textContent = name;

        // Load default template content
        const defaultContent = getDefaultHtmlTemplate(name);
        const editorContent = document.getElementById('htmlEditorContent');
        editorContent.innerHTML = defaultContent;
        makeEditable(editorContent);

        // New documents open in edit mode
        switchEditorMode('edit');

        closeNewTemplateModal();

        // Store category for saving
        document.getElementById('saveTemplateCategory').value = category;
    }

    function getDefaultHtmlTemplate(title) {
        return `
            <div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; max-width: 800px; margin: 0 auto;">
                <h1 style="color: #00b207; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">${escapeHtml(title)}</h1>
                <p>Start editing your document here. Click on any text to edit it.</p>

                <h2 style="color: #374151; border-bottom: 2px solid #e5e7eb; padding-bottom: 5px;">Section Title</h2>
                <p>Add your content here. You can format text and add sections as needed.</p>

                <h3>Subsection</h3>
                <ul>
                    <li>Point one</li>
                    <li>Point two</li>
                    <li>Point three</li>
                </ul>

                <p><strong>Note:</strong> Click any text to edit it. Use Save to store your changes.</p>
            </div>
        `;
    }

    function openLoadHtmlModal() {
        document.getElementById('htmlFileInput').value = '';
        document.getElementById('htmlPasteInput').value = '';
        document.getElementById('loadHtmlModal').classList.add('active');
    }

    function closeLoadHtmlModal() {
        document.getElementById('loadHtmlModal').classList.remove('active');
    }

    function loadHtmlFile() {
        const fileInput = document.getElementById('htmlFileInput');
        const file = fileInput.files[0];

        if (!file) {
            alert('Please select a file first');
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            loadHtmlContent(e.target.result, file.name.replace(/\.[^/.]+$/, ''));
            closeLoadHtmlModal();
        };
        reader.readAsText(file);
    }

    function loadHtmlFromPaste() {
        const htmlCode = document.getElementById('htmlPasteInput').value.trim();

        if (!htmlCode) {
            alert('Please paste some HTML code first');
            return;
        }

        loadHtmlContent(htmlCode, 'Imported Document');
        closeLoadHtmlModal();
    }

    function loadHtmlContent(htmlString, title = 'Imported Document') {
        currentTemplateId = null;
        currentTemplateName = title;
        isEditorDirty = true;

        document.getElementById('currentDocTitle').textContent = title;

        // Extract body content if present
        let content = htmlString;
        const bodyMatch = htmlString.match(/<body[^>]*>([\s\S]*)<\/body>/i);
        if (bodyMatch) {
            content = bodyMatch[1];
        }

        // Extract and scope styles to prevent layout issues while preserving document styling
        let scopedStyles = '';
        let originalStyles = ''; // Store original styles for saving
        const styleMatches = htmlString.match(/<style[^>]*>([\s\S]*?)<\/style>/gi);
        if (styleMatches) {
            styleMatches.forEach(match => {
                let styleContent = match.replace(/<\/?style[^>]*>/gi, '');
                originalStyles += styleContent + '\n'; // Keep original for saving
                // Scope styles and filter out problematic selectors
                scopedStyles += scopeStylesForEditor(styleContent);
            });
        }
        currentDocumentStyles = originalStyles.trim(); // Store for saving later

        // Remove original style tags from content
        content = content.replace(/<style[^>]*>[\s\S]*?<\/style>/gi, '');

        const editorContent = document.getElementById('htmlEditorContent');

        // Remove any previous scoped styles
        const existingScopedStyle = document.getElementById('editor-scoped-styles');
        if (existingScopedStyle) {
            existingScopedStyle.remove();
        }

        // Inject scoped styles INSIDE the editor area (after planner CSS) so they win by source order
        if (scopedStyles) {
            const styleElement = document.createElement('style');
            styleElement.id = 'editor-scoped-styles';
            styleElement.textContent = scopedStyles;
            editorContent.parentElement.insertBefore(styleElement, editorContent);
        }

        editorContent.innerHTML = content;
        makeEditable(editorContent);

        showSavedIndicator('Loaded');

        // Show document in view mode
        switchEditorMode('view');

        // Auto-collapse sidebar after loading to give more editing space
        collapseSidebar();
    }

    /**
     * Toggle the editor sidebar visibility
     */
    function toggleEditorSidebar() {
        const sidebar = document.querySelector('.editor-sidebar');
        const toggleBtn = document.getElementById('sidebarToggle');

        if (sidebar) {
            sidebar.classList.toggle('collapsed');
            // Update toggle icon direction
            const icon = toggleBtn.querySelector('i');
            if (sidebar.classList.contains('collapsed')) {
                icon.className = 'fa-solid fa-chevron-right';
                toggleBtn.title = 'Show Sidebar';
            } else {
                icon.className = 'fa-solid fa-chevron-left';
                toggleBtn.title = 'Hide Sidebar';
            }
        }
    }

    /**
     * Collapse the sidebar
     */
    function collapseSidebar() {
        const sidebar = document.querySelector('.editor-sidebar');
        const toggleBtn = document.getElementById('sidebarToggle');

        if (sidebar && !sidebar.classList.contains('collapsed')) {
            sidebar.classList.add('collapsed');
            const icon = toggleBtn.querySelector('i');
            icon.className = 'fa-solid fa-chevron-right';
            toggleBtn.title = 'Show Sidebar';
        }
    }

    /**
     * Expand the sidebar
     */
    function expandSidebar() {
        const sidebar = document.querySelector('.editor-sidebar');
        const toggleBtn = document.getElementById('sidebarToggle');

        if (sidebar && sidebar.classList.contains('collapsed')) {
            sidebar.classList.remove('collapsed');
            const icon = toggleBtn.querySelector('i');
            icon.className = 'fa-solid fa-chevron-left';
            toggleBtn.title = 'Hide Sidebar';
        }
    }

    /**
     * Scope CSS styles to only apply within .editable-content
     * Filters out body/html/* selectors that could affect layout
     */
    function scopeStylesForEditor(cssText) {
        const SCOPE = '#htmlEditorContent';

        // Scope a single CSS rule's selector
        function scopeSelector(selector, declarations) {
            const selectorLower = selector.toLowerCase().trim();

            // Remap body/html styles to editor container
            if (selectorLower === 'body' || selectorLower === 'html') {
                return `${SCOPE} ${declarations}`;
            }

            // Remap * reset
            if (selectorLower === '*' || selectorLower.startsWith('*, ')) {
                return `${SCOPE} * ${declarations}`;
            }

            // Skip :root
            if (selectorLower === ':root') {
                return '';
            }

            // Handle comma-separated selectors: scope each part
            if (selector.includes(',')) {
                const parts = selector.split(',').map(s => `${SCOPE} ${s.trim()}`).join(', ');
                return `${parts} ${declarations}`;
            }

            return `${SCOPE} ${selector} ${declarations}`;
        }

        // Split CSS into top-level rules using brace counting
        const rules = [];
        let currentRule = '';
        let braceCount = 0;

        for (let i = 0; i < cssText.length; i++) {
            const char = cssText[i];
            currentRule += char;

            if (char === '{') braceCount++;
            if (char === '}') {
                braceCount--;
                if (braceCount === 0) {
                    rules.push(currentRule.trim());
                    currentRule = '';
                }
            }
        }

        // Process each rule
        const scopedRules = rules.map(rule => {
            // Handle @media and other @-rules: scope the selectors INSIDE
            if (rule.startsWith('@')) {
                const firstBrace = rule.indexOf('{');
                const atRule = rule.substring(0, firstBrace + 1); // e.g. "@media (...) {"
                const innerCSS = rule.substring(firstBrace + 1, rule.lastIndexOf('}'));

                // Parse inner rules and scope each one
                const innerRules = [];
                let innerRule = '';
                let innerBraceCount = 0;

                for (let i = 0; i < innerCSS.length; i++) {
                    const char = innerCSS[i];
                    innerRule += char;
                    if (char === '{') innerBraceCount++;
                    if (char === '}') {
                        innerBraceCount--;
                        if (innerBraceCount === 0) {
                            const trimmed = innerRule.trim();
                            if (trimmed) {
                                const bi = trimmed.indexOf('{');
                                if (bi !== -1) {
                                    const sel = trimmed.substring(0, bi).trim();
                                    const decl = trimmed.substring(bi);
                                    innerRules.push(scopeSelector(sel, decl));
                                }
                            }
                            innerRule = '';
                        }
                    }
                }

                return `${atRule}\n${innerRules.filter(r => r).join('\n')}\n}`;
            }

            // Regular rule
            const braceIndex = rule.indexOf('{');
            if (braceIndex === -1) return '';

            const selector = rule.substring(0, braceIndex).trim();
            const declarations = rule.substring(braceIndex);

            return scopeSelector(selector, declarations);
        });

        return scopedRules.filter(r => r).join('\n');
    }

    function saveCurrentTemplate() {
        const content = getHtmlEditorContent();

        if (!content || content.trim() === '' || content.includes('class="welcome-state"')) {
            alert('Please create or load a document first');
            return;
        }

        if (currentTemplateId) {
            // Update existing template
            document.getElementById('saveTemplateName').value = currentTemplateName;
            document.getElementById('saveChangeSummaryGroup').style.display = 'block';
            document.getElementById('saveChangeSummary').value = '';
        } else {
            // New template
            document.getElementById('saveTemplateName').value = currentTemplateName || '';
            document.getElementById('saveChangeSummaryGroup').style.display = 'none';
        }

        document.getElementById('saveTemplateModal').classList.add('active');
    }

    function closeSaveTemplateModal() {
        document.getElementById('saveTemplateModal').classList.remove('active');
    }

    async function confirmSaveTemplate() {
        const name = document.getElementById('saveTemplateName').value.trim();
        const category = document.getElementById('saveTemplateCategory').value;
        const changeSummary = document.getElementById('saveChangeSummary').value.trim();
        const content = getHtmlEditorContent();

        if (!name) {
            alert('Please enter a document name');
            return;
        }

        if (currentTemplateId && !changeSummary) {
            alert('Please enter a change summary');
            return;
        }

        try {
            const body = {
                user_id: currentUser.id,
                name: name,
                category: category,
                content: content
            };

            if (currentTemplateId) {
                body.id = currentTemplateId;
                body.change_summary = changeSummary;
            }

            const response = await fetch(`${API_URL}/templates`, {
                method: currentTemplateId ? 'PUT' : 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(body)
            });

            const result = await response.json();

            if (result.success) {
                if (!currentTemplateId) {
                    currentTemplateId = result.id;
                }
                currentTemplateName = name;
                isEditorDirty = false;

                closeSaveTemplateModal();
                loadTemplates();
                loadActivity();
                showSavedIndicator('Saved');
                document.getElementById('currentDocTitle').textContent = name;
            } else {
                alert('Failed to save: ' + (result.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error saving template:', error);
            alert('Failed to save template');
        }
    }

    function getHtmlEditorContent() {
        const editorContent = document.getElementById('htmlEditorContent');
        const clone = editorContent.cloneNode(true);

        // Remove contenteditable attributes
        clone.querySelectorAll('[contenteditable]').forEach(el => {
            el.removeAttribute('contenteditable');
        });

        // Remove welcome state if present
        const welcome = clone.querySelector('.welcome-state');
        if (welcome) {
            return '';
        }

        // Include stored styles with the content so they persist when saved
        let htmlContent = clone.innerHTML;
        if (currentDocumentStyles) {
            htmlContent = `<style>\n${currentDocumentStyles}\n</style>\n${htmlContent}`;
        }

        return htmlContent;
    }

    function exportHtmlContent() {
        const content = getHtmlEditorContent();

        if (!content) {
            alert('Please create or load a document first');
            return;
        }

        const fullHtml = `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>${escapeHtml(currentTemplateName || 'Document')}</title>
</head>
<body>
${content}
</body>
</html>`;

        document.getElementById('exportCodeOutput').textContent = fullHtml;
        document.getElementById('exportHtmlModal').classList.add('active');
    }

    function closeExportHtmlModal() {
        document.getElementById('exportHtmlModal').classList.remove('active');
    }

    function copyExportedHtml() {
        const code = document.getElementById('exportCodeOutput').textContent;
        navigator.clipboard.writeText(code).then(() => {
            alert('HTML copied to clipboard!');
        });
    }

    function downloadHtmlContent() {
        const content = getHtmlEditorContent();

        if (!content) {
            alert('Please create or load a document first');
            return;
        }

        const fullHtml = `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>${escapeHtml(currentTemplateName || 'Document')}</title>
</head>
<body>
${content}
</body>
</html>`;

        const blob = new Blob([fullHtml], { type: 'text/html' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = (currentTemplateName || 'document').toLowerCase().replace(/[^a-z0-9]+/g, '-') + '.html';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }

    async function downloadPdfContent() {
        const content = getHtmlEditorContent();

        if (!content) {
            alert('Please create or load a document first');
            return;
        }

        const title = currentTemplateName || 'Document';

        // Show loading state
        const pdfBtn = document.querySelector('button[onclick="downloadPdfContent()"]');
        const originalHtml = pdfBtn.innerHTML;
        pdfBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Generating...';
        pdfBtn.disabled = true;

        try {
            const response = await fetch(`${API_URL}/templates/pdf`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    html: content,
                    title: title
                })
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.error || 'Failed to generate PDF');
            }

            // Get the PDF blob
            const blob = await response.blob();

            // Create download link
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = title.toLowerCase().replace(/[^a-z0-9]+/g, '-') + '.pdf';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);

            showSavedIndicator('PDF Downloaded');
        } catch (error) {
            console.error('PDF generation error:', error);
            alert('Failed to generate PDF: ' + error.message);
        } finally {
            // Restore button state
            pdfBtn.innerHTML = originalHtml;
            pdfBtn.disabled = false;
        }
    }

    function showSavedIndicator(text = 'Saved') {
        const indicator = document.getElementById('htmlSavedIndicator');
        indicator.innerHTML = '✓ ' + text;
        indicator.classList.add('show');
        setTimeout(() => {
            indicator.classList.remove('show');
        }, 2000);
    }

    // Keep old displayTemplates for backward compatibility (used by grid view if needed)
    function displayTemplates(templates) {
        displayTemplatesInSidebar(templates);
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // ========================================
    // TEMPLATE PREVIEW FUNCTIONS
    // ========================================

    async function previewTemplate(templateId, templateName) {
        previewTemplateId = templateId;

        try {
            const response = await fetch(`${API_URL}/templates/show?id=${templateId}`);
            const template = await response.json();

            if (template.error) {
                alert('Failed to load template: ' + template.error);
                return;
            }

            previewTemplateContent = template.content;
            document.getElementById('previewModalTitle').textContent = templateName;

            // Load content into preview iframe
            const iframe = document.getElementById('templatePreviewFrame');
            const doc = iframe.contentDocument || iframe.contentWindow.document;
            doc.open();
            doc.write(template.content);
            doc.close();

            // Show modal
            document.getElementById('templatePreviewModal').style.display = 'flex';
        } catch (error) {
            console.error('Error loading template preview:', error);
            alert('Failed to load template preview');
        }
    }

    function closePreviewModal() {
        document.getElementById('templatePreviewModal').style.display = 'none';
        previewTemplateId = null;
        previewTemplateContent = null;
    }

    function openTemplateInNewTab() {
        if (!previewTemplateContent) return;

        // Create a blob URL for the content
        const blob = new Blob([previewTemplateContent], { type: 'text/html' });
        const url = URL.createObjectURL(blob);

        // Open in new tab
        const newTab = window.open(url, '_blank');

        // Clean up blob URL after a short delay
        setTimeout(() => URL.revokeObjectURL(url), 1000);
    }

    async function downloadTemplate() {
        if (!previewTemplateContent) return;

        // Get template name for filename
        const title = document.getElementById('previewModalTitle').textContent || 'template';
        // Clean filename - remove special characters, replace spaces with hyphens
        const filename = title.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .substring(0, 50) + '.pdf';

        try {
            // Show loading state
            const downloadBtn = document.querySelector('.btn-download');
            const originalText = downloadBtn.innerHTML;
            downloadBtn.innerHTML = '<span>Generating PDF...</span>';
            downloadBtn.disabled = true;

            // Send HTML to server for PDF generation
            const response = await fetch('<?= url("api/pdf/generate") ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    html: previewTemplateContent,
                    filename: filename
                })
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.error || 'PDF generation failed');
            }

            // Get the PDF blob
            const blob = await response.blob();

            // Create download link
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            // Clean up
            setTimeout(() => URL.revokeObjectURL(url), 1000);

            // Restore button
            downloadBtn.innerHTML = originalText;
            downloadBtn.disabled = false;

        } catch (error) {
            console.error('PDF generation failed:', error);
            alert('Failed to generate PDF: ' + error.message);

            // Restore button
            const downloadBtn = document.querySelector('.btn-download');
            downloadBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/></svg> Download PDF';
            downloadBtn.disabled = false;
        }
    }

    function editFromPreview() {
        if (!previewTemplateId) return;

        // Save ID before closing modal (which clears it)
        const templateId = previewTemplateId;
        closePreviewModal();
        editTemplate(templateId);
    }

    // Close preview modal when clicking outside
    document.getElementById('templatePreviewModal')?.addEventListener('click', function(event) {
        if (event.target === this) {
            closePreviewModal();
        }
    });

    function openTemplateEditor(templateId = null) {
        editingTemplateId = templateId;
        document.getElementById('templateEditorTitle').textContent = templateId ? 'Edit Template' : 'New Template';
        document.getElementById('changeSummaryGroup').style.display = templateId ? 'block' : 'none';

        // Reset to rich text mode
        currentEditorMode = 'rich';
        document.getElementById('richEditorContainer').style.display = 'flex';
        document.getElementById('codeEditorContainer').style.display = 'none';
        document.getElementById('richTextModeBtn').classList.add('active');
        document.getElementById('codeModeBtn').classList.remove('active');
        document.getElementById('formatBtn').style.display = 'none';
        document.getElementById('editorModeLabel').textContent = 'Rich Text Editor';

        if (!templateId) {
            document.getElementById('templateName').value = '';
            document.getElementById('templateCategory').value = 'job-descriptions';
            document.getElementById('changeSummary').value = '';

            // Clear any preserved wrapper from previous edits
            preservedHtmlWrapper = null;

            const defaultContent = getDefaultRichTemplate();
            document.getElementById('templateContent').value = defaultContent;
        }

        document.getElementById('templateEditorModal').style.display = 'flex';

        // Initialize Quill after modal is visible
        setTimeout(() => {
            initQuillEditor();
            if (!templateId) {
                setTimeout(() => {
                    if (quillEditor) {
                        quillEditor.clipboard.dangerouslyPasteHTML(getDefaultRichTemplate());
                    }
                    refreshPreview();
                }, 100);
            }
        }, 100);
    }

    async function editTemplate(id) {
        try {
            const response = await fetch(`${API_URL}/templates/show?id=${id}`);
            const template = await response.json();

            if (template.error) {
                alert('Failed to load template: ' + template.error);
                return;
            }

            editingTemplateId = id;
            document.getElementById('templateEditorTitle').textContent = 'Edit Template';
            document.getElementById('templateName').value = template.name;
            document.getElementById('templateCategory').value = template.category;
            document.getElementById('templateContent').value = template.content;
            document.getElementById('changeSummary').value = '';
            document.getElementById('changeSummaryGroup').style.display = 'block';

            // Check if template has complex HTML - if so, start in code mode
            const bodyContent = extractBodyContent(template.content);
            const isComplex = hasComplexHtmlStructure(bodyContent);

            if (isComplex) {
                // Complex template - start in code mode to preserve formatting
                currentEditorMode = 'code';
                document.getElementById('richEditorContainer').style.display = 'none';
                document.getElementById('codeEditorContainer').style.display = 'block';
                document.getElementById('richTextModeBtn').classList.remove('active');
                document.getElementById('codeModeBtn').classList.add('active');
                document.getElementById('formatBtn').style.display = 'inline-block';
                document.getElementById('editorModeLabel').textContent = 'HTML Code';
            } else {
                // Simple template - start in rich text mode
                currentEditorMode = 'rich';
                document.getElementById('richEditorContainer').style.display = 'flex';
                document.getElementById('codeEditorContainer').style.display = 'none';
                document.getElementById('richTextModeBtn').classList.add('active');
                document.getElementById('codeModeBtn').classList.remove('active');
                document.getElementById('formatBtn').style.display = 'none';
                document.getElementById('editorModeLabel').textContent = 'Rich Text Editor';
            }

            document.getElementById('templateEditorModal').style.display = 'flex';

            // Initialize Quill and set content (only load into Quill if in rich mode)
            setTimeout(() => {
                initQuillEditor();
                setTimeout(() => {
                    if (quillEditor && !isComplex) {
                        // Only load into Quill if starting in rich mode
                        quillEditor.clipboard.dangerouslyPasteHTML(bodyContent);
                    }
                    refreshPreview();
                }, 100);
            }, 100);
        } catch (error) {
            console.error('Error loading template:', error);
            alert('Failed to load template');
        }
    }

    function closeTemplateEditor() {
        document.getElementById('templateEditorModal').style.display = 'none';
        editingTemplateId = null;
        // Clean up Quill and preserved wrapper
        destroyQuillEditor();
        preservedHtmlWrapper = null;
    }

    function refreshPreview() {
        const content = getEditorContent();
        const iframe = document.getElementById('templatePreview');
        const doc = iframe.contentDocument || iframe.contentWindow.document;
        doc.open();
        doc.write(content);
        doc.close();
    }

    // Auto-refresh preview on content change with debounce
    let previewDebounce = null;
    document.addEventListener('DOMContentLoaded', function() {
        const contentArea = document.getElementById('templateContent');
        if (contentArea) {
            contentArea.addEventListener('input', function() {
                clearTimeout(previewDebounce);
                previewDebounce = setTimeout(refreshPreview, 500);
            });
        }
    });

    function formatHtml() {
        const textarea = document.getElementById('templateContent');
        let html = textarea.value;

        // Simple HTML formatting (basic indentation)
        html = html.replace(/></g, '>\n<');
        html = html.replace(/\n\s*\n/g, '\n');

        let formatted = '';
        let indent = 0;
        const lines = html.split('\n');

        lines.forEach(line => {
            line = line.trim();
            if (!line) return;

            // Decrease indent for closing tags
            if (line.match(/^<\/[^>]+>$/)) {
                indent = Math.max(0, indent - 1);
            }

            formatted += '    '.repeat(indent) + line + '\n';

            // Increase indent for opening tags (not self-closing or void elements)
            if (line.match(/^<[^\/!][^>]*[^\/]>$/) &&
                !line.match(/^<(br|hr|img|input|meta|link|area|base|col|embed|param|source|track|wbr)[^>]*>$/i)) {
                indent++;
            }
        });

        textarea.value = formatted.trim();
        refreshPreview();
    }

    // Simple content for rich text editor
    function getDefaultRichTemplate() {
        return `<h1>Document Title</h1>
<p>Start typing your content here...</p>
<h2>Section Heading</h2>
<p>Add your section content.</p>
<ul>
    <li>List item 1</li>
    <li>List item 2</li>
    <li>List item 3</li>
</ul>`;
    }

    // Full HTML template for code view
    function getDefaultTemplate() {
        return `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Title</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
        }
        h1 { color: #2c3e50; }
        h2 { color: #34495e; border-bottom: 2px solid #ecf0f1; padding-bottom: 10px; }
    </style>
</head>
<body>
    <h1>Document Title</h1>
    <p>Your content here...</p>
</body>
</html>`;
    }

    async function saveTemplate() {
        const name = document.getElementById('templateName').value.trim();
        const category = document.getElementById('templateCategory').value;
        const content = getEditorContent();
        const changeSummary = document.getElementById('changeSummary').value.trim();

        if (!name) {
            alert('Please enter a template name');
            return;
        }

        if (!content || content.trim() === '' || content.trim() === '<p></p>') {
            alert('Please enter template content');
            return;
        }

        if (editingTemplateId && !changeSummary) {
            alert('Please enter a change summary describing what you modified');
            return;
        }

        try {
            const url = editingTemplateId
                ? `${API_URL}/templates`
                : `${API_URL}/templates`;

            const method = editingTemplateId ? 'PUT' : 'POST';

            const body = {
                user_id: currentUser.id,
                name: name,
                category: category,
                content: content
            };

            if (editingTemplateId) {
                body.id = editingTemplateId;
                body.change_summary = changeSummary;
            }

            const response = await fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(body)
            });

            const result = await response.json();

            if (result.success) {
                closeTemplateEditor();
                loadTemplates();
                loadActivity();
            } else {
                alert('Failed to save template: ' + (result.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error saving template:', error);
            alert('Failed to save template');
        }
    }

    async function deleteTemplate(id) {
        if (!confirm('Are you sure you want to delete this template? All revision history will be lost.')) {
            return;
        }

        try {
            const response = await fetch(`${API_URL}/templates`, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id: id,
                    user_id: currentUser.id
                })
            });

            const result = await response.json();

            if (result.success) {
                loadTemplates();
                loadActivity();
            } else {
                alert('Failed to delete template: ' + (result.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error deleting template:', error);
            alert('Failed to delete template');
        }
    }

    async function showRevisions(templateId, templateName) {
        currentRevisionTemplateId = templateId;
        document.getElementById('revisionHistoryTitle').textContent = `Revision History: ${templateName}`;
        document.getElementById('revisionHistoryModal').style.display = 'flex';

        try {
            const response = await fetch(`${API_URL}/templates/revisions?template_id=${templateId}`);
            const revisions = await response.json();

            if (revisions.error) {
                document.getElementById('revisionList').innerHTML = `
                    <div style="text-align: center; padding: 40px; color: #e74c3c;">
                        ${revisions.error}
                    </div>
                `;
                return;
            }

            displayRevisions(revisions);
        } catch (error) {
            console.error('Error loading revisions:', error);
            document.getElementById('revisionList').innerHTML = `
                <div style="text-align: center; padding: 40px; color: #e74c3c;">
                    Failed to load revisions
                </div>
            `;
        }
    }

    function displayRevisions(revisions) {
        const container = document.getElementById('revisionList');

        if (!revisions || revisions.length === 0) {
            container.innerHTML = `
                <div style="text-align: center; padding: 40px; color: #666;">
                    No revision history available
                </div>
            `;
            return;
        }

        const maxRevision = Math.max(...revisions.map(r => r.revision_number));

        let html = `
            <div class="revision-header">
                <div>Version</div>
                <div>Summary</div>
                <div>Changed By</div>
                <div>Actions</div>
            </div>
        `;

        html += revisions.map(rev => {
            const isCurrent = rev.revision_number === maxRevision;
            return `
                <div class="revision-item">
                    <div class="revision-version ${isCurrent ? 'current' : ''}">
                        v${rev.revision_number}
                        ${isCurrent ? '<br><small>(current)</small>' : ''}
                    </div>
                    <div class="revision-summary">${escapeHtml(rev.change_summary) || 'No summary'}</div>
                    <div class="revision-meta">
                        <div>${escapeHtml(rev.changed_by_name) || 'Unknown'}</div>
                        <div>${formatDate(rev.created_at)}</div>
                    </div>
                    <div class="revision-actions">
                        <button onclick="viewRevision(${rev.id})" style="background: #3498db; color: white;">View</button>
                        ${!isCurrent ? `<button onclick="confirmRestore(${rev.id}, ${rev.revision_number})" style="background: #f39c12; color: white;">Restore</button>` : ''}
                    </div>
                </div>
            `;
        }).join('');

        container.innerHTML = html;
    }

    function formatDate(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function closeRevisionHistory() {
        document.getElementById('revisionHistoryModal').style.display = 'none';
        currentRevisionTemplateId = null;
    }

    async function viewRevision(revisionId) {
        selectedRevisionId = revisionId;

        try {
            const response = await fetch(`${API_URL}/templates/revision?revision_id=${revisionId}`);
            const revision = await response.json();

            if (revision.error) {
                alert('Failed to load revision: ' + revision.error);
                return;
            }

            document.getElementById('revisionViewTitle').textContent = `Version ${revision.revision_number} - ${revision.template_name}`;

            const iframe = document.getElementById('revisionPreview');
            const doc = iframe.contentDocument || iframe.contentWindow.document;
            doc.open();
            doc.write(revision.content);
            doc.close();

            document.getElementById('revisionViewModal').style.display = 'flex';
        } catch (error) {
            console.error('Error loading revision:', error);
            alert('Failed to load revision');
        }
    }

    function closeRevisionView() {
        document.getElementById('revisionViewModal').style.display = 'none';
        selectedRevisionId = null;
    }

    function confirmRestore(revisionId, versionNumber) {
        if (!confirm(`Are you sure you want to restore version ${versionNumber}? This will create a new revision with the restored content.`)) {
            return;
        }
        restoreRevisionById(revisionId);
    }

    async function restoreRevision() {
        if (!selectedRevisionId) return;
        restoreRevisionById(selectedRevisionId);
    }

    async function restoreRevisionById(revisionId) {
        try {
            const response = await fetch(`${API_URL}/templates/restore`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    template_id: currentRevisionTemplateId,
                    revision_id: revisionId,
                    user_id: currentUser.id
                })
            });

            const result = await response.json();

            if (result.success) {
                closeRevisionView();
                showRevisions(currentRevisionTemplateId, document.getElementById('revisionHistoryTitle').textContent.replace('Revision History: ', ''));
                loadTemplates();
                loadActivity();
            } else {
                alert('Failed to restore revision: ' + (result.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error restoring revision:', error);
            alert('Failed to restore revision');
        }
    }

    // Close modals when clicking outside
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('template-modal')) {
            event.target.style.display = 'none';
        }
    });

    // ==========================================
    // MEETINGS FUNCTIONALITY
    // ==========================================

    let currentMeeting = null;
    let meetingAttendees = [];
    let meetingItems = { agenda: [], discussion: [], decision: [] };
    let meetingActions = [];
    let teamMembers = [];

    // Load meetings when tab is opened
    async function loadMeetings() {
        const status = document.getElementById('meetingStatusFilter').value;
        const month = document.getElementById('meetingMonthFilter').value;

        let url = `${API_URL}/meetings?`;
        if (status && status !== 'all') url += `status=${status}&`;
        if (month) url += `month=${month}&`;

        try {
            const response = await fetch(url);
            const data = await response.json();

            if (data.success) {
                renderMeetingsList(data.meetings);
            }
        } catch (error) {
            console.error('Error loading meetings:', error);
        }
    }

    function renderMeetingsList(meetings) {
        const container = document.getElementById('meetingsList');

        if (!meetings || meetings.length === 0) {
            container.innerHTML = `
                <div class="empty-meetings">
                    <i class="fa-solid fa-calendar-days" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
                    <p>No meetings found</p>
                    <button class="btn-planner primary" onclick="createNewMeeting()" style="margin-top: 15px;">
                        Create Your First Meeting
                    </button>
                </div>
            `;
            return;
        }

        container.innerHTML = meetings.map(meeting => `
            <div class="meeting-card status-${meeting.status}" onclick="openMeeting(${meeting.id})">
                <div class="meeting-card-info">
                    <h4>${escapeHtml(meeting.title)}</h4>
                    <div class="meeting-card-meta">
                        <span><i class="fa-solid fa-calendar"></i> ${formatDate(meeting.meeting_date)}</span>
                        ${meeting.meeting_time ? `<span><i class="fa-solid fa-clock"></i> ${escapeHtml(meeting.meeting_time)}</span>` : ''}
                        ${meeting.location ? `<span><i class="fa-solid fa-location-dot"></i> ${escapeHtml(meeting.location)}</span>` : ''}
                        <span><i class="fa-solid fa-users"></i> ${meeting.attendee_count || 0} attendees</span>
                    </div>
                </div>
                <div class="meeting-card-status ${meeting.status}">${meeting.status.replace('_', ' ')}</div>
            </div>
        `).join('');
    }

    async function createNewMeeting() {
        // Reset state
        currentMeeting = null;
        meetingAttendees = [];
        meetingItems = { agenda: [], discussion: [], decision: [] };
        meetingActions = [];

        // Clear form
        document.getElementById('meetingId').value = '';
        document.getElementById('meetingTitle').value = '';
        document.getElementById('meetingDate').value = new Date().toISOString().split('T')[0];
        document.getElementById('meetingTime').value = '';
        document.getElementById('meetingLocation').value = '';
        document.getElementById('previousMeetingId').value = '';
        document.getElementById('nextMeetingDate').value = '';
        document.getElementById('nextMeetingTopics').value = '';
        document.getElementById('meetingNotes').value = '';

        // Update UI
        document.getElementById('meetingStatusBadge').textContent = 'Draft';
        document.getElementById('meetingStatusBadge').className = 'meeting-status-badge draft';
        document.getElementById('sendEmailBtn').style.display = 'none';

        // Load team members and previous meetings
        await Promise.all([loadTeamMembers(), loadPreviousMeetings()]);

        // Render empty lists
        renderAttendeesList();
        renderMeetingItemsList('agenda');
        renderMeetingItemsList('discussion');
        renderMeetingItemsList('decision');
        renderActionItemsList();
        populateAssigneeDropdown();

        // Show editor
        document.getElementById('meetingsListView').style.display = 'none';
        document.getElementById('meetingEditorView').style.display = 'block';
    }

    async function openMeeting(meetingId) {
        try {
            const response = await fetch(`${API_URL}/meetings/show?id=${meetingId}`);
            const data = await response.json();

            if (data.success) {
                currentMeeting = data.meeting;
                meetingAttendees = data.attendees || [];
                meetingItems = {
                    agenda: data.items.filter(i => i.item_type === 'agenda'),
                    discussion: data.items.filter(i => i.item_type === 'discussion'),
                    decision: data.items.filter(i => i.item_type === 'decision')
                };
                meetingActions = data.actions || [];

                // Fill form
                document.getElementById('meetingId').value = currentMeeting.id;
                document.getElementById('meetingTitle').value = currentMeeting.title;
                document.getElementById('meetingDate').value = currentMeeting.meeting_date;
                document.getElementById('meetingTime').value = currentMeeting.meeting_time || '';
                document.getElementById('meetingLocation').value = currentMeeting.location || '';
                document.getElementById('nextMeetingDate').value = currentMeeting.next_meeting_date || '';
                document.getElementById('nextMeetingTopics').value = currentMeeting.next_meeting_topics || '';
                document.getElementById('meetingNotes').value = currentMeeting.notes || '';

                // Update status badge
                document.getElementById('meetingStatusBadge').textContent = currentMeeting.status.replace('_', ' ');
                document.getElementById('meetingStatusBadge').className = `meeting-status-badge ${currentMeeting.status}`;

                // Show send button if email was generated
                document.getElementById('sendEmailBtn').style.display = currentMeeting.email_draft ? 'inline-flex' : 'none';

                // Load dropdowns
                await Promise.all([loadTeamMembers(), loadPreviousMeetings()]);
                document.getElementById('previousMeetingId').value = currentMeeting.previous_meeting_id || '';

                // Render lists
                renderAttendeesList();
                renderMeetingItemsList('agenda');
                renderMeetingItemsList('discussion');
                renderMeetingItemsList('decision');
                renderActionItemsList();
                populateAssigneeDropdown();

                // Show editor
                document.getElementById('meetingsListView').style.display = 'none';
                document.getElementById('meetingEditorView').style.display = 'block';
            }
        } catch (error) {
            console.error('Error loading meeting:', error);
            alert('Failed to load meeting');
        }
    }

    function backToMeetingsList() {
        document.getElementById('meetingEditorView').style.display = 'none';
        document.getElementById('meetingsListView').style.display = 'block';
        loadMeetings();
    }

    async function loadTeamMembers() {
        try {
            const response = await fetch(`${API_URL}/meetings/team-members`);
            const data = await response.json();

            if (data.success) {
                teamMembers = data.members;
                const select = document.getElementById('teamMemberSelect');
                select.innerHTML = '<option value="">-- Select Team Member --</option>' +
                    teamMembers.map(m => `<option value="${m.id}" data-email="${escapeHtml(m.email)}" data-name="${escapeHtml(m.name)}">${escapeHtml(m.name)} (${escapeHtml(m.email)})</option>`).join('');
            }
        } catch (error) {
            console.error('Error loading team members:', error);
        }
    }

    async function loadPreviousMeetings() {
        try {
            const response = await fetch(`${API_URL}/meetings/previous`);
            const data = await response.json();

            if (data.success) {
                const select = document.getElementById('previousMeetingId');
                const currentId = currentMeeting?.id;
                select.innerHTML = '<option value="">-- None --</option>' +
                    data.meetings
                        .filter(m => m.id !== currentId)
                        .map(m => `<option value="${m.id}">${m.title} (${formatDate(m.meeting_date)})</option>`)
                        .join('');
            }
        } catch (error) {
            console.error('Error loading previous meetings:', error);
        }
    }

    function addTeamMemberAttendee() {
        const select = document.getElementById('teamMemberSelect');
        const option = select.options[select.selectedIndex];

        if (!option.value) return;

        const userId = option.value;
        const email = option.dataset.email;
        const name = option.dataset.name;

        // Check if already added
        if (meetingAttendees.find(a => a.email === email)) {
            alert('This attendee is already added');
            return;
        }

        meetingAttendees.push({
            user_id: userId,
            email: email,
            name: name,
            attended: true
        });

        select.value = '';
        renderAttendeesList();
        populateAssigneeDropdown();
    }

    function addManualAttendee() {
        const nameInput = document.getElementById('manualAttendeeName');
        const emailInput = document.getElementById('manualAttendeeEmail');

        const name = nameInput.value.trim();
        const email = emailInput.value.trim();

        if (!name || !email) {
            alert('Please enter both name and email');
            return;
        }

        // Check if already added
        if (meetingAttendees.find(a => a.email === email)) {
            alert('This attendee is already added');
            return;
        }

        meetingAttendees.push({
            user_id: null,
            email: email,
            name: name,
            attended: true
        });

        nameInput.value = '';
        emailInput.value = '';
        renderAttendeesList();
        populateAssigneeDropdown();
    }

    function removeAttendee(index) {
        meetingAttendees.splice(index, 1);
        renderAttendeesList();
        populateAssigneeDropdown();
    }

    function toggleAttendance(index) {
        meetingAttendees[index].attended = !meetingAttendees[index].attended;
        renderAttendeesList();
    }

    function renderAttendeesList() {
        const container = document.getElementById('attendeesList');
        container.innerHTML = meetingAttendees.map((a, index) => `
            <div class="attendee-chip">
                <span>${escapeHtml(a.name)}</span>
                <span class="attended-toggle ${a.attended ? 'present' : 'absent'}" onclick="toggleAttendance(${index})">
                    ${a.attended ? 'Present' : 'Absent'}
                </span>
                <span class="remove-attendee" onclick="removeAttendee(${index})"><i class="fa-solid fa-times"></i></span>
            </div>
        `).join('');
    }

    function populateAssigneeDropdown() {
        const select = document.getElementById('newActionAssignee');
        select.innerHTML = '<option value="">Assign to...</option>' +
            meetingAttendees.map((a, index) => `<option value="${index}">${escapeHtml(a.name)}</option>`).join('');
    }

    async function addMeetingItem(type) {
        const inputId = type === 'agenda' ? 'newAgendaItem' :
                       type === 'discussion' ? 'newDiscussionItem' : 'newDecisionItem';
        const input = document.getElementById(inputId);
        const content = input.value.trim();

        if (!content) return;

        if (currentMeeting?.id) {
            // Save to API
            try {
                const response = await fetch(`${API_URL}/meetings/item`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ meeting_id: currentMeeting.id, item_type: type, content: content })
                });

                const data = await response.json();
                if (data.success) {
                    meetingItems[type].push({ id: data.id, item_type: type, content: content });
                }
            } catch (error) {
                console.error('Error adding item:', error);
            }
        } else {
            // Add locally for new meeting
            meetingItems[type].push({
                id: null,
                item_type: type,
                content: content,
                sort_order: meetingItems[type].length
            });
        }

        input.value = '';
        renderMeetingItemsList(type);
    }

    async function deleteMeetingItem(type, index) {
        const item = meetingItems[type][index];

        if (item.id && currentMeeting?.id) {
            try {
                await fetch(`${API_URL}/meetings/item`, {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: item.id })
                });
            } catch (error) {
                console.error('Error deleting item:', error);
            }
        }

        meetingItems[type].splice(index, 1);
        renderMeetingItemsList(type);
    }

    function renderMeetingItemsList(type) {
        const listId = type === 'agenda' ? 'agendaItemsList' :
                      type === 'discussion' ? 'discussionItemsList' : 'decisionItemsList';
        const container = document.getElementById(listId);

        container.innerHTML = meetingItems[type].map((item, index) => `
            <div class="meeting-item">
                <span class="item-content">${escapeHtml(item.content)}</span>
                <span class="delete-item" onclick="deleteMeetingItem('${type}', ${index})">
                    <i class="fa-solid fa-trash"></i>
                </span>
            </div>
        `).join('');
    }

    async function addMeetingAction() {
        const descInput = document.getElementById('newActionDescription');
        const assigneeSelect = document.getElementById('newActionAssignee');
        const dueDateInput = document.getElementById('newActionDueDate');

        const description = descInput.value.trim();
        if (!description) return;

        const assigneeIndex = assigneeSelect.value;
        const assignee = assigneeIndex !== '' ? meetingAttendees[assigneeIndex] : null;
        const dueDate = dueDateInput.value;

        const actionData = {
            description: description,
            assigned_to: assignee?.user_id || null,
            assigned_name: assignee?.name || null,
            due_date: dueDate || null,
            status: 'pending'
        };

        if (currentMeeting?.id) {
            try {
                const response = await fetch(`${API_URL}/meetings/action`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ ...actionData, meeting_id: currentMeeting.id })
                });

                const data = await response.json();
                if (data.success) {
                    meetingActions.push({ ...actionData, id: data.id });
                }
            } catch (error) {
                console.error('Error adding action:', error);
            }
        } else {
            meetingActions.push({ ...actionData, id: null });
        }

        descInput.value = '';
        assigneeSelect.value = '';
        dueDateInput.value = '';
        renderActionItemsList();
    }

    async function toggleActionStatus(index) {
        const action = meetingActions[index];
        const newStatus = action.status === 'completed' ? 'pending' : 'completed';

        if (action.id && currentMeeting?.id) {
            try {
                const response = await fetch(`${API_URL}/meetings/action`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: action.id, status: newStatus })
                });

                const data = await response.json();
                if (data.success) {
                    meetingActions[index].status = newStatus;
                }
            } catch (error) {
                console.error('Error updating action:', error);
            }
        } else {
            meetingActions[index].status = newStatus;
        }

        renderActionItemsList();
    }

    async function deleteMeetingAction(index) {
        const action = meetingActions[index];

        if (action.id && currentMeeting?.id) {
            try {
                await fetch(`${API_URL}/meetings/action`, {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: action.id })
                });
            } catch (error) {
                console.error('Error deleting action:', error);
            }
        }

        meetingActions.splice(index, 1);
        renderActionItemsList();
    }

    function renderActionItemsList() {
        const container = document.getElementById('actionItemsList');

        container.innerHTML = meetingActions.map((action, index) => `
            <div class="action-item ${action.status === 'completed' ? 'completed' : ''}">
                <input type="checkbox" class="action-checkbox"
                       ${action.status === 'completed' ? 'checked' : ''}
                       onclick="toggleActionStatus(${index})">
                <div class="action-details">
                    <div class="action-description">${escapeHtml(action.description)}</div>
                    <div class="action-meta">
                        ${action.assigned_name ? `<span><i class="fa-solid fa-user"></i> ${escapeHtml(action.assigned_name)}</span>` : ''}
                        ${action.due_date ? `<span><i class="fa-solid fa-calendar"></i> Due: ${formatDate(action.due_date)}</span>` : ''}
                    </div>
                </div>
                <span class="delete-action" onclick="deleteMeetingAction(${index})">
                    <i class="fa-solid fa-trash"></i>
                </span>
            </div>
        `).join('');
    }

    async function saveMeeting() {
        const title = document.getElementById('meetingTitle').value.trim();
        const meetingDate = document.getElementById('meetingDate').value;

        if (!title || !meetingDate) {
            alert('Please enter a meeting title and date');
            return;
        }

        const meetingData = {
            title: title,
            meeting_date: meetingDate,
            meeting_time: document.getElementById('meetingTime').value || null,
            location: document.getElementById('meetingLocation').value || null,
            previous_meeting_id: document.getElementById('previousMeetingId').value || null,
            next_meeting_date: document.getElementById('nextMeetingDate').value || null,
            next_meeting_topics: document.getElementById('nextMeetingTopics').value || null,
            notes: document.getElementById('meetingNotes').value || null,
            attendees: meetingAttendees,
            items: [...meetingItems.agenda, ...meetingItems.discussion, ...meetingItems.decision],
            actions: meetingActions
        };

        try {
            const isUpdate = !!currentMeeting?.id;
            const url = `${API_URL}/meetings`;
            const method = isUpdate ? 'PUT' : 'POST';

            const response = await fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(isUpdate ? { ...meetingData, id: currentMeeting.id } : meetingData)
            });

            const data = await response.json();

            if (data.success) {
                if (!currentMeeting?.id) {
                    currentMeeting = data.meeting;
                    document.getElementById('meetingId').value = currentMeeting.id;

                    // Reload the full meeting data to get all IDs
                    await openMeeting(currentMeeting.id);
                }

                // Show success feedback
                const badge = document.getElementById('meetingStatusBadge');
                badge.textContent = 'Saved!';
                setTimeout(() => {
                    badge.textContent = currentMeeting.status.replace('_', ' ');
                    badge.className = `meeting-status-badge ${currentMeeting.status}`;
                }, 1500);
            } else {
                alert('Failed to save meeting: ' + (data.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error saving meeting:', error);
            alert('Failed to save meeting');
        }
    }

    async function generateMeetingEmail() {
        // Save first
        await saveMeeting();

        if (!currentMeeting?.id) {
            alert('Please save the meeting first');
            return;
        }

        try {
            const response = await fetch(`${API_URL}/meetings/generate-email?id=${currentMeeting.id}`, {
                method: 'GET'
            });

            const data = await response.json();

            if (data.success) {
                // Update meeting with email data
                currentMeeting.email_subject = data.subject;
                currentMeeting.email_draft = data.email_html;

                // Show send button
                document.getElementById('sendEmailBtn').style.display = 'inline-flex';

                // Open email modal
                openSendEmailModal();
            } else {
                alert('Failed to generate email: ' + (data.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error generating email:', error);
            alert('Failed to generate email');
        }
    }

    function openSendEmailModal() {
        if (!currentMeeting?.email_draft) {
            alert('Please generate the email first');
            return;
        }

        // Set subject
        document.getElementById('emailSubject').value = currentMeeting.email_subject || `Meeting Minutes - ${currentMeeting.title}`;

        // Show recipients
        const recipientsList = document.getElementById('emailRecipientsList');
        recipientsList.innerHTML = meetingAttendees.map((a, index) => `
            <label class="email-recipient-chip">
                <input type="checkbox" checked data-email="${escapeHtml(a.email)}" data-name="${escapeHtml(a.name)}">
                <span>${escapeHtml(a.name)} &lt;${escapeHtml(a.email)}&gt;</span>
            </label>
        `).join('');

        // Show email preview in sandboxed iframe to prevent CSS leaking
        const iframe = document.getElementById('emailPreviewFrame');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        iframeDoc.open();
        iframeDoc.write(currentMeeting.email_draft);
        iframeDoc.close();

        // Show modal
        document.getElementById('meetingEmailModal').style.display = 'flex';
    }

    function closeMeetingEmailModal() {
        document.getElementById('meetingEmailModal').style.display = 'none';
    }

    async function sendMeetingEmail() {
        const subject = document.getElementById('emailSubject').value.trim();
        if (!subject) {
            alert('Please enter an email subject');
            return;
        }

        // Get selected recipients
        const checkboxes = document.querySelectorAll('#emailRecipientsList input[type="checkbox"]:checked');
        const recipients = Array.from(checkboxes).map(cb => ({
            email: cb.dataset.email,
            name: cb.dataset.name
        }));

        if (recipients.length === 0) {
            alert('Please select at least one recipient');
            return;
        }

        try {
            const response = await fetch(`${API_URL}/meetings/send-email`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id: currentMeeting.id,
                    subject: subject,
                    recipients: recipients
                })
            });

            const data = await response.json();

            if (data.success) {
                closeMeetingEmailModal();
                currentMeeting.status = 'sent';
                document.getElementById('meetingStatusBadge').textContent = 'sent';
                document.getElementById('meetingStatusBadge').className = 'meeting-status-badge sent';
                alert('Email sent successfully to ' + recipients.length + ' recipient(s)!');
            } else {
                alert('Failed to send email: ' + (data.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error sending email:', error);
            alert('Failed to send email');
        }
    }

    function formatDate(dateStr) {
        if (!dateStr) return '';
        const date = new Date(dateStr + 'T00:00:00');
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Initialize meetings when tab becomes active
    const originalSwitchTab = switchTab;
    switchTab = function(tabName) {
        originalSwitchTab(tabName);
        if (tabName === 'meetings') {
            loadMeetings();
        }
    };
</script>

<!-- Document Preview Modal -->
<div id="docModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.9);">
    <div style="position: relative; margin: 2% auto; width: 90%; height: 90%; background: white; border-radius: 8px; overflow: hidden;">
        <div style="background: #2c3e50; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center;">
            <h3 id="modalTitle" style="margin: 0;">Document Preview</h3>
            <button onclick="closeModal()" style="color: white; font-size: 28px; font-weight: bold; cursor: pointer; background: none; border: none;">&times;</button>
        </div>
        <div id="modalBody" style="width: 100%; height: calc(100% - 60px); overflow: auto;"></div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>

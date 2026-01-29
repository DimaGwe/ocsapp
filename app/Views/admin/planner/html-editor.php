<?php
/**
 * HTML Editor - Admin Planner Sub-Tab
 * File: app/Views/admin/planner/html-editor.php
 */

$currentPage = 'html-editor';
$currentLang = $_SESSION['language'] ?? 'fr';

ob_start();
?>

<style>
    /* Override admin layout styles for full-screen editor */
    .page-content {
        padding: 0 !important;
        display: flex;
        flex-direction: column;
        height: calc(100vh - 64px);
        overflow: hidden;
    }

    /* HTML Editor Styles */
    .html-editor-container {
        display: flex;
        flex: 1;
        min-height: 0;
        background: #fafaf9;
    }

    .editor-toolbar {
        position: sticky;
        top: 0;
        background: #292524;
        color: white;
        padding: 12px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        z-index: 100;
    }

    .editor-toolbar-title {
        font-family: 'Poppins', serif;
        font-size: 16px;
        font-weight: 600;
        letter-spacing: -0.5px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .editor-toolbar-title i {
        color: var(--primary);
    }

    .editor-toolbar-actions {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .editor-btn {
        padding: 8px 16px;
        border: none;
        border-radius: 6px;
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .editor-btn-primary {
        background: var(--primary);
        color: white;
    }

    .editor-btn-primary:hover {
        background: var(--primary-600);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 178, 7, 0.3);
    }

    .editor-btn-secondary {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .editor-btn-secondary:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .edit-mode-indicator {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        background: rgba(34, 197, 94, 0.15);
        border: 1px solid rgba(34, 197, 94, 0.3);
        border-radius: 20px;
        font-size: 12px;
        color: #86efac;
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    .status-dot {
        width: 6px;
        height: 6px;
        background: #22c55e;
        border-radius: 50%;
        animation: blink 1.5s ease-in-out infinite;
    }

    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.3; }
    }

    .editor-sidebar {
        width: 280px;
        background: #f5f5f4;
        border-right: 1px solid #e5e5e5;
        padding: 20px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
    }

    .sidebar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e5e5e5;
    }

    .sidebar-header h3 {
        font-family: 'Poppins', serif;
        font-size: 16px;
        font-weight: 600;
        margin: 0;
    }

    .sidebar-files {
        flex: 1;
        overflow-y: auto;
    }

    .file-item {
        padding: 12px 15px;
        border-radius: 8px;
        margin-bottom: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid transparent;
        background: white;
    }

    .file-item:hover {
        background: rgba(255, 255, 255, 0.9);
        border-color: #e5e5e5;
    }

    .file-item.active {
        background: white;
        border-color: var(--primary);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .file-name {
        font-weight: 500;
        font-size: 13px;
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .file-meta {
        display: flex;
        justify-content: space-between;
        font-size: 11px;
        color: #6b7280;
    }

    .file-actions {
        display: flex;
        gap: 8px;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .file-item:hover .file-actions {
        opacity: 1;
    }

    .file-action-btn {
        background: none;
        border: none;
        color: #6b7280;
        cursor: pointer;
        font-size: 14px;
        padding: 2px;
    }

    .file-action-btn:hover {
        color: var(--primary);
    }

    .editor-content-area {
        flex: 1;
        padding: 40px;
        background: white;
        overflow-y: auto;
        min-width: 0;
    }

    .editable-content {
        position: relative;
        width: 100%;
        max-width: 900px;
        margin: 0 auto;
    }

    /* Make text elements editable on hover */
    .editable-content [contenteditable="true"] {
        position: relative;
        padding: 4px;
        margin: -4px;
        border-radius: 4px;
        transition: all 0.2s ease;
        cursor: text;
        outline: none;
    }

    .editable-content [contenteditable="true"]:hover {
        background: #fef3c7;
        box-shadow: 0 0 0 2px #fed7aa;
    }

    .editable-content [contenteditable="true"]:focus {
        background: white;
        box-shadow: 0 0 0 3px var(--primary);
        cursor: text;
    }

    /* Document preview styles */
    .document-preview {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
        color: #333;
    }

    .document-preview h1, .document-preview h2, .document-preview h3, .document-preview h4 {
        color: var(--primary);
        margin-top: 24px;
        margin-bottom: 12px;
    }

    .document-preview h2 {
        font-size: 20px;
        padding-bottom: 8px;
        border-bottom: 2px solid #e5e5e5;
    }

    .document-preview p {
        margin-bottom: 12px;
    }

    .document-preview ul, .document-preview ol {
        margin-left: 24px;
        margin-bottom: 16px;
    }

    .document-preview li {
        margin-bottom: 6px;
    }

    /* Modal styles */
    .editor-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        z-index: 200;
        align-items: center;
        justify-content: center;
    }

    .editor-modal.active {
        display: flex;
    }

    .modal-content {
        background: white;
        padding: 32px;
        border-radius: 12px;
        max-width: 700px;
        width: 90%;
        max-height: 80vh;
        overflow-y: auto;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    }

    .modal-header {
        font-family: 'Poppins', serif;
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 20px;
        color: var(--dark);
    }

    .code-output {
        background: #1e1e1e;
        color: #d4d4d4;
        padding: 20px;
        border-radius: 8px;
        font-family: 'Courier New', monospace;
        font-size: 12px;
        line-height: 1.5;
        overflow-x: auto;
        white-space: pre-wrap;
        word-wrap: break-word;
        margin-bottom: 20px;
        max-height: 400px;
    }

    .modal-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    textarea.html-input {
        width: 100%;
        min-height: 200px;
        padding: 15px;
        border: 1px solid #e5e5e5;
        border-radius: 6px;
        font-family: 'Courier New', monospace;
        font-size: 12px;
        margin-top: 15px;
        resize: vertical;
    }

    .saved-indicator {
        display: none;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        background: rgba(34, 197, 94, 0.15);
        border: 1px solid rgba(34, 197, 94, 0.3);
        border-radius: 20px;
        font-size: 12px;
        color: #22c55e;
    }

    .saved-indicator.show {
        display: flex;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
    }

    .empty-sidebar {
        text-align: center;
        padding: 30px 20px;
        color: #6b7280;
        font-size: 13px;
    }

    .empty-sidebar i {
        font-size: 48px;
        margin-bottom: 15px;
        display: block;
        color: #cbd5e1;
    }

    .welcome-screen {
        text-align: center;
        padding: 80px 40px;
        color: #666;
    }

    .welcome-screen h2 {
        font-family: 'Poppins', serif;
        font-size: 28px;
        color: #1c1917;
        margin-bottom: 16px;
    }

    .welcome-screen p {
        font-size: 15px;
        margin-bottom: 24px;
        color: #6b7280;
    }

    @media (max-width: 768px) {
        .editor-sidebar {
            width: 200px;
        }

        .editor-content-area {
            padding: 24px;
        }
    }

    @media (max-width: 576px) {
        .editor-sidebar {
            display: none;
        }
    }
</style>

<!-- Editor Toolbar -->
<div class="editor-toolbar">
    <div class="editor-toolbar-title">
        <i class="fa-solid fa-code"></i>
        HTML Text Editor
    </div>
    <div class="editor-toolbar-actions">
        <div class="saved-indicator" id="savedIndicator">
            <i class="fa-solid fa-check"></i> Saved
        </div>
        <div class="edit-mode-indicator">
            <span class="status-dot"></span>
            Edit Mode Active
        </div>
        <button class="editor-btn editor-btn-secondary" onclick="openImportModal()">
            <i class="fa-solid fa-upload"></i> Load HTML
        </button>
        <button class="editor-btn editor-btn-secondary" onclick="saveDocument()">
            <i class="fa-solid fa-floppy-disk"></i> Save
        </button>
        <button class="editor-btn editor-btn-secondary" onclick="exportHTML()">
            <i class="fa-solid fa-file-export"></i> Export
        </button>
        <button class="editor-btn editor-btn-primary" onclick="downloadHTML()">
            <i class="fa-solid fa-download"></i> Download
        </button>
    </div>
</div>

<!-- Main Editor Container -->
<div class="html-editor-container">
    <!-- Sidebar -->
    <div class="editor-sidebar">
        <div class="sidebar-header">
            <h3>Saved Documents</h3>
            <button class="editor-btn editor-btn-secondary" style="padding: 4px 10px; font-size: 11px;" onclick="refreshSidebar()">
                <i class="fa-solid fa-refresh"></i>
            </button>
        </div>
        <div class="sidebar-files" id="sidebarFiles">
            <!-- Files will be loaded here dynamically -->
        </div>
    </div>

    <!-- Content Area -->
    <div class="editor-content-area">
        <div class="editable-content document-preview" id="editableContent">
            <div class="welcome-screen">
                <i class="fa-solid fa-file-code" style="font-size: 64px; color: var(--primary); margin-bottom: 20px;"></i>
                <h2>Welcome to HTML Editor</h2>
                <p>Create and edit HTML documents for your team. Click "Load HTML" to get started or paste your HTML content.</p>
                <button class="editor-btn editor-btn-primary" onclick="openImportModal()" style="font-size: 14px; padding: 12px 28px;">
                    <i class="fa-solid fa-upload"></i> Load HTML Document
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="editor-modal" id="importModal">
    <div class="modal-content">
        <div class="modal-header">Load HTML Document</div>

        <div style="margin-bottom: 20px;">
            <h4 style="margin-bottom: 10px; font-size: 14px; color: var(--dark); font-weight: 600;">
                <i class="fa-solid fa-file-arrow-up"></i> Option 1: Upload File
            </h4>
            <div style="display: flex; gap: 12px; align-items: center;">
                <input type="file" id="fileInput" accept=".html,.htm" style="flex: 1; padding: 10px; border: 1px solid #e5e5e5; border-radius: 6px; font-size: 13px;">
                <button class="editor-btn editor-btn-primary" onclick="loadHTMLFile()">Upload</button>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <h4 style="margin-bottom: 10px; font-size: 14px; color: var(--dark); font-weight: 600;">
                <i class="fa-solid fa-paste"></i> Option 2: Paste HTML Code
            </h4>
            <textarea id="htmlInput" placeholder="Paste your HTML code here..." class="html-input"></textarea>
        </div>

        <div style="margin-bottom: 20px; padding: 15px; background: #f0fdf4; border-radius: 6px; border-left: 4px solid var(--primary);">
            <strong style="color: var(--primary); display: block; margin-bottom: 5px;">
                <i class="fa-solid fa-clock-rotate-left"></i> Option 3: Load Last Saved
            </strong>
            <p style="color: #166534; font-size: 13px; margin-bottom: 10px;">Restore your previously saved work from browser storage.</p>
            <button class="editor-btn editor-btn-secondary" onclick="loadSavedDocument()" style="background: var(--primary); color: white; border: none;">
                <i class="fa-solid fa-rotate-left"></i> Load Saved Document
            </button>
        </div>

        <div class="modal-actions">
            <button class="editor-btn editor-btn-secondary" onclick="closeImportModal()" style="background: #f3f4f6; color: #374151; border: 1px solid #d1d5db;">Cancel</button>
            <button class="editor-btn editor-btn-primary" onclick="loadFromTextarea()">
                <i class="fa-solid fa-check"></i> Load from Paste
            </button>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="editor-modal" id="exportModal">
    <div class="modal-content">
        <div class="modal-header">Exported HTML</div>
        <div class="code-output" id="codeOutput"></div>
        <div class="modal-actions">
            <button class="editor-btn editor-btn-secondary" onclick="closeModal()" style="background: #f3f4f6; color: #374151; border: 1px solid #d1d5db;">Close</button>
            <button class="editor-btn editor-btn-primary" onclick="copyCode()">
                <i class="fa-solid fa-copy"></i> Copy to Clipboard
            </button>
        </div>
    </div>
</div>

<script>
    // Initialize sidebar when page loads
    document.addEventListener('DOMContentLoaded', function() {
        refreshSidebar();
    });

    // Save document to localStorage with metadata
    function saveDocument() {
        const content = document.getElementById('editableContent').innerHTML;
        const title = extractTitle(content) || 'Untitled Document';
        const timestamp = new Date().toISOString();

        // Create unique key for this document
        const docKey = 'htmlDoc_' + Date.now();

        // Save document content
        localStorage.setItem(docKey, content);

        // Save document metadata
        const metadata = {
            title: title,
            timestamp: timestamp,
            key: docKey
        };

        const metadataKey = 'docMeta_' + docKey;
        localStorage.setItem(metadataKey, JSON.stringify(metadata));

        // Show saved indicator
        const indicator = document.getElementById('savedIndicator');
        indicator.classList.add('show');
        setTimeout(() => {
            indicator.classList.remove('show');
        }, 2000);

        // Refresh sidebar to show new document
        refreshSidebar();
    }

    // Extract title from HTML content
    function extractTitle(html) {
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        const h1 = tempDiv.querySelector('h1');
        const h2 = tempDiv.querySelector('h2');
        const title = tempDiv.querySelector('title');
        return title?.textContent || h1?.textContent || h2?.textContent || null;
    }

    // Load saved document from localStorage
    function loadSavedDocument() {
        const saved = localStorage.getItem('htmlEditorContent');
        if (saved) {
            document.getElementById('editableContent').innerHTML = saved;
            closeImportModal();
            showIndicator('Loaded');
        } else {
            alert('No saved document found. Make edits and click Save to store your work.');
        }
    }

    // Show indicator with custom text
    function showIndicator(text) {
        const indicator = document.getElementById('savedIndicator');
        indicator.innerHTML = '<i class="fa-solid fa-check"></i> ' + text;
        indicator.classList.add('show');
        setTimeout(() => {
            indicator.classList.remove('show');
            indicator.innerHTML = '<i class="fa-solid fa-check"></i> Saved';
        }, 2000);
    }

    // Open import modal
    function openImportModal() {
        document.getElementById('importModal').classList.add('active');
    }

    // Close import modal
    function closeImportModal() {
        document.getElementById('importModal').classList.remove('active');
        document.getElementById('htmlInput').value = '';
        document.getElementById('fileInput').value = '';
    }

    // Load HTML from textarea
    function loadFromTextarea() {
        const htmlCode = document.getElementById('htmlInput').value.trim();
        if (!htmlCode) {
            alert('Please paste some HTML code first.');
            return;
        }

        loadHTMLContent(htmlCode);
        closeImportModal();
    }

    // Load HTML from file upload
    function loadHTMLFile() {
        const fileInput = document.getElementById('fileInput');
        const file = fileInput.files[0];
        if (!file) {
            alert('Please select a file first.');
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const htmlContent = e.target.result;
            loadHTMLContent(htmlContent);
            closeImportModal();
        };
        reader.readAsText(file);
    }

    // Process and load HTML content
    function loadHTMLContent(htmlString) {
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = htmlString;

        // Extract and preserve all styles
        const styles = [];
        const styleElements = tempDiv.querySelectorAll('style');
        styleElements.forEach(styleEl => {
            styles.push(styleEl.textContent);
            styleEl.remove();
        });

        // Extract body content if present
        let content = tempDiv.innerHTML;
        const bodyMatch = htmlString.match(/<body[^>]*>([\s\S]*)<\/body>/i);
        if (bodyMatch) {
            content = bodyMatch[1];
        }

        // Reconstruct with preserved styles
        const finalContent = `
            ${content}
            <style id="injectedStyles">${styles.join('\n')}</style>
        `;

        // Set the content
        document.getElementById('editableContent').innerHTML = finalContent;

        // Make text elements editable
        makeEditable(document.getElementById('editableContent'));

        // Clear the textarea
        const htmlInput = document.getElementById('htmlInput');
        if (htmlInput) {
            htmlInput.value = '';
        }
    }

    // Recursively make text elements editable
    function makeEditable(element) {
        const textElements = [
            'P', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6',
            'LI', 'SPAN', 'DIV', 'TD', 'TH', 'STRONG',
            'EM', 'B', 'I', 'A', 'LABEL'
        ];

        Array.from(element.children).forEach(child => {
            if (textElements.includes(child.tagName) && child.textContent.trim()) {
                const hasOnlyInlineChildren = Array.from(child.children).every(c =>
                    ['STRONG', 'EM', 'B', 'I', 'SPAN', 'BR', 'SMALL', 'CODE', 'A'].includes(c.tagName)
                );

                if (hasOnlyInlineChildren || child.children.length === 0) {
                    child.setAttribute('contenteditable', 'true');
                }
            }

            makeEditable(child);
        });
    }

    // Export HTML function
    function exportHTML() {
        const content = document.getElementById('editableContent');
        const clone = content.cloneNode(true);

        // Remove contenteditable attributes
        const editables = clone.querySelectorAll('[contenteditable]');
        editables.forEach(el => el.removeAttribute('contenteditable'));

        // Get all injected styles
        const injectedStyles = document.getElementById('injectedStyles');
        const styles = injectedStyles ? injectedStyles.textContent : '';

        const fullHTML = `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
${styles}
    </style>
</head>
<body>
${clone.innerHTML.replace(/<style[^>]*id="injectedStyles"[^>]*>.*?<\/style>/gi, '')}
</body>
</html>`;

        document.getElementById('codeOutput').textContent = fullHTML;
        document.getElementById('exportModal').classList.add('active');
    }

    function closeModal() {
        document.getElementById('exportModal').classList.remove('active');
    }

    function copyCode() {
        const code = document.getElementById('codeOutput').textContent;
        navigator.clipboard.writeText(code).then(() => {
            alert('HTML copied to clipboard!');
        });
    }

    function downloadHTML() {
        const content = document.getElementById('editableContent');
        const clone = content.cloneNode(true);

        const editables = clone.querySelectorAll('[contenteditable]');
        editables.forEach(el => el.removeAttribute('contenteditable'));

        const injectedStyles = document.getElementById('injectedStyles');
        const styles = injectedStyles ? injectedStyles.textContent : '';

        const fullHTML = `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
${styles}
    </style>
</head>
<body>
${clone.innerHTML.replace(/<style[^>]*id="injectedStyles"[^>]*>.*?<\/style>/gi, '')}
</body>
</html>`;

        const blob = new Blob([fullHTML], { type: 'text/html' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'edited-document.html';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }

    // Sidebar functions
    function refreshSidebar() {
        const sidebarFiles = document.getElementById('sidebarFiles');
        sidebarFiles.innerHTML = '';

        const docKeys = Object.keys(localStorage).filter(key => key.startsWith('docMeta_'));

        if (docKeys.length === 0) {
            sidebarFiles.innerHTML = `
                <div class="empty-sidebar">
                    <i class="fa-regular fa-folder-open"></i>
                    <p>No saved documents yet</p>
                    <p style="font-size: 11px; margin-top: 10px;">Create and save documents to see them here</p>
                </div>
            `;
            return;
        }

        const docs = [];
        docKeys.forEach(key => {
            try {
                const meta = JSON.parse(localStorage.getItem(key));
                docs.push(meta);
            } catch (e) {
                console.error('Error parsing document metadata:', key);
            }
        });

        docs.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));

        docs.forEach(doc => {
            const date = new Date(doc.timestamp);
            const formattedDate = date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});

            const fileItem = document.createElement('div');
            fileItem.className = 'file-item';
            fileItem.dataset.key = doc.key;
            fileItem.innerHTML = `
                <div class="file-name">${doc.title}</div>
                <div class="file-meta">
                    <span class="file-date">${formattedDate}</span>
                    <div class="file-actions">
                        <button class="file-action-btn" title="Load document" onclick="loadDocument('${doc.key}')">
                            <i class="fa-solid fa-folder-open"></i>
                        </button>
                        <button class="file-action-btn" title="Delete document" onclick="deleteDocument('${doc.key}', event)">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;

            sidebarFiles.appendChild(fileItem);
        });
    }

    function loadDocument(key) {
        const content = localStorage.getItem(key);
        if (content) {
            document.getElementById('editableContent').innerHTML = content;

            document.querySelectorAll('.file-item').forEach(item => {
                item.classList.remove('active');
            });
            const activeItem = document.querySelector(`.file-item[data-key="${key}"]`);
            if (activeItem) activeItem.classList.add('active');

            makeEditable(document.getElementById('editableContent'));
            showIndicator('Loaded');
        } else {
            alert('Document not found.');
        }
    }

    function deleteDocument(key, event) {
        event.stopPropagation();

        if (confirm('Are you sure you want to delete this document?')) {
            localStorage.removeItem(key);
            localStorage.removeItem('docMeta_' + key);
            refreshSidebar();
            showIndicator('Deleted');
        }
    }
</script>

<?php
$content = ob_get_clean();

// Include admin layout
include __DIR__ . '/../layout.php';
?>

/**
 * Real-time collaboration module for notes
 */
class NoteCollaboration {
    constructor() {
        this.socket = null;
        this.isConnected = false;
        this.noteId = null;
        this.userId = null;
        this.username = null;
        this.participants = [];
        this.lastCursorUpdate = Date.now();
        this.collaborationPanel = null;
        this.collaborationIndicators = {};
        
        // Bind methods to this
        this.connect = this.connect.bind(this);
        this.disconnect = this.disconnect.bind(this);
        this.handleMessage = this.handleMessage.bind(this);
        this.sendUpdate = this.sendUpdate.bind(this);
        this.sendCursorPosition = this.sendCursorPosition.bind(this);
        this.handleContentChange = this.handleContentChange.bind(this);
        this.handleCursorMovement = this.handleCursorMovement.bind(this);
        this.renderParticipants = this.renderParticipants.bind(this);
        this.showCollaborationUI = this.showCollaborationUI.bind(this);
    }
    
    /**
     * Initialize collaboration for a note
     * @param {string} noteId The note ID
     * @param {string} userId The user ID
     * @param {string} username The username
     */
    init(noteId, userId, username) {
        this.noteId = noteId;
        this.userId = userId;
        this.username = username;
        
        // Create collaboration UI
        this.createCollaborationUI();
        
        // Connect to the WebSocket server
        this.connect();
        
        // Set up event listeners for real-time updates
        $('#title').on('input', this.handleContentChange);
        $('#textareaElem').on('input', this.handleContentChange);
        
        // Set up event listeners for cursor movement
        $('#textareaElem').on('click keyup', this.handleCursorMovement);
        
        console.log(`Collaboration initialized for note ${noteId}`);
    }
    
    /**
     * Create UI elements for collaboration
     */
    createCollaborationUI() {
        // Create collaboration panel
        this.collaborationPanel = $(`
            <div id="collaborationPanel" class="border rounded p-2 mb-3 bg-light">
                <h5><i class="fas fa-users"></i> Collaborating with:</h5>
                <div id="participantsList"></div>
            </div>
        `);
        
        // Initially hide the panel until users join
        this.collaborationPanel.hide();
        
        // Add panel to the page
        $('#statusDiv').after(this.collaborationPanel);
        
        // Create container for cursor indicators
        $('body').append('<div id="cursorIndicators"></div>');
    }
    
    /**
     * Connect to the WebSocket server
     */
    connect() {
        try {
            // Get current hostname
            const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
            const host = window.location.hostname;
            const port = 8081;
            const wsUrl = `${protocol}//${host}:${port}`;
            
            console.log(`Connecting to WebSocket server at ${wsUrl}`);
            
            // Create WebSocket connection
            this.socket = new WebSocket(wsUrl);
            
            // Set up event handlers
            this.socket.onopen = () => {
                console.log('Connected to collaboration server');
                this.isConnected = true;
                
                // Join the note room
                this.socket.send(JSON.stringify({
                    action: 'join',
                    noteId: this.noteId,
                    userId: this.userId,
                    username: this.username
                }));
            };
            
            this.socket.onmessage = (event) => {
                const data = JSON.parse(event.data);
                this.handleMessage(data);
            };
            
            this.socket.onclose = () => {
                console.log('Disconnected from collaboration server');
                this.isConnected = false;
                
                // Hide collaboration UI when disconnected
                this.collaborationPanel.hide();
                $('#cursorIndicators').empty();
                
                // Try to reconnect after a delay
                setTimeout(this.connect, 5000);
            };
            
            this.socket.onerror = (error) => {
                console.error('WebSocket error:', error);
                this.showConnectionError();
            };
        } catch (err) {
            console.error('Failed to connect to WebSocket server:', err);
            this.showConnectionError();
        }
    }
    
    /**
     * Show connection error message
     */
    showConnectionError() {
        const errorMsg = `
            <div class="alert alert-warning" role="alert">
                <strong>Collaboration unavailable:</strong> Could not connect to the WebSocket server.
                <br>
                Make sure the server is running and port 8081 is accessible.
            </div>
        `;
        $('#statusDiv').after(errorMsg);
    }
    
    /**
     * Disconnect from the WebSocket server
     */
    disconnect() {
        if (this.socket && this.isConnected) {
            this.socket.close();
        }
    }
    
    /**
     * Handle incoming WebSocket messages
     * @param {Object} data The message data
     */
    handleMessage(data) {
        if (!data || !data.action) {
            return;
        }
        
        console.log('Received:', data.action);
        
        switch (data.action) {
            case 'joined':
                this.participants = data.participants || [];
                this.renderParticipants();
                this.showCollaborationUI();
                break;
                
            case 'user_joined':
                // Add new participant
                this.participants.push({
                    userId: data.userId,
                    username: data.username
                });
                this.renderParticipants();
                this.showCollaborationUI();
                
                // Show notification
                this.showNotification(`${data.username} joined the note`);
                break;
                
            case 'user_left':
                // Remove participant
                this.participants = this.participants.filter(p => p.username !== data.username);
                this.renderParticipants();
                
                // Remove their cursor indicator
                this.removeCursorIndicator(data.username);
                
                // Hide panel if no participants
                if (this.participants.length === 0) {
                    this.collaborationPanel.hide();
                }
                
                // Show notification
                this.showNotification(`${data.username} left the note`);
                break;
                
            case 'update':
                // Apply content updates only if we're not currently editing
                if (!$('#title').is(':focus') && !$('#textareaElem').is(':focus')) {
                    $('#title').val(data.title);
                    $('#textareaElem').val(data.content);
                } else {
                    // Show notification about updates
                    this.showNotification(`${data.username} updated the note`);
                }
                break;
                
            case 'cursor':
                // Update cursor position indicator for this user
                this.updateCursorIndicator(data.username, data.position);
                break;
                
            case 'error':
                console.error('Collaboration error:', data.message);
                alert('Collaboration error: ' + data.message);
                break;
        }
    }
    
    /**
     * Send note content updates to the server
     */
    sendUpdate() {
        if (!this.isConnected) return;
        
        this.socket.send(JSON.stringify({
            action: 'update',
            noteId: this.noteId,
            title: $('#title').val(),
            content: $('#textareaElem').val()
        }));
    }
    
    /**
     * Send cursor position updates to the server
     */
    sendCursorPosition() {
        if (!this.isConnected) return;
        
        // Don't send updates too frequently
        const now = Date.now();
        if (now - this.lastCursorUpdate < 100) return;
        this.lastCursorUpdate = now;
        
        const textarea = $('#textareaElem')[0];
        
        this.socket.send(JSON.stringify({
            action: 'cursor',
            noteId: this.noteId,
            position: {
                start: textarea.selectionStart,
                end: textarea.selectionEnd
            }
        }));
    }
    
    /**
     * Handle content changes in title or textarea
     */
    handleContentChange() {
        // Send update with debounce (don't send too many updates)
        if (this.updateTimeout) {
            clearTimeout(this.updateTimeout);
        }
        
        this.updateTimeout = setTimeout(() => {
            this.sendUpdate();
        }, 500);
    }
    
    /**
     * Handle cursor movement in the textarea
     */
    handleCursorMovement() {
        this.sendCursorPosition();
    }
    
    /**
     * Update cursor indicator for a specific user
     * @param {string} username The username
     * @param {Object} position The cursor position
     */
    updateCursorIndicator(username, position) {
        const textarea = $('#textareaElem')[0];
        
        // Create or get existing indicator
        if (!this.collaborationIndicators[username]) {
            const color = this.getRandomColor();
            
            this.collaborationIndicators[username] = {
                element: $(`<div class="cursor-indicator" style="background-color: ${color};">
                             <div class="cursor-flag">${username}</div>
                           </div>`),
                color: color
            };
            
            $('#cursorIndicators').append(this.collaborationIndicators[username].element);
        }
        
        // Position the indicator
        const indicator = this.collaborationIndicators[username];
        
        // Calculate cursor position in the textarea
        const text = textarea.value.substring(0, position.start);
        const lines = text.split('\n');
        const lineCount = lines.length;
        const charPos = lines[lineCount - 1].length;
        
        // Get line height and character width
        const style = window.getComputedStyle(textarea);
        const lineHeight = parseInt(style.lineHeight);
        const charWidth = 8; // Approximate character width
        
        // Calculate position
        const top = (lineCount - 1) * lineHeight + textarea.offsetTop;
        const left = charPos * charWidth + textarea.offsetLeft;
        
        // Update indicator position
        indicator.element.css({
            top: top + 'px',
            left: left + 'px'
        });
    }
    
    /**
     * Remove cursor indicator for a user
     * @param {string} username The username
     */
    removeCursorIndicator(username) {
        if (this.collaborationIndicators[username]) {
            this.collaborationIndicators[username].element.remove();
            delete this.collaborationIndicators[username];
        }
    }
    
    /**
     * Render participants list in the collaboration panel
     */
    renderParticipants() {
        const list = $('#participantsList');
        list.empty();
        
        if (this.participants.length === 0) {
            list.html('<p class="text-muted">No one else is editing</p>');
            return;
        }
        
        const ul = $('<ul class="list-group list-group-flush"></ul>');
        
        this.participants.forEach(user => {
            const color = this.collaborationIndicators[user.username] 
                ? this.collaborationIndicators[user.username].color 
                : this.getRandomColor();
                
            ul.append(`
                <li class="list-group-item p-1">
                    <span class="badge badge-pill" style="background-color: ${color};">&nbsp;</span>
                    ${user.username}
                </li>
            `);
        });
        
        list.append(ul);
    }
    
    /**
     * Show collaboration UI when users are collaborating
     */
    showCollaborationUI() {
        if (this.participants.length > 0) {
            this.collaborationPanel.show();
        }
    }
    
    /**
     * Show a temporary notification
     * @param {string} message The notification message
     */
    showNotification(message) {
        const notification = $(`
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `);
        
        // Add to page
        $('#statusDiv').after(notification);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            notification.alert('close');
        }, 5000);
    }
    
    /**
     * Generate a random color for a user
     * @return {string} A color in hex format
     */
    getRandomColor() {
        const colors = [
            '#FF5733', '#33FF57', '#3357FF', '#FF33F5',
            '#F5FF33', '#33FFF5', '#FF5733', '#5733FF'
        ];
        
        return colors[Math.floor(Math.random() * colors.length)];
    }
}

// Create and export singleton instance
const collaboration = new NoteCollaboration();
export default collaboration; 
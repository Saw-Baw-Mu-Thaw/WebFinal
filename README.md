# Note Taking Web Application

A web-based note-taking application with sharing and real-time collaboration features.

## Important things to note

1. SQL file is in sql folder
2. If you use bcdn extension to generate html template, replace jquery script tag. jquery slim doesn't have ajax method.
3. If you change html, js code but it doesn't change on screen, clear your browser cache.
4. You need a environment variable called "Environment" with value "Testing" or else skeletondb will use production connection string.

## Real-Time Collaboration

This application supports real-time collaboration on shared notes. When a note is shared with edit permissions, multiple users can work on it simultaneously.

### Setup

To enable real-time collaboration:

1. Install the required dependencies using Composer:
   ```
   composer install
   ```

2. Start the WebSocket server:
   ```
   php ws-server.php
   ```
   
   Or on Windows, you can use the provided batch file:
   ```
   start-collaboration-server.bat
   ```

3. The WebSocket server will run on port 8080 by default.

### How It Works

- Users with edit permissions on a shared note can see each other's changes in real-time
- Cursor positions are tracked and displayed to show where each collaborator is working
- Real-time notifications inform users when someone joins or leaves the note
- All changes are automatically saved to the server

### Troubleshooting

If you encounter issues with the collaboration feature:

1. **Server not starting**: Make sure you've installed dependencies properly:
   ```
   composer install
   ```
   
   Then check the console output when starting the server. You should see:
   ```
   WebSocket server started on port 8080
   Collaboration server started.
   ```

2. **Connection errors in browser**: 
   - Open your browser's developer console (F12) to see error messages
   - Check if the WebSocket server is running
   - Ensure port 8080 is not blocked by any firewall
   - Try using `localhost` instead of an IP address or hostname

3. **Permission issues**:
   - Make sure the user has EDITOR permission for the note
   - Check if the note owner has shared the note correctly

4. **Other WebSocket issues**:
   - If running behind a proxy, ensure WebSocket connections are supported and configured
   - Some anti-virus or security software might block WebSocket connections
   - Try using a different browser to isolate the issue

'use strict';

/**
 * Offline Notes Module
 * Handles offline note management and synchronization
 */
const OfflineNotesManager = (function() {
  // Private variables
  let isOnline = navigator.onLine;
  let syncInProgress = false;
  
  // Track the connection status
  window.addEventListener('online', () => {
    console.log('App is online');
    isOnline = true;
    showOnlineStatus(true);
    syncWithServer();
  });
  
  window.addEventListener('offline', () => {
    console.log('App is offline');
    isOnline = false;
    showOnlineStatus(false);
  });
  
  // Show online/offline status
  function showOnlineStatus(online) {
    const statusEl = document.getElementById('offline-status');
    if (!statusEl) {
      // Create status element if it doesn't exist
      const statusDiv = document.createElement('div');
      statusDiv.id = 'offline-status';
      statusDiv.className = online ? 'online-status' : 'offline-status';
      statusDiv.textContent = online ? 'Online' : 'Offline - Changes will sync when online';
      
      // Add styles
      statusDiv.style.position = 'fixed';
      statusDiv.style.bottom = '10px';
      statusDiv.style.right = '10px';
      statusDiv.style.padding = '8px 16px';
      statusDiv.style.borderRadius = '4px';
      statusDiv.style.color = 'white';
      statusDiv.style.zIndex = 1000;
      statusDiv.style.fontSize = '14px';
      statusDiv.style.backgroundColor = online ? 'rgba(40, 167, 69, 0.8)' : 'rgba(220, 53, 69, 0.8)';
      statusDiv.style.transition = 'background-color 0.3s ease';
      
      document.body.appendChild(statusDiv);
    } else {
      // Update existing element
      statusEl.className = online ? 'online-status' : 'offline-status';
      statusEl.textContent = online ? 'Online' : 'Offline - Changes will sync when online';
      statusEl.style.backgroundColor = online ? 'rgba(40, 167, 69, 0.8)' : 'rgba(220, 53, 69, 0.8)';
      
      // Auto-hide online status after 3 seconds
      if (online) {
        setTimeout(() => {
          statusEl.style.opacity = '0';
          statusEl.style.transition = 'opacity 0.5s ease';
          setTimeout(() => {
            statusEl.style.display = 'none';
          }, 500);
        }, 3000);
      } else {
        statusEl.style.opacity = '1';
        statusEl.style.display = 'block';
      }
    }
  }
  
  // Save notes to IndexedDB
  async function saveNotesToIndexedDB(notes) {
    try {
      // Clear existing notes
      await idbHelper.clear('notes');
      
      // Add each note with a synced status
      for (const note of notes) {
        note.syncStatus = 'synced';
        note.modifiedAt = new Date().toISOString();
        await idbHelper.put('notes', note);
      }
      console.log('Notes saved to IndexedDB');
    } catch (error) {
      console.error('Error saving notes to IndexedDB:', error);
    }
  }
  
  // Get notes from IndexedDB
  async function getNotesFromIndexedDB() {
    try {
      const notes = await idbHelper.getAll('notes');
      return notes;
    } catch (error) {
      console.error('Error getting notes from IndexedDB:', error);
      return [];
    }
  }
  
  // Save a single note to IndexedDB
  async function saveNoteToIndexedDB(note, syncStatus = 'pending') {
    try {
      // Mark the note's sync status
      note.syncStatus = syncStatus;
      note.modifiedAt = new Date().toISOString();
      await idbHelper.put('notes', note);
      
      // If we're offline, add a pending action
      if (!isOnline && syncStatus === 'pending') {
        await addPendingAction({
          type: 'update',
          noteId: note.id,
          timestamp: Date.now()
        });
      }
      console.log('Note saved to IndexedDB');
      return true;
    } catch (error) {
      console.error('Error saving note to IndexedDB:', error);
      return false;
    }
  }
  
  // Add a pending action to be processed when back online
  async function addPendingAction(action) {
    try {
      await idbHelper.put('pendingActions', action);
      console.log('Pending action added:', action);
      return true;
    } catch (error) {
      console.error('Error adding pending action:', error);
      return false;
    }
  }
  
  // Get pending actions
  async function getPendingActions() {
    try {
      const actions = await idbHelper.getAll('pendingActions');
      return actions;
    } catch (error) {
      console.error('Error getting pending actions:', error);
      return [];
    }
  }
  
  // Clear a pending action
  async function clearPendingAction(timestamp) {
    try {
      await idbHelper.delete('pendingActions', timestamp);
      return true;
    } catch (error) {
      console.error('Error clearing pending action:', error);
      return false;
    }
  }
  
  // Sync pending changes with the server
  async function syncWithServer() {
    if (!isOnline || syncInProgress) return;
    
    try {
      syncInProgress = true;
      console.log('Starting sync with server...');
      
      // Get pending actions
      const pendingActions = await getPendingActions();
      if (pendingActions.length === 0) {
        console.log('No pending actions to sync');
        syncInProgress = false;
        return;
      }
      
      console.log(`Found ${pendingActions.length} pending actions to sync`);
      
      // Process each action in order
      for (const action of pendingActions) {
        let success = false;
        
        if (action.type === 'update') {
          // Get the note from IndexedDB
          const note = await idbHelper.get('notes', action.noteId);
          if (!note) {
            console.warn(`Note ${action.noteId} not found in IndexedDB`);
            await clearPendingAction(action.timestamp);
            continue;
          }
          
          // Update on server
          success = await updateNoteOnServer(note);
        } else if (action.type === 'delete') {
          success = await deleteNoteOnServer(action.noteId);
        } else if (action.type === 'create') {
          const note = await idbHelper.get('notes', action.noteId);
          if (!note) {
            console.warn(`New note ${action.noteId} not found in IndexedDB`);
            await clearPendingAction(action.timestamp);
            continue;
          }
          
          success = await createNoteOnServer(note);
        }
        
        if (success) {
          await clearPendingAction(action.timestamp);
        } else {
          console.warn(`Failed to sync action: ${action.type} for note ${action.noteId}`);
        }
      }
      
      console.log('Sync completed successfully');
      
      // Refresh the notes list after sync
      if (window.location.pathname.includes('index.php')) {
        location.reload();
      }
    } catch (error) {
      console.error('Error during sync:', error);
    } finally {
      syncInProgress = false;
    }
  }
  
  // Update a note on the server
  async function updateNoteOnServer(note) {
    try {
      // Use the existing API endpoint
      const response = await fetch('api/update_note.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          noteId: note.id,
          title: note.Title,
          content: note.Content
        })
      });
      
      const result = await response.json();
      
      if (result.code === 0) {
        // Update the note status in IndexedDB
        note.syncStatus = 'synced';
        await idbHelper.put('notes', note);
        return true;
      } else {
        console.error('Server error:', result.message);
        return false;
      }
    } catch (error) {
      console.error('Error updating note on server:', error);
      return false;
    }
  }
  
  // Delete a note on the server
  async function deleteNoteOnServer(noteId) {
    try {
      const response = await fetch(`api/delete_note.php?noteId=${noteId}`, {
        method: 'DELETE'
      });
      
      const result = await response.json();
      
      if (result.code === 0) {
        // Remove from IndexedDB
        await idbHelper.delete('notes', noteId);
        return true;
      } else {
        console.error('Server error:', result.message);
        return false;
      }
    } catch (error) {
      console.error('Error deleting note on server:', error);
      return false;
    }
  }
  
  // Create a note on the server
  async function createNoteOnServer(note) {
    try {
      const response = await fetch('api/create_note.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          title: note.Title,
          content: note.Content
        })
      });
      
      const result = await response.json();
      
      if (result.code === 0) {
        // Update the note with the server ID
        note.id = result.noteId;
        note.syncStatus = 'synced';
        await idbHelper.put('notes', note);
        return true;
      } else {
        console.error('Server error:', result.message);
        return false;
      }
    } catch (error) {
      console.error('Error creating note on server:', error);
      return false;
    }
  }
  
  // Create a note locally while offline
  async function createNoteOffline(title, content) {
    try {
      // Generate a temporary ID (negative to avoid conflicts with server IDs)
      const tempId = -Date.now();
      
      const note = {
        id: tempId,
        Title: title || 'New Offline Note',
        Content: content || '',
        CreatedAt: new Date().toISOString(),
        syncStatus: 'pending'
      };
      
      await idbHelper.put('notes', note);
      
      // Add pending action
      await addPendingAction({
        type: 'create',
        noteId: tempId,
        timestamp: Date.now()
      });
      
      return note;
    } catch (error) {
      console.error('Error creating offline note:', error);
      return null;
    }
  }

  // Public API
  return {
    init: async function() {
      // Initialize
      console.log('Initializing OfflineNotesManager');
      await idbHelper.init();
      
      // Show current connection status
      showOnlineStatus(isOnline);
      
      // Register sync when coming back online
      navigator.serviceWorker.ready
        .then(registration => {
          console.log('Service Worker is ready, registering sync');
          return registration.sync.register('sync-notes');
        })
        .catch(err => console.log('Error registering sync:', err));
      
      // Try to sync on init if online
      if (isOnline) {
        setTimeout(() => {
          syncWithServer();
        }, 2000);
      }
      
      // Set up PWA install prompt
      setupInstallPrompt();
    },
    
    getNotesFromIndexedDB,
    saveNotesToIndexedDB,
    saveNoteToIndexedDB,
    syncWithServer,
    isOnline: () => isOnline,
    createNoteOffline
  };
})();

/**
 * Set up the PWA install prompt
 */
function setupInstallPrompt() {
  let deferredPrompt;
  const installKey = 'pwa-install-prompted';
  
  // Check if already prompted in the last 14 days
  const lastPrompt = localStorage.getItem(installKey);
  if (lastPrompt) {
    const daysSincePrompt = (Date.now() - parseInt(lastPrompt)) / (1000 * 60 * 60 * 24);
    if (daysSincePrompt < 14) {
      console.log('PWA install prompt was shown recently, skipping');
      return;
    }
  }
  
  // Listen for beforeinstallprompt event
  window.addEventListener('beforeinstallprompt', (e) => {
    // Prevent Chrome 76+ from automatically showing the prompt
    e.preventDefault();
    // Stash the event so it can be triggered later
    deferredPrompt = e;
    
    // Show the install banner after 3 seconds
    setTimeout(() => {
      showInstallBanner(deferredPrompt);
    }, 3000);
  });
}

/**
 * Show the install banner
 */
function showInstallBanner(deferredPrompt) {
  // Create banner if it doesn't exist
  if (!document.getElementById('pwa-install-banner')) {
    const banner = document.createElement('div');
    banner.id = 'pwa-install-banner';
    banner.className = 'pwa-install-prompt';
    banner.innerHTML = `
      <div class="mr-3">
        <i class="fas fa-download mr-2"></i>
        <strong>Install this app</strong>
      </div>
      <p class="m-0 text-muted flex-grow-1">Add to your home screen for offline use</p>
      <button class="btn btn-primary btn-sm mr-2" id="pwa-install-btn">Install</button>
      <button class="btn btn-light btn-sm" id="pwa-install-dismiss">Not now</button>
    `;
    document.body.appendChild(banner);
    
    // Set up event listeners
    document.getElementById('pwa-install-btn').addEventListener('click', () => {
      // Hide the banner
      banner.style.display = 'none';
      
      // Show the installation prompt
      deferredPrompt.prompt();
      
      // Wait for the user to respond to the prompt
      deferredPrompt.userChoice.then((choiceResult) => {
        if (choiceResult.outcome === 'accepted') {
          console.log('User accepted the install prompt');
        } else {
          console.log('User dismissed the install prompt');
        }
        // We no longer need the prompt - clear it
        deferredPrompt = null;
        
        // Store that we've prompted the user
        localStorage.setItem('pwa-install-prompted', Date.now());
      });
    });
    
    document.getElementById('pwa-install-dismiss').addEventListener('click', () => {
      // Hide the banner
      banner.style.display = 'none';
      
      // Store that we've prompted the user
      localStorage.setItem('pwa-install-prompted', Date.now());
    });
  }
}

// Initialize when the DOM is ready
document.addEventListener('DOMContentLoaded', function() {
  // Check for service worker support and register it
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js')
      .then(reg => {
        console.log('Service Worker registered with scope:', reg.scope);
        // Initialize the offline notes manager
        OfflineNotesManager.init();
      })
      .catch(error => {
        console.error('Service Worker registration failed:', error);
      });
  }
}); 
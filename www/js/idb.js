'use strict';

// IndexedDB wrapper to simplify database operations
// Based on Jake Archibald's idb library but simplified for this project

(function() {
  // Check for IndexedDB support
  if (!('indexedDB' in window)) {
    console.log('This browser doesn\'t support IndexedDB');
    return;
  }

  const dbPromise = {
    // Database connection
    _db: null,

    // Initialize the database
    async init() {
      if (this._db) {
        return this._db;
      }

      try {
        this._db = await new Promise((resolve, reject) => {
          const openRequest = indexedDB.open('notes-app-db', 1);
          
          openRequest.onupgradeneeded = event => {
            const db = event.target.result;
            
            // Create notes store
            if (!db.objectStoreNames.contains('notes')) {
              const notesStore = db.createObjectStore('notes', { keyPath: 'id' });
              notesStore.createIndex('modifiedAt', 'modifiedAt', { unique: false });
              notesStore.createIndex('syncStatus', 'syncStatus', { unique: false });
            }
            
            // Create pending actions store for offline changes
            if (!db.objectStoreNames.contains('pendingActions')) {
              const actionsStore = db.createObjectStore('pendingActions', { 
                keyPath: 'timestamp' 
              });
              actionsStore.createIndex('type', 'type', { unique: false });
            }
          };
          
          openRequest.onsuccess = () => resolve(openRequest.result);
          openRequest.onerror = () => reject(openRequest.error);
        });
        
        return this._db;
      } catch (error) {
        console.error('Error initializing database:', error);
        throw error;
      }
    },

    // Get a transaction on the specified store
    async getTransaction(storeName, mode = 'readonly') {
      const db = await this.init();
      return db.transaction(storeName, mode);
    },

    // Get a specific store
    async getStore(storeName, mode = 'readonly') {
      const tx = await this.getTransaction(storeName, mode);
      return tx.objectStore(storeName);
    },

    // Get all items from a store
    async getAll(storeName) {
      const store = await this.getStore(storeName);
      return new Promise((resolve, reject) => {
        const request = store.getAll();
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
      });
    },

    // Get a specific item by key
    async get(storeName, key) {
      const store = await this.getStore(storeName);
      return new Promise((resolve, reject) => {
        const request = store.get(key);
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
      });
    },

    // Add or update an item
    async put(storeName, item) {
      const store = await this.getStore(storeName, 'readwrite');
      return new Promise((resolve, reject) => {
        const request = store.put(item);
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
      });
    },

    // Delete an item
    async delete(storeName, key) {
      const store = await this.getStore(storeName, 'readwrite');
      return new Promise((resolve, reject) => {
        const request = store.delete(key);
        request.onsuccess = () => resolve();
        request.onerror = () => reject(request.error);
      });
    },

    // Clear a store
    async clear(storeName) {
      const store = await this.getStore(storeName, 'readwrite');
      return new Promise((resolve, reject) => {
        const request = store.clear();
        request.onsuccess = () => resolve();
        request.onerror = () => reject(request.error);
      });
    }
  };

  // Expose to global scope
  window.idbHelper = dbPromise;
})(); 
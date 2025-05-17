<?php
// PWA Health Check
$pageTitle = "PWA Health Check";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="Description" content="PWA Health Check for Notes App" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#007bff">
    <title><?php echo $pageTitle; ?></title>
    <style>
        .check-item {
            margin-bottom: 1rem;
            padding: 1rem;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }
        .check-title {
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .success {
            color: #28a745;
        }
        .warning {
            color: #ffc107;
        }
        .error {
            color: #dc3545;
        }
        #offline-test-btn {
            margin-top: 1rem;
        }
    </style>
</head>

<body class="mode-target">
    <div class="container mt-4">
        <h1><?php echo $pageTitle; ?></h1>
        <p class="lead">This page tests various aspects of the Progressive Web App functionality</p>

        <div class="card mt-4">
            <div class="card-header">
                <h2 class="h5 mb-0">PWA Requirements</h2>
            </div>
            <div class="card-body">
                <div id="serviceWorkerCheck" class="check-item">
                    <div class="check-title">Service Worker</div>
                    <div class="check-result">Checking...</div>
                </div>
                
                <div id="manifestCheck" class="check-item">
                    <div class="check-title">Web App Manifest</div>
                    <div class="check-result">Checking...</div>
                </div>
                
                <div id="httpsCheck" class="check-item">
                    <div class="check-title">HTTPS</div>
                    <div class="check-result">Checking...</div>
                </div>
                
                <div id="cacheCheck" class="check-item">
                    <div class="check-title">Cache Storage</div>
                    <div class="check-result">Checking...</div>
                </div>
                
                <div id="indexedDBCheck" class="check-item">
                    <div class="check-title">IndexedDB</div>
                    <div class="check-result">Checking...</div>
                </div>
                
                <div id="installCheck" class="check-item">
                    <div class="check-title">Installability</div>
                    <div class="check-result">Checking...</div>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h2 class="h5 mb-0">Offline Functionality</h2>
            </div>
            <div class="card-body">
                <div id="offlineCheck" class="check-item">
                    <div class="check-title">Offline Support</div>
                    <div class="check-result">
                        <p>To test offline support:</p>
                        <ol>
                            <li>Click the "Simulate Offline" button below</li>
                            <li>Try to navigate to the home page</li>
                            <li>The app should load with cached content</li>
                        </ol>
                        <button id="offline-test-btn" class="btn btn-primary">Simulate Offline</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h2 class="h5 mb-0">Debugging Information</h2>
            </div>
            <div class="card-body">
                <div id="debugInfo" class="check-item">
                    <pre id="debugText" class="mb-0"></pre>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const debugText = document.getElementById('debugText');
        
        // Check Service Worker
        const serviceWorkerCheck = document.querySelector('#serviceWorkerCheck .check-result');
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.getRegistration()
                .then(registration => {
                    if (registration) {
                        serviceWorkerCheck.innerHTML = `<i class="fas fa-check success"></i> Service Worker registered (scope: ${registration.scope})`;
                        debugText.textContent += `Service Worker registered with scope: ${registration.scope}\n`;
                    } else {
                        serviceWorkerCheck.innerHTML = `<i class="fas fa-exclamation-triangle warning"></i> Service Worker not registered`;
                        debugText.textContent += 'Service Worker not registered\n';
                    }
                })
                .catch(error => {
                    serviceWorkerCheck.innerHTML = `<i class="fas fa-times error"></i> Service Worker error: ${error.message}`;
                    debugText.textContent += `Service Worker error: ${error.message}\n`;
                });
        } else {
            serviceWorkerCheck.innerHTML = `<i class="fas fa-times error"></i> Service Worker not supported`;
            debugText.textContent += 'Service Worker not supported\n';
        }
        
        // Check Web App Manifest
        const manifestCheck = document.querySelector('#manifestCheck .check-result');
        const manifestLink = document.querySelector('link[rel="manifest"]');
        if (manifestLink) {
            fetch(manifestLink.href)
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    }
                    throw new Error('Manifest could not be loaded');
                })
                .then(manifest => {
                    let missingFields = [];
                    const requiredFields = ['name', 'short_name', 'icons', 'start_url', 'display'];
                    
                    requiredFields.forEach(field => {
                        if (!manifest[field]) {
                            missingFields.push(field);
                        }
                    });
                    
                    if (missingFields.length === 0) {
                        manifestCheck.innerHTML = `<i class="fas fa-check success"></i> Manifest found and valid`;
                        debugText.textContent += `Manifest found at ${manifestLink.href}\n`;
                    } else {
                        manifestCheck.innerHTML = `<i class="fas fa-exclamation-triangle warning"></i> Manifest missing fields: ${missingFields.join(', ')}`;
                        debugText.textContent += `Manifest missing fields: ${missingFields.join(', ')}\n`;
                    }
                })
                .catch(error => {
                    manifestCheck.innerHTML = `<i class="fas fa-times error"></i> Manifest error: ${error.message}`;
                    debugText.textContent += `Manifest error: ${error.message}\n`;
                });
        } else {
            manifestCheck.innerHTML = `<i class="fas fa-times error"></i> No manifest link found`;
            debugText.textContent += 'No manifest link found\n';
        }
        
        // Check HTTPS
        const httpsCheck = document.querySelector('#httpsCheck .check-result');
        if (window.location.protocol === 'https:') {
            httpsCheck.innerHTML = `<i class="fas fa-check success"></i> Site is served over HTTPS`;
            debugText.textContent += 'Site is served over HTTPS\n';
        } else {
            if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                httpsCheck.innerHTML = `<i class="fas fa-check success"></i> Local development (${window.location.hostname}) - HTTPS not required`;
                debugText.textContent += `Local development (${window.location.hostname}) - HTTPS not required\n`;
            } else {
                httpsCheck.innerHTML = `<i class="fas fa-times error"></i> Site is not served over HTTPS`;
                debugText.textContent += 'Site is not served over HTTPS\n';
            }
        }
        
        // Check Cache Storage
        const cacheCheck = document.querySelector('#cacheCheck .check-result');
        if ('caches' in window) {
            caches.keys()
                .then(cacheNames => {
                    if (cacheNames.length > 0) {
                        cacheCheck.innerHTML = `<i class="fas fa-check success"></i> Caches found: ${cacheNames.join(', ')}`;
                        debugText.textContent += `Caches found: ${cacheNames.join(', ')}\n`;
                    } else {
                        cacheCheck.innerHTML = `<i class="fas fa-exclamation-triangle warning"></i> Cache API supported but no caches found`;
                        debugText.textContent += 'Cache API supported but no caches found\n';
                    }
                })
                .catch(error => {
                    cacheCheck.innerHTML = `<i class="fas fa-times error"></i> Cache error: ${error.message}`;
                    debugText.textContent += `Cache error: ${error.message}\n`;
                });
        } else {
            cacheCheck.innerHTML = `<i class="fas fa-times error"></i> Cache API not supported`;
            debugText.textContent += 'Cache API not supported\n';
        }
        
        // Check IndexedDB
        const indexedDBCheck = document.querySelector('#indexedDBCheck .check-result');
        if ('indexedDB' in window) {
            try {
                const request = indexedDB.open('notes-app-db');
                request.onsuccess = function(event) {
                    const db = event.target.result;
                    const dbStores = Array.from(db.objectStoreNames);
                    
                    if (dbStores.length > 0) {
                        indexedDBCheck.innerHTML = `<i class="fas fa-check success"></i> IndexedDB available with stores: ${dbStores.join(', ')}`;
                        debugText.textContent += `IndexedDB available with stores: ${dbStores.join(', ')}\n`;
                    } else {
                        indexedDBCheck.innerHTML = `<i class="fas fa-exclamation-triangle warning"></i> IndexedDB available but no object stores found`;
                        debugText.textContent += 'IndexedDB available but no object stores found\n';
                    }
                    db.close();
                };
                
                request.onerror = function(event) {
                    indexedDBCheck.innerHTML = `<i class="fas fa-times error"></i> IndexedDB error: ${event.target.error}`;
                    debugText.textContent += `IndexedDB error: ${event.target.error}\n`;
                };
            } catch (error) {
                indexedDBCheck.innerHTML = `<i class="fas fa-times error"></i> IndexedDB error: ${error.message}`;
                debugText.textContent += `IndexedDB error: ${error.message}\n`;
            }
        } else {
            indexedDBCheck.innerHTML = `<i class="fas fa-times error"></i> IndexedDB not supported`;
            debugText.textContent += 'IndexedDB not supported\n';
        }
        
        // Check Installability
        const installCheck = document.querySelector('#installCheck .check-result');
        if (window.matchMedia('(display-mode: standalone)').matches) {
            installCheck.innerHTML = `<i class="fas fa-check success"></i> App is already installed and running in standalone mode`;
            debugText.textContent += 'App is running in standalone mode\n';
        } else {
            installCheck.innerHTML = `<i class="fas fa-info-circle"></i> App can be installed if all other requirements are met`;
            debugText.textContent += 'App can be installed if requirements are met\n';
            
            // Listen for beforeinstallprompt event
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                installCheck.innerHTML = `<i class="fas fa-check success"></i> App is installable <button id="install-btn" class="btn btn-sm btn-primary ml-2">Install Now</button>`;
                debugText.textContent += 'App is installable (beforeinstallprompt fired)\n';
                
                // Set up install button
                document.getElementById('install-btn').addEventListener('click', () => {
                    e.prompt();
                    e.userChoice.then((choiceResult) => {
                        if (choiceResult.outcome === 'accepted') {
                            installCheck.innerHTML = `<i class="fas fa-check success"></i> App installed successfully`;
                            debugText.textContent += 'App installed successfully\n';
                        } else {
                            installCheck.innerHTML = `<i class="fas fa-exclamation-triangle warning"></i> App installation declined by user`;
                            debugText.textContent += 'App installation declined by user\n';
                        }
                    });
                });
            });
        }
        
        // Offline test button
        document.getElementById('offline-test-btn').addEventListener('click', function() {
            const offlineMode = window.localStorage.getItem('offlineMode') === 'true';
            
            if (offlineMode) {
                // Turn off offline mode
                window.localStorage.setItem('offlineMode', 'false');
                this.textContent = 'Simulate Offline';
                this.classList.remove('btn-success');
                this.classList.add('btn-primary');
                alert('Offline simulation disabled. The app will use the network normally.');
            } else {
                // Turn on offline mode
                window.localStorage.setItem('offlineMode', 'true');
                this.textContent = 'Disable Offline Simulation';
                this.classList.remove('btn-primary');
                this.classList.add('btn-success');
                alert('Offline simulation enabled. The app will now behave as if you are offline.');
            }
        });
        
        // Check if offline mode is active on page load
        if (window.localStorage.getItem('offlineMode') === 'true') {
            const offlineBtn = document.getElementById('offline-test-btn');
            offlineBtn.textContent = 'Disable Offline Simulation';
            offlineBtn.classList.remove('btn-primary');
            offlineBtn.classList.add('btn-success');
        }
    });
    </script>
    
    <script>
    // Update service worker to handle the offline simulation
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => {
                console.log('Service Worker registered with scope:', registration.scope);
                
                // Override fetch to simulate offline mode
                const originalFetch = window.fetch;
                window.fetch = function() {
                    if (window.localStorage.getItem('offlineMode') === 'true') {
                        return Promise.reject(new Error('Offline mode simulated'));
                    }
                    return originalFetch.apply(this, arguments);
                };
            })
            .catch(error => {
                console.error('Service Worker registration failed:', error);
            });
    }
    </script>
</body>
</html> 
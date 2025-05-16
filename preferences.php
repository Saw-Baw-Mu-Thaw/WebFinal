<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Make sure we have the userId for preferences
require_once 'api/user_helper.php';
ensure_user_id();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Preferences - Note Taking App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Minimalist preferences UI */
        :root {
            --pref-primary: #4361ee;
            --pref-secondary: #3f37c9;
            --pref-accent: #4cc9f0;
            --pref-light: #f8f9fa;
            --pref-dark: #212529;
            --pref-border: #dee2e6;
            --pref-shadow: rgba(0, 0, 0, 0.05);
            --pref-radius: 0.5rem;
        }
        
        .mode-target.bg-dark {
            --pref-primary: #4cc9f0;
            --pref-secondary: #4895ef;
            --pref-accent: #4361ee;
            --pref-light: #121212;
            --pref-dark: #f8f9fa;
            --pref-border: #2d3748;
            --pref-shadow: rgba(0, 0, 0, 0.2);
        }
        
        .preferences-wrapper {
            min-height: 100vh;
            background-color: var(--pref-light);
            color: var(--pref-dark);
            display: flex;
            flex-direction: column;
        }
        
        .preferences-header {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--pref-border);
        }
        
        .preferences-title {
            font-size: 1.25rem;
            font-weight: 500;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .preferences-container {
            flex: 1;
            max-width: 960px;
            margin: 0 auto;
            width: 100%;
            padding: 2rem 1rem;
        }
        
        .preferences-tabs {
            display: flex;
            border-bottom: 1px solid var(--pref-border);
            margin-bottom: 2rem;
            overflow-x: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        
        .preferences-tabs::-webkit-scrollbar {
            display: none;
        }
        
        .tab-button {
            padding: 0.75rem 1.5rem;
            background: transparent;
            border: none;
            border-bottom: 3px solid transparent;
            color: var(--pref-dark);
            opacity: 0.7;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .tab-button:hover {
            opacity: 1;
        }
        
        .tab-button.active {
            border-bottom-color: var(--pref-primary);
            opacity: 1;
            color: var(--pref-primary);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .preference-group {
            margin-bottom: 2rem;
        }
        
        .preference-group-title {
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--pref-dark);
            opacity: 0.6;
            margin-bottom: 1rem;
        }
        
        .preference-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            border-radius: var(--pref-radius);
            margin-bottom: 0.5rem;
            background-color: rgba(var(--pref-dark-rgb, 33, 37, 41), 0.02);
            transition: all 0.2s ease;
        }
        
        .preference-item:hover {
            background-color: rgba(var(--pref-dark-rgb, 33, 37, 41), 0.05);
        }
        
        .preference-label {
            font-weight: 500;
            margin-bottom: 0.25rem;
        }
        
        .preference-description {
            font-size: 0.875rem;
            opacity: 0.7;
            margin: 0;
        }
        
        .preference-control {
            min-width: 120px;
        }
        
        .color-preview {
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            border: 2px solid var(--pref-border);
            display: inline-block;
            cursor: pointer;
            transition: transform 0.2s ease;
            vertical-align: middle;
        }
        
        .color-preview:hover {
            transform: scale(1.1);
        }
        
        .preview-box {
            padding: 1rem;
            border-radius: var(--pref-radius);
            border: 1px solid var(--pref-border);
            margin-top: 1rem;
        }
        
        .preferences-footer {
            position: sticky;
            bottom: 0;
            background-color: var(--pref-light);
            border-top: 1px solid var(--pref-border);
            padding: 1rem;
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            box-shadow: 0 -2px 10px var(--pref-shadow);
            z-index: 10;
        }
        
        .btn-cancel {
            background-color: transparent;
            border: 1px solid var(--pref-border);
            color: var(--pref-dark);
            padding: 0.5rem 1rem;
            border-radius: var(--pref-radius);
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .btn-cancel:hover {
            background-color: rgba(var(--pref-dark-rgb, 33, 37, 41), 0.05);
        }
        
        .btn-save {
            background-color: var(--pref-primary);
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: var(--pref-radius);
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .btn-save:hover {
            background-color: var(--pref-secondary);
            transform: translateY(-2px);
        }
        
        .form-range {
            width: 100%;
            -webkit-appearance: none;
            height: 0.5rem;
            border-radius: 1rem;
            background-color: var(--pref-border);
            outline: none;
        }
        
        .form-range::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 1rem;
            height: 1rem;
            border-radius: 50%;
            background-color: var(--pref-primary);
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .form-range::-webkit-slider-thumb:hover {
            transform: scale(1.2);
        }
        
        /* Dark mode specific styles */
        .bg-dark .preferences-wrapper {
            background-color: var(--pref-light);
            color: var(--pref-dark);
        }
        
        .bg-dark .preference-item {
            background-color: rgba(var(--pref-dark-rgb, 248, 249, 250), 0.05);
        }
        
        .bg-dark .preference-item:hover {
            background-color: rgba(var(--pref-dark-rgb, 248, 249, 250), 0.1);
        }
        
        /* Responsive styles */
        @media (max-width: 768px) {
            .preferences-container {
                padding: 1rem 0.5rem;
            }
            
            .preference-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .preference-control {
                width: 100%;
                margin-top: 1rem;
            }
            
            .preferences-tabs {
                gap: 0;
            }
            
            .tab-button {
                padding: 0.75rem 1rem;
                font-size: 0.875rem;
            }
        }
        
        @media (max-width: 576px) {
            .preferences-header {
                padding: 1rem;
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .preferences-title {
                font-size: 1.1rem;
            }
            
            .preferences-footer {
                padding: 0.75rem;
            }
            
            .btn-save, .btn-cancel {
                padding: 0.5rem 0.75rem;
                font-size: 0.875rem;
            }
        }
    </style>
</head>
<body class="mode-target">
    <div class="preferences-wrapper mode-target">
        <!-- Header with logo and navigation -->
        <div class="preferences-header">
            <h1 class="preferences-title">
                <img src="images/Skeleton.png" alt="Notes App Logo" style="height: 30px;">
                <span>Preferences</span>
            </h1>
            <div>
                <a href="index.php" class="btn btn-cancel">
                    <i class="fas fa-arrow-left"></i> Back to Notes
                </a>
            </div>
        </div>

        <!-- Main content area -->
        <div class="preferences-container">
            <!-- Tabs navigation -->
            <div class="preferences-tabs">
                <button class="tab-button active" data-tab="appearance">
                    <i class="fas fa-palette"></i> Appearance
                </button>
                <button class="tab-button" data-tab="text">
                    <i class="fas fa-font"></i> Text
                </button>
            </div>
            
            <!-- Appearance tab content -->
            <div id="appearance" class="tab-content active">
                <div class="preference-group">
                    <h3 class="preference-group-title">Colors</h3>
                    
                    <div class="preference-item">
                        <div>
                            <div class="preference-label">Note Background Color</div>
                            <p class="preference-description">Default background color for all new notes</p>
                        </div>
                        <div class="preference-control">
                            <div class="color-preview" id="colorPreview" onclick="document.getElementById('noteColor').click()"></div>
                            <input type="color" class="d-none" id="noteColor" value="#ffffff">
                        </div>
                    </div>
                    
                    <div class="preview-box" id="colorPreview2">
                        <p>This is a preview of your note color.</p>
                    </div>
                </div>
                

            </div>
            
            <!-- Text tab content -->
            <div id="text" class="tab-content">
                <div class="preference-group">
                    <h3 class="preference-group-title">Font Size</h3>
                    
                    <div class="preference-item">
                        <div>
                            <div class="preference-label">Note Text Size</div>
                            <p class="preference-description">Size of text in your notes (10-30px)</p>
                        </div>
                        <div class="preference-control">
                            <input type="number" class="form-control" id="fontSize" min="10" max="30" value="16" step="1">
                        </div>
                    </div>
                    
                    <div class="preference-item">
                        <div class="w-100">
                            <input type="range" class="form-range" id="fontSizeRange" min="10" max="30" value="16" step="1">
                        </div>
                    </div>
                    
                    <div class="preview-box" id="fontSizePreview">
                        <p>This is a preview of your selected font size.</p>
                    </div>
                </div>
            </div>
            

        </div>
        
        <!-- Footer with action buttons -->
        <div class="preferences-footer">
            <a href="index.php" class="btn btn-cancel">Cancel</a>
            <button class="btn btn-save" id="savePreferences">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/logout.js"></script>
    <script src="js/loadUserAvatar.js"></script>
    <script type="module" src="js/preferences.js"></script>
</body>
</html> 
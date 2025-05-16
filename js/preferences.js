import { changeMode } from './mode.js';

// Load saved preferences when the page loads
document.addEventListener('DOMContentLoaded', () => {
    loadPreferences();
    setupEventListeners();
    initializePreviewElements();
    setupTabNavigation();
});

function loadPreferences() {
    // Load font size preference - now a numeric value in pixels
    const savedFontSize = localStorage.getItem('fontSize') || '16';
    document.getElementById('fontSize').value = savedFontSize;
    
    // Also update the range slider if it exists
    const fontSizeRange = document.getElementById('fontSizeRange');
    if (fontSizeRange) {
        fontSizeRange.value = savedFontSize;
    }
    
    // Apply font size immediately for demonstration
    applyFontSize(savedFontSize);

    // Load note color preference
    const savedNoteColor = localStorage.getItem('noteColor') || '#ffffff';
    document.getElementById('noteColor').value = savedNoteColor;
    document.getElementById('colorPreview').style.backgroundColor = savedNoteColor;
    
    // Update the second color preview if it exists
    const colorPreview2 = document.getElementById('colorPreview2');
    if (colorPreview2) {
        colorPreview2.style.backgroundColor = savedNoteColor;
    }
    
    // Set dark mode toggle based on current mode
    const darkModeToggle = document.getElementById('darkModeToggle');
    if (darkModeToggle) {
        const currentMode = localStorage.getItem('mode') || 'LIGHT';
        darkModeToggle.checked = (currentMode === 'DARK');
    }
}

function setupEventListeners() {
    // Font size number input change - apply immediately for visual feedback
    document.getElementById('fontSize').addEventListener('input', (e) => {
        const fontSize = e.target.value;
        // Check if the font size is within valid range
        if (fontSize >= 10 && fontSize <= 30) {
            // Update the range slider to match
            const fontSizeRange = document.getElementById('fontSizeRange');
            if (fontSizeRange) {
                fontSizeRange.value = fontSize;
            }
            applyFontSize(fontSize);
        }
    });
    
    // Font size range slider change
    const fontSizeRange = document.getElementById('fontSizeRange');
    if (fontSizeRange) {
        fontSizeRange.addEventListener('input', (e) => {
            const fontSize = e.target.value;
            // Update the number input to match
            document.getElementById('fontSize').value = fontSize;
            applyFontSize(fontSize);
        });
    }

    // Note color change
    document.getElementById('noteColor').addEventListener('input', (e) => {
        const color = e.target.value;
        document.getElementById('colorPreview').style.backgroundColor = color;
        
        // Update the second color preview if it exists
        const colorPreview2 = document.getElementById('colorPreview2');
        if (colorPreview2) {
            colorPreview2.style.backgroundColor = color;
        }
    });
    
    // Dark mode toggle
    const darkModeToggle = document.getElementById('darkModeToggle');
    if (darkModeToggle) {
        darkModeToggle.addEventListener('change', (e) => {
            const isDarkMode = e.target.checked;
            const mode = isDarkMode ? 'DARK' : 'LIGHT';
            localStorage.setItem('mode', mode);
            changeMode(mode);
        });
    }
    
    // Autosave toggle (just for UI, not functional in this demo)
    const autosaveToggle = document.getElementById('autosaveToggle');
    if (autosaveToggle) {
        autosaveToggle.addEventListener('change', () => {
            // This would normally save the autosave preference
            // Just for demonstration purposes
        });
    }

    // Save preferences button
    document.getElementById('savePreferences').addEventListener('click', savePreferences);
}

function setupTabNavigation() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked button
            button.classList.add('active');
            
            // Show corresponding content
            const tabId = button.getAttribute('data-tab');
            const tabContent = document.getElementById(tabId);
            if (tabContent) {
                tabContent.classList.add('active');
            }
        });
    });
}

function applyFontSize(size) {
    // Size is now a direct pixel value (string or number)
    const fontSizePixels = `${size}px`;
    
    // Set CSS variable
    document.documentElement.style.setProperty('--note-font-size', fontSizePixels);
    
    // Apply to some specific elements for immediate feedback
    const elements = document.querySelectorAll('.note, .card, textarea, p, h4, h5');
    elements.forEach(el => {
        el.style.fontSize = fontSizePixels;
    });
    
    // Update the font size preview text
    const fontSizePreview = document.getElementById('fontSizePreview');
    if (fontSizePreview) {
        fontSizePreview.style.fontSize = fontSizePixels;
    }
}

function initializePreviewElements() {
    // Initialize the font size preview
    const fontSizePreview = document.getElementById('fontSizePreview');
    if (fontSizePreview) {
        const savedFontSize = localStorage.getItem('fontSize') || '16';
        fontSizePreview.style.fontSize = `${savedFontSize}px`;
    }
    
    // Initialize the color preview
    const colorPreview2 = document.getElementById('colorPreview2');
    if (colorPreview2) {
        const savedNoteColor = localStorage.getItem('noteColor') || '#ffffff';
        colorPreview2.style.backgroundColor = savedNoteColor;
    }
    
    // Add click handler to color preview to focus the color input
    const colorPreview = document.getElementById('colorPreview');
    if (colorPreview) {
        colorPreview.addEventListener('click', () => {
            document.getElementById('noteColor').click();
        });
    }
}

function savePreferences() {
    // Get the font size as a number for validation
    const fontSizeInput = document.getElementById('fontSize');
    let fontSize = parseInt(fontSizeInput.value, 10);
    
    // Enforce minimum and maximum values
    if (fontSize < 10) fontSize = 10;
    if (fontSize > 30) fontSize = 30;
    
    // Update the input field if it was adjusted
    fontSizeInput.value = fontSize;
    
    const preferences = {
        fontSize: fontSize,
        noteColor: document.getElementById('noteColor').value
    };

    // Save to localStorage (keep fontSize as a string)
    localStorage.setItem('fontSize', fontSize.toString());
    localStorage.setItem('noteColor', preferences.noteColor);

    // Apply changes
    applyFontSize(fontSize);

    // Save to server
    $.ajax({
        url: 'api/update_preferences.php',
        type: 'POST',
        data: preferences,
        dataType: 'json',
        success: function(response) {
            console.log('Response:', response);
            if (response.code === 0 || response.code == 0) {
                alert('Preferences saved successfully!');
            } else {
                console.error('Error saving preferences:', response);
                alert('Failed to save preferences: ' + (response.message || 'Please try again.'));
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', status, error);
            alert('Failed to connect to server. Changes saved locally only.');
        }
    });
} 
function showError(msg) {
    $("#errorDiv").show();
    $("#errorDiv").text(msg);

    window.setTimeout(function () { $("#errorDiv").hide() }, 2500)
}

function showOfflineWarning() {
  const warningDiv = document.createElement('div');
  warningDiv.className = 'alert alert-warning text-center';
  warningDiv.id = 'offlineWarning';
  warningDiv.style.position = 'fixed';
  warningDiv.style.top = '70px';
  warningDiv.style.left = '50%';
  warningDiv.style.transform = 'translateX(-50%)';
  warningDiv.style.zIndex = '1000';
  warningDiv.style.width = '80%';
  warningDiv.style.maxWidth = '500px';
  warningDiv.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
  
  warningDiv.innerHTML = `
    <i class="fas fa-wifi mr-2"></i>
    <strong>You're offline.</strong> 
    <span>Viewing cached notes. Changes will sync when you're back online.</span>
    <button type="button" class="close" aria-label="Close" onclick="document.getElementById('offlineWarning').remove()">
      <span aria-hidden="true">&times;</span>
    </button>
  `;
  
  document.body.appendChild(warningDiv);
  
  // Auto-hide after 5 seconds
  setTimeout(() => {
    if (document.getElementById('offlineWarning')) {
      document.getElementById('offlineWarning').remove();
    }
  }, 5000);
}

export { showError, showOfflineWarning }
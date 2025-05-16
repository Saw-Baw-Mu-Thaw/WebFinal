<?
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="Description" content="Enter your description here" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <!-- Collaboration CSS will be loaded dynamically when needed -->
    <title>Edit</title>
</head>

<body class='mode-target'>
    <div class="container mode-target">
        <!-- App Header -->
        <header class="mb-4">
            <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm rounded mb-3">
                <div class="logo-container">
                    <a class="navbar-brand" href="#">
                        <img src="images/Skeleton.png" alt="Notes App Logo" height="40" class="d-inline-block align-top">
                        <span class="ml-2 font-weight-bold">Notes App</span>
                    </a>
                </div>
                
                <div class="d-flex align-items-center ml-auto">
                    <!-- Home Button -->
                    <button class="btn btn-outline-primary mr-2 shadow-sm" type="button" id="homeBtn" title="Back to Home">
                        <i class="fas fa-home"></i> Home
                    </button>
                    
                    <!-- View Mode Toggle -->
                    <div class="btn-group btn-group-sm mr-3 border rounded shadow-sm" role="group" aria-label="View Mode">
                        <button type="button" class="btn btn-light" data-toggle="tooltip" title="Light Mode" onclick="$('input:radio[name=mode][value=LIGHT]').click()">
                            <i class="far fa-sun"></i>
                        </button>
                        <button type="button" class="btn btn-light" data-toggle="tooltip" title="Dark Mode" onclick="$('input:radio[name=mode][value=DARK]').click()">
                            <i class="far fa-moon"></i>
                        </button>
                    </div>
                    
                    <!-- Hidden radio inputs for compatibility -->
                    <div class="d-none">
                        <input type="radio" name="mode" value="LIGHT">
                        <input type="radio" name="mode" value="DARK">
                    </div>
                    
                    <!-- Share Button -->
                    <button class="btn btn-info mr-2 shadow-sm" type="button" id="shareBtn" data-toggle="modal" data-target="#shareModal">
                        <i class="fas fa-share-alt"></i> Share
                    </button>
                    
                    <!-- User Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle shadow-sm" type="button" id="userMenuDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-user-circle"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right shadow" aria-labelledby="userMenuDropdown">
                            <a class="dropdown-item" href="profile.php">
                                <i class="fas fa-user-circle mr-2"></i>Profile
                            </a>
                            <a class="dropdown-item" href="preferences.php">
                                <i class="fas fa-cog mr-2"></i>Preferences
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" onclick="Logout(); return false;">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                        </div>
                    </div>
                </div>
            </nav>
            
            <!-- Status bar -->
            <div class="alert alert-info text-center mb-3 shadow-sm" id="statusDiv" role="status">
                Ready to edit
            </div>
        </header>

        <!-- Share Modal -->
        <div class="modal fade" id="shareModal" tabindex="-1" role="dialog" aria-labelledby="shareModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="shareModalLabel">Share Note</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="shareErrorMsg" class="alert alert-danger d-none"></div>
                        <div id="shareSuccessMsg" class="alert alert-success d-none"></div>

                        <div class="form-group">
                            <label for="shareEmail">Email address</label>
                            <input type="email" class="form-control" id="shareEmail" placeholder="Enter email">
                            <small id="emailHelp" class="form-text text-muted">Enter the email of a registered user.</small>
                        </div>

                        <div class="form-group">
                            <label for="sharePermission">Permission</label>
                            <select class="form-control" id="sharePermission">
                                <option value="VIEWER">View only</option>
                                <option value="EDITOR">Can edit</option>
                            </select>
                            <small class="form-text text-muted">Users with edit permission can collaborate in real-time.</small>
                        </div>

                        <button type="button" class="btn btn-primary" id="addShareBtn">Share</button>

                        <hr>

                        <h5>Shared with</h5>
                        <div id="collaboratorsList" class="mt-3">
                            <!-- Collaborators will be listed here -->
                            <div class="text-center" id="loadingCollaborators">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                            <div id="noCollaboratorsMsg" class="alert alert-info d-none">
                                This note is not shared with anyone yet.
                            </div>
                            <ul class="list-group" id="collaboratorsListGroup">
                                <!-- Collaborators items will be added here -->
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-9">
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-3">
                        <!-- Title input with better styling -->
                        <input class="form-control-lg border-0 w-100 font-weight-bold mb-3" 
                               type="text" id="title" placeholder="Note Title" />
                        
                        <!-- Note content textarea with improved styling -->
                        <div class="form-group mb-0">
                            <textarea class="form-control note-editor border-0" 
                                     id="textareaElem" rows="15" 
                                     placeholder="Start typing your note here..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <!-- Labels Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-tags mr-2"></i>Labels</h5>
                    </div>
                    <div class="card-body">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">#</span>
                            </div>
                            <input id="txtLabel" type="text" class="form-control" placeholder="Add a label">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" id="AddLabelBtn">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        
                        <small class="d-block text-muted mb-3">
                            To rename a label, enter the new name and click the edit icon.
                        </small>
                        
                        <div id="labelDiv" class="d-flex flex-wrap">
                            <!-- Labels will appear here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
    <!-- Offline Support -->
    <script src="js/idb.js"></script>
    <script src="js/offlineNotes.js"></script>
    <!-- App Scripts -->
    <script src="js/edit.js" type="module"></script>
    <script src="js/logout.js"></script>
    <script src='js/labelActions.js'></script>
    <script src='js/shareNote.js'></script>
</body>

</html>
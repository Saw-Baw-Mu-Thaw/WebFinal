<?php
session_start();


if (!isset($_SESSION['username'])) {
    header('Location: login.php');
}

include 'api/unverified.php';
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
    <title>Index</title>
</head>

<body class="mode-target">
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
                    <!-- View Mode Toggles -->
                    <div class="btn-group btn-group-sm mr-3 border rounded shadow-sm" role="group" aria-label="View Mode">
                        <button type="button" class="btn btn-light" data-toggle="tooltip" title="Light Mode" onclick="$('input:radio[name=mode][value=LIGHT]').click()">
                            <i class="far fa-sun"></i>
                        </button>
                        <button type="button" class="btn btn-light" data-toggle="tooltip" title="Dark Mode" onclick="$('input:radio[name=mode][value=DARK]').click()">
                            <i class="far fa-moon"></i>
                        </button>
                    </div>
                    
                    <!-- Layout Toggles -->
                    <div class="btn-group btn-group-sm mr-3 border rounded shadow-sm" role="group" aria-label="Layout Mode">
                        <button type="button" class="btn btn-light" data-toggle="tooltip" title="Grid View" onclick="$('input:radio[name=layout][value=GRID]').click()">
                            <i class="fas fa-th-large"></i>
                        </button>
                        <button type="button" class="btn btn-light" data-toggle="tooltip" title="List View" onclick="$('input:radio[name=layout][value=LIST]').click()">
                            <i class="fas fa-list-ul"></i>
                        </button>
                    </div>
                    
                    <!-- Hidden radio inputs for compatibility -->
                    <div class="d-none">
                        <input type="radio" name="mode" value="LIGHT">
                        <input type="radio" name="mode" value="DARK">
                        <input type="radio" name="layout" value="GRID">
                        <input type="radio" name="layout" value="LIST">
                    </div>
                    
                    <!-- Notifications dropdown -->
                    <div class="dropdown mr-2">
                        <button class="btn btn-light position-relative shadow-sm" type="button" id="notificationsBtn"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-bell"></i>
                            <span id="notificationBadge" class="position-absolute badge badge-danger d-none"
                                style="top: -5px; right: -5px;">0</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right shadow" aria-labelledby="notificationsBtn"
                            style="width: 300px; max-height: 300px; overflow-y: auto;">
                            <h6 class="dropdown-header">Notifications</h6>
                            <div id="notificationsContainer">
                                <div class="text-center p-2" id="loadingNotifications">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                                <div class="dropdown-item text-center text-muted d-none" id="noNotificationsMsg">
                                    No notifications
                                </div>
                                <!-- Notifications will be added here dynamically -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- User Avatar -->
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle text-decoration-none" id="userDropdown" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <img id="headerAvatar" src="images/default_profile_pic.jpg" alt="User Avatar"
                                class="rounded-circle border shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow" aria-labelledby="userDropdown">
                            <div class="px-4 py-3 text-center">
                                <h6 id="userHeading" class="mb-0"></h6>
                                <small class="text-muted">Logged in</small>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="profile.php">
                                <i class="fas fa-user-circle mr-2"></i>View Profile
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
        </header>

                <!-- delete Modal -->
                <div class="modal fade" id='deleteModal' tabindex=-1>
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id='delMessage'></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-footer">
                                <button class="btn btn-danger" type="button" id='delConfirmBtn'>Yes</button>
                                <button class="btn btn-secondary" type="button" data-dismiss="modal">No</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- password Modal -->
                <div class="modal fade" id='passwordModal' tabindex=-1>
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <div class="input-group mb-3">
                                    <input class="form-control" type="password" id='notePwd' maxlength="30" required />
                                    <div class="input-group-append">
                                        <button type="submit" id='pwdSubmitBtn'
                                            class="btn btn-success mr-3">Enter</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- set password modal -->
                <div class="modal fade" id='setPwdModal' tabindex=-1>
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <label class="form-label" for='pwd1'>Enter password</label>
                                <input class="form-control" type="password" id='pwd1' maxlength="30" required />

                                <label class="form-label" for='pwd2'>Enter password again</label>
                                <input class="form-control" type="password" id='pwd2' maxlength="30" required />

                                <p id='pwdError' class="alert alert-danger m-2"></p>
                                <button type="submit" id='pwdSetBtn' class="btn btn-success m-3">Enter</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- change note password Modal -->
                <div class="modal fade" id='changePwdModal' tabindex=-1>
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <label class="form-label" for='oldPwd'>Old password</label>
                                <input class="form-control" type="password" id='oldPwd' maxlength="30" required />

                                <label class="form-label" for='pwd3'>Enter new password</label>
                                <input class="form-control" type="password" id='pwd3' maxlength="30" required />

                                <label class="form-label" for='pwd4'>Enter new password again</label>
                                <input class="form-control" type="password" id='pwd4' maxlength="30" required />

                                <p id='pwdChangeError' class="alert alert-danger m-2"></p>
                                <button type="submit" id='pwdChangeBtn' class="btn btn-success m-3">Change</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row m-3">
            <div class="col-12">
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type='text' class="form-control shadow-sm" id='txtSearch'
                        placeholder="Search notes or use # to search for labels (e.g. #important)" />
                </div>
                <div class='list-group shadow-sm' id='searchList'>
                    <!-- Search results will appear here -->
                </div>
            </div>
        </div>

        <div class="row m-2">
            <div class="col-12">
                <div id='errorDiv' class="alert alert-danger text-center p-3 d-none">
                    <!-- Error messages will appear here -->
                </div>
            </div>
        </div>

        <div class='row'>
            <div class='col-lg-3 col-md-4 col-12'>
                <div class="card shadow-sm mb-4 mode-target">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-tags mr-2"></i>Your Labels</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class='list-group list-group-flush' id='labelList'>
                            <!-- Labels will be populated here -->
                        </div>
                    </div>
                </div>
            </div>

            <div class='col-lg-9 col-md-8 col-12'>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0" id="contentTitle">Your Notes</h4>
                    <button class="btn btn-success" type="button" onclick="createEmptyNote()">
                        <i class="fas fa-plus mr-1"></i> New Note
                    </button>
                </div>
                <div id='mainContent' class="row mode-target">
                    <!-- Notes list or grid will be generated here -->
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
    <!-- Offline Support -->
    <script src="./js/idb.js"></script>
    <script src="./js/offlineNotes.js"></script>
    <!-- App Scripts -->
    <script src="./js/noteActions.js"></script>
    <script src="./js/pinActions.js"></script>
    <script type='module' src="./js/index.js"></script>
    <script type="module" src='./js/utils.js'></script>
    <script src="./js/logout.js"></script>
    <script src='./js/notifications.js'></script>
    <script src='./js/imgUpload.js'></script>
    <script src='./js/loadUserAvatar.js'></script>
    
    <!-- Add Web App Manifest for PWA -->
    <link rel="manifest" href="manifest.json">
    <!--  -->
</body>

</html>
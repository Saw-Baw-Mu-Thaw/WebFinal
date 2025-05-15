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
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Index</title>
</head>

<body class="mode-target">
    <div class="container mode-target">
        <div class="row mode-target">
            <div class="col-3 mr-auto p-3 text-center">
                <img class="img-fluid" src="images/Skeleton.png" alt="SkeleLogo" />
            </div>
            <div class="d-flex col-3 justify-content-end align-items-center">
                <!-- Notifications dropdown -->
                <div class="dropdown mr-2">
                    <button class="btn btn-secondary position-relative" type="button" id="notificationsBtn"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-bell"></i>
                        <span id="notificationBadge" class="position-absolute badge badge-danger d-none"
                            style="top: -5px; right: -5px;">0</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="notificationsBtn"
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
                <button class="btn btn-secondary" onclick="Logout()">Logout</button>
            </div>
        </div>

        <div class="row border mode-target">
            <div class="col-6 p-3">
                <h3 id='userHeading'></h3>
            </div>
            <div class="col-4 p-3">
                <div class='btn-group btn-group-toggle border rounded m-1' data-toggle="button">
                    <label class="btn btn-light">
                        <input type="radio" name="mode" value='LIGHT'> <i class="far fa-sun"></i>
                    </label>
                    <label class="btn btn-dark">
                        <input type='radio' name='mode' value='DARK'> <i class="far fa-moon"></i>
                    </label>
                </div>

                <div class='btn-group btn-group-toggle border rounded m-1' data-toggle="button">
                    <label class="btn btn-light">
                        <input type="radio" name="layout" value='GRID'> <i class="fas fa-th-large"></i>
                    </label>
                    <label class="btn btn-light">
                        <input type='radio' name='layout' value='LIST'> <i class="fas fa-list-ul"></i>
                    </label>
                </div>
            </div>
            <div class="d-flex col-2 p-3 justify-content-end">
                <button class="btn btn-success" type="button" data-toggle='modal' data-target='#CreateNoteModal'><i
                        class="fas fa-plus"></i></button>

                <!-- create Note Modal -->
                <div class="modal fade" id='CreateNoteModal' tabindex=-1>
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Create New Note</h5>
                                <button type="button" class="close" data-dismiss="modal"
                                    onclick="(() => $('#createError').hide())">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <div class="container">
                                    <div class="col-12">
                                        <div class="input-group mb-3">
                                            <input class="form-control" type="text" placeholder='Name here'
                                                id='txtTitle' maxlength="30" required />
                                            <div class="input-group-append">
                                                <button class="btn btn-success mr-3"
                                                    onclick='createNote()'>Create</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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
                                    <input class="form-control" type="text" id='notePwd' maxlength="30" required />
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
                                <input class="form-control" type="text" id='pwd1' maxlength="30" required />

                                <label class="form-label" for='pwd2'>Enter password again</label>
                                <input class="form-control" type="text" id='pwd2' maxlength="30" required />

                                <p id='pwdError' class="alert alert-danger m-2"></p>
                                <button type="submit" id='pwdSetBtn' class="btn btn-success m-3">Enter</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row m-2">
            <div class='input-group'>
                <div class='input-group-prepend'>
                    <span class='input-group-text'> Search </span>
                </div>
                <input type='text' class="form-control" id='txtSearch' placeholder="Use # to search for labels e.g. #potato" />
            </div>

            <div class='list-group col-12' id='searchList'>

            </div>
        </div>

        <div class="row m-2">									 
            <h3 id='errorDiv' class="alert alert-danger col-12 text-center p-3">

            </h3>
        </div>

        <div id='mainContent' class="row m-3 mode-target">
            <!-- list or grid goes here, generated by js -->
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
    <script src="./js/noteActions.js"></script>
    <script src="./js/pinActions.js"></script>
    <script type='module' src="./js/index.js"></script>
    <script type="module" src='./js/utils.js'></script>
    <script src="./js/logout.js"></script>
    <script src='./js/notifications.js'></script>
    <script src='./js/imgUpload.js'></script>
    <!--  -->
</body>

</html>
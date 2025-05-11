<?php
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
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Index</title>
</head>

<body class="">
    <div class="container">
        <div class="row">
            <div class="col-3 mr-auto p-3 text-center">
                <img class="img-fluid" src="images/Skeleton.png" alt="SkeleLogo" />
            </div>
            <div class="d-flex col-3 justify-content-end align-items-center">
                <button class="btn btn-secondary" onclick="Logout()">Logout</button>
            </div>
        </div>

        <div class="row border">
            <div class="col-8 p-3">
                <h3 id='userHeading'></h3>
            </div>
            <div class="d-flex col-4 p-3 justify-content-end">
                <button class="btn btn-success" type="button" data-toggle='modal' data-target='#CreateNoteModal'><i class="fas fa-plus"></i></button>

                <!-- create Note Modal -->
                <div class="modal fade" id='CreateNoteModal' tabindex=-1>
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Create New Note</h5>
                                <button type="button" class="close" data-dismiss="modal" onclick="(() => $('#createError').hide())">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <div class="container">
                                    <div class="col-12">
                                        <div class="input-group mb-3">
                                            <input class="form-control" type="text" placeholder='Name here' id='txtTitle' maxlength="30" required />
                                            <div class="input-group-append">
                                                <button class="btn btn-success mr-3" onclick='createNote()'>Create</button>
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
                                        <button type="submit" id='pwdSubmitBtn' class="btn btn-success mr-3">Enter</button>
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

                <!-- sharing modal -->
                <div class="modal fade" id='sharingModal' tabindex=-1>
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Manage Sharing</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="shareEmail">Share with (Email)</label>
                                    <input type="email" class="form-control" id="shareEmail" required>
                                </div>
                                <div class="form-group">
                                    <label for="shareRole">Permission</label>
                                    <select class="form-control" id="shareRole">
                                        <option value="VIEWER">View Only</option>
                                        <option value="EDITOR">Can Edit</option>
                                    </select>
                                </div>
                                <button type="button" class="btn btn-primary" onclick="updateSharing()">Share</button>
                                
                                <hr>
                                <h6>Current Sharing</h6>
                                <div id="sharingList" class="list-group">
                                    <!-- Sharing list will be populated here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row m-2">
            <h3 id='errorDiv' class="alert alert-danger col-12 text-center p-3">

            </h3>
        </div>

        <div id='mainContent' class="row m-3">
            <!-- list or grid goes here, generated by js -->
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
    <script src="js/index.js" type='module'></script>
    <script src="js/logout.js"></script>
    <script src="js/noteActions.js"></script>
</body>

</html>
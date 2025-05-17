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
    <title>User Profile</title>
</head>

<body class="mode-target">
    <div class="container mode-target">
        <div class="row mode-target">
            <div class="col-3 mr-auto p-3 text-center">
                <a href="index.php"><img class="img-fluid" src="images/Skeleton.png" alt="SkeleLogo" /></a>
            </div>
            <div class="d-flex col-3 justify-content-end align-items-center">
                <button class="btn btn-secondary" onclick="window.location.href='index.php'">Back to Notes</button>
            </div>
        </div>

        <div class="row border mode-target">
            <div class="col-12 p-3">
                <h2 class="text-center">User Profile</h2>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-4 text-center">
                <div class="card mb-4">
                    <div class="card-body">
                        <div id="profile-image-container" class="mb-3">
                            <img id="profileImage" src="images/default_profile_pic.jpg" alt="Profile Picture" class="img-fluid rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                        </div>
                        <h4 id="profileUsername"></h4>
                        <p id="profileEmail" class="text-muted"></p>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#editProfileModal">
                            <i class="fas fa-edit"></i> Edit Profile
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Profile Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-3">
                                <h6 class="mb-0">Username</h6>
                            </div>
                            <div class="col-sm-9 text-secondary" id="displayUsername"></div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <h6 class="mb-0">Email</h6>
                            </div>
                            <div class="col-sm-9 text-secondary" id="displayEmail"></div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <h6 class="mb-0">Account Status</h6>
                            </div>
                            <div class="col-sm-9 text-secondary" id="verificationStatus"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editProfileForm" enctype="multipart/form-data">
                        <div class="form-group text-center">
                            <img id="avatarPreview" src="images/default_profile_pic.jpg" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="profilePictureInput" name="profilePicture" accept="image/*">
                                <label class="custom-file-label" for="profilePictureInput">Choose avatar...</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="usernameInput">Username</label>
                            <input type="text" class="form-control" id="usernameInput" name="username" disabled>
                            <small class="form-text text-muted">Username cannot be changed</small>
                        </div>
                        <div class="form-group">
                            <label for="emailInput">Email</label>
                            <input type="email" class="form-control" id="emailInput" name="email">
                        </div>
                        <div class="form-group">
                            <label for="currentPasswordInput">Current Password (required to save changes)</label>
                            <input type="password" class="form-control" id="currentPasswordInput" name="currentPassword" required>
                        </div>
                        <div class="form-group">
                            <label for="newPasswordInput">New Password (leave blank to keep current)</label>
                            <input type="password" class="form-control" id="newPasswordInput" name="newPassword">
                        </div>
                        <div class="form-group">
                            <label for="confirmPasswordInput">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirmPasswordInput" name="confirmPassword">
                        </div>
                        <div id="profileEditError" class="alert alert-danger d-none"></div>
                        <div id="profileEditSuccess" class="alert alert-success d-none"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveProfileBtn">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
    <script src="./js/profile.js"></script>
</body>

</html>
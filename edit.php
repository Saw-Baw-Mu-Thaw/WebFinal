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
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Edit</title>
</head>

<body class='mode-target'>
    <div class="container mode-target">
        <div class="row mode-target">
            <div class="col-3 mr-auto p-3 text-center">
                <img class="img-fluid" src="images/Skeleton.png" alt="SkeleLogo" />
            </div>
            <div class="d-flex col-3 justify-content-end align-items-center">
                <button class="btn btn-secondary" onclick="Logout()">Logout</button>
            </div>
        </div>

        <div class="border rounded">
            <div class="row p-3 mode-target">
                <div class="col-4 p-1">
                    <button class="btn btn-primary" type="button" id="homeBtn">
                        <i class="fas fa-home"></i></button>
                    <div class='btn-group btn-group-toggle border rounded m-1' data-toggle="button">
                        <label class="btn btn-light">
                            <input type="radio" name="mode" value='LIGHT'> <i class="far fa-sun"></i>
                        </label>
                        <label class="btn btn-dark">
                            <input type='radio' name='mode' value='DARK'> <i class="far fa-moon"></i>
                        </label>
                    </div>
                </div>

                <div class="col-4 text-center" id="statusDiv">

                </div>



                <div class="col-4 p-1">
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-info ml-auto" type="button" id="shareBtn" data-toggle="modal" data-target="#shareModal">
                            <i class="fas fa-share-alt"></i> Share
                        </button>
                    </div>

                </div>
            </div>
        </div>

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

        <div class="border-bottom">
            <div class="row mt-1 p-3 mode-target">
                <h3>Labels</h3>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">#</span>
                    </div>
                    <input id='txtLabel' type="text" class="form-control" placeholder="Label">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="AddLabelBtn">Add Label</button>
                    </div>
                </div>


                <div id='labelDiv' class='p-1'>

                </div>
            </div>
        </div>



        <div class="row">
            <div class="col-12 p-3">
                <span class="d-flex justify-content-start">
                    <input class="border-0 h2 form-control" type="text" id="title"
                        value="" />

                </span>

                <hr />
                <div class="form-group">
                    <textarea class="form-control border-0" id='textareaElem' id="content" rows="13"></textarea>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
    <script src="js/edit.js" type="module"></script>
    <script src="js/logout.js"></script>
    <script src='js/labelActions.js'></script>
    <script src='js/shareNote.js'></script>
</body>

</html>
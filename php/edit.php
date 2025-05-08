<?php
session_start();

require 'skeletondb.php';

// for the title
$action = "Edit";
$title = "";
$content = "";
$location = "";

// to check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
}

// create new note
if (isset($_GET['action']) && !empty($_GET['action'])) {
    $action = $_GET['action'];
    $title = $_GET['title'];
    $location = 'notes/' . $title . '.txt';
    // create the file
    $file = fopen($location, 'w');

    // create db entry
    create_note($_SESSION['username'], $title);

    fclose($file);
    // echo "title : " . $title . " Action: " . $action;
}


// edit an existing note
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $location = "notes/" . $_GET['title'] . '.txt';

    // open the note
    $file = fopen($location, 'r');

    // get file name and set value of title
    $title = $_GET['title'];

    // get content and set value of textarea
    while (!feof($file)) {
        $content .= fgets($file, 5000);
    }

    fclose($file);
}

// code to handle logout
if (isset($_POST['logout']) && !empty($_POST['logout'])) {
    session_destroy();
    setcookie(session_name(), "");
    header('Location: logout.php');
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
    <title><?= $action ?></title>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-3 mr-auto p-3 text-center">
                <img class="img-fluid" src="/images/Skeleton.png" alt="SkeleLogo" />
            </div>
            <div class="d-flex col-3 justify-content-end align-items-center">
                <form method="post" action=""> <!-- this will post to index.php and clear session -->
                    <button class="btn btn-secondary" name="logout" value="logout" type="submit">Logout</button>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-12 p-3">
                <!-- <form method="post" action=""> send to edit.php -->
                <span class="d-flex justify-content-start">
                    <input class="border-0 h2 form-control" type="text" name="title" id="title"
                        value="<?= $title ?>" />
                    <button class="btn btn-success justify-self-end" type="button" id='saveBtn'><i
                            class="far fa-save"></i></button>
                    <button class="btn btn-primary justify-self-end" type="button" id="homeBtn">
                        <i class="fas fa-home"></i></button>
                </span>

                <hr />
                <div class="form-group">
                    <textarea class="form-control border-0" id='textareaEle' name="content" id="content" rows="13"><?= $content ?></textarea>
                </div>
                <!-- </form> -->
            </div>
        </div>

        <div id="saveModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <h1 class="modal-title">You haven't saved yet</h1>
                    </div>
                    <div class="modal-footer">
                        <button type='button' class="btn btn-success" id="SaveLeaveBtn">Save and Leave</button>
                        <button type="button" class="btn btn-danger" id="NoSaveBtn">Leave without Saving</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
    <script>
        contentSaved = true;
        titleSaved = true;
        oldTitle = $('#title').val();

        $('#textareaEle').on("input", hasChanged);
        $('#saveBtn').on("click", saveNote);
        $('#saveBtn').prop('disabled', true);
        $('#title').on('input', titleChanged);

        $('#homeBtn').on('click', () => {
            if (contentSaved == false || titleSaved == false) {
                $('#saveModal').modal('show');
            } else {
                window.location.replace('index.php');
            }
        });

        $('#SaveLeaveBtn').on('click', () => {
            saveNote(true);
        });

        $('#NoSaveBtn').on('click', () => {
            window.location.replace('index.php');
        })

        function hasChanged() {
            contentSaved = false;
            $('#saveBtn').prop('disabled', false);
        }

        function titleChanged() {
            titleSaved = false;
            $('#saveBtn').prop('disabled', false);
        }

        function saveNote(redirect = false) {

            if (contentSaved === false || titleSaved === false) {
                title = $('#title').val();
                content = $('#textareaEle').val();
                titleChanged = !titleSaved;

                console.log("title:" + title + "\ncontent:" + content + "\ntitleChanged:" + titleChanged + "\noldTitle:" + oldTitle);

                $.ajax({
                    url: 'save.php',
                    data: {
                        "action": "save",
                        "title": title,
                        "content": content,
                        "titleChanged": titleChanged,
                        "old": oldTitle
                    },
                    type: "POST",
                    dataType: "json"
                }).done(function(response) {
                    console.log('POST sent');

                    if (response['success'] == true) {
                        // success
                        contentSaved = true;
                        titleSaved = true;
                        $('#saveBtn').prop('disabled', true);

                        if (redirect == true) {
                            window.location.replace('index.php');
                        }
                    } else {
                        // some error
                        alert(response['reason']);
                    }
                });
            }
        }
    </script>
</body>

</html>
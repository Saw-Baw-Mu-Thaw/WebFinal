<?php
session_start();

require 'skeletondb.php';

$res = true;
$error = "";

// checks if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
}

$rows = get_notes($_SESSION['username']);


// the code that handles deleting
if (isset($_POST['delete']) && !empty($_POST['delete'])) {

    //send delete signal to db
    $dbres = delete_note($_SESSION['username'], $_POST['delete']);
    //then unlink file here
    $fileres = unlink("notes/" . $_POST['delete'] . '.txt');
    //then reload this page
    if (!$dbres || !$fileres) {
        echo ('There\'s been a problem');
    } else {
        header('Refresh:0');
    }
}

// code that handles the create redirect
if (isset($_POST['create']) && !empty($_POST['create'])) {
    $action = "Create";
    // set title
    if (!empty($_POST['title'])) {
        $title = $_POST['title'];
    }

    if (file_exists('notes/' . $title . '.txt') === true) {
        // echo ("sorry that file already exists");
        $error = "File already exists with that name";
    } else {
        $array = array("action" => $action, "title" => $title);
        $queryString = http_build_query($array);
        header('Location: edit.php?' . $queryString);
        die();
    }
}

// code that handles the edit redirect
if (isset($_POST['edit']) && !empty($_POST['edit'])) {
    $title = $_POST['edit'];
    $action = "Edit";
    $array = array("title" => $title, "edit" => $action);
    $queryString = http_build_query($array);
    header('Location: edit.php?' . $queryString);
    die();
}

// code that handles logging out
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
    <title>Home</title>
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

        <div class="row border">
            <div class="col-8 p-3">
                <h3><?= $_SESSION['username'] ?>'s Notes</h3>
            </div>
            <div class="d-flex col-4 p-3 justify-content-end">
                <div class="btn-group pl-auto" role="group">
                    <button class="btn btn-success" type="button" data-toggle='modal' data-target='#CreateNoteModal'><i class="fas fa-plus"></i></button>
                </div>

                <div class="modal fade" id='CreateNoteModal' tabindex=-1>
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Create New Note</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <div class="container">
                                    <div class="col-12">
                                        <form method='post' action='index.php'>
                                            <input class="input-control w-100" type="text" placeholder='Name here' name='title' maxlength="30" required />

                                            <div class="d-flex m-5 justify-content-end">
                                                <button type="submit" class="btn btn-success mr-3" name="create" value="create">Create</button>
                                            </div>
                                        </form>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class='row m-2'>
            <?php
            if (!empty($error)) {
            ?>
                <div class="alert alert-danger col-12 text-center p-3">
                    <p><?= $error ?></p>
                </div>
            <?php
            }
            ?>
        </div>


        <div class="row m-3">
            <div class="col-lg-12 col-12">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th></th>
                            <th class="text-center">Title</th>
                            <th class="text-center">Last Modified</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        if (!empty($rows)) {
                            foreach ($rows as $row) {
                                $name = $row['Title'];
                                $date = $row['date'];
                        ?>
                                <tr>
                                    <td>
                                        <form method='post' action='index.php'>
                                            <button class='btn btn-danger' type='submit' name='delete' value='<?= $name ?>'><i class='fas fa-trash'></i></button>
                                        </form>
                                    </td>
                                    <td class='d-flex justify-content-center'>
                                        <form method='post' action='index.php'>
                                            <button class='btn btn-link' type='submit' name='edit' value='<?= $name ?>'><?= $name ?></button>
                                        </form>
                                    </td>
                                    <td class='text-center'><?= $date ?></td>
                                </tr>
                        <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>



    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
</body>

</html>
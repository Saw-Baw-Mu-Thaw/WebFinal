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

<body>
    <div class="container">
        <div class="row">
            <div class="col-3 mr-auto p-3 text-center">
                <img class="img-fluid" src="images/Skeleton.png" alt="SkeleLogo" />
            </div>
            <div class="d-flex col-3 justify-content-end align-items-center">
                <button class="btn btn-secondary" onclick="Logout()">Logout</button>
            </div>
        </div>

        <div class="border rounded">
            <div class="row p-3">
                <div class="d-flex justify-content-start col-4">
                    <button class="btn btn-primary" type="button" id="homeBtn">
                        <i class="fas fa-home"></i></button>
                </div>

                <div class="col-4 text-center" id="statusDiv">

                </div>
            </div>
        </div>

        <div class="row border rounded mt-1 p-2">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">#</span>
                </div>
                <input id='txtLabel' type="text" class="form-control" placeholder="Label">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" id="AddLabelBtn">Add Label</button>
                </div>
            </div>

            <div id='labelDiv'>

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
    <script src="js/edit.js"></script>
    <script src="js/logout.js"></script>
    <script src='js/labelActions.js'></script>
</body>

</html>
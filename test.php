<?php

$hashValue = "";

if (isset($_POST['submit']) && !empty($_POST['submit'])) {
    if (isset($_POST['hashPwd']) && !empty($_POST['hashPwd'])) {
        $hashValue = password_hash($_POST['hashPwd'], PASSWORD_BCRYPT);
    }
}

if (isset($_GET['sendMail']) && !empty($_GET['sendMail'])) {
    if ((bool)$_GET['sendMail'] == true) {
        $res = mail($_GET['email'], $_GET['subject'], $_GET['body']);
    }
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
    <title>Hash test</title>
</head>

<body>

    <div class="container">
        <div class='col-4'>
            <form method="post" action="test.php">
                <div class="form-group">
                    <label for="hashPwd">Password</label>
                    <input type="text"
                        class="form-control" name="hashPwd" id="hashPwd" aria-describedby="helpId" placeholder="">
                </div>
                <button type="submit" name="submit" value="submit" class="btn btn-primary">Hash</a>
            </form>

            <!-- <button onclick="sendMail()" class="btn btn-primary" id="sendMailBtn">Send Mail</button> -->
        </div>

        <div class="col-4">
            <?php
            if (!empty($hashValue)) {
                echo "<p>Your hash value is " . $hashValue . '<br>Length : ' . strlen($hashValue) . "</p>";
            }
            ?>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
    <script>
        // $('#sendMailBtn')
        function sendMail() {
            $.ajax({
                url: 'test.php',
                data: {
                    'sendMail': 1,
                    'email': 'thawthibaw@gmail.com',
                    'subject': 'Just a mail',
                    'body': "This is just a test mail from your test php script"
                },
                type: "GET"
            }).done(function() {
                console.log('Done');
            })
        }
    </script>
</body>

</html>
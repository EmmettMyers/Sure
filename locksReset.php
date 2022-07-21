<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href = "https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css" rel = "stylesheet">
    <script src = "https://code.jquery.com/jquery-1.10.2.js"></script>
    <script src = "https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
    <link rel="stylesheet" href="scss/style.css">
    <title>Sure</title>
    <meta http-equiv = "refresh" content = "2; url = home.php" />
</head>

<body class="bg-dark">

    <script>
        $(document).ready(function(){
            $.ajax({ method: "POST", url: "ajax/chatLocks.php", data: { removeAll: "yes" } });
        });
    </script>

    <div class="text-center fs-1 fw-bold vertCenter">
        <p style="color: lime;">All locks reset!</p>
        <p class="text-white">Redirecting to home...</p>
    </div>
</body>

</html>
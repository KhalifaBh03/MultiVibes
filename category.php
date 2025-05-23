<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MultiVibes</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/6133de38b3.js" crossorigin="anonymous"></script>
    <style>
        body {

            height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;

        }

        .container {
            flex-grow: 1;
        }
    </style>
</head>

<?php
session_start();
include('connect.php');
?>

<body>
    <?php include("include/navbar.php") ?>
    <br><br>
    <div class="container">
        <?php
        $query = "SELECT * FROM `category`";
        $result = mysqli_query($db, $query);

        if (!$result) {
            die("Query failed: " . mysqli_error($db));
        } else {
            echo '<ul class="list-group">'; // Start the list group
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<li class="list-group-item"><a href="posts.php?id=' . $row['id'] . '" class="text-decoration-none">' . htmlspecialchars($row['category']) . '</a></li>';
            }
            echo '</ul>';

            // Close the list group
        }
        ?>
    </div>
    <br><br>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="index.js"></script>
    <?php include("include/footer.php") ?>
</body>

</html>
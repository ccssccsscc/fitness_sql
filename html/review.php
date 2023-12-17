<?php
$servername = "host.docker.internal";
$username = "root";
$password = "443453";
$database = "fc2";

$mysqli = new mysqli($servername, $username, $password, $database);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

function showError($error) {
    echo "<p style='color: red;'>Error: $error</p>";
}

if (isset($_POST['add'])) {
    $comment = $_POST['comment'];

    $rating = $_POST['rating'];

    $insertQuery = "INSERT INTO review (comment, rating) VALUES ('$comment', $rating)";

    if ($mysqli->query($insertQuery) === TRUE) {
        header('Location: review.php');
    } else {
        showError($mysqli->error);
    }
}

if (isset($_POST['edit'])) {
    $review_code = $_POST['review_code'];
    $comment = $_POST['comment'];
    $rating = $_POST['rating'];

    $updateQuery = "CALL UpdateReview($review_code, '$comment', $rating)";

    if ($mysqli->query($updateQuery) === TRUE) {
        header('Location: review.php');
    } else {
        showError($mysqli->error);
    }
}

if (isset($_GET['delete'])) {
    $review_code = $_GET['delete'];
    $deleteQuery = "DELETE FROM review WHERE review_code=$review_code";

    if ($mysqli->query($deleteQuery) === TRUE) {
        header('Location: review.php');
    } else {
        showError($mysqli->error);
    }
}

$sortField = isset($_GET['sort']) ? $_GET['sort'] : 'review_code';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'asc';
$nextSortOrder = ($sortOrder === 'asc') ? 'desc' : 'asc';

$selectQuery = "SELECT * FROM review ORDER BY $sortField $sortOrder";
$result = $mysqli->query($selectQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reviews</title>
</head>
<body>
    <h1>Reviews</h1>

    <h2>Add Review</h2>
    <form method="post">
        <label>Comment: <textarea name="comment"></textarea></label><br>
        <label>Rating: <input type="text" name="rating"></label><br>
        <input type="submit" name="add" value="Add Review">
    </form>

    <h2>Review List</h2>
    <form method="get">
        <button type="submit" name="sort" value="review_code">Sort by Review Code</button>
        <input type="hidden" name="order" value="<?php echo $nextSortOrder; ?>">
    </form>
    <table border="1">
        <tr>
            <th>Review Code</th>
            <th>Comment</th>
            <th>Date Written</th>
            <th>Time Written</th>
            <th>Rating</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['review_code'] . "</td>";
                echo "<td>" . $row['comment'] . "</td>";
                echo "<td>" . $row['date_written'] . "</td>";
                echo "<td>" . $row['time_written'] . "</td>";
                echo "<td>" . $row['rating'] . "</td>";
                echo "<td><a href='review.php?edit=" . $row['review_code'] . "'>Edit</a> | <a href='review.php?delete=" . $row['review_code'] . "'>Delete</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No reviews found.</td></tr>";
        }
        ?>
    </table>

    <?php
    if (isset($_GET['edit'])) {
        $review_code = $_GET['edit'];
        $editQuery = "SELECT * FROM review WHERE review_code=$review_code";
        $editResult = $mysqli->query($editQuery);
        if ($editResult->num_rows > 0) {
            $editRow = $editResult->fetch_assoc();
            ?>
            <h2>Edit Review</h2>
            <form method="post">
                <input type="hidden" name="review_code" value="<?php echo $editRow['review_code']; ?>">
                <label>Comment: <textarea name="comment"><?php echo $editRow['comment']; ?></textarea></label><br>
                <label>Rating: <input type="text" name="rating" value="<?php echo $editRow['rating']; ?>"></label><br>
                <input type="submit" name="edit" value="Update Review">
            </form>
        <?php
        }
    }
    ?>

    <a href="index.php">Back to Home</a>
</body>
</html>
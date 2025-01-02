<?php
require_once 'config.php'; // Include database configuration

// CRUD Functions
function createPost($userId, $categoryId, $title, $description) {
    global $pdo;
    $sql = "INSERT INTO Posts (userId, categoryId, title, description) VALUES (:userId, :categoryId, :title, :description)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':userId' => $userId, ':categoryId' => $categoryId, ':title' => $title, ':description' => $description]);
    return $pdo->lastInsertId(); // Return the ID of the created post
}

function readPosts() {
    global $pdo;
    $sql = "SELECT Posts.*, Users.username, Categories.name AS category FROM Posts
            JOIN Users ON Posts.userId = Users.userId
            LEFT JOIN Categories ON Posts.categoryId = Categories.categoryId";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updatePost($postId, $title, $description) {
    global $pdo;
    $sql = "UPDATE Posts SET title = :title, description = :description, updatedAt = NOW() WHERE postId = :postId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':postId' => $postId, ':title' => $title, ':description' => $description]);
    return $stmt->rowCount(); // Return the number of rows updated
}

function deletePost($postId) {
    global $pdo;
    $sql = "DELETE FROM Posts WHERE postId = :postId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':postId' => $postId]);
    return $stmt->rowCount(); // Return the number of rows deleted
}

// Get the request method
$method = $_SERVER['REQUEST_METHOD'];

// Handle Requests
switch ($method) {
    case 'GET':
        $posts = readPosts();
        echo json_encode($posts);
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        if (isset($input['userId'], $input['categoryId'], $input['title'], $input['description'])) {
            $postId = createPost($input['userId'], $input['categoryId'], $input['title'], $input['description']);
            echo json_encode(["message" => "Post created successfully", "postId" => $postId]);
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(["message" => "Invalid input"]);
        }
        break;

    case 'PUT':
        $input = json_decode(file_get_contents('php://input'), true);
        if (isset($input['postId'], $input['title'], $input['description'])) {
            $rowsUpdated = updatePost($input['postId'], $input['title'], $input['description']);
            echo json_encode(["message" => "Post updated successfully", "rowsUpdated" => $rowsUpdated]);
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(["message" => "Invalid input"]);
        }
        break;

    case 'DELETE':
        if (isset($_GET['postId'])) {
            $rowsDeleted = deletePost($_GET['postId']);
            echo json_encode(["message" => "Post deleted successfully", "rowsDeleted" => $rowsDeleted]);
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(["message" => "Invalid input"]);
        }
        break;

    default:
        http_response_code(405); // Method Not Allowed
        echo json_encode(["message" => "Method not allowed"]);
}
?>

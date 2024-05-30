<?php

require_once('libs/auth.php');
require_once('libs/db.php');

// Start output buffering to prevent premature output
ob_start();

// Set the content type to JSON
header('Content-Type: application/json');

// Initialize user ID from session, override if user_id is provided via GET
$user_id = isset($_GET['user_id'])? $_GET['user_id'] : $_SESSION['user_id'];

// Check if an action is specified
if (!isset($_GET['action'])) {
    die(json_encode(['error' => 'No action specified']));
}

$response = [];

try {
    switch ($_GET['action']) {
        case 'list_posts':
            $response = select_all('SELECT post_id, public FROM posts WHERE author_id =?', $user_id);
            break;

        case 'read':
            if ($_SESSION['user_id']!= $user_id) {
                throw new Exception('User ID mismatch');
            }

            $post_id = $_GET['id'];
            $post = select_one('SELECT content, public, author_id, file_path FROM posts WHERE post_id =? AND (public = 1 OR author_id =?)', $post_id, $user_id);

            if (!$post) {
                throw new Exception('Not Found');
            }

            $response = $post;
            break;

        case 'create':
            if ($_SESSION['user_id']!= $user_id) {
                throw new Exception('User ID mismatch');
            }

            $content = $_POST['content'];
            $public = $_POST['public'];
            $post_id = generate_id();
            $file_path = handle_file_upload();

            $query = 'INSERT INTO posts (post_id, content, public, author_id, file_path) VALUES (?,?,?,?,?)';
            $params = [$post_id, $content, $public, $user_id, $file_path];

            exec_query($query,...$params);

            header('Refresh:2; url=wall.php'); // Redirect to wall.php after 2 seconds
            $response = ['success' => 'Post created'];
            break;

        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    $response = ['error' => $e->getMessage()];
}

// Flush the output buffer and send the response
echo json_encode($response);
ob_end_flush();

function handle_file_upload() {
    global $user_id;
    $file_path = null;

    if (!empty($_FILES['file']['name'])) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['file']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if (in_array($ext, $allowed)) {
            $upload_dir = "uploads/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $new_filename = uniqid(). '.'. $ext;
            $full_path = $upload_dir. $new_filename;

            if (move_uploaded_file($_FILES['file']['tmp_name'], $full_path)) {
                $file_path = $full_path;
            } else {
                throw new Exception('File upload failed');
            }
        } else {
            throw new Exception('Invalid file type');
        }
    }

    return $file_path;
}

// Debugging output
var_dump($response);

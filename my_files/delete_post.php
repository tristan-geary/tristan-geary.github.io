<?php

session_start();


/*Responsibility: accept POST requests from logged-in users.  Reads posts from blog_posts.JSON, removes the 
matching post and writes back.  Responds w/ a json object that the front-end javascript can interpret.

*/

///logged in session
$is_logged_in = isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;

///all responses from this script are JSON 
header('Content-Type: application/json');

///logged in check, if not authorized: returns an error
if (!$is_logged_in) {
	
    echo json_encode([
        'success' => false,
        'error'   => 'Not authorized.'
    ]);
	
    exit;
}

/*I do a request method check because deletion must only happen through post requests, not random get requests
If they method isn't POST, it returns an error*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	
    echo json_encode([
        'success' => false,
        'error'   => 'Invalid request method.'
    ]);
	
    exit;
}

/*I require an ID parameter in POST body to know which post to delete*/
/*if missing, I can't proceed*/
if (!isset($_POST['id']) || $_POST['id'] === '') {
	
    echo json_encode([
        'success' => false,
        'error'   => 'Missing post id.'
    ]);
	
    exit;
}

///the id of the post we are supposed to delete
$post_id = $_POST['id'];

///going to the json file
$json_path = __DIR__ . '/blog_posts.json';


///check for json file (if it exists)
if (!file_exists($json_path)) {
	
    echo json_encode([
        'success' => false,
        'error'   => 'JSON file not found.'
    ]);
	
    exit;
}
///load and decode JSON
$json_content = file_get_contents($json_path);


///validate json structure
$data = json_decode($json_content, true);

if (!isset($data['posts']) || !is_array($data['posts'])) {
	
    echo json_encode([
        'success' => false,
        'error'   => 'Invalid JSON structure.'
    ]);
	
    exit;
}

///filter out post w/ matching id
$original_count = count($data['posts']);

$data['posts'] = array_values(array_filter($data['posts'], function ($post) use ($post_id) {
    return !isset($post['id']) || $post['id'] !== $post_id;
}));

$new_count = count($data['posts']);

///save updated file
file_put_contents(

    $json_path,
    json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
    LOCK_EX
);

///if not found still call it a success so UI isn't broken
if ($new_count === $original_count) {
	
    echo json_encode([
        'success' => true,
        'warning' => 'Post not found, nothing deleted.'
    ]);
} else {
	
    echo json_encode([
        'success' => true
    ]);
}

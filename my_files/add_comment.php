<?php

session_start();

/* what does this code do?: it is reponsible for adding new comments under respective
blog posts. it validates and santizes (cleans user input so its safe to store/display).
It locates the correct post inside blog_posts.json, appends the new comment and then writes
the updated JSON back to the disc*/


///JSON file for blog posts/comments
/*calling this path allows me to store all the comments in a single JSON 
file rather than using a database.  By using DIR it makes my path correct even if 
the current directory changes*/

$json_path = __DIR__ . '/blog_posts.json';

///only accept POST requests
/*comments should only be created by a POST request (submission). 
If someone tries to access this script directly via GET, I immediately 
redirect them back to blog.php to avoid accidental/malicious uses*/
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: blog.php');
    exit;
}


///get form inputs
$post_id      = isset($_POST['post_id']) ? $_POST['post_id'] : '';
$raw_name     = isset($_POST['name']) ? $_POST['name'] : '';
$raw_comment  = isset($_POST['comment']) ? $_POST['comment'] : '';
/* I read the raw form fields form POST requests for: 
- post id: idenifies which blog post to attach the comment to
-name: optional commenter name
-comment: Required comment body

I use the ternary pattern to avoid undefined index notices if a key is missin*/

///trimming comment text for empty check
$raw_comment = trim($raw_comment);
/*trimming whitespace from the comment so that a user cannot submit only whitespaces
and bypass a required check*/

///if no post id or empty comment, go back
if ($post_id === '' || $raw_comment === '') {
	
    header('Location: blog.php');
	
    exit;
}

///sanitize name/comment
$safe_name = trim(strip_tags($raw_name));
$safe_comment = trim(strip_tags($raw_comment));
/* using strip_tags() allows me to remove any HTML tags fromt the name and body. 
This avoids users injecting HTML or JS into the page, I also trim here for any whitespace*
references: strip_tags(): https://www.php.net/manual/en/function.strip-tags.php
trim(): https://www.php.net/manual/en/function.trim.php
*/

///if file is missing we can't attach comment
if (!file_exists($json_path)) {
    
    header('Location: blog.php');
    exit;
}
///load JSON data
$json  = file_get_contents($json_path);
$data  = json_decode($json, true);
/*references: file_get_contents(): https://www.php.net/manual/en/function.file-get-contents.php
json_decode(): https://www.php.net/manual/en/function.json-decode.php
*/

///ensure base structure
if (!is_array($data)) {
    $data = [];
}
if (!isset($data['posts']) || !is_array($data['posts'])) {
    $data['posts'] = [];
}

///find index of matching post
$posts = $data['posts'];
$index = -1;

foreach ($posts as $i => $post) {
    if (isset($post['id']) && $post['id'] === $post_id) {
		
        $index = $i;
		
        break;
    }
}

///if no post found, go back
if ($index === -1) {
	
    ///post not found
    header('Location: blog.php');
	
    exit;
}

/*make sure comments array exists for this post
why?: older posts may not have comments key yet.  I create an empty array if necessary so that I 
can safely append to it*/

if (!isset($posts[$index]['comments']) || !is_array($posts[$index]['comments'])) {
	
    $posts[$index]['comments'] = [];
}

///build new comment entry
///date(): https://www.php.net/manual/en/function.date.php
$new_comment = [
    'name' => $safe_name,
    'text' => $safe_comment,
    'date' => date('Y-m-d')
];

///add comment to post
$posts[$index]['comments'][] = $new_comment;

///save updated posts back into data
$data['posts'] = $posts;

/*This following code writes data back to the JSON file
why?: I put the modified posts array back into data['posts'] and the re-encode It
the entire structure JSON format.  JSON pretty print makes the file easier to read/edit by hand, while
JSON unescaped unicode keep unicode characters readable*/
///references: json_encode():   https://www.php.net/manual/en/function.json-encode.php
/// file_put_contents(): https://www.php.net/manual/en/function.file-put-contents.php
file_put_contents(
    $json_path,
    json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
);

///redirect back to blog page
header('Location: blog.php#' . urlencode($post_id));

exit;


?>
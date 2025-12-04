<?php
session_start();

///same login sessions
$is_logged_in = isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;

///redirect if not logged in
if (!$is_logged_in) {
    header('Location: login.php');
    exit;
}

//still keep blog highlighted
$current_page = 'blog';

$json_path = __DIR__ . '/blog_posts.json';

///for validation errors
$errors = [];

///form errors when error occurs
$title_value = '';
$content_value = '';

///form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    ///getting raw input
    $raw_title   = isset($_POST['title'])   ? $_POST['title']   : '';
    $raw_content = isset($_POST['content']) ? $_POST['content'] : '';

    ///getting rid of white space
    $title_value   = trim($raw_title);
    $content_value = trim($raw_content);

    //validating that both fields are filled out
    if ($title_value === '') {
        $errors[] = "Title is required.";
    }
	
    if ($content_value === '') {
        $errors[] = "Content is required.";
    }

    if (empty($errors)) {
        // SANITIZATION:
        $safe_title   = trim(strip_tags($title_value));
        $safe_content = trim(strip_tags($content_value));

        ///split content into paragraphs and trim each one and remove any empty entries
        $paragraphs = preg_split("/\r?\n\s*\r?\n/", $safe_content);
        $paragraphs = array_map('trim', $paragraphs);
        $paragraphs = array_filter($paragraphs, function ($p) {
            return $p !== '';
        });

        ///one paragraph if no blank lines
        if (empty($paragraphs)) {
            $paragraphs = [$safe_content];
        }

        //load json files unless there aren't any, then just start from scratch
        $posts_data = ['posts' => []];

        if (file_exists($json_path)) {
            $existing_json = file_get_contents($json_path);
            $decoded = json_decode($existing_json, true);
			
            if (isset($decoded['posts']) && is_array($decoded['posts'])) {
                $posts_data['posts'] = $decoded['posts'];
            }
        }

        //id, timestamp
        $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $safe_title));
        $slug = trim($slug, '-');
		
        if ($slug === '') {
            $slug = 'post';
        }
        $id = $slug . '-' . time();

        //new post structure
        $new_post = [
            'id'         => $id,
            'title'      => $safe_title,
            'date'       => date('Y-m-d'),
            'paragraphs' => array_values($paragraphs)
        ];

        ///add the new post to the blog
        $posts_data['posts'][] = $new_post;

        ///save to json
        file_put_contents(
            $json_path,
            json_encode($posts_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        //redirct to blog page
        header('Location: blog.php#' . urlencode($id));
		
		
		
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add new blog post</title>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="my_style.css">
	
    <script src="nav_menu.js"></script>
	
	
    <!-- Auto-save draft logic -->
    <script src="add_post.js" defer></script>
</head>
<body>
    <div class="body_wrapper">

        <?php include 'includes/nav.php'; ?>

        <main class="page-body">

            <h1>Add a new blog post</h1>

            <?php if (!empty($errors)): ?>
                <div class="error-messages">
                    <ul>
                        <?php foreach ($errors as $e): ?>
                            <li><?php echo htmlspecialchars($e); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" action="add_post.php">
                <fieldset>
                    <legend>New blog post</legend>

                    <p>
                        <label for="title">Title:</label><br>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            value="<?php echo htmlspecialchars($title_value); ?>"
                            required
                        >
                    </p>

                    <p>
                        <label for="content">Content:</label><br>
                        <!-- mutliparagraph posts -->
                        <textarea
                            id="content"
                            name="content"
                            rows="10"
                            cols="60"
                            required
                        ><?php echo htmlspecialchars($content_value); ?></textarea>
                    </p>

                    <p>
                        <input type="submit" value="Save post">
                        <!-- save draft -->
                        <button type="button" id="save-draft-button">Save draft</button>
                        <a href="blog.php">Cancel</a>
                    </p>
                </fieldset>
            </form>

        </main>

        <?php include 'includes/footer.php'; ?>

    </div>
</body>
</html>

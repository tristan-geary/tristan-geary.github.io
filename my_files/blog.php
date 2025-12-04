<?php
session_start();

/*
why does this file exist?: 
this is my blog page. It contains the content for the blog.  It helps determine
if the user is logged in, and shows the posts based on that information.  It uses a JSON file to 
load the posts.

I decided to load php-related things at the top to make it have nicer presentation and to keep the code more 
separated (logic at the top, view at the bottom), which makes things easier to find and understand.
*/

/* 

This PHP block is necessary for determining if the user is logged in, highlight the page in the nav, and loading
the JSON file content (posts) **if it exists**.  If not it displays a message saying that there is no 
content in the JSON file*/

//using same session for logged in user
$is_logged_in = isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;

//current page highlighting
$current_page = 'blog';

// loading posts from json
$json_path = __DIR__ . '/blog_posts.json';
$posts = [];

if (file_exists($json_path)) {
    $json_content = file_get_contents($json_path);
    $data = json_decode($json_content, true);

    if (isset($data['posts']) && is_array($data['posts'])) {
        $posts = $data['posts'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>

    <title>Blog</title>

    <!-- Metadata -->
    <meta name="author" content="Tristan Geary">
    <meta name="description" content="Blog for lab">
    <meta charset="UTF-8">
    <meta name="keywords" content="html, blog, Tristan Geary">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Stylesheet -->
    <link rel="stylesheet" id="theme-stylesheet" href="my_style.css">

    <!-- nav javascript -->
    <script src="nav_menu.js"></script>


    <!-- calling blog javascript -->
    <script src="blog.js"></script>

</head>
<body>
    <div class="body_wrapper">

		<!--calling on the nav-->
        <?php include 'includes/nav.php'; ?>

        <main class="page-body blog-page">

            <!-- Hero section -->
            <section class="hero" id="blog-hero">

                <!-- logging in -->
                <div class="hero-login-row">
				<!--this is determining if the user is logged in and if its from the blog, 
				if so then show the logged in status as true-->
                    <?php if (!$is_logged_in): ?>
                        <a href="login.php?from=blog" class="hero-login-button">Login</a>
                    <?php else: ?>
                        <span class="hero-login-status">Logged in</span>
                    <?php endif; ?>
                </div>

				<!--hero content-->
                <div class="hero-content">
                    <h1>Tristan’s CS203 Blog</h1>
                    <p>
                        My Blog where I post about things that I'm doing in my CS203 class.  
                    </p>
                    <p class="hero-tagline">
                        We've done a lot over the semester, and this is the final lab!
                    </p>


					<!--showing add new post only if logged in -->
                    <?php if ($is_logged_in): ?>
                        
                        <div class="blog-actions">
                            <a class="add-post-button" href="add_post.php">+ Add new post</a>
                        </div>
                    <?php endif; ?>

                    <!-- switch to dark theme -->
                    <div class="blog-theme-toggle">
                        <button type="button" id="theme-toggle-button">
                            Switch Theme
                        </button>
                    </div>
                </div>
            </section>

            <!-- main content/aside.  I keep everything in these blocks so that I can 
			style the CSS a little easier-->
            <div class="blog-content-wrapper">

                <!-- blog posts from the json file -->
                <section class="blog-main" id="blog-main">
                    <?php
						/*this extracts each individual field w/ safe fall backs.  Why?:this prevents undefined index warnings if the 
						JSON file is missing some keys or is only partially filled*/
					if (!empty($posts)): ?>
                        <?php foreach ($posts as $post): ?>
                            <?php
							
                                $id    = isset($post['id']) ? $post['id'] : '';
                                $title = isset($post['title']) ? $post['title'] : 'Untitled post';
                                $date  = isset($post['date']) ? $post['date'] : '';
                                $paras = isset($post['paragraphs']) && is_array($post['paragraphs'])? $post['paragraphs']: [];
                                $comments = isset($post['comments']) && is_array($post['comments'])? $post['comments']: [];
								
                            ?>
							
							<!--printing the blog posts from php-->
                            <article class="blog-post" id="<?php echo htmlspecialchars($id); ?>">
                                
								<div class="blog-post-header">
                                    <div>
                                        <h2><?php 
										/*I use this because each blog post is wrapped in an article w/ an id
										that matches the post.  The id attribute lets the sidebar links scroll to the right
										post.*/
										
										echo htmlspecialchars($title); ?></h2>

                                        <?php if ($date !== ''): ?>
                                            <p class="blog-date"><?php echo htmlspecialchars($date); ?></p>
                                        <?php endif; ?>
                                    </div>
									
									<!--allowing them to edit/delete if logged in-->
                                    <?php if ($is_logged_in && $id !== ''): ?>
                                        <div class="blog-post-actions">
										<!--POST ACTIONS. why am i checking if they are logged in? users must be logged in to Edit
										or delete posts, so this serves that purpose.  edit uses astandard link w/ the post id, whereas
										dlete uses a button with a data post id attribute so that blog.js can attach a click handler and decide how to handle deletion
										-->
                                            <!-- Edit button -->
                                            <a
                                                href="edit_post.php?id=<?php echo urlencode($id); ?>"
                                                class="edit-post-button"
                                            >
                                                Edit
                                            </a>

                                            <!-- Delete button-->
                                            <button
                                                type="button"
                                                class="delete-post-button"
                                                data-post-id="<?php echo htmlspecialchars($id); ?>"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Collapsible into 'read more'
								blog.js toggles this class when the user clicks read more.  i use nl2br/htmlspecialchars
								to safely display text while keeping any line breaks from the original content-->
                                <div class="blog-post-content collapsed">
								
								
                                    <?php foreach ($paras as $p): ?>
                                        <p><?php echo nl2br(htmlspecialchars($p)); ?></p>
                                    <?php endforeach; ?>
									
									
                                </div>
								<!--triggering .js file-->
                                <button type="button" class="toggle-content-button">
                                    Read More
                                </button>

                                <!-- commenting on posts.
								this block shows existing comments and a form for new comments. 
								its nested inside an article so comments can clearly belong to
								their specific posts-->
                                <section class="comments-section">
								
								
                                    <h3>Comments</h3>

                                    <?php if (!empty($comments)): ?>
                                        <ul class="comment-list">
                                            <?php foreach ($comments as $comment): ?>
                                                
												<?php
												/* normalize comment field and set a default name.  Why?: this ensures that the page still renders
												nicely even it some comments are missing fields in the json*/
                                                    $c_name = isset($comment['name']) ? $comment['name'] : '';
                                                    $c_text = isset($comment['text']) ? $comment['text'] : '';
                                                    $c_date = isset($comment['date']) ? $comment['date'] : '';
                                                    if ($c_name === '') {
                                                        $c_name = 'Anonymous';
                                                    }
                                                ?>
												
                                                <li class="comment">
                                                    <p class="comment-meta">
                                                        <strong><?php echo htmlspecialchars($c_name); ?></strong>
                                                        <?php if ($c_date !== ''): ?>
                                                            <span class="comment-date">
                                                                (<?php echo htmlspecialchars($c_date); ?>)
                                                            </span>
                                                        <?php endif; ?>
                                                    </p>
                                                    <p class="comment-text">
                                                        <?php echo nl2br(htmlspecialchars($c_text)); ?>
                                                    </p>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p class="no-comments">No comments, comment to be the first.</p>
                                    <?php endif; ?>

                                    <!-- Comment form.  Why?: this form posts to add_comment.php, which acts as a controller for adding
									comments to json model.  The hidden post id ties the comment to the correct blog post & I require the comment text
									but leave the name optional to allow anonymous comments	-->
									
                                    <form class="comment-form" method="post" action="add_comment.php">
                                        <input type="hidden" name="post_id"
										
                                               value="<?php echo htmlspecialchars($id); ?>">

                                        <p>
                                            <label for="name-<?php echo htmlspecialchars($id); ?>">
                                                Name (optional):
                                            </label><br>
                                            <input type="text"
                                                   id="name-<?php echo htmlspecialchars($id); ?>"
                                                   name="name">
                                        </p>

                                        <p>
                                            <label for="comment-<?php echo htmlspecialchars($id); ?>">
                                                Comment:
                                            </label><br>
											
                                            <textarea
                                                id="comment-<?php echo htmlspecialchars($id); ?>"
                                                name="comment"
                                                rows="3"
                                                required
                                            ></textarea>
											
                                        </p>

                                        <p>
                                            <input type="submit" value="Submit Comment">
                                        </p>
                                    </form>
                                </section>
                            </article>
                        <?php endforeach; ?>
						
					<!--if no posts in the blog-->
                    <?php else: ?>
                        <article class="blog-post">
                            <h2>No posts yet</h2>
                            <p>
                                It looks like the blog has no posts loaded from the JSON file.
                                Once <code>blog_posts.json</code> is created and filled, they will appear here
                            </p>
                        </article>
                    <?php endif; ?>
                </section>

                <!--aside -->
				<!--this sidebar gives quick access to search, sort, and limit how many posts are shown
				and a list of links to each post-->
				
                <aside class="blog-sidebar">
                    <h2>Posts</h2>

                    <!-- controls -->
                    <form id="blog-controls" onsubmit="return false;">
                        <p>
							<!--searching-->
                            <label for="search-input">Search:</label><br>
                            <input type="text" id="search-input" name="search">
                        </p>

                        <p>
						
						<!--sort by-->
                            <label for="sort-select">Sort by:</label><br>
                            <select id="sort-select" name="sort">
                                <option value="date_desc">Date (newest first)</option>
                                <option value="date_asc">Date (oldest first)</option>
                                <option value="title_asc">Title (A–Z)</option>
                                <option value="title_desc">Title (Z–A)</option>
                            </select>
                        </p>

                        <p>
							<!--how many posts to show-->
                            <label for="limit-select">Posts to show:</label><br>
                            <select id="limit-select" name="limit">
                                <option value="all">All</option>
                                <option value="3">3</option>
                                <option value="5">5</option>
                            </select>
                        </p>
                    </form>
					
					
					<!--sidebar post list:
					why?: this list mirrors the main posts and provides quick anchor links to jump
					direclty to a specific post.  it uses the same posts array to stay in sync
					with the main section-->
					<!--displaying posts in aside-->
                    <?php if (!empty($posts)): ?>
                        <ul class="blog-post-list">
                            <?php foreach ($posts as $post): ?>
                                <?php
                                    $id    = isset($post['id']) ? $post['id'] : '';
                                    $title = isset($post['title']) ? $post['title'] : 'Untitled post';
                                ?>
                                <?php if ($id !== ''): ?>
								
                                    <li data-post-id="<?php echo htmlspecialchars($id); ?>">
                                        <a href="#<?php echo htmlspecialchars($id); ?>">
                                            <?php echo htmlspecialchars($title); ?>
                                        </a>
										
                                    </li>
									
                                <?php endif; ?>
								
                            <?php endforeach; ?>
							
                        </ul>
                    <?php else: ?>
						<!--no posts-->
                        <p>No posts to list yet.</p>
                    <?php endif; ?>
                </aside>

            </div> 

        </main>
		
		<!--footer-->
        <?php include 'includes/footer.php'; ?>

    </div> 
</body>
</html>

<?php

	session_start();

		
		/*
		What this does: acts as a controller and a view for editing an existing blog Post
		
		On GET: validates post id from query string, loads the matching post from blog_posts.json, 
		prefills a form w/ the current post title and content.
		
		on POST: validates the edited title/content, sanitizes them, converts the content back  to paragraph, 
		arrays, saves changes to JSON file, redirects back to blog page at updated POST
		*/

		
		///logged in session
		$is_logged_in = isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;
		if (!$is_logged_in) {
			header('Location: login.php');
			exit;
		}

		///highlight blog
		$current_page = 'blog';

		$json_path = __DIR__ . '/blog_posts.json';

		///current post data/form values
		$post_id       = '';
		$title_value   = '';
		$content_value = '';
		$date_value    = '';
		$errors        = [];

		///load all posts from json
		/*reads all the posts from the JSON file, and returns a consisten structure.
			Why?: I wrap JSON reading logic in a function to avoid repeating the same checks
			in multiple files and to keep the main control flow cleaner
		*/
		function load_all_posts($path) {
			if (!file_exists($path)) {
				return ['posts' => []];
			}
			$json = file_get_contents($path);
			$data = json_decode($json, true);
			if (!is_array($data)) {
				$data = [];
			}
			if (!isset($data['posts']) || !is_array($data['posts'])) {
				$data['posts'] = [];
			}
			return $data;
		}

		/*finding post by id
		
		what it does: given an array of posts an id, return the numeric index of the matching post.
		
		Why: I use a simple linear search because the number of posts is small in this content.
		Returning the index makes it easy to update the post directly in the array
		*/
		
		function find_post_index_by_id($posts, $id) {
			foreach ($posts as $index => $post) {
				if (isset($post['id']) && $post['id'] === $id) {
					return $index;
				}
			}
			return -1;
		}

		///determining post id
		if ($_SERVER['REQUEST_METHOD'] === 'GET') {
			$post_id = isset($_GET['id']) ? $_GET['id'] : '';
		} else {
			$post_id = isset($_POST['id']) ? $_POST['id'] : '';
		}

		if ($post_id === '') {
			$errors[] = "Missing post id.";
		} else {
			
			//load all posts
			$data  = load_all_posts($json_path);
			$posts = $data['posts'];
			$idx   = find_post_index_by_id($posts, $post_id);

			if ($idx === -1) {
				
				$errors[] = "Post not found.";
				
			} else {
				
				
				///if this is the first time, fill all fields
				if ($_SERVER['REQUEST_METHOD'] === 'GET') {
					/*inital form display (GET)
					
					why?: for the first visit, I take the existing post data and convert it
					into simple strings that can populate the form fields.  Paragraphs are joined w/ blank
					lines so the user can edit the full content as one block of text*/
					
					$post = $posts[$idx];

					$title_value = isset($post['title']) ? $post['title'] : '';
					$date_value  = isset($post['date'])  ? $post['date']  : '';

					///combine paragraphs
					$paragraphs = isset($post['paragraphs']) && is_array($post['paragraphs'])
						? $post['paragraphs']
						: [];

					if (!empty($paragraphs)) {
						$content_value = implode("\n\n", $paragraphs);
					} else {
						$content_value = '';
					}

				} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
					
					/*form submission: POST
					why: when the user submits the form, I validate and sanitize the new 
					values before saving back to the JSON file*/
					
					///handle form submission
					$raw_title   = isset($_POST['title'])   ? $_POST['title']   : '';
					$raw_content = isset($_POST['content']) ? $_POST['content'] : '';

					$title_value   = trim($raw_title);
					$content_value = trim($raw_content);

					if ($title_value === '') {
						$errors[] = "Title is required.";
						
					}
					
					if ($content_value === '') {
						
						$errors[] = "Content is required.";
						
					}

					if (empty($errors)) {
						
						/*sanitize the input: 
						why: 
						strip_tags() removes any html tags so users cannot inject markup/script
						trim(): removes extra whitespace from the start/end
						
						references: strip_tags(): https://www.php.net/manual/en/function.strip-tags.php
									trim():https://www.php.net/manual/en/function.trim.php
						*/
						
						$safe_title   = trim(strip_tags($title_value));
						$safe_content = trim(strip_tags($content_value));

						
						/*split content into paragraphs
						why: in the JSON: the post body is stored as an array of paragraphs.
						in the form, the user edits all content as one big text area.
						Here I split the text area into paragraphs wherever there are one or more
						blank lines
						
						reference: 
						preg_split(): https://www.php.net/manual/en/function.preg-split.php
						array_map():  https://www.php.net/manual/en/function.array-map.php
						array_filter(): https://www.php.net/manual/en/function.array-filter.php
						*/
						
						$paragraphs = preg_split("/\r?\n\s*\r?\n/", $safe_content);
						$paragraphs = array_map('trim', $paragraphs);
						$paragraphs = array_filter($paragraphs, function ($p) {
							return $p !== '';
						});

						if (empty($paragraphs)) {
							$paragraphs = [$safe_content];
						}

						///update post
						$post = $posts[$idx];

						$post['title'] = $safe_title;
						
						$post['paragraphs'] = array_values($paragraphs);

						//save back into  array
						$posts[$idx]   = $post;
						$data['posts'] = $posts;

						//Write JSON back to file
						file_put_contents(
							$json_path,
							json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
						);

						// Redirect back to the blog, scrolled to that post
						header('Location: blog.php#' . urlencode($post_id));
						
						
						exit;
					}
				}
			}
		}
		?>
		<!DOCTYPE html>
		<html>
		<head>
			<title>Edit blog post</title>


			<!--metadata, don't need everything from other files-->
			<meta charset="UTF-8">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			
			
			<!-- style sheet-->
			<link rel="stylesheet" href="my_style.css">
			
			<!--nav javascript-->
			<script src="nav_menu.js"></script>
			
			
		</head>
		
		
		<body>
			<div class="body_wrapper">

				<!--nav menu-->
				<?php include 'includes/nav.php'; ?>

				<main class="page-body">

					<h1>Edit blog post</h1>


					<!--show error messages-->
					<?php if (!empty($errors)): ?>
					
						<div class="error-messages">
						
							<ul>
							
								<?php foreach ($errors as $e): ?>
								
									<li><?php echo htmlspecialchars($e); ?></li>
									
								<?php endforeach; ?>
							</ul>
							
						</div>
						
					<?php endif; ?>
					
					<!--get request w/ valid id and no errors shows prefilled form-->
					
					<?php if ($post_id !== '' && empty($errors) && $_SERVER['REQUEST_METHOD'] === 'GET'): ?>
						
						<form method="post" action="edit_post.php">
						
							<fieldset>
							
								<legend>Edit post</legend>

								<!-- Hidden field to keep the post id  to track which post is being edited-->
								<input type="hidden" name="id" value="<?php echo htmlspecialchars($post_id); ?>">

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
									
									<textarea
										id="content"
										name="content"
										rows="10"
										cols="60"
										required
									>
									<?php echo htmlspecialchars($content_value); ?></textarea>
								</p>

								<p>
									<input type="submit" value="Save changes">
									
									<!--link back to blog-->
									<a href="blog.php#<?php echo htmlspecialchars($post_id); ?>">Cancel</a>
									
								</p>
								
							</fieldset>
							
						</form>
					
					<!--post request w/ errors, resiplay w/ user input-->
					<?php elseif ($post_id !== '' && $_SERVER['REQUEST_METHOD'] === 'POST' && !empty($errors)): ?>
						
						<form method="post" action="edit_post.php">
							
							<fieldset>
							
								<legend>Edit post</legend>

								<input type="hidden" name="id" value="<?php echo htmlspecialchars($post_id); ?>">

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
									
									<textarea
									
										id="content"
										name="content"
										rows="10"
										cols="60"
										required
										
									>
									<?php echo htmlspecialchars($content_value); ?></textarea>
									
								</p>

								<p>
									<input type="submit" value="Save changes">
									
									<a href="blog.php#<?php echo htmlspecialchars($post_id); ?>">Cancel</a>
								</p>
								
							</fieldset>
							
						</form>
						
					<?php endif; ?>

				</main>

				<?php include 'includes/footer.php'; ?>

			</div>
			
			
		</body>
		
</html>

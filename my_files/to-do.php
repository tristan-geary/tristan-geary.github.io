<?php 

	include "includes/config.php";
	session_start();
	if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: login.php");
    exit();
	}
	
	
	$current_page = 'to-do'; 
	
	$username = $_COOKIE["to-do-username"]?? "Guest";
?>
<!DOCTYPE html>

<html>
<head>
	
	<!--metadata-->
	<meta name="author" content="Tristan Geary">
	<meta name="Description" content="to-do list">
	<meta name="keywords" content="html">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<title>To-Do List</title>
	
	<!--css stylesheet-->
	<link rel="stylesheet" href="my_style.css">
	<!--link to font-awesome-->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
	
	<script src="nav_menu.js"></script>
</head>

<body>
	
	<!--main container wrapping entire content-->
	<div class="body_wrapper">
	
	
	
	<?php include 'includes/nav.php'; ?>	
	
	<form action="login.php" method="POST" style="position: relative;">
		<button type="submit" name="logout" value="true" style="position: absolute; top: 10px; right: 10px;">
		Log out</button>
	</form>
			
	<!--main content area-->
	<main class="page-body">
	
		<h1>Welcome Back, <?php echo $username;?>!</h1>
		<div class="todo-wrapper">
			<br><h1>My To-do List</h1>
			
			<!--form for to do items-->
			<form id="todo-form">
				<input id="new-item" type="text" value="Enter a new to-do item">
				<button id="add_button" type="submit">Add to list</button>
			</form>
			
			<!--unordered list to display items-->
			<ul id="todo-list"></ul>
		</div>
	</main>
	
	<?php include 'includes/footer.php' ?>
	
	</div>
	
	<script>
			
		// Load saved items from localStorage
		let items = JSON.parse(localStorage.getItem("items")) || [];
		
		const form = document.getElementById("todo-form");
		const input = document.getElementById("new-item");
		const list = document.getElementById("todo-list");
		
		//form submission handling
		form.addEventListener("submit", function(e) { 
			
			e.preventDefault();
			const text = input.value.trim();
			if(!text){
				window.alert("Please enter a to-do item"); 
				return; 
			}
			

			// Save in storage
			const newItem = {
				text: text,
				id: Date.now()  // unique timestamp-based id
			};
			
			items.push(newItem);
			localStorage.setItem("items", JSON.stringify(items));
			
			renderItem(newItem.text, newItem.id); 
			input.value = ""; 
			input.focus(); 
		}); 

		//render the full list from array
		function renderList(){
			list.innerHTML = ""; 
			items.forEach(item => renderItem(item.text, item.id));
		 }
		 
		//render a single to do item 
		function renderItem(item_text, id){
			const li = document.createElement("li"); 
			//store id for later reference
			li.dataset.id = id; 
			
			const spanText = document.createElement("span"); 
			spanText.className = "text"; 
			spanText.textContent = item_text; 
			
			const trash = document.createElement("span"); 
			trash.className = "trash fas fa-trash"; 
			trash.setAttribute("role", "button"); 
			trash.setAttribute("aria-label", "Delete item"); 
			
			//click handler to delete item
			trash.addEventListener("click", () => { 
				li.remove(); 
				const numericId= Number(li.dataset.id); 
				items = items.filter(x => x.id !==numericId); 
				localStorage.setItem("items", JSON.stringify(items));
			});
			
			li.appendChild(spanText); 
			li.appendChild(trash); 
			list.appendChild(li); 
		}
	
		renderList();

	</script>
</body>


</html>
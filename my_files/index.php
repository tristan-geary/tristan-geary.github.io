<!DOCTYPE html>
<html>
    <head>
	
		<!--linking javascript nav.js-->
		<script src="nav.js"></script>
		
			
			
        <title>Tristan Geary</title>
		
		<!-- Metadata -->
		<meta name="author" content="Tristan Geary">
		<meta name="description" content="lab3 assignment index.html page">
		<meta charset="UTF-8">
		<meta name="keywords" content="html">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		
		<!-- defining style for table -->
		<style>
			table, 
			th, 
			td{border: 1px solid black; 
			}
		</style>
		
		<!-- linking external css sheet-->
		<link rel="stylesheet" href="my_style.css">
		
		
    </head>
	
    <body>
	
	
		
		
		<div class="body_wrapper">
		
		<main class="page-body">
		
		<!--calling the nav-->
		<nav id="main-nav"></nav>
		<script>
			const current_path = location.pathname;
			setNav(current_path);
		</script>

		
		<br><br><br>
		
		
		<div>
		<h1><b>Tristan Geary: Undergraduate Student</b></h1>
		
		<!-- horizontal ruler -->
		<hr>
		
        <h2>Major: Neuroscience</h2>
		
		<h2>Minor: Computer Science </h2>
		</div>
		
		<hr>
		
		<div>
		<!-- fun fact -->
		<p>Fun Fact:I was originally a biology and business student, but <br> switched
		because it didn't align with my goals</p>
		</div>
		
		<hr>
        
		<!-- career goals section -->
		<div>
		<h3><em>Career Goals:</em></h3>
		
		<!-- unordered list -->
		<ul>
		<li><p>I hope to go to <strong> medical school </strong> and become a <br> <b>doctor</b>
		(I am currently applying to medical schools this semester).</p></li>
		<li><p>I hope to do a lot of medically-related research.</p></li>
		<li><p>Currently, I want to become a <b>surgeon</b>.</p></li>
		</ul>
		
		</div>
		
		<hr>
		
		
		
		<!--Link to dream vacation website -->
		<div>
		<p><em><b>My Dream Vacation: </b></em></p><a href="my_vacation.html">My Dream Vacation</a>
		</div>
		
		
		<!--Link to My Artistic self website-->
		<div>
		<p><em><b>My Artistic Self:</b></em></p>
		<a href="my_artistic_self.html">My Artistic Self Page</a>
		</div>
		
		
		
		
		
		
		
		
		
		
		<div class="slideshow">
			
			<!--running image-->
			<div class="slideshow_img">
				
				<img src="images/running.jpg" alt="Running image" >
			
			</div>
			
			<!--steelers image-->
			<div class="slideshow_img">
				
				<img src="images/steel_curtain.jpg" alt="Steeler's steel curtain">
			
			</div>
			
			<!--medicine image-->
			<div class="slideshow_img">
		
				<img src="images/medicine.jpg" alt="Medicine image">
			
			</div>
				
			<!--next and prev buttons-->
			<a id="prev" onclick="previous()">Previous</a>
			<a id="next" onclick="next()">Next</a>
			
		</div>
		
		</main>
		
		<footer class="site-footer">
			This is for my CS203 Lab.
		</footer>
		
		
		
		<script>
			let current_slide = 0; 
			showSlide(current_slide); 
			
			//function to go to previous image
			function previous(){
				current_slide--;
				const slides = document.getElementsByClassName("slideshow_img");
				//handling logic case if we go under 0
				if(current_slide < 0){
				
				current_slide = slides.length - 1; 
			}
				showSlide(current_slide);
			}
			
			//function for next image
			function next(){
				current_slide++;
				const slides = document.getElementsByClassName("slideshow_img");
				//handling logic case if we go over the amount of slides
				if(current_slide >= slides.length){
					current_slide=0;
				}
				
				showSlide(current_slide); 
			}
			//function to show slide
			function showSlide(n){
				const slides = document.getElementsByClassName("slideshow_img");
				
				for(let i=0; i<slides.length; i++){
					
					///hide it
					slides[i].style.display = "none"; 
				}
					///show it
					slides[n].style.display = "block"; 
			
			}
			
			
		</script>
		
    </body>
	
</html>

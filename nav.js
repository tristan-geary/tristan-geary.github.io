
//Return a path
function splitAtRoot(path){
	//create URL object based on the path and its origin
    const url = new URL(path, location.origin);
	//return only pathname
    return url.pathname;
}

//load nav bar from nav.html
function setNav(current_path){

	//convert path into normal pathname
	current_path = splitAtRoot(current_path);
	
	//load the nav.html file
	fetch("nav.html")
	
		//convert response to text
		.then(r => r.text())
		.then(html => {
			
			//insert nav html inot the tag w/ nav id element
			document.getElementById("main-nav").innerHTML = html;
			
			//store reference to nav container
			const nav = document.getElementById("main-nav");
		
			//loop through each child element in nav bar
			for (let child of nav.children){
				//only process links
				if (child instanceof HTMLAnchorElement) {
					
					//get path name for each link
					const child_path = splitAtRoot(child.href); 
					
					//check if link corresponds to current page
					const isCurrentPage = 
						child_path === current_path ||
						(current_path === "/" && child_path.endsWith("/index.html"));
					
					//if it is current page, highlight it
					if(isCurrentPage){
						child.classList.add("current_page");
					}
				}
			}
		
		
		})
		//if nav.html fails to load, print error to console
		.catch(error => console.error("error loading nav:", error)); 
}
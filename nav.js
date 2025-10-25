function splitAtRoot(path){
    const url = new URL(path, location.origin);
    return url.pathname;
}

function setNav(current_path){

	current_path = splitAtRoot(current_path);
		
	fetch("nav.html")
		.then(r => r.text())
		.then(html => {
			
			document.getElementById("main-nav").innerHTML = html;
		
			const nav = document.getElementById("main-nav");
		
			for (let child of nav.children){
				if (child instanceof HTMLAnchorElement) {
					const child_path = splitAtRoot(child.href); 
					
					const isCurrentPage = 
						child_path === current_path ||
						(current_path === "/" && child_path.endsWith("/index.html"));
						
					if(isCurrentPage){
						child.classList.add("current_page");
					}
				}
			}
		
		
		});
		.catch(error => console.error("error loading nav:", error)); 
}
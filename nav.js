function splitAtRoot(path){
    const url = new URL(path, location.origin);
    const pathFromRoot = url.pathname;
    
	return pathFromRoot
}

function setNav(current_path){

	current_path = splitAtRoot(current_path);
		
	fetch("nav.html")
		.then(r => r.text())
		.then(html => {document.getElementById("main-nav").innerHTML = html;
		
		let nav = document.getElementById("main-nav");
		
		for (let child of nav.children){
			if (child instanceof HTMLAnchorElement) {
				let child_path = splitAtRoot(child.href); 
				
				if(child_path === current_path){
					child.classList.add("current_page");
				}
			}
		}
		});

}


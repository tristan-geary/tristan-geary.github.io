


///toggling visibility for mobile phones
function toggleNavMenu() {
	
  var x = document.getElementById("myLinks");
  
  ///if menu is visible, hide it
  if (x.style.display === "block") {
    x.style.display = "none";
	
	///if not, show it
	} else {
    x.style.display = "block";
  }
}


///for dropdown discover me tab
function toggleDropdown(e) {
	
	
	var dd = document.getElementById("discoverDropdown");
	  
	///if dropdown element doesn't exist, return
	if (!dd) return;
	
	///if the dropdown is visible, hide it
	if (dd.style.display === "block") {
		  
		dd.style.display = "none";
		dd.setAttribute('aria-hidden', 'true');
	
	///otherwise show it and mark it visible
	} else {
		dd.style.display = "block";
		dd.setAttribute('aria-hidden', 'false');
	}
		///prevent click event from closing dropdown
		e.stopPropagation();
	}


///adds listener to close dropdowon when clicking anywhere else
	window.addEventListener('click', function () {
		var dd = document.getElementById("discoverDropdown");
	  
		///if dropdown is open, hide it when clicking outside
		if (dd && dd.style.display === 'block') {
		
		dd.style.display = 'none';
		
		dd.setAttribute('aria-hidden', 'true');
	 }
	}
	);

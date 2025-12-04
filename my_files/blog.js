


document.addEventListener("DOMContentLoaded", function () {
	
	
	//delete buttons
    const deleteButtons = document.querySelectorAll(".delete-post-button");

    deleteButtons.forEach(function (button) {
        button.addEventListener("click", function () {
            const postId = button.getAttribute("data-post-id");

            if (!postId) {
                return; 
            }

            const sure = confirm("Are you sure you want to delete this post?");
            if (!sure) {
                return;
            }

            fetch("delete_post.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "id=" + encodeURIComponent(postId)
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    if (data.success) {
                        ///remove from main section
                        const article = document.getElementById(postId);
                        if (article && article.parentNode) {
                            article.parentNode.removeChild(article);
                        }

                        ///remove from aside section
                        const asideItem = document.querySelector(
                            ".blog-post-list li[data-post-id='" + postId + "']"
                        );
                        if (asideItem && asideItem.parentNode) {
                            asideItem.parentNode.removeChild(asideItem);
                        }

                        ///reapply controls
                        applyControls();
                    } else {
						
						///can't delete controls
                        alert("Could not delete the post: " + (data.error || "unknown error."));
                    }
                })
				///catching errors
                .catch(function (err) {
                    console.error("Error while deleting post:", err);
                    alert("An error occurred while trying to delete the post.");
                });
        });
    });

	///toggle buttons
    const toggleButtons = document.querySelectorAll(".toggle-content-button");

    toggleButtons.forEach(function (btn) {
		
		///collapsing to read more
        btn.addEventListener("click", function () {
            const contentDiv = btn.previousElementSibling;

            if (!contentDiv) return;

            const isCollapsed = contentDiv.classList.contains("collapsed");

            if (isCollapsed) {
                contentDiv.classList.remove("collapsed");
                btn.textContent = "Show less";
            } else {
                contentDiv.classList.add("collapsed");
                btn.textContent = "Read more";
            }
        });
    });

	///changing the theme
    const themeLink = document.getElementById("theme-stylesheet");
    const themeButton = document.getElementById("theme-toggle-button");
    const THEME_KEY = "blog_theme_mode";

    function setTheme(mode) {
		
		
        if (!themeLink) return;

        if (mode === "dark") {
            themeLink.setAttribute("href", "my_style_dark.css");
        } else {
            themeLink.setAttribute("href", "my_style.css");
            mode = "light";
        }
		
		///try to save the theme
        try {
            localStorage.setItem(THEME_KEY, mode);
        } catch (e) {
            
        }
    }

    ///load saved theme, if any
    if (themeLink) {
        let savedMode = "light";
        try {
            const stored = localStorage.getItem(THEME_KEY);
            if (stored === "dark" || stored === "light") {
                savedMode = stored;
            }
        } catch (e) {
            
        }
        setTheme(savedMode);
    }

    if (themeButton) {
        themeButton.addEventListener("click", function () {
            let currentMode = "light";
            try {
                const stored = localStorage.getItem(THEME_KEY);
                if (stored === "dark" || stored === "light") {
                    currentMode = stored;
                }
            } catch (e) {
            
            }
            const newMode = currentMode === "light" ? "dark" : "light";
            setTheme(newMode);
        });
    }


	///searching and sorting controls
    const searchInput = document.getElementById("search-input");
    const sortSelect = document.getElementById("sort-select");
    const limitSelect = document.getElementById("limit-select");

    const mainContainer = document.querySelector(".blog-main");
    const asideList = document.querySelector(".blog-post-list");

    ///get current info from the DOM
    function collectPosts() {
        const posts = [];
		
		///return empty array if none
        if (!mainContainer || !asideList) return posts;


		///get all blog posts from main area
        const articles = mainContainer.querySelectorAll(".blog-post");
        
		
		articles.forEach(function (article) {
			
            const id = article.id;
            if (!id) return;
			
			///find title and date elements inside post
            const titleEl = article.querySelector("h2");
            const dateEl = article.querySelector(".blog-date");
			
			
			///read text/use empty strings if not found
            const title = titleEl ? titleEl.textContent.trim() : "";
            const date = dateEl ? dateEl.textContent.trim() : "";
			
			
			///find the matching list element
            const asideItem = asideList.querySelector(
                "li[data-post-id='" + id + "']"
            );

            ///visible text for keyword search
            const text = article.innerText.toLowerCase();
	
	
			///store all useful info about this post in object
            posts.push({
                id: id,
                article: article,
                asideItem: asideItem,
                title: title,
                date: date,
                text: text
            });
        });
		
		
		///return array of post objects
        return posts;
    }

    function applyControls() {
        if (!mainContainer || !asideList) return;

        let posts = collectPosts();

        //searching
        let q = "";
		
		
        if (searchInput) {
            q = searchInput.value.trim().toLowerCase();
        }
		
        if (q !== "") {
            posts = posts.filter(function (p) {
                return (
                    (p.title && p.title.toLowerCase().includes(q)) ||
                    (p.text && p.text.includes(q))
                );
            });
        }

        ///sorting
        let sortValue = "date_desc";
        if (sortSelect) {
            sortValue = sortSelect.value;
        }

        posts.sort(function (a, b) {
            if (sortValue === "title_asc" || sortValue === "title_desc") {
				
				
                const ta = a.title.toLowerCase();
                const tb = b.title.toLowerCase();
                if (ta < tb) return sortValue === "title_asc" ? -1 : 1;
                if (ta > tb) return sortValue === "title_asc" ? 1 : -1;
                return 0;
				
				
            } else {
				
				
                //date ascending vs date descending
                const da = a.date;
                const db = b.date;
                if (da < db) return sortValue === "date_asc" ? -1 : 1;
                if (da > db) return sortValue === "date_asc" ? 1 : -1;
				
                return 0;
            }
        });

        // limiting number of posts to show
        let limitedPosts = posts;
        if (limitSelect && limitSelect.value !== "all") {
            const n = parseInt(limitSelect.value, 10);
            if (!isNaN(n) && n > 0) {
                limitedPosts = posts.slice(0, n);
            }
        }

        ///reattach
        const newArticles = limitedPosts.map(function (p) {
            return p.article;
        });
        mainContainer.replaceChildren.apply(mainContainer, newArticles);

        ///aside
        const newListItems = limitedPosts
            .map(function (p) {
                return p.asideItem;
            })
            .filter(function (li) {
                return li !== null;
            });

        asideList.replaceChildren.apply(asideList, newListItems);
    }

   ///reapplying filters
    if (searchInput) {
        searchInput.addEventListener("input", applyControls);
    }
    if (sortSelect) {
        sortSelect.addEventListener("change", applyControls);
    }
    if (limitSelect) {
        limitSelect.addEventListener("change", applyControls);
    }

    // Run once on load
    applyControls();
});

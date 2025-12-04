/*Purpose:  provide a draft system to the add post form using local storage.
I automatically save drafts after 2 seconds of user not typing and also add an option to manually save draft.

I wait for all the DOM content to load so all form elements exist before querying them.
Reference: https://developer.mozilla.org/en-US/docs/Web/API/Document/DOMContentLoaded_event

I store 2 keys (title/content) in local storage as strings.
*/



document.addEventListener("DOMContentLoaded", function () {
	
	///get references to main form elements that are used in the draft
    const titleInput   = document.getElementById("title");
    const contentInput = document.getElementById("content");
    const draftButton  = document.getElementById("save-draft-button");
    const form         = document.querySelector('form[action="add_post.php"]');


	///if this page doesn't have the expected fields, stop/return (allows the same script toe be included 
	///on pages that may not have a title/content form without throwing the errors)
    if (!titleInput || !contentInput) {
        return;
    }
	
	///keys for local storage
    const DRAFT_TITLE_KEY   = "blog_draft_title";
    const DRAFT_CONTENT_KEY = "blog_draft_content";

    /*restore posts from draft from local storage
	I use a try and catch so that if local storage is disabled, the page still works
	normally*/
	
    try {
        const savedTitle   = localStorage.getItem(DRAFT_TITLE_KEY);
        const savedContent = localStorage.getItem(DRAFT_CONTENT_KEY);

        if (savedTitle !== null && titleInput.value.trim() === "") {
            titleInput.value = savedTitle;
        }
        if (savedContent !== null && contentInput.value.trim() === "") {
            contentInput.value = savedContent;
        }
	
	///ignore error and keep going
    } catch (e) {
        ///if local storage not availabe, ignore
    }
	
	///timer for saving draft
    let saveTimer;
	
	
	///autosave based on time
    function scheduleAutoSave() {
		///clear any pending auto-save to avoid overlapping timers
        clearTimeout(saveTimer);
        saveTimer = setTimeout(function () {
            try {
                localStorage.setItem(DRAFT_TITLE_KEY, titleInput.value);
                localStorage.setItem(DRAFT_CONTENT_KEY, contentInput.value);
                
            } catch (e) {
              
            }
        }, 2000); ///2 seconds after the user STOPS typing
    }

    ///autosave when user types
    titleInput.addEventListener("input", scheduleAutoSave);
    contentInput.addEventListener("input", scheduleAutoSave);

    ///save draft button
	///shows simple alert to confirm that draft was stored
	///I use try and catch so that the page can still work even if they can't save
    if (draftButton) {
        draftButton.addEventListener("click", function () {
            try {
                localStorage.setItem(DRAFT_TITLE_KEY, titleInput.value);
                localStorage.setItem(DRAFT_CONTENT_KEY, contentInput.value);
                alert("Draft saved!");
            } catch (e) {
                alert("Could not save draft (localStorage not available).");
            }
        });
    }

    ///clear draft when submitted
    if (form) {
        form.addEventListener("submit", function () {
            try {
                localStorage.removeItem(DRAFT_TITLE_KEY);
                localStorage.removeItem(DRAFT_CONTENT_KEY);
            } catch (e) {
               
            }
        });
    }
});

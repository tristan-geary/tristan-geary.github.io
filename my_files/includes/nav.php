<?php

///Define variable for the dropdown's active state
$discover_active = ($current_page === 'my_vacation' || $current_page === 'my_artistic_self') ? 'active' : '';
?>
<nav class="topnav" id="myTopnav">

    <!--logo link to index-->
    <a href="index.php" id="logo" class="<?=($current_page === 'index')?'active':''?>">
        <img src="images/TG.jpg" alt="Tristan Geary Logo" class="nav-logo">
    </a>

    <div id="myLinks">
        <!-- dropdown for discover me -->
        <div class="dropdown">
            <button type="button" class="dropbtn <?= $discover_active ?>" onclick="toggleDropdown(event)">
                Discover me!
            </button>

            <div id="discoverDropdown" class="dropdown-content" aria-hidden="true">
                <a href="my_vacation.php" class="<?=($current_page === 'my_vacation')?'active': ''?>">My Dream Vacation</a>
                <a href="my_artistic_self.php" class="<?=($current_page === 'my_artistic_self')?'active': ''?>">My Artistic Self</a>
            </div>
        </div>

        <a href="marketplace.php" class="<?=($current_page === 'marketplace')?'active': ''?>">Marketplace</a>
        <a href="my_form.php" class="<?=($current_page === 'my_form')?'active': ''?>">My Quiz</a>
        <a href="login.php" class="<?=($current_page === 'login')?'active': ''?>">To-Do List</a>
        <a href="blog.php" class="<?=($current_page === 'blog')?'active': ''?>">Blog</a>
    </div>

    <!-- hamburger for small screens-->
    <a href="javascript:void(0);" class="icon" onclick="toggleNavMenu()">&#9776;</a>
</nav>

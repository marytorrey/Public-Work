<?php
//The page name is passed and returns back the title tag, I am pretty sure echo will work too.
function getTitle($page){
	switch ($page) {
		case ($page == 'index.php'):
			print_r('Shope Concrete LLC');
			break;
		case ($page == 'about.php'):
			print_r('Shope - About Us');
			break;
		case ($page == 'contact.php'):
			print_r('Contact Us');
			break;
		default: 
            print_r('Add a new case in header');
            break;
	}
}
?>

<!--Calls the function and pulls the URL then gets the page name of the url using substring-->
getTitle(substr("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",33));
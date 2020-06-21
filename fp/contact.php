<!DOCTYPE html>
<html lang="en">
 <head>
     <title>Food Is Love Project</title>
  <meta charset="utf-8" />
  <meta name="robots" content="noindex,nofollow" />
  <meta name="viewport" content="width=device-width" />
  <link rel="stylesheet" href="css/big.css" />
  <link rel="stylesheet" href="css/nav.css" />
  <link rel="stylesheet" href="css/lightbox.css" />
  <link rel="stylesheet" href="css/forms.css" />
   <!-- jQuery -->
   <script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
   <!-- MenuMaker Plugin --> 
   <script src="https://s3.amazonaws.com/menumaker/menumaker.min.js" type="text/javascript"></script>
   <!-- Icon Library -->
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">      
   <script src="js/script.js"></script>
</head>
 <body>
     
  <main>
     <header>
     <h1><a href="index.html">Food is Love Project</a></h1>
            <nav id="cssmenu">
          <ul>
              <li><a href="index.html"> Food Is Love Project</a></li>
             <li><a href="gallery.html">About</a></li>
              <!-- drop down menu for research pages -->
              <li><a href="#"> Updates</a>
                <ul>
                    <li><a href="smo.html"> Meal Counter</a></li>
                    <li><a href="accessibility.html"> Those We Serve</a></li>   
                </ul>
             </li>          
             <!-- drop down for Google Tool pages -->
              <li><a href="#"> Impact</a>
                <ul>
                   <li><a href="seo.html"> Serving our Community</a></li>
                   <li><a href="calendar.html"> Service Schedule</a></li>
                   <li><a href="map.html"> Service Area</a></li>
                </ul>
             </li>
             <li><a href="contact.php"> Contact</a></li>
          </ul>
        </nav>
            
     </header>
       
       
       <!-- START LEFT COLUMN -->
       <section class="fullwidth">
           <h2 class="subheader">Contact Food Is Love Project</h2>
	<?php
        /*
         * Below are 2 different forms to be re-used       
         * 
         * Only use one at a time, comment out the other!       
         *
         */

        include 'includes/contact_include.php'; #site keys & code here
    
        $toAddress = "sharonalexander22@outlook.com";  //place your/your client's email address here
        $toName = "Food Is Love Project"; //place your client's name here
        $website = "https://www.thefoodisloveproject.org/";  //place NAME of your client's website

        echo loadContact('simple.php');#demonstrates a simple contact form
        //echo loadContact('multiple.php');#demonstrates multiple form elements

	?>
        </section>
       <!-- END LEFT COLUMN -->
    
     <footer>
      <p><small>&copy; 2020 by <a href="contact.php">Food is Love Project</a>, All Rights Reserved ~ <a href="http://validator.w3.org/check/referer" target="_blank">Valid HTML</a> ~ <a href="http://jigsaw.w3.org/css-validator/check?uri=referer" target="_blank">Valid CSS</a></small></p>
    </footer>
  </main>
     
 </body>
</html>
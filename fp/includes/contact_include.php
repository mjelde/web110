<?php
/**
 * contact_include.php hides the messy code that supports simple.php and multiple.php 
 *
 * These pages provide a postback application designed to provide a 
 * contact form for users to email our clients.  The code references 
 * Google's ReCAPTCHA code as include files and provides all the web service plumbing 
 * to connect and serve up the CAPTCHA image and verify we have a human entering data.
 *
 * v 2.0 adds support for email headers to avoid spam trap via function, email_handler()
 *
 * Only the form elements 'Email' and 'Name' are significant.  Any other form 
 * elements added, with any name or type (radio, checkbox, select, etc.) will be delivered via  
 * email with user entered data.  Form elements named with underscores like: "How_We_Heard" 
 * will be replaced with spaces to allow for a better formatted email:
 *
 * <code>
 * How We Heard: Internet
 * </code>
 *
 * If checkboxes are used, place "[]" at the end of each checkbox name, or PHP will not deliver 
 * multiple items, only the last item checked:
 *
 * <code>
 * <input type="checkbox" name="Interested_In[]" value="New Website" /> New Website <br />
 * <input type="checkbox" name="Interested_In[]" value="Website Redesign" /> Website Redesign <br />
 * <input type="checkbox" name="Interested_In[]" value="Lollipops" /> Complimentary Lollipops <br />
 * </code>
 *
 * The CAPTCHA is handled by reCAPTCHA requiring an API key for each separate domain. 
 * Get your reCAPTCHA private/public keys from: http://recaptcha.net/api/getkey
 *
 * Place your target email in the $toAddress variable.  Place a default 'noreply' email address 
 * for your domain in the $fromAddress variable.
 *
 * After testing, change the variable $sendEmail to TRUE to send email.
 *
 * @package nmCAPTCHA2
 * @author Bill Newman <williamnewman@gmail.com>
 * @version 2 2019/10/13
 * @link http://www.newmanix.com/
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @see simple.php  
 * @see ReCaptcha.php   
 * @todo none
 */

//place your site keys here (keys for web-students.net posted here)
$siteKey = "6LeDaSoUAAAAACnEiqA3QAkiRU-Q_wtk0vuBa_OX";
$secretKey = "6LeDaSoUAAAAACJ69mIHYOxL4atri9oPrjkIVMFv";
date_default_timezone_set('America/Los_Angeles'); #sets default date/timezone for this website
$server = 'hostgator.com';
//end config area ----------------------------------------

spl_autoload_register('MyAutoLoader::NamespaceLoader');#will check subfolders as namespaces
include 'ReCaptcha/ReCaptcha.php'; #required reCAPTCHA class code 
if(
    !isset($siteKey) || 
    !isset($secretKey) || 
    $siteKey == ''  ||  
    $secretKey == ''
)      
{//siteKeys not provided - exit
    echo '<p>Please go into the contact_include.php file and place 
    the <b>$siteKey</b> and <b>$secretKey</b> for the domain where your forms 
    will be posted.</p>';
    die;
}

//functions below----

/*
    
    //example with default feedback page, feedback.php
    $toAddress = "yourname@example.com";  //place your/your client's email address here
    $toName = "CLIENT NAME HERE"; //place your client's name here
    $website = "CLIENT WEBSITE NAME HERE";  //place NAME of your client's 
    echo loadContact('simple.php');
    
    //example with custom feedback page
    $toAddress = "yourname@example.com";  //place your/your client's email address here
    $toName = "CLIENT NAME HERE"; //place your client's name here
    $website = "CLIENT WEBSITE NAME HERE";  //place NAME of your client's 
    echo loadContact('simple.php','new_feedback.php');//custom feedback page
*/

function loadContact($form,$feedback='')
{
    global $toName,$toAddress,$website,$siteKey,$secretKey,$server;
    
    if($toAddress=='' || $toAddress == 'name@example.com')
    {
        echo '<p>Please place a real email into the variable named <b>$toAddress</b> on your web page.</p>';
        die;
    }

    //fields to skip in email message
    $skipFields = 'g-recaptcha-response,Email';
    if($feedback == '')
    {
        $feedback = 'feedback.php';
    }
    
    if (isset($_POST['g-recaptcha-response'])):
    // If the form submission includes the "g-captcha-response" field
    // Create an instance of the service using your secret
    $recaptcha = new \ReCaptcha\ReCaptcha($secretKey);

    // Make the call to verify the response and also pass the user's IP address
    $resp = $recaptcha->setExpectedHostname($_SERVER['SERVER_NAME'])
                      ->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
    if ($resp->isSuccess()):
        // If the response is a success, process data!
        $aSkip = explode(",",$skipFields); #split form elements to skip into array
        $postData = show_POST($aSkip);#loops through and creates select POST data for display/email
        $fromAddress = "";//default
        if(is_email($_POST['Email']))
        {#Only use Email for return address if valid
            $fromAddress = $_POST['Email'];
            # extra email injector paranoia courtesy of DH: http://wiki.dreamhost.com/PHP_mail()#Mail_Header_Injection
            $fromAddress = preg_replace("([\r\n])", "", $fromAddress);
        }

        if(isset($_POST['Name'])){$Name = $_POST['Name'];}else{$Name = "";} #Name, if used part of subject

        if($Name != ""){$SubjectName = " from: " . $Name . ",";}else{$SubjectName = "";} #Name, if used part of subject
        $postData = str_replace("<br />",PHP_EOL . PHP_EOL,$postData);#replace <br /> tags with double c/r
        $Subject= $website . " message" . $SubjectName . " " . date('F j, Y g:i a');
        $txt =  $Subject . PHP_EOL . PHP_EOL  . $postData; 
        
        //optional identification of name of email server reduces chance of being identified as spam
        if($server==''){
            $server=$_SERVER["SERVER_NAME"];
        }
         email_handler($toAddress,$toName,$Subject,$txt,$fromAddress,$Name,$website,$server);

        //show feedback
        include_once $feedback;
    else:
        // If it's not successful, then one or more error codes will be returned.
        //show form
        include_once $form;
        include_once 'ReCaptcha/js_includes.php'; #hides JS
    endif;
else:
    // Add the g-recaptcha tag to the form you want to include the reCAPTCHA element
    include_once $form;
    include_once 'ReCaptcha/js_includes.php'; #hides JS
endif;

}//end loadContact()

/**
 * formats PHP POST data to text for email, feedback
 * 
 * @param Array $aSkip array of POST elements to be skipped
 * @return string text of all POST elements & data, underscores removed
 * @todo none
 */
function show_POST($aSkip)
{#formats PHP POST data to text for email, feedback
	$myReturn = ""; #init return var
	foreach($_POST as $varName=> $value)
	{#loop POST vars to create JS array on the current page - include email
	 	if(!in_array($varName,$aSkip) || $varName == 'Email')
	 	{#skip passover elements
	 		$strippedVarName = str_replace("_"," ",$varName);#remove underscores
			if(is_array($_POST[$varName]))
		 	{#checkboxes are arrays, and we need to loop through each checked item to insert
		 	    $myReturn .= $strippedVarName . ": " . sanitize_it(implode(",",$_POST[$varName])) . "<br />";
	 		}else{//not an array, create line
	 			$strippedValue = nl_2br2($value); #turn c/r to <br />
	 			$strippedValue = str_replace("<br />","~!~!~",$strippedValue);#change <br /> to our 'unique' string: "~!~!~"
	 			//sanitize_it() function commented out as it can cause errors - see word doc
	 			//$strippedValue = sanitize_it($strippedValue); #remove hacker bits, etc. 
	 			$strippedValue = str_replace("~!~!~","\n",$strippedValue);#our 'unique string changed to line break
	 			$myReturn .= $strippedVarName . ": " . $strippedValue . "<br />"; #
	 		}
		}
	}
	return $myReturn;
}#end show_POST()

/**
 * Strips tags & extraneous stuff, leaving text, numbers, punctuation.  
 *
 * Not recommended for databases, but since we're only sending email,
 * this is hopefully better than nothing
 *
 * Change in version 1.11 is to use spaces as replacement instead of empty strings
 *
 * @param string $str data as entered by user
 * @return data returned after 'sanitized'
 * @todo none
 */
function sanitize_it($str)
{#We would like to trust the user, and aren't using a DB, but we'll limit input to alphanumerics & punctuation
	$str = strip_tags($str); #remove HTML & script tags	
	$str = preg_replace("/[^[:alnum:][:punct:]]/"," ",$str);  #allow alphanumerics & punctuation - convert the rest to single spaces
	return $str;
}#end sanitize_it()

/**
 * Checks for email pattern using PHP regular expression.  
 *
 * Returns true if matches pattern.  Returns false if it doesn't.   
 * It's advised not to trust any user data that fails this test.
 *
 * @param string $str data as entered by user
 * @return boolean returns true if matches pattern.
 * @todo none
 */
function is_email($myString)
{
  if(preg_match("/^[a-zA-Z0-9_\-\.]+@[a-zA-Z0-9_\-]+\.[a-zA-Z0-9_\-]+$/",$myString))
  {return true;}else{return false;}
}#end is_email()

/**
 * br2nl() changes '<br />' tags  to '\n' (newline)  
 * Preserves user formatting for preload of <textarea>
 *
 * <code>
 * $myText = br_2nl($myText); # <br /> changed to \n
 * </code>
 *
 * @param string $text Data from DB to be loaded into <textarea>
 * @return string Data stripped of <br /> tag variations, replaced with new line 
 * @todo none 
 */
function br_2nl($text)
{
	$nl = "\n";   //new line character
    $text = str_replace("<br />",$nl,$text);  //XHTML <br />
    $text = str_replace("<br>",$nl,$text); //HTML <br>
    $text = str_replace("<br/>",$nl,$text); //bad break!
    return $text;
    /* reference (unsused)
    $cr = chr(13); // 0x0D [\r] (carriage return)
	$lf = chr(10); // 0x0A [\n] (line feed)
	$crlf = $cr . $lf; // [\r\n] carriage return/line feed)
    */
}#end br2nl()

/**
 * nl2br2() changes '\n' (newline)  to '<br />' tags
 * Break tags can be stored in DB and used on page to replicate user formatting
 * Use on input/update into DB from forms
 *
 * <code>
 * $myText = nl_2br2($myText); # \n changed to <br />
 * </code>
 * 
 * @param string $text Data from DB to be loaded into <textarea>
 * @return string Data stripped of <br /> tag variations, replaced with new line 
 * @todo none
 */
function nl_2br2($text)
{
	$text = str_replace(array("\r\n", "\r", "\n"), "<br />", $text);
	return $text;
}#end nl2br2()

function email_handler($toEmail,$toName,$subject,$body,$fromEmail,$fromName,$website,$domain)
{
	$debug=false;//true may show message
	if($fromName==""){$fromName = $website;} //default to website if name not provided
	$headers[] = "MIME-Version: 1.0";
	$headers[] = "Content-type: text/plain; charset=iso-8859-1";
	$headers[] = "From: {$fromName} <noreply@{$domain}>";
    
	if(isset($fromEmail) && $fromEmail != "")
	{//only add reply info if provided
		$headers[] = "Reply-To: {$fromName} <{$fromEmail}>";
	}
	$headers[] = "Subject: {$subject}";
	$headers[] = "X-Mailer: PHP/".phpversion();
	
    //target of form
	$toEmail = 'To:' . $toName . ' <' . $toEmail . '>'; 
	if(@mail($toEmail, $subject, $body, implode(PHP_EOL, $headers)))
	{//only echo if debug is true
		if($debug){echo 'Email sent! ' . date("m/d/y, g:i A");}
	}else{
		if($debug){echo 'Email NOT sent! Unknown error. ' . date("m/d/y, g:i A");}	
	}	

}//end email_handler()

/**
 * This class stores a collection of static methods to load any number of 
 * library configurations.
 *
 * It must be referenced inside the config file to be active:
 *
 * <code>
 * include INCLUDE_PATH . 'MyAutoLoader.php'; #Allows multiple versions of AutoLoaded classes
 * </code>
 *
 * Each method must be registered before a class is called. 
 * 
 * Registration looks like the following: 
 *
 * <code>
 * spl_autoload_register('MyAutoLoader::NamespaceLoader');
 * </code>
 */

class MyAutoLoader
{
    /**
	 * Uses context of calling file's relative path to call 
	 * a class in a relative sub-folder accessible by it's namespace:
	 * 
	 * <code>
	 * $mySurvey = new SurveySez\Survey(1);
	 * </code>
	 */
	public static function NamespaceLoader($class)
    {
        //namespaces use backslashes, file paths use forward slashes in UNIX.  
		//we convert them here, but use a constant to remain platform independent
		$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $class);
        $path = __DIR__ . '/' . $path . '.php';
		//if file exists, include and load class file
		if (file_exists($path)) {
			include $path;
			return; //go no farther
		}else{
            echo 'include file not found!';
            die;
        }
    }#end NamespaceLoader()

}#end MyAutoLoader class




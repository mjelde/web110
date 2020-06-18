UPDATED 10/13/2019

These pages provide a postback application designed to provide a contact form for users to email our clients.  

The code references Google's reCAPTCHA code as include files and provides all the web service plumbing to connect and serve up the CAPTCHA image and verify we have a human entering data.

v 2.0 adds support for email headers to avoid spam trap via function, email_handler()

View the file named test.php, which allows one of two PHP include files, named simple.php or multiple.php, which are forms wired to google's reCAPTCHA API, to email info to your client from their website, eliminating form spam. 

Inside test.php are PHP variables to indicate where to send the email once filled out.

Inside contact_include.php are the Site Key and Secret Key that must be setup specifically for your domain.

If you don't have reCAPTCHA setup for your domain, go to the following URL:
https://www.google.com/recaptcha

INSTALLATION: The main installation steps can be outlined as follows:

1) Verify Site Keys in include file contact_include.php
2) Add Client name & email in test.php, which contains code for your contact form
3) Check to be sure correct file is included (simple.php or multiple.php) in the file test.php
4) Test the file test.php to be sure it can send an email successfully
5) Move the code inside test.php into your form
6) (optional) Edit or change simple.php or multiple.php to meet your needs and test

SITE KEYS: If you're using web-students.net as your server as done in WEB110, you won't need to edit the API keys inside contact_include.php.  However, to use this code on other servers you'll need to get a pair of API keys and load them into the variables inside contact_include.php.

//MAKE SURE THE SITE KEYS MATCH YOUR SITE!  THESE ARE FOR web-students.net
$siteKey = "6LeDaSoUAAAAACnEiqA3QAkiRU-Q_wtk0vuBa_OX";
$secretKey = "6LeDaSoUAAAAACJ69mIHYOxL4atri9oPrjkIVMFv";

BACKGROUND: The main file we have which interacts with google's reCAPTCHA service is named contact_include.php.  This file provides the code that supports simple.php and multiple.php

EDITING simple.php & multiple.php: In these forms, only the form elements 'Email' and 'Name' are significant.  Any other form elements added, with any name or type (radio, checkbox, select, etc.) will be delivered via email with user entered data.  

Form elements named with underscores like: "How_We_Heard" will be replaced with spaces to allow for a better formatted email:

 How We Heard: Internet

If checkboxes are used, place "[]" at the end of each checkbox name, or PHP will not deliver multiple items, only the last item checked:

<input type="checkbox" name="Interested_In[]" value="New Website" /> New Website <br />
<input type="checkbox" name="Interested_In[]" value="Website Redesign" /> Website Redesign <br />
<input type="checkbox" name="Interested_In[]" value="Lollipops" /> Complimentary Lollipops <br />

Place your target email in the $toAddress variable.

Advice from google about how to make sure your contact form messages are not seen as spam:
https://support.google.com/mail/answer/1366858?hl=en&authuser=1&expand=5





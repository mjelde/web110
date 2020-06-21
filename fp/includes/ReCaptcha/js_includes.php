    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=en"></script>
    <script>
        /**
        * requires reCAPTCHA so user doesn't lose form data 
        *
        * https://stackoverflow.com/questions/27706594/how-can-i-make-recaptcha-a-required-field
        * 
        * In this version, jQuery is required and code will find first form on page and require 
        * the reCAPTCHA for that form using document.forms[0]
        */
    
        function checkRecaptcha() {
            res = $('#g-recaptcha-response').val();
            if (res == "" || res == undefined || res.length == 0)
                return false;
            else
                return true;
        }
        $(function() {
            let thisForm = document.forms[0];
            $(thisForm).submit(function(e) {            
                if(!checkRecaptcha()) {
                    alert("Please confirm you are not a robot.");
                    return false;
                }
            });
        }); 
    </script>
/* profile.js
 * This file checks if the passwords entered in the "password" and 
 * "confirm_password" fields match as the user types and displays
 * the appropriate message in real-time
 */

/* Check for equality in the password fields when the user starts
 * typing in the 'Confirm Password' field
 */
$(document).ready(function() {
  $("#confirm_password").keyup(validate);
});

/* This is the main function that checks if the two fields are equal
 * If they are, a green message confirming the passwords match is showed.
 * Otherwise, a red message telling the user the passwords don't match
 */
function validate() {
  var password = $("#password").val();
  var confirm = $("#confirm_password").val();
 
    if(password == confirm) {
       $("#validate-status").text("passwords match").css("color", "#33cc59"); // green if passwords match         
    }
    else {
        $("#validate-status").text("passwords do not match").css("color", "red"); // red if passwords don't match 
    }   
}
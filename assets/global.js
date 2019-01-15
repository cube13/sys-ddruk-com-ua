/* Global JavaScript File for working with JQuery library */

// execute when the HTML file's (document object model: DOM) has loaded
$(document).ready(function() {

	/* USERNAME VALIDATION */
	// use element id=username within the element id=registration_form
	// bind our function to the element's onBlur event
	$('#registration_form').find('#username').blur(function() {
															
		// get the value from the username field															
		var username = $('#username').val();
		
		// Ajax request sent to the CodeIgniter controller "ajax" method "username_taken"
		// post the username field's value
		$.post('/index.php/ajax/username_taken',
			{ 'username':username },

			// when the Web server responds to the request
			function(result) {
				// clear any message that may have already been written
				$('#bad_username').replaceWith('');
				
				// if the result is TRUE write a message to the page
				if (result) {
					$('#username').after('<div id="bad_username" style="color:red;">' +
					  '<p>(That Username is already taken. Please choose another.)</p></div>');
				}
			}
		);
	});
	
	/* AUTOSAVE PARTICIPATION */
	// use input element name=participation_type_id and type=radio
	// bind our function to the element's onClick event
	$('input[name=participation_type_id]:radio').click(function() {
		
		var participation_type_id = this.value;

		// create global variables for use below
		var class_activity_id, user_id;
		
		// get the form's two hidden input elements 
		// each is a sibling of the parent of the clicked radio button
		// store their values in the global variables
		var hidden_elements = $(this).parent().siblings('input:hidden');
		$(hidden_elements).map(function() {
			if (this.name == 'class_activity_id') {
				class_activity_id = this.value;
			}
			if (this.name == 'user_id') {
				user_id = this.value;
			}
		});

		// Ajax request to CodeIgniter controller "ajax" method "update_user_participation"
		// post the user_id, class_activity_id and participation_type_id fields' values
		$.post('/index.php/ajax/update_user_participation',
			{ 'user_id':user_id, 
				'class_activity_id':class_activity_id, 
				'participation_type_id':participation_type_id },
			// when the Web server responds to the request
			function(result) { }
		);

		// set the text next to the clicked radio button to red
		$(this).next().css("color", "red");

		// set the text next to the remaining radio buttons to black
		var other_r_buttons = $(this).siblings('input[name=participation_type_id]:radio');
		$(other_r_buttons).map(function() {
			$(this).next().css("color", "black");
		});

	});
	
	/* jQUERY UI CALENDAR PLUGIN */
	// bind the Datepicker to the date-picker class
	$(".date-picker").datepicker();

});

/* AUTOSUGGEST SEARCH */
// triggered by input field onKeyUp
function autosuggest(str){
	// if there's no text to search, hide the list div
	if (str.length == 0) {
		$('#autosuggest_list').fadeOut(500);
	} else {
		// first show the loading animation
		$('#class_activity').addClass('loading');
		
		// Ajax request to CodeIgniter controller "ajax" method "autosuggest"
		// post the str paramter value
		$.post('/index.php/ajax/autosuggest',
			{ 'str':str },
			function(result) {
				// if there is a result, fill the list div, fade it in 
				// then remove the loading animation
				if(result) {
					$('#autosuggest_list').html(result);
					$('#autosuggest_list').fadeIn(500);
					$('#class_activity').removeClass('loading');
			}
		});
	}
}

/* AUTOSUGGEST SET ACTIVITY */
// triggered by an onClick from any of the li's in the autosuggest list
// set the class_acitity field, wait and fade the autosuggest list
// then display the activity details
function set_activity(activity_name, master_activity_id) {
	$('#class_activity').val(activity_name);
	setTimeout("$('#autosuggest_list').fadeOut(500);", 250);
	display_activity_details(master_activity_id);
}

/* AUTOSUGGEST DISPLAY ACTIVITY DETAILS */
// called by set_activity()
// get the HTML to display and display it
function display_activity_details(master_activity_id) {
	
	// Ajax request to CodeIgniter controller "ajax" method "get_activity_html"
	// post the master_class_activity parameter values
	$.post('/index.php/ajax/get_activity_html',
		{ 'master_activity_id':master_activity_id },
		// when the Web server responds to the request
		// replace the innerHTML of the select_activity element
		function(result) { 
			$('#select_activity').html(result);

			// because the add datepicker is not loaded with the DOM
			// manually add it after the date input field is written
			$(".date-picker").datepicker();
		}
	);
}



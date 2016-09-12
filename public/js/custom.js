var ingredient_id;
var step_id;

$(document).ready(function() {

	/*******************/
	// COMMENT CAPTCHA	/
	/*******************/
	$("#captcha img").after("<a class='captcha-link' href='javascript:void()'><img class='captcha-reload' src='/img/reload.png' /></a>");

    $('.captcha-reload').click(function() { 
    	$(".captcha-link").after("<img style='border-width:0px;margin-left:-20px;margin-bottom:30px;' class='reload-animation' src='/img/ajax-loader.gif' />");
        $.ajax({ 
            url: '/news/refresh', 
            dataType:'json', 
            success: function(data) { 
                $('#captcha .captcha-image').attr('src', data.src); 
                $('#captcha-id').attr('value', data.id); 
                $('.reload-animation').remove();
            },
            error: function() {
            	/*alert("fail");*/
            }
        }); 
    }); 

	/*******************/
	// RECIPE			/
	/*******************/
	// Get value of id - integer appended to dynamic form field names and ids
	ingredient_id = (parseInt($("#ingredient_sort_id").val())).toString();
	step_id = (parseInt($("#step_sort_id").val())).toString();

	if (ingredient_id == 1) {
		$('#removeIngredientElement').toggle();
	}
	if (step_id == 1) {
		$('#removeStepElement').toggle();
	}
	
	$("#addIngredientElement").click(
		function() {
			ajaxAddIngredientField();
		}
	);

	$("#removeIngredientElement").click(
		function() {
			removeIngredientField();
		}
	);
	
	$("#addStepElement").click(
		function() {
			ajaxAddStepField();
		}
	);

	$("#removeStepElement").click(
		function() {
			removeStepField();
		}
	);
	
	$("#recipeTags").tagit({singleField:true});
	
	$('#add_more_images').click(function(e){
		var file_array = document.getElementsByName('file[]');
		var next_id = parseInt(file_array.length);
        e.preventDefault();
        $(this).before("<input name='file[]' id='file-" + next_id + "' type='file' onchange='readImageURL(this);' /><br>");
    });
});

function remove_image(image_id,file_index) {
	$("#close_" + image_id).remove();
	$("#close_new_" + image_id).remove();
	$("div.gallery_id input#" + image_id).remove();
	$("div.gallery_id input#new_" + image_id).remove();
	$("input#file-" + file_index).remove();
	var file_array_len = document.getElementsByName('file[]').length;
	if ($("input#file-1").val() != '' && $("div.gallery_id").children().length == 0) {
		$("input#file-1").remove();
		$('#gallery').before("<input name='file[]' id='file-0' type='file' onchange='readImageURL(this);' /><br>");
	}
}

function readImageURL(input) {

	var file_array = document.getElementsByName('file[]');
	var next_id = parseInt(file_array.length) + 1;

	if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function (e) {
			//Add to gallery
			$('div.gallery_id').append('<input type=\"hidden\" id=\"new_'+next_id+'\" name=\"image_ids[]\" value=\"new_'+next_id+'\">');
			
			$('div.gallery').append('<span class=\"gallery_content\" id=\"close_new_'+next_id+'\"><img src =\"'+e.target.result+'\" width=\"100\" height=\"100\" /><img onclick=\"return remove_image(\''+next_id+'\',\''+(file_array.length-1)+'\');\" class=\"close_button\" src=\"/css/img/close_button.png\" /></span>');
			
			e.preventDefault();
			$('#file-' + (next_id-2)).after("<input name='file[]' id='file-" + (next_id-1) + "' type='file' onchange='readImageURL(this);' /><br>");
			
			$('#file-' + (next_id-2)).hide();
			
		};

		reader.readAsDataURL(input.files[0]);
	}
}

// Retrieve new element's html from controller
function ajaxAddIngredientField() {

	ingredient_id++;

	$.ajax({
		type: "POST",
		url: "/food/newingredientfield",
		data: "ingredient_sort_id=" + ingredient_id,
		success: function(newElement) {
		
			if (ingredient_id == 2) {
				$('#removeIngredientElement').toggle();
			}
		
			// Insert new element before the Add button
			$("#addIngredientElement").before(newElement);

			// Increment and store ingredient_id
			$("#ingredient_sort_id").val(ingredient_id);
		},
		error: function (request, status, error) {
		}
	});
}

function removeIngredientField() {

	// Get the last used ingredient_id
	var lastId = ingredient_id;

	// Build the attribute search string.  This will match the last added  dt and dd elements.  
	// Specifically, it matches any element where the ingredient_id begins with 'newIngredient*_<int>-'.
	searchString = "*[id*=newIngredientAmount_" + lastId + "]";

	// Remove the elements that match the search string.
	$(searchString).remove();
	
	searchString = "*[id*=newIngredientUnit_" + lastId + "]";

	// Remove the elements that match the search string.
	$(searchString).remove();
	
	searchString = "*[id*=newIngredient_" + lastId + "]";
	
	$(searchString).next('br').remove();
	$(searchString).next('br').remove();

	// Remove the elements that match the search string.
	$(searchString).remove();

	// Decrement and store ingredient_id
	$("#ingredient_sort_id").val(--ingredient_id);
	
	if (ingredient_id == 1) {
		$('#removeIngredientElement').toggle();
	}
}

// Retrieve new element's html from controller
function ajaxAddStepField() {

	step_id++;

	$.ajax({
		type: "POST",
		url: "/food/newstepfield",
		data: "step_sort_id=" + step_id,
		success: function(newElement) {

			if (step_id == 2) {
				$('#removeStepElement').toggle();
			}

			// Insert new element before the Add button
			$("#addStepElement-label").before(newElement);

			// Increment and store step_id
			$("#step_sort_id").val(step_id);
		}
	});
}

function removeStepField() {

	// Get the last used step_id
	var lastId = step_id;

	// Build the attribute search string.  This will match the last added  dt and dd elements.  
	// Specifically, it matches any element where the step_id begins with 'newStep_<int>-'.
	searchString = "*[id*=newStep_" + lastId + "-]";

	// Remove the elements that match the search string.
	$(searchString).remove();

	// Decrement and store step_id
	$("#step_sort_id").val(--step_id);
	
	if (step_id == 1) {
		$('#removeStepElement').toggle();
	}
}
$ = jQuery;

$('#pages').multiSelect();
$('#posts_types').multiSelect();


$('#allMetas').on('change', function() {
  	search = this.value;
  	if (search == "clear") {
  		$('.title_meta_box').val('');
		$('.no_inputs').val('');
		$('.no_editors').val('');
  	} else {
	  	$.ajax({
			url: variables.ajax_url,
			type: 'post',
			data: {action: 'ajCall', method: 'getMetaData', search: search},
			async: true,
			success: function (data) {
			    obj = JSON.parse(data);
			    if(obj._title_meta_box != '') {
			    	$('.title_meta_box').val(obj._title_meta_box);
			    }
			    if(obj._no_inputs != '') {
			    	$('.no_inputs').val(obj._no_inputs);
			    }
			    if(obj._no_editors != '') {
			    	$('.no_editors').val(obj._no_editors)
			    }
			    
			    if(obj._img_featured != '' && obj._img_featured == 'yes') { 
			    	$( ".img_featured" ).prop( "checked", true )
			    }

			    if(obj._video_featured != '' && obj._video_featured == 'yes') { 
			    	$( ".video_featured" ).prop( "checked", true )
			    }

			    if(obj._document_featured != '' && obj._document_featured == 'yes') { 
			    	$( ".document_featured" ).prop( "checked", true )
			    }

			    if(obj._pages != '') {
				    pages = obj._pages;
				    $("#pages option").each( function() {
				    	for (p in pages) {
				    		if( pages[p] == this.value)
					    		 $(this).prop('selected', true);
				    	}
					});
					$('#pages').multiSelect();
				}
			    
			}.bind(this),
			error: function (jqXHR) {
			    this.errors = jqXHR.responseJSON.errors;
			}.bind(this)
		});
  	}
});


$('.add_item').click(function(e) {
    e.preventDefault();
    var this_btn_img = $(this);
    var classInput = $(this).data('id');
    var selectedVal = $(this).data('type');
    var image = wp.media({
        title: 'Agregar Imagen',
        multiple: false
    }).open()
    .on('select', function(e){
        this_btn_img.prev().remove();
        var file_json_object = (image.state().get('selection').first()).toJSON();
        var element_img = '';
       
        if(file_json_object.type == 'image' && selectedVal == 'foto')
        	element_img = "<img class='image-attachment' src="+file_json_object.sizes.thumbnail.url+" heigh='150' width='150'>";
        else if(file_json_object.subtype == 'pdf' && selectedVal == 'pdf')
	        element_img = "<div><img class='image-attachment' src="+file_json_object.icon+" heigh='64' width='48'><label>"+file_json_object.title+"</label></div>";
	    else if(file_json_object.type == 'video' && selectedVal == 'video')
	    	element_img = "<div><img class='image-attachment' src="+file_json_object.icon+" heigh='64' width='48'><label>"+file_json_object.title+"</label></div>";
        /*else 
        	element_img = "<img class='image-attachment' src="+file_json_object.icon+" heigh='64' width='48'>";*/

         if( isDefined(element_img) && element_img != '' ) {
        	this_btn_img.before(element_img);
        	$('.'+classInput).val(file_json_object.id);
        } else {
        	alert('Error. Favor de checar el tipo de documento.');
        	$(".type_document").prop('checked', false);
        	return false;
        }

    });
});

/* SECTION GALLERY */
var file_frame;
$('.gallery-wrapper-sortable').sortable();

gallery_bind_delete_item = function(el){
	
	el.find('.gallery_remove_item').bind('click', function(event){
		
		event.preventDefault();
		
		$(this).parent().parent().fadeOut('fast', function(){
			$(this).remove();
		});
		
	});
	
}

gallery_bind_delete_item($('.gallery-wrapper-sortable'));

$('.gallery_add').bind('click', function( event ){

	event.preventDefault();
	
	var the_for = $(this).attr('data-for');

	// Create the media frame.
	file_frame = wp.media.frames.file_frame = wp.media({
		title	: 'Select image to insert to gallery',
		button	: {
			text: 'Insert to gallery',
		},
		multiple: true  // Set to true to allow multiple files to be selected
	});

	// When an image is selected, run a callback.
	file_frame.on( 'select', function() {
		
		var selection = file_frame.state().get('selection');
 
		selection.map( function( attachment ) {

			attachment = attachment.toJSON();
			// console.log(attachment.length);
			// if(attachment.sizes.thumbnail.url.length > 0)
			if((typeof attachment.sizes.thumbnail !== 'undefined'))
				img_url = attachment.sizes.thumbnail.url
			else
				img_url = attachment.sizes.full.url

			var the_list = $('<li class="gallery_thumnails"><div><span class="gallery-movable"></span><a href="#" class="gallery_remove_item"><span>delete</span></a><img src="'+ img_url +'"><input type="hidden" name="'+ the_for +'[gallery_media][]" value="'+ attachment.id +'" /></div></li>').hide();
			
			gallery_bind_delete_item(the_list);
			
			$('#gallery' + the_for).append(the_list);
			
			the_list.fadeIn();
		});

	});

	// Finally, open the modal
	file_frame.open();
});

function isDefined(variable) 
{
  return typeof variable !== 'undefined' && variable !== null;
}

/*function createName(name) 
{
	if(name != "") {
		name = clean(name);
		name = name.toLowerCase();
		name = name.replace(/ /g,"_");
		return '_'+name;
	} else {
		errors('field_empty');
	}
}

function clean(str) 
{
  if (isDefined(str)) {
    return removeHTML(str).replace(/[`ª´·¨Ç~¿!#$%^&*()|+\-=?;'",<>\{\}\[\]\\]/gi, '');
  }

  return false;
}

function removeHTML(str) 
{
  if (isDefined(str)) {
    return str.replace(/(<([^>]+)>)/ig, '');
  }

  return false;
}

function isDefined(variable) 
{
  return typeof variable !== 'undefined' && variable !== null;
}

function errors(nameError) 
{
	if( nameError == 'field_empty') {
		alert('The field cannot be empty');
	} else {
		alert('Error..!!');
	}
}
*/
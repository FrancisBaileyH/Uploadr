function selectFile()
{
	$( "#file" ).click();
}


/*
 * Max Post Size Needs
 * To Be Taken Into Account
 */
function alertFile()
{
	var file = $( "#file" )[0].files;
	var container = $( ".msg-container" );
	
	container.html('');
	
	for (var i = 0; i < file.length; i++)
	{
		container.append( '<div class="fileSelect">' + file[i].name + '</div>' );
		container.append( '<div class="progress" id="progid' + i + '"><div class="progressBar"></div></div>' );
	}
}



function promptRm()
{
		
	if (!confirm( "Are you sure you want to remove this directory and all files within it?" ))
	{
		return false;
	}
}



$( document ).ready( function() {
	
	
	
	$( "#upload" ).on( "click", function( event ) {
		
		event.preventDefault();
				
		var formData = new FormData();
		var uri = $( "#uploadfile" ).attr( "action" );
		var currentDir = $( "#currentdir" ).attr( "value" );
		var maxsize = $( "#maxsize" ).attr( "value" );
		var files = $( "#file" )[0].files;
		var successFlag = 0;
		var errorFlag = 0;
		
		if (files.length < 1)
		{
			$( '.msg-container' ).html( '<p class="error">Please Select a File To Upload</p>' );
		}
		
		$.each( files, function( i, file ) {
			
			if (file.size < maxsize)
			{
			
				formData.append(0, file );
						
				$.ajax({
						url: uri,
						type: 'POST',
						dataType: 'json',
						data: formData,
						error: function( data ) { 
									errorFlag++;
									requestComplete( data, i );
							},
						xhr: function()	{
								var xhr = new window.XMLHttpRequest();
							
								xhr.upload.addEventListener("progress", function( evt ) {
									if (evt.lengthComputable) 
									{
										var percentComplete = evt.loaded / evt.total;
									
										progressBar( percentComplete, i );
									}
								}, false);
												
								return xhr;
							},
						cache: false,
						contentType: false,
						processData: false,
						success: function( data ) { 
									requestComplete( data, i );
								}	
				});
			}
			else
			{
				$( "#progid" + i ).html( '<p class="uploadError">Maximum File Size Exceeded' );
			}
			
		});
		
		
		
		function requestComplete( data, eventId )
		{
			responseHandler( data, eventId );
			
			if (successFlag != files.length)
			{
				setInterval( function() { window.location = currentDir; }, 2000);
			}
			else if (successFlag + errorFlag == files.length)
			{
				setInterval( function() { window.location = currentDir; }, 4000 );
			}
			
			
		}
		
		
		
		
		function responseHandler( data, eventId )
		{
			if ( data == 1)
			{
				progressBar( 1, eventId );
				$( '#progid' + eventId + " .progressBar").css( "background-color", "#87c8fd");
				successFlag++;
			}
			else
			{
			    $( '#progid' + eventId ).html( '<p class="uploadError">' + data + '</p>' );
				errorFlag++;
			}
		}
		
		
		
		
		function progressBar( percent, eventId )
		{
			var width = Math.ceil( 100 * percent ) + '%';
		
			$( '#progid' + eventId + " .progressBar" ).width( width );
		}
		
										
	});
	
	
});



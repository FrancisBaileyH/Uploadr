
function selectFile()
{
    $( "#file" ).click();
}


function alertFile()
{
    var file = $( "#file" )[0].files;
    var container = $( ".msg-container" );
    
    container.html('');
    
    for (var i = 0; i < file.length; i++)
    {
        container.append( '<div class="fileSelect">' + file[i].name + '</div>' );
        container.append( '<div class="progress" id="progid' + i + '"><div class="progressBar"></div></div>' );
        
        if ( i == 5 )
        {
            /*
             * based on max post size
            */
            container.append( '<p class="error">Maxiumum 5 uploads at at time</p>' );   
            break;
        }
    }
}


$( document ).ready( function() {


    $( '#deleteDir' ).submit( function( e )
    {
        if (!confirm( "Are you sure you want to remove this directory and all files within it?" ))
        {
            e.preventDefault();
        }
    });

    
    
    /*
     * If javascript is disabled display the default
     * file select button
    */ 
    $( function()
    {
        $( '.uploadform input[type=file]' ).css( 'display', 'none' );
        $( '.uploadform input[type=button]' ).css( 'display', 'block' );
    });
    
    
    
    /*
     * Upload files function
    */ 
    $( "#upload" ).on( "click", function( event ) {
        
        event.preventDefault();
                
        var formData = new FormData();
        var uri = $( "#uploadfile" ).attr( "action" );
        var currentDir = $( "#currentdir" ).attr( "value" );
        var maxsize = $( "#maxsize" ).attr( "value" );
        var files = $( "#file" )[0].files;
        var successFlag = 0;
        var errorFlag = 0;
        
        //Disable upload button
        $( this ).prop( 'disabled', true );
        
        
        if (files.length < 1)
        {
            $( '.msg-container' ).html( '<p class="error">Please Select a File To Upload</p>' );
        }
        
        
        /*
         * Loop through each file and send a seperate request
         * Track files individually and handle response
         * with functions below
        */ 
        $.each( files, function( i, file ) {
            
            if (file.size < maxsize)
            {
    
                formData.append(0, file );
                formData.append(0, $( '#selectfile [name="csrf"]' ).val() );
                        
                $.ajax({
                        url: uri,
                        type: 'POST',
                        dataType: 'json',
                        data: formData,
                        error: function( data ) { 
                                    errorFlag++;
                                    requestComplete( data, i );
                            },
                        xhr: function() {
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
                        success:function( data ) 
                                { 
                                    successFlag++;
                                    requestComplete( data, i );
                                }   
                });
            }
            else
            {
                $( "#progid" + i ).html( '<p class="uploadError">Maximum File Size Exceeded</p>' );
            }
            
            //Re-enable upload button
            $( this ).prop( 'disabled', false );
                            
        });
        
    
        /*
        * Only reload page once every request
        * has either completed or failed
        */ 
        function requestComplete( data, eventId )
        {
            responseHandler( data, eventId );
            
            if (successFlag == files.length)
            {
                setInterval( function() { window.location = currentDir; }, 2000);
            }
            else if (successFlag + errorFlag == files.length)
            {
                setInterval( function() { window.location = currentDir; }, 4000 );
            }
        }
    
    
        /*
        * Once response is received, if errors occurred
        * append them to message container
        * Otherwise simulate 100% completion of progress bar
        */ 
        function responseHandler( data, eventId )
        {
            if ( data == 1)
            {
                progressBar( 1, eventId );
                $( '#progid' + eventId + " .progressBar").css( "background-color", "#87c8fd");
            }
            else
            {
                $( '#progid' + eventId ).html( '<p class="uploadError">' + data + '</p>' );
            }
        }
    
    
        /*
        * Generate progress bar 
        */  
        function progressBar( percent, eventId )
        {
            var width = Math.ceil( 100 * percent ) + '%';
    
            $( '#progid' + eventId + " .progressBar" ).width( width );
        }

    });

});



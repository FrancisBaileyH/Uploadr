$( document ).ready( function() 
{
    
    var MAX_NUM_FILES = $( '#maxnum' ).val();
    var MAX_FILE_SIZE = $( '#maxsize' ).val();
    
    
    $( '#filebutton' ).click( function()
    {
        $( '#file' ).click();
    });
        
    
    $( '#file' ).on( 'change', function()
    {
        var file = $( '#file' )[0].files;
        var container = $( '.msg-container' );
    
        container.html('');
    
        for (var i = 0; i < file.length; i++)
        {
            container.append( '<div class="fileSelect">' + file[i].name + '</div>' );
            container.append( '<div class="progress" id="progid' + i + '"><div class="progressBar"></div></div>' );
        
            if ( i == MAX_NUM_FILES - 1 )
            {
                // based on max post size
                container.append( '<p class="error">Maxiumum ' + MAX_NUM_FILES + ' uploads at at time</p>' );   
                break;
            }
        }
    });
    


    $( '.deleteDir' ).submit( function( e )
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
    $( '#upload' ).on( 'click', function( event ) {
        
        event.preventDefault();
                
        var formData = new FormData();
        var uri = $( '#uploadfile' ).attr( 'action' );
        var currentDir = $( '#currentdir' ).attr( 'value' );
        var files = $( '#file' )[0].files;
        var successFlag = 0;
        var errorFlag = 0;
        
        //Disable upload button
        $( this ).prop( 'disabled', true );
        $( '#filebutton' ).prop( 'disabled', true );
        
        //Ensure there are files to upload
        checkFileLength( files );
      
    
        /*
         * Loop through each file and send a seperate request
         * Track files individually and handle response
         * with functions below
        */ 
        $.each( files, function( i, file ) 
        {
            
            if ( i == MAX_NUM_FILES )
            {
                return false;
            }
            if ( checkFileSize( file, i ) )
            {
    
                formData.append(0, file );
                formData.append(0, $( '#selectfile [name="csrf"]' ).val() );
     
                $.ajax({
                        url: uri,
                        type: 'POST',
                        dataType: 'json',
                        data: formData,
                        xhr: 
                            function() 
                            {
                                var xhr = new window.XMLHttpRequest();
                            
                                xhr.upload.addEventListener("progress", function( evt ) 
                                {
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
                        processData: false
                })
                .always( function( data )
                {
                    requestComplete( data, i );
                });
            }
            else
            {
                errorFlag++;
            }
                           
        });
        
        // Renable button
        $( '#upload' ).prop( 'disabled', false );
        $( '#filebutton' ).prop( 'disabled', false );
        
        
        /*
         * Only reload page once every request
         * has either completed or failed
        */ 
        function requestComplete( data, eventId, flag )
        {
            responseHandler( data, eventId, flag );
            
            var numFiles = ( files.length < MAX_NUM_FILES ? files.length : MAX_NUM_FILES );
            
            if ( successFlag == numFiles )
            {
                setInterval( function() { window.location = currentDir; }, 2000);
            }
            else if ( successFlag + errorFlag == numFiles )
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
                successFlag++;
                progressBar( 1, eventId );
                $( '#progid' + eventId + ' .progressBar').css( 'background-color', '#87c8fd');
            }
            else
            {   
                errorFlag++;
                $( '#progid' + eventId ).html( '<p class="uploadError">' + data + '</p>' );
            }
        }
        
        

    
    
        function checkFileSize( file, id )
        {
            if ( file.size >= MAX_FILE_SIZE )
            {
                $( '#progid' + id ).html( '<p class="uploadError">Maximum File Size of ' + MAX_FILE_SIZE + ' Exceeded</p>' );
                return false;
            }
            
            return true;
        }
        
        
        function checkFileLength( file )
        {
            if ( file.length < 1 )
            {   
                $( '.msg-container' ).html( '<p class="error">Please Select a File To Upload</p>' );
            }
        }
            
        
        /*
        * Generate progress bar 
        */  
        function progressBar( percent, eventId )
        {
            var width = Math.ceil( 100 * percent ) + '%';
        
            $( '#progid' + eventId + ' .progressBar' ).width( width );
        }
    
    });

});



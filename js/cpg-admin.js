jQuery(document).ready(function($){
    var mediaUploader;

    $(document).on('click', '.cpg-upload-logo', function(e){
        e.preventDefault();

        // If frame already exists, re-open it.
        if ( mediaUploader ) {
            mediaUploader.open();
            return;
        }

        // Create the media frame.
        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Brand Logo',
            button: {
                text: 'Choose Logo'
            },
            multiple: false
        });

        mediaUploader.on('select', function(){
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#_brand_logo').val( attachment.url );
        });

        mediaUploader.open();
    });
});

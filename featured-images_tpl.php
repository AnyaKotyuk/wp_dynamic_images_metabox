<?php
/**
 * Template for button metabox on edit post page
 *
 */

?>
<style type="text/css">
    #featured_images .empty-image-container{
        display: none;
    }
    #featured_images .featured-image-container{
        box-shadow: 0 0 1px 1px #ccc;
        margin-bottom: 10px;
        padding: 5px;
    }
    #featured_images p{
        margin-top: 3px;
    }
    #featured_images .delete-img{
        color: #a00;
    }
    #featured_images .add-new{
        display: block;
        background-color: #f1f1f1;
        padding: 10px;
        text-align: center;
    }
</style>

<?php
$n = 0;
foreach ($images as $k => $image) {
    echo show_featured_image($image, $n); // show featured image element
    $n++;
}
?>
<a class="add-new" href="#"><?php _e('Add new +', 'thx') ?></a>
<div class="empty-image-container">
    <?php echo show_featured_image(null); ?>
</div>

<script type="text/javascript" >

    var frame, container, n = <?php echo count($images); ?>;

    function addNewFeaturedImage(){
        jQuery(jQuery('#featured_images .empty-image-container').html()).insertBefore('#featured_images .inside .add-new');
        jQuery('#featured_images .inside > .featured-image-container input[name=featured-img-new]').attr('name', 'featured-img-id['+n+']');
        n++;
    }

    jQuery(document).ready(function(){
        jQuery('#featured_images .inside .add-new').click(function(){
            addNewFeaturedImage();
            return false;
        });
    });

    jQuery(document).on('click', '#featured_images.postbox .upload-img', function(){
        event.preventDefault();

        container = jQuery(this).closest('.featured-image-container');
        // If the media frame already exists, reopen it.
        if ( frame ) {
            frame.open();
            return;
        }

        // Create a new media frame
        frame = wp.media({
            title: 'Select or Upload Media Of Your Chosen Persuasion',
            button: {
                text: 'Use this media'
            },
            multiple: false  // Set to true to allow multiple files to be selected
        });


        // When an image is selected in the media frame...
        frame.on( 'select', function() {

            // Get media attachment details from the frame state
            var attachment = frame.state().get('selection').first().toJSON();

            // Send the attachment URL to our custom image input field.
            jQuery(container).find('.img-container').append( '<img src="'+attachment.url+'" alt="" style="max-width:100%;"/>' );

            // Send the attachment id to our hidden input
            jQuery(container).find('.image-id').val( attachment.id );

            // Hide the add image link
            jQuery(container).find('.upload-img').addClass( 'hidden' );

            // Unhide the remove image link
            jQuery(container).find('.delete-img').removeClass( 'hidden' );
        });

        // Finally, open the modal on click
        frame.open();
    });
    jQuery(document).on('click', '#featured_images.postbox .delete-img', function(){
        var container = jQuery(this).parent().parent();
        event.preventDefault();

//        console.log(container, container);
        // Clear out the preview image
        jQuery(container).find('.img-container').html( '' );

        // Un-hide the add image link
        jQuery(container).find('.upload-img').removeClass( 'hidden' );

        // Hide the delete image link
        jQuery(container).find('.delete-img').addClass( 'hidden' );

        // Delete the image id from the hidden input
        jQuery(container).find('.image-id').val( '' );
    });


</script>

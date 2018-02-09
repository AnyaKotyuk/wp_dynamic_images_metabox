<?php
/**
 * Class Button_Metabox
 */

class FeaturedImages_Metabox
{

    /**
     * Init featured images metabox
     *
     * @return void
     */
    public function init()
    {
        $screen = 'post';
        $this->add_metabox($screen);
    }


    /**
     * Add metabox
     *
     * @param  null $screen - post-types which will use this metabox
     * @return void
     */
    public function add_metabox($screen = null)
    {
        add_meta_box(
            'featured_images',
            esc_html__('Gallery', 'thx'),
            array($this, 'show_metabox'),
            $screen,
            'side',
            'low'
        );
    }

    /**
     * Show metabox on post edit
     *
     * @param  object $post - current post object
     * @return void;
     */
    public function show_metabox($post)
    {
        if (!isset($post)) {
            $thx_images = '';
        } else {
            $thx_images = get_post_meta($post->ID, 'thx_featured_images', true);
            $thx_images = json_decode($thx_images, true);
        }

        // form images array
        $images = array();
        if ($thx_images) {
            foreach ($thx_images as $k => $thx_image) {
                if (!$thx_image) {
                    continue;
                }
                $featured_image_src = wp_get_attachment_image_src($thx_image, 'full');
                $have_img = is_array($featured_image_src);
                $images[] = array(
                    'featured_image_src' => $featured_image_src[0],
                    'featured_image_id' => $thx_image,
                    'have_img' => $have_img
                );
            }
        }
        // end form images array


        $tpl_atts = array(
            'images' => $images
        );

        $filename = substr_replace(__FILE__, '_tpl.php', - 4, 4);
        $out_ = thx_get_template($filename, $tpl_atts);

        wp_nonce_field(basename(__FILE__), 'featured_images_nonce');
        if ($out_ != '') {
            echo $out_;
        }
    }


    /**
     * Save metabox
     *
     * @param  int    $post_id
     * @param  object $post
     * @return int, void
     */
    public function save_matabox($post_id, $post)
    {

        if (!isset($_POST['featured_images_nonce']) || !wp_verify_nonce($_POST['featured_images_nonce'], basename(__FILE__))) {
            return $post_id;
        }

        if(!isset($_POST['featured-img-id'])) return false;
        $post_type = get_post_type_object($post->post_type);

        if (!current_user_can($post_type->cap->edit_post, $post_id)) {
            return $post_id;
        }

        $images = array();
        $featured_images = $_POST['featured-img-id'];
        if (!empty($featured_images)) {
            foreach ($featured_images as $k => $featured_image) {
                if($featured_image) $images[] = $featured_image;
            }
        }

        $images = json_encode($images);

        update_post_meta($post_id, 'thx_featured_images', $images);

    }

}



/**
 * Init featured images metabox
 *
 */
function init_featured_images_metabox()
{
    $metabox = new FeaturedImages_Metabox();
    $metabox->init();
}

/**
 * Save featured images metabox when post is save
 *
 * @param int    $post_id
 * @param object $post
 */
function save_featured_images_metabox($post_id = null, $post = null)
{
    $metabox = new FeaturedImages_Metabox();
    $metabox->save_matabox($post_id, $post);
}

/**
 * Add scripts for media
 *
 */
function add_featured_images_scripts()
{
    wp_enqueue_media();
}

/**
 * Show featured image metabox element
 *
 * @param  array $image - image data, null if this is block for new image
 * @param  int   $n     - block order
 * @return string
 */
function show_featured_image($image = null, $n = null){
    global $post;
    $upload_link = esc_url(get_upload_iframe_src('image', $post->ID));

    $input_name = ($n !== null)?'featured-img-id['.$n.']':'featured-img-new';
    ob_start();
    ?>

    <div class="featured-image-container">
        <div class="img-container">
            <?php if ($image['have_img'] ) { ?>
                <img src="<?php echo $image['featured_image_src']; ?>" alt="" style="max-width:100%;" />
            <?php } ?>
        </div>
        <p class="hide-if-no-js">
            <a class="upload-img <?php if ($image['have_img']) { echo 'hidden'; } ?>"
               href="<?php echo $upload_link ?>">
                <?php _e('Set gallery image') ?>
            </a>
            <a class="delete-img <?php if (!$image['have_img']) {echo 'hidden';} ?>"
               href="#">
                <?php _e('Remove this image') ?>
            </a>
        </p>
        <input class="image-id" name="<?php echo $input_name; ?>" type="hidden" value="<?php echo esc_attr( $image['featured_image_id'] ); ?>" />
    </div>

<?php
    return ob_get_clean();
}
?>

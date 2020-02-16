<?php

namespace TestStars\Controller;

use TestStars\Model\Stars;

class Admin
{
    private $starsObj;

    public function __construct()
    {
        if (is_admin()) {
            add_action('add_meta_boxes', array($this, 'registerMetaBox'));
            add_action('save_post', array($this, 'saveMetaBox' ), 10, 2);
            $this->starsObj = new Stars();
        }
    }

    public function registerMetaBox()
    {
        add_meta_box(
            'stars',
            'Stars',
            array($this, 'renderMetaBox'),
            'post',
            'advanced',
            'default'
        );
    }

    public function renderMetaBox()
    {
        wp_nonce_field( 'custom_nonce_action', 'custom_nonce' );

        echo '<input
        name="stars"
        type="number"
        min="1"
        max="5"
        value="' . (int)$this->starsObj->getStars() . '">';
    }


    /**
     * Handles saving the meta box.
     *
     * @param int     $post_id Post ID.
     * @param \WP_Post $post   Post object.
     * @return null
     */
    public function saveMetaBox( $post_id, $post )
    {
        // Add nonce for security and authentication.
        $nonce_name   = isset($_POST['custom_nonce']) ? $_POST['custom_nonce'] : '';
        $nonce_action = 'custom_nonce_action';

        // Check if nonce is valid.
        if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
            return;
        }

        // Check if user has permissions to save data.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Check if not an autosave.
        if ( wp_is_post_autosave( $post_id ) ) {
            return;
        }

        // Check if not a revision.
        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }

        // Make sure that it is set.
        if ( ! isset( $_POST['stars'] ) ) {
            return;
        }

        $stars = $this->validateNumberOfStars((int)$_POST['stars']);
        $this->starsObj->setStars($stars);
    }

    /**
     * @param int $number
     * @return int
     */
    private function validateNumberOfStars($number){
    	$stars = abs($number);
    	if($stars > 5){
			$stars = 5;
		}
    	return $stars;
	}
}

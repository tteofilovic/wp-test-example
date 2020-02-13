<?php

use \TestStars\Model\Stars;
use \TestStars\Controller\Admin;

class Test_Admin extends WP_UnitTestCase
{

    private $postId;
    private $stars;
    private $admin;
    private $userId;

    public function setUp()
    {
        parent::setUp();

        $this->postId = $this->factory->post->create([
            'post_status' => 'publish',
        ]);

        $this->userId = $this->factory->user->create(['role' => 'editor']);
        wp_set_current_user($this->userId);

        set_current_screen( 'edit-post' );

        $this->stars = new Stars();
        $this->admin = new Admin($this->stars);
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function test_construct()
    {
        $action = has_action('add_meta_boxes', [$this->admin, 'registerMetaBox']);
        $this->assertTrue( $action === 10 );

        $action = has_action('save_post', [$this->admin, 'saveMetaBox']);
        $this->assertTrue( $action === 10 );
    }

    public function test_registerMetaBox()
    {
        global $wp_meta_boxes;

        $this->admin->registerMetaBox();
        $metaBoxId = $wp_meta_boxes['post']['advanced']['default']['stars']['id'];
        $this->assertTrue( $metaBoxId === 'stars' );
    }

    public function test_renderMetaBox()
    {
        ob_start();
        $this->admin->renderMetaBox();
        $content = ob_get_clean();

        $this->assertTrue( strpos($content, 'name="custom_nonce"') !== false );
        $this->assertTrue( strpos($content, 'name="stars"') !== false );
    }

    public function test_saveMetaBox()
    {
        ob_start();
        wp_nonce_field( 'custom_nonce_action', 'custom_nonce' );
        $content = ob_get_clean();
        $pattern = '/<input(.*)name=\"custom_nonce\" value=\"(.*?)\"/i';
        preg_match_all($pattern, $content, $matches);

        $wp_query = new WP_Query([
            'post__in' => [$this->postId],
            'post_per_page' => 1
        ]);
        $value = 0;

        if($wp_query->have_posts()){
            while($wp_query->have_posts()){
                $wp_query->the_post();
                $_POST['stars'] = 1;
                $_POST['custom_nonce'] = $matches[2][0];
                $this->admin->saveMetaBox($this->postId, null);
                $value = (int)get_post_meta($this->postId, 'stars', true);
            }
        }
        $this->assertTrue( $value === 1 );
    }

}
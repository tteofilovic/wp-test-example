<?php

use \TestStars\Model\Stars;
use \TestStars\Controller\Front;

class Test_Front extends WP_UnitTestCase
{
    private $postId;
    private $stars;
    private $front;

    public function setUp()
    {
        parent::setUp();

        $this->postId = $this->factory->post->create([
            'post_status' => 'publish',
        ]);

        $this->stars = new Stars();
        $this->front = new Front($this->stars);
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function test_construct() {

        $action = has_action('wp_enqueue_scripts', [$this->front, 'enqueueScripts']);
        $this->assertTrue( $action === 10 );

        $action = has_filter('the_content', [$this->front, 'addStarsToTheContent']);
        $this->assertTrue( $action === 10 );
    }

    public function test_enqueueScripts() {

        $this->front->enqueueScripts();
        $this->assertTrue( wp_style_is( 'test-stars-style', 'enqueued' ) );
    }

    public function test_addStarsToTheContent() {

        update_post_meta($this->postId, 'stars', '5');

        $wp_query = new WP_Query([
            'post__in' => [$this->postId],
            'post_per_page' => 1
        ]);
        $content = 'Content';

        if($wp_query->have_posts()){
            while($wp_query->have_posts()){
                $wp_query->the_post();
                $content = $this->front->addStarsToTheContent($content);
            }
        }

        $this->assertTrue( substr_count($content, '★ ') == 5 );
    }
}
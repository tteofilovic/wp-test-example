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

        // Create a dummy post using the 'WP_UnitTest_Factory_For_Post' class
        $this->postId = $this->factory->post->create(
            [
                'post_status' => 'publish',
                'post_type' => 'post'
            ]
        );

        // Initialize Front class
        $this->stars = new Stars();
        $this->front = new Front($this->stars);
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function class_constructor()
    {
        // Check if action "wp_enqueue_scripts" is registered.
        $action = has_action('wp_enqueue_scripts', [$this->front, 'enqueueScripts']);
        $this->assertTrue($action === 10);

        // Check if filter "the_content" is registered.
        $action = has_filter('the_content', [$this->front, 'addStarsToTheContent']);
        $this->assertTrue($action === 10);
    }

    /**
     * @test
     */
    public function enqueue_style_check()
    {
        // Check if style is enqueued.
        $this->front->enqueueScripts();
        $this->assertTrue(wp_style_is('test-stars-style', 'enqueued'));
    }

    /**
     * @test
     */
    public function add_stars_to_the_content()
    {
        // Create a custom query for the post with the above created post id.
        $wp_query = new WP_Query(
            [
                'post__in' => [$this->postId],
                'post_per_page' => 1
            ]
        );

        // Run the WordPress loop through this query to set the global $post.
        if ($wp_query->have_posts()) {
            while ($wp_query->have_posts()) {
                $wp_query->the_post();
            }
        }

        // Set number of stars
        update_post_meta($this->postId, 'stars', '5');

        // Initialize content with string "Content"
        $content = 'Content';

        // Execute method addStarsToTheContent
        $content = $this->front->addStarsToTheContent($content);

        // Check number of stars in the content.
        // It should look like: '<div class="stars">★ ★ ★ ★ ★ </div>Content'
        $this->assertTrue(substr_count($content, '★ ') === 5);

        // Initialize content with string "Content"
        $content = 'Content';

        // Use filter to alter number of stars
        add_filter('stars_string', function($stars){
            return 2 * $stars;
        });

        // Execute method addStarsToTheContent and check number of stars
        $content = $this->front->addStarsToTheContent($content);
        $this->assertTrue(substr_count($content, '★ ') === 10);
    }
}

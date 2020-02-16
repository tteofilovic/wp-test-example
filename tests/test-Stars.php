<?php

use \TestStars\Model\Stars;

class Test_Stars extends WP_UnitTestCase
{

    private $postId;
    private $stars;

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

        // Initialize Stars class
        $this->stars = new Stars();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function test_get_stars()
    {
        // Set number of stars
        update_post_meta($this->postId, 'stars', '4');

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

        // Execute method getStars()
        $value = (int)$this->stars->getStars();

        // Check value
        $this->assertTrue($value === 4);
    }

    /**
     * @test
     */
    public function test_set_stars()
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

        // Execute method setStars()
        $this->stars->setStars(3);

        // Get post meta and check number of stars
        $value = (int)get_post_meta($this->postId, 'stars', true);
        $this->assertTrue($value === 3);
    }
}

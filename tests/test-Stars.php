<?php

use \TestStars\Model\Stars;

class Test_Stars extends WP_UnitTestCase
{

    private $postId;
    private $stars;

    public function setUp()
    {
        parent::setUp();

        $this->postId = $this->factory->post->create([
            'post_status' => 'publish',
            'post_title' => 'Test title',
            'post_content' => 'Test content'
        ]);
        $this->stars = new Stars();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function test_getStars() {

        global $wp_query;

        update_post_meta($this->postId, 'stars', '4');
        $wp_query = new WP_Query([
            'post__in' => [$this->postId],
            'post_per_page' => 1
        ]);
        $value = 0;

        if($wp_query->have_posts()){
            while($wp_query->have_posts()){
                $wp_query->the_post();
                $value = (int)$this->stars->getStars();
            }
        }

        $this->assertTrue( $value === 4 );
    }

    public function test_setStars() {

        global $wp_query;

        $wp_query = new WP_Query([
            'post__in' => [$this->postId],
            'post_per_page' => 1
        ]);

        if($wp_query->have_posts()){
            while($wp_query->have_posts()){
                $wp_query->the_post();
                $this->stars->setStars(3);
            }
        }

        $value = (int)get_post_meta($this->postId, 'stars', true);
        $this->assertTrue( $value === 3 );
    }
}
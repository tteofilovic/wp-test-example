<?php

namespace TestStars\Model;

class Stars
{
    public function __construct() { }

    public function getStars()
    {
        $postId = get_the_ID();
        $stars = get_post_meta($postId, 'stars', true);
        return $stars;
    }

    public function setStars($stars)
    {
        $postId = get_the_ID();
        update_post_meta($postId, 'stars', $stars);
        return;
    }
}
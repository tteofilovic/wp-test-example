<?php

namespace TestStars\Controller;

use TestStars\Model\Stars;

class Front
{
    private $starsObj;

    public function __construct(Stars $stars)
    {
        if (! is_admin()) {
            add_action('wp_enqueue_scripts',array($this, 'enqueueScripts'));
            add_filter('the_content', array($this, 'addStarsToTheContent'));
            $this->starsObj = $stars;
        }
    }

    public function enqueueScripts()
    {
        wp_enqueue_style( 'test-stars-style', TEST_STARS_DIR_URI . 'assets/front/css/stars.css' );
    }

    public function addStarsToTheContent($content)
    {
        $stars = $this->starsObj->getStars();
        $starsString = '<div class="stars">';
        for ($i=0; $i < $stars; $i++){
            $starsString .= '★ ';
        }
        $starsString .= '</div>';

        $starsString = apply_filters('stars_string', $starsString, $stars);

        $content = $starsString . $content;
        return $content;
    }
}
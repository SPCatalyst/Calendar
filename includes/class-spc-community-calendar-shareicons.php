<?php

class SPC_Community_Calendar_Shareicons
{
    /**
     * Generate
     * @param $args
     * @return string[]
     */
    public function generate($args)
    {
        $url = urlencode($args['url']);
        $title = urlencode($args['title']);
        $desc = urlencode($args['desc']);
        $via = isset($args['via']) ? urlencode($args['via']) : '';

        $hash_tags = isset($args['hash_tags']) ? urlencode($args['hash_tags']) : '';

        $text = $title;

        if ($desc) {
            $text .= '%20%3A%20';    # This is just this, " : "
            $text .= $desc;
        }

        $via = !empty($via) ? '&via='.$via : '';
        $hash_tags = !empty($hash_tags) ? '&hashtags='.$hash_tags  : '';

        return [
            'mail' => '#',
            'facebook' => 'http://www.facebook.com/sharer.php?u=' . $url,
            'twitter' => 'https://twitter.com/intent/tweet?url=' . $url . '&text=' . $text . $via . $hash_tags,
            'linkedin' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $url,
            //'pinterest' => 'http://pinterest.com/pin/create/button/?url=' . $url,
        ];
    }
}
<?php
if (is_page() || is_single()) {
    global $post;
    $post_id = $post->ID;
    $post_vseo_meta = get_post_meta($post_id, 'vseo_meta');
    $translated_post_vseo_meta = '
    <script type="application/ld+json">
        {
            "@context": "http://schema.org/",
            "@type": "VideoObject",
            "name": "Cat video",
            "duration": "P10M",
            "uploadDate": "2019-07-19",
            "thumbnailUrl": "http://www.example.com/cat.jpg",
            "description": "Watch this cat jump over a fence!",
            "contentUrl": "http://www.example.com/cat_video_full.mp4",
            "hasPart": [{
                    "@type": "Clip",
                    "name": "Cat jumps",
                    "startOffset": 30,
                    "endOffset": 45,
                    "url": "http://www.example.com/example?t=30"
                },
                {
                    "@type": "Clip",
                    "name": "Cat misses the fence",
                    "startOffset": 111,
                    "endOffset": 150,
                    "url": "http://www.example.com/example?t=111"
                }
            ]
        }
    </script>';
    echo $translated_post_vseo_meta;
}
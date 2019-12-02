<?php
if (is_page() || is_single()) {
    function metadata_veso_populate() {
        global $post;
        $post_id = $post->ID;
        $post_url = get_permalink($post_id);
        $post_vseo_meta = get_post_meta($post_id, 'vseo_meta');
        foreach ($post_vseo_meta as $meta) {
            try {
                $meta = json_decode($meta, true);
                $playlist_id = $meta['playlist_id'];
                $playlist_title = $meta['playlist_title'];
                if (isset($meta['clips'])) {
                    $clip_meta_all = array();
                    $thumbnails = array();
                    foreach ($meta['clips'] as $clip) {
                        $thumbnails[] = $clip['moment_thumb'];
                        $clip_meta_all[] = array(
                            "@type" => "Clip",
                            "name" => $clip['moment_tag'],
                            "startOffset" => $clip['moment_time_start'],
                            "endOffset" => $clip['moment_time_end'],
                            "url" => $clip['moment_cust_url'] . (strpos($clip['moment_cust_url'], '?') > 0 ? '&' : '?') . 'playlist=' . $playlist_id . '&moment=' . $clip['moment_id']
                        );
                    }
                    $clip_meta_all = json_encode($clip_meta_all);
                    $thumbnails = json_encode($thumbnails);

                    $translated_post_vseo_meta = sprintf(
                        '<script type="application/ld+json">
                            {
                                "@context": "http://schema.org/",
                                "@type": "VideoObject",
                                "name": %s,
                                "thumbnailUrl": %s,
                                "contentUrl": %s,
                                "hasPart": %s
                            }
                        </script>',
                        $playlist_title,
                        $thumbnails,
                        $post_url,
                        $clip_meta_all
                    );
                    echo $translated_post_vseo_meta;
                }
            } catch (Exception $e) { }
        }
    }
    metadata_veso_populate();
}

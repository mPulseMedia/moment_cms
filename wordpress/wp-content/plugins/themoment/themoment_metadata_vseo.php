<?php
if (is_page() || is_single()) {
    function metadata_veso_populate() {
        global $post;
        $post_id = $post->ID;
        $post_url = get_permalink($post_id);
        $post_playlist_data = get_post_meta($post_id, 'playlist_data');
        foreach ($post_playlist_data as $playlist_data) {
            try {
                $playlist_data = json_decode($playlist_data, true);
                $playlist_id = $playlist_data['playlist_id'];
                $playlist_title = $playlist_data['playlist_title'];
                if (isset($playlist_data['clips'])) {
                    $clip_meta_all = array();
                    $thumbnails = array();
                    foreach ($playlist_data['clips'] as $clip) {
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

                    $metadata_vseo = sprintf(
                        '<script type="application/ld+json">
                            {
                                "@context": "http://schema.org/",
                                "@type": "VideoObject",
                                "name": "%s",
                                "thumbnailUrl": %s,
                                "contentUrl": "%s",
                                "hasPart": %s
                            }
                        </script>',
                        $playlist_title,
                        $thumbnails,
                        $post_url,
                        $clip_meta_all
                    );
                    echo $metadata_vseo;
                }
            } catch (Exception $e) { }
        }
    }
    metadata_veso_populate();
}

<?php
if (is_singular()) {
    function metadata_veso_populate()
    {
        $post_id = get_queried_object_id();
        $post_url = get_permalink($post_id);

        $wp_db_data = get_post_meta($post_id, 'playlist_data');
        $upload_date = date("c");
        foreach ($wp_db_data as $playlist_data) {
            try {
                $playlist_data = json_decode($playlist_data, true);
                $playlist_id = $playlist_data['playlist_id'];

                $playlist_object = playlist_object_get($playlist_id);
                $playlist_title = playlist_object_title_get($playlist_object);
                $moment_objects = playlist_object_moment_object_array_get($playlist_object);
                
                $clip_meta_all = array();
                $thumbnails = array();
                foreach ($moment_objects as $clip) {
                    $thumbnails[] = $clip['moment_thumb'];
                    $clip_meta_all[] = array(
                        "@type" => "Clip",
                        "name" => $clip['tag'],
                        "startOffset" => $clip['time_start'],
                        "endOffset" => $clip['time_end'],
                        "url" => url_update($clip['moment_cust_url'], array('playlist' => $playlist_id, 'moment' => $clip['id']), 'anchor-' . $post_id)
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
                                "description": "%s",
                                "thumbnailUrl": %s,
                                "contentUrl": "%s",
                                "uploadDate": "%s",
                                "hasPart": %s
                            }
                        </script>',
                    $playlist_title,
                    $playlist_title,
                    $thumbnails,
                    $post_url,
                    $upload_date,
                    $clip_meta_all
                );
                echo $metadata_vseo;
            } catch (Exception $e) {
            }
        }
    }
    function url_update($url = '', $params = array(), $hash = '')
    {
        return $url . (strpos($url, '?') > 0 ? '&' : '?') . http_build_query($params) . (strlen($hash) > 0 ? ('#' . $hash) : '');
    }
    metadata_veso_populate();
}
?>
<style type="text/css">
    iframe[src*="youtube.com"]:not([src*="enablejsapi=1"]) {
        opacity: 0;
    }
    iframe[src*="youtube.com"][src*="enablejsapi=1"] {
        opacity: 1;
    }
</style>
<?php

/**
 * Shown in the Post page (called from SQ_Menu)
 *
 */
class Model_SQ_Post {

    /**
     * Set the callback to tinymce with javascript SQ_eventChange function call on event
     * @param string $init
     * @return string
     */
    public function setCallback($init) {

        if (wp_default_editor() == 'tinymce') {
            $init['setup'] = 'window.sq_tinymce.setup';
            $init['onchange_callback'] = 'window.sq_tinymce.callback';
            $init['content_css'] = ((isset($init['content_css']) && $init['content_css'] <> '') ? $init['content_css'] . ',' : '' ) . _SQ_THEME_URL_ . 'css/sq_frontend.css';
        }

        return $init;
    }

    /**
     * Register a button in tinymce editor
     */
    public function registerButton($buttons) {
        array_push($buttons, "|", "heading");
        return $buttons;
    }

    public function addHeadingButton($plugin_array) {
        $plugin_array['heading'] = _SQ_THEME_URL_ . 'js/tinymce.js';
        return $plugin_array;
    }

    /**
     * Search for posts in the local blog
     *
     * @global object $wpdb
     * @param string $q
     * @return array
     */
    public function searchPost($q, $exclude = array(), $start = 0, $nbr = 8) {
        global $wpdb;
        $responce = array();
        if (sizeof($exclude) > 1) {
            $exclude = join(',', $exclude);
        } else
            $exclude = (int) $exclude;

        $q = trim($q, '"');
        /* search in wp database */
        $posts = $wpdb->get_results("SELECT ID, post_title, post_date_gmt, post_content, post_type FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND (post_title LIKE '%$q%' OR post_content LIKE '%$q%') AND ID not in ($exclude) ORDER BY post_title LIMIT " . $start . ',' . ($start + $nbr));


        if ($posts) {

            $responce['total'] = $wpdb->num_rows;
            foreach ($posts as $post) {
                $responce['results'][] = array('id' => $post->ID,
                    'url' => get_permalink($post->ID),
                    'title' => $post->post_title,
                    'content' => $this->truncate($post->post_content, 50),
                    'date' => $post->post_date_gmt);
            }
        } else {
            $responce['error'] .= __('Squirrly could not find any results for: ') . ' "' . stripslashes($q) . '"';
        }
        return json_encode($responce);
    }

    private function truncate($text, $length = 25) {
        if (!$length)
            return $text;

        $text = str_replace(']]>', ']]&gt;', $text);
        $text = @preg_replace('|\[(.+?)\](.+?\[/\\1\])?|s', '', $text);
        $text = strip_tags($text);
        $words = explode(' ', $text, $length + 1);

        if (count($words) > $length) {
            array_pop($words);
            array_push($words, '...');
            $text = implode(' ', $words);
        }
        return $text;
    }

    /**
     * Add the image for Open Graph
     *
     * @param string $file
     * @return array [name (the name of the file), image (the path of the image), message (the returned message)]
     *
     */
    function addImage(&$file, $overrides = false, $time = null) {
        // The default error handler.
        if (!function_exists('wp_handle_upload_error')) {

            function wp_handle_upload_error(&$file, $message) {
                return array('error' => $message);
            }

        }

        /**
         * Filter data for the current file to upload.
         *
         * @since 2.9.0
         *
         * @param array $file An array of data for a single file.
         */
        $file = apply_filters('wp_handle_upload_prefilter', $file);

        // You may define your own function and pass the name in $overrides['upload_error_handler']
        $upload_error_handler = 'wp_handle_upload_error';

        // You may have had one or more 'wp_handle_upload_prefilter' functions error out the file. Handle that gracefully.
        if (isset($file['error']) && !is_numeric($file['error']) && $file['error'])
            return $upload_error_handler($file, $file['error']);

        // You may define your own function and pass the name in $overrides['unique_filename_callback']
        $unique_filename_callback = null;

        // $_POST['action'] must be set and its value must equal $overrides['action'] or this:
        $action = 'sq_save_ogimage';

        // Courtesy of php.net, the strings that describe the error indicated in $_FILES[{form field}]['error'].
        $upload_error_strings = array(false,
            __("The uploaded file exceeds the upload_max_filesize directive in php.ini."),
            __("The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form."),
            __("The uploaded file was only partially uploaded."),
            __("No file was uploaded."),
            '',
            __("Missing a temporary folder."),
            __("Failed to write file to disk."),
            __("File upload stopped by extension."));

        // All tests are on by default. Most can be turned off by $overrides[{test_name}] = false;
        $test_form = true;
        $test_size = true;
        $test_upload = true;

        // If you override this, you must provide $ext and $type!!!!
        $test_type = true;
        $mimes = false;

        // Install user overrides. Did we mention that this voids your warranty?
        if (is_array($overrides))
            extract($overrides, EXTR_OVERWRITE);

        // A correct form post will pass this test.
        if ($test_form && (!isset($_POST['action']) || ($_POST['action'] != $action ) ))
            return call_user_func($upload_error_handler, $file, __('Invalid form submission.'));

        // A successful upload will pass this test. It makes no sense to override this one.
        if (isset($file['error']) && $file['error'] > 0) {
            return call_user_func($upload_error_handler, $file, $upload_error_strings[$file['error']]);
        }

        // A non-empty file will pass this test.
        if ($test_size && !($file['size'] > 0 )) {
            if (is_multisite())
                $error_msg = __('File is empty. Please upload something more substantial.');
            else
                $error_msg = __('File is empty. Please upload something more substantial. This error could also be caused by uploads being disabled in your php.ini or by post_max_size being defined as smaller than upload_max_filesize in php.ini.');
            return call_user_func($upload_error_handler, $file, $error_msg);
        }

        // A properly uploaded file will pass this test. There should be no reason to override this one.
        if ($test_upload && !@ is_uploaded_file($file['tmp_name']))
            return call_user_func($upload_error_handler, $file, __('Specified file failed upload test.'));

        // A correct MIME type will pass this test. Override $mimes or use the upload_mimes filter.
        if ($test_type) {
            $wp_filetype = wp_check_filetype_and_ext($file['tmp_name'], $file['name'], $mimes);

            extract($wp_filetype);

            // Check to see if wp_check_filetype_and_ext() determined the filename was incorrect
            if ($proper_filename)
                $file['name'] = $proper_filename;

            if ((!$type || !$ext ) && !current_user_can('unfiltered_upload'))
                return call_user_func($upload_error_handler, $file, __('Sorry, this file type is not permitted for security reasons.'));

            if (!$ext)
                $ext = ltrim(strrchr($file['name'], '.'), '.');

            if (!$type)
                $type = $file['type'];
        } else {
            $type = '';
        }

        // A writable uploads dir will pass this test. Again, there's no point overriding this one.
        if (!( ( $uploads = wp_upload_dir($time) ) && false === $uploads['error'] ))
            return call_user_func($upload_error_handler, $file, $uploads['error']);

        $filename = wp_unique_filename($uploads['path'], $file['name'], $unique_filename_callback);

        // Move the file to the uploads dir
        $new_file = $uploads['path'] . "/$filename";
        if (false === @ move_uploaded_file($file['tmp_name'], $new_file)) {
            if (0 === strpos($uploads['basedir'], ABSPATH))
                $error_path = str_replace(ABSPATH, '', $uploads['basedir']) . $uploads['subdir'];
            else
                $error_path = basename($uploads['basedir']) . $uploads['subdir'];

            return $upload_error_handler($file, sprintf(__('The uploaded file could not be moved to %s.'), $error_path));
        }

        // Set correct file permissions
        $stat = stat(dirname($new_file));
        $perms = $stat['mode'] & 0000666;
        @ chmod($new_file, $perms);

        // Compute the URL
        $url = $uploads['url'] . "/$filename";

        if (is_multisite())
            delete_transient('dirsize_cache');

        /**
         * Filter the data array for the uploaded file.
         *
         * @since 2.1.0
         *
         * @param array  $upload {
         *     Array of upload data.
         *
         *     @type string $file Filename of the newly-uploaded file.
         *     @type string $url  URL of the uploaded file.
         *     @type string $type File type.
         * }
         * @param string $context The type of upload action. Accepts 'upload' or 'sideload'.
         */
        return apply_filters('wp_handle_upload', array('file' => $new_file, 'url' => $url, 'type' => $type), 'upload');
    }

    /**
     * Upload the image on server from version 2.0.4
     *
     * Add configuration page
     */
    public function upload_image($url) {
        $dir = null;
        $file = array();

        $response = wp_remote_get($url, array('timeout' => 30));
        $file = wp_upload_bits(basename($url), '', wp_remote_retrieve_body($response), $dir);

        $file['type'] = wp_remote_retrieve_header($response, 'content-type');

        if (!isset($file['error']) || $file['error'] == '')
            if (isset($file['url']) && $file['url'] <> '') {
                $file['url'] = str_replace(get_bloginfo('url'), '', $file['url']);
                $file['filename'] = basename($file['url']);

                return $file;
            }

        return false;
    }

    /**
     * Save/Update Meta in database
     *
     * @param integer $post_id
     * @param array $metas [key,value]
     * @return true
     */
    public function saveAdvMeta($post_id, $metas) {
        global $wpdb;

        if ((int) $post_id == 0 || !is_array($metas))
            return;

        foreach ($metas as $meta) {
            $sql = "SELECT `meta_value`
                    FROM `" . $wpdb->postmeta . "`
                    WHERE `meta_key` = '" . $meta['key'] . "' AND `post_id`=" . (int) $post_id;

            if ($wpdb->get_row($sql)) {
                $sql = "UPDATE `" . $wpdb->postmeta . "`
                       SET `meta_value` = '" . addslashes($meta['value']) . "'
                       WHERE `meta_key` = '" . $meta['key'] . "' AND `post_id`=" . (int) $post_id;
            } else {
                $sql = "INSERT INTO `" . $wpdb->postmeta . "`
                    (`post_id`,`meta_key`,`meta_value`)
                    VALUES (" . (int) $post_id . ",'" . $meta['key'] . "','" . $meta['value'] . "')";
            }
            $wpdb->query($sql);
        }

        return $metas;
    }

    /**
     * check if there are keywords saved
     * @global object $wpdb
     * @return integer|false
     */
    public function countKeywords() {
        global $wpdb;

        if ($posts = $wpdb->get_row("SELECT count(`post_id`) as count
                       FROM `" . $wpdb->postmeta . "`
                       WHERE (`meta_key` = 'sq_post_keyword')")) {

            return $posts->count;
        }

        return false;
    }

    /**
     * check if there are keywords saved
     * @global object $wpdb
     * @return integer|false
     */
    public function getPostWithKeywords($filter = '', $ord = 'meta_id') {
        global $wpdb;

        if ($filter <> '') {
            $filter = ' AND (' . $filter . ') ';
        }

        if ($posts = $wpdb->get_results("SELECT `post_id`, `meta_value`
                FROM `" . $wpdb->postmeta . "`
                WHERE (`meta_key` = 'sq_post_keyword')"
                . $filter .
                'ORDER BY `' . $ord . '`')) {

            $posts = array_map(create_function('$post', '$post->meta_value = json_decode($post->meta_value); return $post;'), $posts);
            return $posts;
        }

        return false;
    }

    /**
     * get the keyword
     * @global object $wpdb
     * @param integer $post_id
     * @return boolean
     */
    public function getKeyword($post_id) {
        global $wpdb;

        if ($row = $wpdb->get_row("SELECT `post_id`, `meta_value`
                       FROM `" . $wpdb->postmeta . "`
                       WHERE (`meta_key` = 'sq_post_keyword' AND `post_id`=" . (int) $post_id . ")
                       ORDER BY `meta_id` DESC")) {

            return json_decode($row->meta_value);
        }

        return false;
    }

    /**
     * Save the post keyword
     * @param integer $post_id
     * @param object $args
     */
    public function saveKeyword($post_id, $args) {
        $args->update = current_time('timestamp');

        $meta[] = array('key' => 'sq_post_keyword',
            'value' => json_encode($args));

        $this->saveAdvMeta($post_id, $meta);

        return json_encode($args);
    }

}

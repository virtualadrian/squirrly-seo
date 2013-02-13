<?php

class Model_SQ_Frontend {
    private $header;
    private $title;
    private $description;
    private $min_title_length = 10;
    private $max_title_length = 75;
    private $max_description_length = 160;
    private $min_description_length = 70;
    
    /**
     * Write the signature
     * @return string
     */
    function setStart(){
        return "\n\n<!-- Squirrly Wordpress SEO Plugin -->\n";
    }
    
    /**
     * End the signature
     * @return string
     */
    function setEnd(){
        return "<!-- /Squirrly Wordpress SEO Plugin -->\n\n";
    }
    
    /**
     * Get the header from wordpress 
     * 
     * @return string
     */
    private function grabHeader(){
        $this->header = ob_get_contents();
        
        /* stop the recording started in constructor */
        ob_end_clean();
    }
    
    /**
     * Overwrite the header with the correct parameters
     * 
     * @return string
     */
    function setHeader($options){
        global $wp_query;
        
        //print_r($wp_query);
        $ret = '';
        $this->grabHeader();
        
        if (!function_exists('preg_replace')) return $this->header;
        
        $ret .= $this->setStart();
        
        /* Meta setting*/
        
        
        $ret .= $this->setCustomTitle();
        $ret .= $this->setCustomDescription();
        $ret .= $this->setCustomKeyword();
       
        $ret .= $this->setCanonical();
        $ret .= $this->getXMLSitemap();
        /* Auto setting*/
        
        
        $ret .= $this->getFavicon();
        $ret .= $this->getCopyright();
        $ret .= $this->getPublisher();
        $ret .= $this->getLanguage();
        $ret .= $this->getDCPublisher();
        
        /* SEO optimizer tool*/
        $ret .= $this->getGoogleWT();
        $ret .= $this->getGoogleAnalytics();
        $ret .= $this->getFacebookIns();
        $ret .= $this->getBingWT();
        
        $ret .= $this->setEnd();
        
        return $this->header . $ret;
    }
    
    /**
     * Get the canonical link for the current page
     * 
     * @return string
     */
    private function setCanonical() {
        //echo $header;
        $url = null;
        $url = $this->getCanonicalUrl();
        if ($url) {
            $this->header = preg_replace('/<.*((link)(.*)("|\')(canonical)*(href\=)*("|\').*)\b[^>]*>/i','',$this->header);
            return sprintf("<link rel=\"canonical\" href=\"%s\" />" , $url) . "\n" ;
        }
        
        return '';
    }

    /**
     * Get the correct title of the article
     * 
     * @return string
     */
    private function setCustomTitle( ) {
        global $wp_query;
        $count  = 0;
        $title = '';
        
        if ($this->checkHomePosts() || $this->checkFrontPage()){
            $title = strip_tags( $this->grabTitleFromPost() );
        }elseif(is_single()){
            $post = $wp_query->get_queried_object();
            $title = strip_tags( $this->grabTitleFromPost($post->ID) );
        }
        
        /* Check if is a predefined Title */
        if(is_home() && SQ_Frontend::$options['sq_fp_title'] <> ''){
            $title = strip_tags( SQ_Frontend::$options['sq_fp_title'] );
        }
        
        if (isset ($title) && !empty($title)){
            $title = $this->truncate($title, $this->min_title_length, $this->max_title_length);
            //Save it and use it later
            $this->title = $title;
            $this->header = @preg_replace('/<title[^<>]*>([^<>]*)<\/title>/si',sprintf("<title>%s</title>" , $title),$this->header, 1, $count);
            if ($count == 0)
               return sprintf("<title>%s</title>" , $this->title) . "\n" ; 
        }
        
        return false;
    }
 
    
    /**
     * Get the description from last/current article
     * 
     * @return string
     */
    private function setCustomDescription() {
        global $wp_query;
        
        if(is_home() || is_single() || is_page() || $this->checkPostsPage()) {
           // if ($this->checkFrontPage()) {
            //    $description = trim(stripcslashes(SQ_Tools::i18n('description...')));
            //} else {
            /* Check if is a predefined Keyword */
                if(is_home() && SQ_Frontend::$options['sq_fp_description'] <> '')
                    $description = strip_tags( SQ_Frontend::$options['sq_fp_description'] );
                else
                    $description = $this->grabDescriptionFromPost();
            //}
        }elseif(is_category()) {
            $description = SQ_Tools::i18n(category_description());
        }
        
        if (isset($description) && !(is_home() && is_paged())) {
           
            $description = trim(strip_tags($description));
            $description = str_replace('"', '', $description);

            // replace newlines on mac / windows?
            $description = str_replace("\r\n", ' ', $description);

            // maybe linux uses this alone
            $description = str_replace("\n", ' ', $description);


            $description = apply_filters('sq_description_override', $description);

            //Save it and use it later
            $this->description = (($description <> '') ? $description : $this->title);
            if ($this->description <> ''){
                $this->header =  @preg_replace('/<.*((meta)(.*)("|\')(description)*(content\=)*("|\').*)\b[^>]*>/i','',$this->header);
                return sprintf("<meta name=\"description\" content=\"%s\" />" , $this->description ) . "\n" ; 
            }else{
                return false; 
            }
        }
        return false;
    }
    
   
    /**
     * Get the keywords from articles
     * 
     * @return string
     */
    private function setCustomKeyword() {
        global $wp_query;
        $keywords = '';
        
        if($this->checkPostsPage()){
            $post = $wp_query->get_queried_object();
            $keywords = stripcslashes(SQ_Tools::i18n($this->grabKeywordsFromPost($post->ID)));
        }elseif(is_single()){
            $post = $wp_query->get_queried_object();
            $keywords = stripcslashes(SQ_Tools::i18n($this->grabKeywordsFromPost($post->ID)));
        }else {
            $keywords = trim(SQ_Tools::i18n($this->grabKeywordsFromPost()));
        }
        
        /* Check if is a predefined Keyword */
        if(is_home() && SQ_Frontend::$options['sq_fp_keywords'] <> ''){
            $keywords = strip_tags( SQ_Frontend::$options['sq_fp_keywords'] );
        }
        
        if (isset ($keywords) && !empty($keywords) && !(is_home() && is_paged())) {
            $keywords = str_replace('"','',$keywords);
                   
            $this->header = @preg_replace('/<.*((meta)(.*)("|\')(keywords)*(content\=)*("|\').*)\b[^>]*>/i','',$this->header);
            return sprintf("<meta name=\"keywords\" content=\"%s\" />" , $keywords) . "\n" ; 
        }
        
        return false;
    }    
    /**
     * Get the copyright meta
     * 
     * @return string
     */
    private function getCopyright(){
        $name = $this->getAuthorLinkFromBlog();
        if (!$name)
            $name = get_bloginfo('blogname');
        
        if (($this->checkHomePosts() || $this->checkFrontPage()) && $name )
            return sprintf("<meta name=\"copyright\" content=\"%s\" />" . "\n", $name) . "\n" ;
        
	return false;	
    }
    
    /**
     * Get the publisher meta
     * 
     * @return string
     */
    private function getPublisher(){
        $author = SQ_Frontend::$options['sq_google_plus'];
        if ($author == '')
            $author = $this->getAuthorLinkFromBlog();
        elseif (!$author)
            $author = get_bloginfo('blogname');
        elseif(strpos($author,'plus.google.com') === false && is_numeric($author))
             $author = 'https://plus.google.com/'.$author.'/posts';
            
        if ( is_singular() && $author )
            return '<link rel="author" href="' . $author . '" />' . "\n" ;
        elseif ($author) 
            return '<link rel="publisher" href="' . $author . '" />' . "\n";
        
        return false;
    }

    /**
     * Get the icons for serachengines
     * 
     * @return string
     */
    private function getFavicon(){
        $str = '';
        $rnd = '';
        
        if(function_exists('base64_encode')){
            if (isset(SQ_Tools::$options['favicon_tmp']))
                $rnd = '?ver='.base64_encode(SQ_Tools::$options['favicon_tmp']);
        }        
        
        $favicon = get_bloginfo('wpurl') . '/favicon.ico'.$rnd;
        
        if (($this->checkHomePosts() || $this->checkFrontPage()) &&  file_exists(ABSPATH.'/favicon.ico')){
            $str .= sprintf("<link rel=\"shortcut icon\" type=\"image/png\"  href=\"%s\" />" . "\n", $favicon);
            $str .= sprintf("<link rel=\"apple-touch-icon\" type=\"image/png\"  href=\"%s\" />" . "\n", $favicon);
            $str .= sprintf("<link rel=\"apple-touch-icon-precomposed\" type=\"image/png\"  href=\"%s\" />" . "\n", $favicon);
        }
	return $str;	
    }
    
    /**
     * Get the language meta
     * 
     * @return string
     */
    private function getLanguage(){
        $language = get_bloginfo('language');
        
        if ( ($this->checkHomePosts() || $this->checkFrontPage()) && $language )
            return sprintf("<meta name=\"language\" content=\"%s\" />" , $language) . "\n" ;
        
	return false;	
    }
    
    /**
     * Get the DC.publisher meta
     * 
     * @return string
     */
    private function getDCPublisher(){
        $name = $this->getAuthorLinkFromBlog();
        if (!$name)
            $name = get_bloginfo('blogname');
        
        if ( ($this->checkHomePosts() || $this->checkFrontPage()) && $name )
            return sprintf("<meta name=\"DC.Publisher\" content=\"%s\" />" , $name) . "\n" ;
        
	return false;	
    }
    
    /**
     * Get the XML Sitemap meta
     * 
     * @return string
     */
    private function getXMLSitemap(){
        $xml_url = SQ_ObjController::getController('SQ_Sitemap', false)->getXmlUrl();
        
        if ( ($this->checkHomePosts() || $this->checkFrontPage()) && $xml_url <> '' )
            return sprintf("<link rel=\"alternate\" type=\"application/rss+xml\" ".(($this->title <> '') ? "title=\"%s\"" : "") . " href=\"%s\" />" , $this->title, $xml_url) . "\n" ;
        
	return false;	
    }
    
    /**
     * Get the google Webmaster Tool code
     * 
     * @return string
     */
    private function getGoogleWT(){
        $sq_google_wt = SQ_Frontend::$options['sq_google_wt'];
        
        if ( ($this->checkHomePosts() || $this->checkFrontPage()) && $sq_google_wt )
            return sprintf("<meta name=\"google-site-verification\" content=\"%s\" />" , $sq_google_wt) . "\n" ;
        
	return false;	
    }
    
    /**
     * Get the google Analytics code
     * 
     * @return string
     */
    private function getGoogleAnalytics(){
        $sq_google_analytics = SQ_Frontend::$options['sq_google_analytics'];
        
        if ($sq_google_analytics )
            return sprintf("<script type=\"text/javascript\">
                            var _gaq = _gaq || [];
                            _gaq.push(['_setAccount', '%s']);
                            _gaq.push(['_trackPageview']);
                            (function() {
                              var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                              ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                              var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
                            })();
                          </script>", $sq_google_analytics) . "\n" ;
        
	return false;	
    }
    
    /**
     * Get the Facebook Insights code
     * 
     * @return string
     */
    private function getFacebookIns(){
        $sq_facebook_insights = SQ_Frontend::$options['sq_facebook_insights'];
        
        if ( ($this->checkHomePosts() || $this->checkFrontPage()) && $sq_facebook_insights )
            return sprintf("<meta property=\"fb:admins\" content=\"%s\" />", $sq_facebook_insights) . "\n" ;
        
	return false;	
    }
    
    /**
     * Get the bing Webmaster Tool code
     * 
     * @return string
     */
    private function getBingWT(){
        $sq_bing_wt = SQ_Frontend::$options['sq_bing_wt'];
        
        if ( ($this->checkHomePosts() || $this->checkFrontPage()) && $sq_bing_wt )
            return sprintf("<meta name=\"msvalidate.01\" content=\"%s\" />" , $sq_bing_wt) . "\n" ;
        
	return false;	
    }
    

    
   
    /*********************************************************************
     **********************************************************************/
    /**
     * Get the title from the curent/last post
     * 
     * @return string
     */
    private function grabTitleFromPost($id = null){
        global $wp_query;
        
        if (isset($id)) 
            $post = get_post($id);
        
        if ($post) return $post->post_title;
        
        foreach ($wp_query->posts as $post){
            if ($post->post_title <> '')
                return $post->post_title;
        }
        
        
    }
    
    /**
     * Get the description from the curent/last post
     * 
     * @return string
     */
    function grabDescriptionFromPost($id = null) {
        global $wp_query;
        $post = $wp_query->get_queried_object();

        if (isset($id)) $post = get_post($id);
        
        if (!$post)
            foreach ($wp_query->posts as $post){
                $id = (is_attachment())?($post->post_parent):($post->ID); 
                $post = get_post($id);
                break;
            }
            
        if ($post) 
            $description = $this->_truncate(SQ_Tools::i18n($post->post_excerpt), $this->min_description_length, $this->max_description_length);
            if (!$description) {
                    $description = $this->truncate(SQ_Tools::i18n($post->post_content), $this->min_description_length, $this->max_description_length);
            }
        
        // "internal whitespace trim"
        $description = @preg_replace("/\s\s+/u", " ", $description);

        return $description;
    }
    
    /**
     * Get the keywords from the curent/last post and from density
     * 
     * @return array
     */
    private function grabKeywordsFromPost($id = null){
        global $wp_query;
        $keywords = array();
        
        
        if (isset($id)){
            $density = array();
            $post = get_post($id);
            $density = $this->calcDensity(strip_tags($post->post_content), $post->post_title, $this->description);
            
            $keywords = $density;
            foreach (wp_get_post_tags($id) as $keyword){
                $keywords[] = SQ_Tools::i18n($keyword->name);
            }
        }else{
            if (is_404()) {
                return null;
            }

            if (!is_home() && !is_page() && !is_single() && !$this->checkFrontPage() && !$this->checkPostsPage()) {
                    return null;
            }
            
            if (is_home()){
                foreach ($wp_query->posts as $post){
                    $more_keywords = $this->calcDensity(strip_tags($post->post_content), $post->post_title, $this->description);
                    
                    if (is_array($more_keywords))
                        $keywords = @array_merge($keywords, $more_keywords);
                    
                }
                $keywords = @array_slice($keywords,0,5);
            }
        
            foreach ($wp_query->posts as $post){
                $id = (is_attachment())?($post->post_parent):($post->ID); 
                
                foreach (wp_get_post_tags($id) as $keyword){
                    $keywords[] = SQ_Tools::i18n($keyword->name);
                }
                
                // Ultimate Tag Warrior integration
                global $utw;
                if ($utw) {
                    $tags = @$utw->GetTagsForPost($post);
                    if (is_array($tags)) {
                        foreach ($tags as $tag) {
                            $tag = $tag->tag;
                            $tag = str_replace('_',' ', $tag);
                            $tag = str_replace('-',' ',$tag);
                            $tag = stripcslashes($tag);
                            $keywords[] = SQ_Tools::i18n($tag);
                        }
                    }
                }

                // autometa
                $autometa = stripcslashes(get_post_meta($id, 'autometa', true));
                //$autometa = stripcslashes(get_post_meta($post->ID, "autometa", true));
                if (isset($autometa) && !empty($autometa)) {
                    $autometa_array = explode(' ', $autometa);
                    foreach ($autometa_array as $e) {
                            $keywords[] = $e;
                    }
                }
            }
        }
        
        
        return $this->getUniqueKeywords($keywords);
    }
    
    /**
     * Calculate the keyword density from blog content
     * 
     * @return array
     */
    private function calcDensity($text, $title = '', $description = ''){   
      $text = $title . '. ' . $text;
      $text = @preg_replace('/[^a-zA-Z0-9-.]/', ' ', $text);
      $title = explode(" ",$title); 
      $description = explode(" ",$description); 
      $words = explode(" ",strtolower($text));  
      $phrases = $this->searchPhrase(strtolower($text));
      
      $common_words = "a,i,he,she,it,and,me,my,you,the,tags,hash,that,this,they,their"; 
      $common_words = strtolower($common_words);
      $common_words = explode(",", $common_words);
      // Get keywords
       $words_sum = 0;     
      foreach ($words as $value){
        $common = false;
        $value = $this->trimReplace($value);
        if (strlen($value) >= 3){
          foreach ($common_words as $common_word){
            if ($common_word == $value){
              $common = true;
            }
          }
          if ($common != true){
            if (!preg_match("/http/i", $value) && !preg_match("/mailto:/i", $value)) {
              $keywords[] = $value;
              $words_sum++;
            }
          }
        }
      }    
      // Do some maths and write array
      $keywords = array_count_values($keywords);
      arsort($keywords);
      
      $keywords = @array_slice($keywords, 0, 10);
      $results = $results1 = $results2 = array();
      $phraseId = 0;
      foreach ($keywords as $key => $value){
            $percent = 100 / $words_sum * $value;
            if ($percent > 1 && in_array($key, $title)  ){
                foreach ($phrases as $phrase => $count){
                    if(strpos($phrase, $key) !== false){
                        $results1[] = trim($key);
                        $results2[] = $phrase;
                    }
                }
            }
            $results = array_merge($results2,$results1);
      }
      $results = @array_slice($results, 0, 4);
      // Return array
      return $results; 
    }
    
    function searchPhrase($text){
        $words = explode(".",strtolower($text)); 
        //print_r($words);
        $frequencies = array();
        foreach ($words as $str) {
            $phrases = $this->twoWordPhrases($str);
            
            foreach ($phrases as $phrase) {
                $key = join(' ', $phrase);
                if (!isset($frequencies[$key])) {
                    $frequencies[$key] = 0;
                }
               $frequencies[$key]++;
            }
        }
        arsort($frequencies);
        $frequencies = @array_slice($frequencies, 0, 10);
        return $frequencies;
    }
    
    function twoWordPhrases($str){
        $words = preg_split('#\s+#', $str, -1, PREG_SPLIT_NO_EMPTY);
        
        $phrases = array();
        if (count($words) > 2)
            foreach (range(0, count($words) - 2) as $offset) {
                    $phrases[] = array_slice($words, $offset, 2);
            }
        return $phrases;
    }
    
    

    /**
     * Get the newlines out
     * 
     * @return string
     */
    private function trimReplace($string) {
      $string = trim($string);
      return (string)str_replace(array("\r", "\r\n", "\n"), '', $string);
    }
    
    
    /**
     * Find the correct canonical url
     * 
     * @return string
     */
    function getCanonicalUrl() {
	global $wp_query;
        
        if (!isset($wp_query) || $wp_query->is_404 || $wp_query->is_search) {
                return false;
        }

        $haspost = count($wp_query->posts) > 0;
        $has_ut = function_exists('user_trailingslashit');

        if (get_query_var('m')) {
            $m = @preg_replace('/[^0-9]/', '', get_query_var('m'));
            switch (strlen($m)) {
                case 4: 
                    $link = get_year_link($m);
                    break;
                case 6: 
                    $link = get_month_link(substr($m, 0, 4), substr($m, 4, 2));
                    break;
                case 8: 
                    $link = get_day_link(substr($m, 0, 4), substr($m, 4, 2), substr($m, 6, 2));
                    break;
                default:
                    return false;
            }
        } elseif (($wp_query->is_single || $wp_query->is_page) && $haspost) {
            $post = $wp_query->posts[0];
            $link = get_permalink($post->ID);
            $link = $this->yoastGetPaged($link); 
        } elseif (($wp_query->is_single || $wp_query->is_page) && $haspost) {
            $post = $wp_query->posts[0];
            $link = get_permalink($post->ID);
            $link = $this->yoastGetPaged($link);
        } elseif ($wp_query->is_author && $haspost) {
            global $wp_version;
            if ($wp_version >= '2') {
                    $author = get_userdata(get_query_var('author'));
                    if ($author === false)
                            return false;
                    $link = get_author_link(false, $author->ID, $author->user_nicename);
                    } else {
                    global $cache_userdata;
                $userid = get_query_var('author');
                $link = get_author_link(false, $userid, $cache_userdata[$userid]->user_nicename);
            }
        } elseif ($wp_query->is_category && $haspost) {
            $link = get_category_link(get_query_var('cat'));
            $link = $this->yoastGetPaged($link);
        } else if ($wp_query->is_tag  && $haspost) {
            $tag = get_term_by('slug',get_query_var('tag'),'post_tag');
            if (!empty($tag->term_id)) {
                $link = get_tag_link($tag->term_id);
            } 
            $link = $this->yoastGetPaged($link);			
        } elseif ($wp_query->is_day && $haspost) {
            $link = get_day_link(get_query_var('year'),
            get_query_var('monthnum'),
            get_query_var('day'));
        } elseif ($wp_query->is_month && $haspost) {
            $link = get_month_link(get_query_var('year'),
            get_query_var('monthnum'));
        } elseif ($wp_query->is_year && $haspost) {
            $link = get_year_link(get_query_var('year'));
        } elseif ($wp_query->is_home) {
            if ((get_option('show_on_front') == 'page') && ($pageid = get_option('page_for_posts'))) {
                $link = get_permalink($pageid);
                $link = $this->yoastGetPaged($link);
                $link = trailingslashit($link);
            } else {
                if ( function_exists( 'icl_get_home_url' ) ) {
                        $link = icl_get_home_url();
                } else {
                        $link = get_option( 'home' );
                }
                $link = $this->yoastGetPaged($link);
                $link = trailingslashit($link);
            }
        } elseif ($wp_query->is_tax && $haspost ) {
                $taxonomy = get_query_var( 'taxonomy' );
                $term = get_query_var( 'term' );
                $link = get_term_link( $term, $taxonomy );
                $link = $this->yoastGetPaged( $link );
        } else {
            return false;
        }

        return $link;

    }  
    
    function getAuthorLinkFromBlog(){
        global $wp_query, $wp_version;
        
        if ($wp_query->is_author && count($wp_query->posts) > 0) {
            if ($wp_version >= '2') {
                    $author = get_userdata(get_query_var('author'));
                    if ($author === false)
                            return false;
                    return get_author_link(false, $author->ID, $author->user_nicename);
            } else {
                global $cache_userdata;
                $userid = get_query_var('author');
                return get_author_link(false, $userid, $cache_userdata[$userid]->user_nicename);
            }
        }
        return false;
    }
    
    function yoastGetPaged($link) {
        $page = get_query_var('paged');
        if ($page && $page > 1) {
            $link = trailingslashit($link) ."page/". "$page";
            if ($has_ut) {
                $link = user_trailingslashit($link, 'paged');
            } else {
                $link .= '/';
            }
        }
        return $link;
    }    
    
    /**
     * Check if page is shown in front
     * 
     * @return bool
     */
    private function checkFrontPage() {
        global $wp_query;
        $post = $wp_query->get_queried_object();
        return is_page() && get_option('show_on_front') == 'page' && $post->ID == get_option('page_on_front');
    }

    /**
     * Check if page is shown in home
     * 
     * @return bool
     */
    private function checkPostsPage() {
        global $wp_query;
        $post = $wp_query->get_queried_object();
        
        return is_home() && get_option('show_on_front') == 'page' && $post->ID == get_option('page_for_posts');
    }
    
    /**
     * Check if posts in home page
     * 
     * @return bool
     */
    private function checkHomePosts() {
        global $wp_query;
        
        if (!$this->checkPostsPage())
            return is_home() && (int)$wp_query->post_count > 0 && isset($wp_query->posts) && is_array($wp_query->posts);
        else
            return false;
    }
    
    
    function truncate($text, $min, $max) {
        $text = str_replace(']]>', ']]&gt;', $text);
        $text = @preg_replace( '|\[(.+?)\](.+?\[/\\1\])?|s', '', $text );
        $text = strip_tags($text);

        if ($max < strlen($text)) {
            while($text[$max] != ' ' && $max > $min) {
                $max--;
            }
        }
        $text = substr($text, 0, $max);
        return trim(stripcslashes($text));
    }
    
    function _truncate($text) {
        $text = str_replace(']]>', ']]&gt;', $text);
        $text = @preg_replace( '|\[(.+?)\](.+?\[/\\1\])?|s', '', $text );
        $text = strip_tags($text);
        return trim(stripcslashes($text));
    }
 
    /**
     * Show just distinct keywords
     * 
     * @return string
     */
    function getUniqueKeywords($keywords) {
        $all = array();
        if(is_array($keywords))
        foreach ($keywords as $word) {
                if (function_exists('mb_strtolower'))			
                    $all[] = mb_strtolower($word, get_bloginfo('charset'));
                else 
                    $all[] = $this->strtolower($word);
        }
        $all = array_unique($all);
        return implode(',', $all);
    }
    
}

?>
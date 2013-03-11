<?php
class SQ_Menu extends SQ_FrontController {
        private $post_type;
	// 
        function init(){}
	
        /*
         * Creates the Setting menu in Wordpress
         */
        public function hookMenu(){
            SQ_Tools::checkErrorSettings(true);
            $this->post_type = array('post','page','product','shopp_page_shopp-products');
            
            /* add the plugin menu in admin */
            $this->model->addMenu(array(    ucfirst(_SQ_NAME_),
                                            ucfirst(_SQ_NAME_) . SQ_Tools::showNotices(SQ_Tools::$errors_count, 'errors_count'),
                                            'edit_posts',
                                            preg_replace ('/\s/','_',_SQ_NAME_) ,
                                            array($this,'showMenu'),
                                            _SQ_THEME_URL_ . 'img/menu_icon_16.png'
                                      ));


            foreach($this->post_type as $type)
                $this->model->addMeta(array(    'post'._SQ_NAME_,
                                            ucfirst(_SQ_NAME_),
                                            array(SQ_ObjController::getController('SQ_Post'), 'init'),
                                            $type, 
                                            'side', 
                                            'high'
                                    ));
           
	}
        
        /**
         * Show the menu content after click event
         * 
         * @return void
         */
	function showMenu(){
            
            SQ_Tools::checkErrorSettings();
            /* Force call of error display */
            SQ_ObjController::getController('SQ_Error', false)->hookNotices();

            /* Get the options from Database*/
            $this->options = SQ_Tools::$options;
            parent::init();
            
 		
	} 
        
        /**
         * Called when Post action is triggered
         * 
         * @return void
         */
        public function action(){
          parent::action();
          
                
          switch (SQ_Tools::getValue('action')){
            case 'sq_howto':
                SQ_Tools::saveOptions('sq_howto', SQ_Tools::getValue('sq_howto'));
                exit();
                break;
            case 'sq_update':
                if(isset($_GET['params'])) {
                    parse_str($_GET['params'],$_GET);
                }
                SQ_Tools::saveOptions('sq_use', (int)SQ_Tools::getValue('sq_use'));
                SQ_Tools::saveOptions('sq_auto_title', (int)SQ_Tools::getValue('sq_auto_title'));
                SQ_Tools::saveOptions('sq_auto_description', (int)SQ_Tools::getValue('sq_auto_description'));
                SQ_Tools::saveOptions('sq_auto_canonical', (int)SQ_Tools::getValue('sq_auto_canonical'));
                SQ_Tools::saveOptions('sq_auto_sitemap', (int)SQ_Tools::getValue('sq_auto_sitemap'));
                SQ_Tools::saveOptions('sq_auto_meta', (int)SQ_Tools::getValue('sq_auto_meta'));
                SQ_Tools::saveOptions('sq_auto_favicon', (int)SQ_Tools::getValue('sq_auto_favicon'));
                
                
                SQ_Tools::saveOptions('sq_auto_seo', (int)SQ_Tools::getValue('sq_auto_seo'));
                SQ_Tools::saveOptions('sq_fp_title', SQ_Tools::getValue('sq_fp_title'));
                SQ_Tools::saveOptions('sq_fp_description', SQ_Tools::getValue('sq_fp_description'));
                SQ_Tools::saveOptions('sq_fp_keywords', SQ_Tools::getValue('sq_fp_keywords'));
                

                SQ_Tools::saveOptions('sq_google_plus', SQ_Tools::getValue('sq_google_plus'));
                SQ_Tools::saveOptions('sq_google_wt', $this->model->checkGoogleWTCode(SQ_Tools::getValue('sq_google_wt')));
                SQ_Tools::saveOptions('sq_google_analytics', $this->model->checkGoogleAnalyticsCode(SQ_Tools::getValue('sq_google_analytics')));
                SQ_Tools::saveOptions('sq_facebook_insights', $this->model->checkFavebookInsightsCode(SQ_Tools::getValue('sq_facebook_insights')));
                SQ_Tools::saveOptions('sq_bing_wt', $this->model->checkBingWTCode(SQ_Tools::getValue('sq_bing_wt')));
                
                SQ_Tools::saveOptions('ignore_warn', (int)SQ_Tools::getValue('ignore_warn'));
                SQ_Tools::saveOptions('sq_keyword_help', (int)SQ_Tools::getValue('sq_keyword_help'));
                SQ_Tools::saveOptions('sq_keyword_information', (int)SQ_Tools::getValue('sq_keyword_information'));
                
                
                update_option('blog_public', (int)SQ_Tools::getValue('sq_google_index'));
                
                /* if there is an icon to upload*/
                if (!empty($_FILES['favicon'])) {
                   
                    $return = $this->model->addFavicon($_FILES['favicon']);
                    if($return['favicon'] <> '')
                        SQ_Tools::saveOptions('favicon', $return['favicon']);
                    if($return['name'] <> '')
                        SQ_Tools::saveOptions('favicon_tmp', $return['name']);
                    if($return['message']<> '')
                        define('SQ_MESSAGE_FAVICON', $return['message']);
                }
                
                /* Generate the sitemap*/
                if(SQ_Tools::getValue('sq_use'))
                    add_action('admin_footer', array(SQ_ObjController::getController('SQ_Sitemap', false), 'generateSitemap'),9999,1);
                
                break;
            case 'sq_fixautoseo':
                SQ_Tools::saveOptions('sq_use', 1);
                break;
            case 'sq_fixprivate':
                update_option('blog_public', 1);
                break;
            
            case 'sq_fixcomments':
                update_option('comments_notify', 1);
                break;
            case 'sq_fixpermalink':
                $GLOBALS['wp_rewrite'] = new WP_Rewrite();
                global $wp_rewrite;
                $permalink_structure = ((get_option('permalink_structure') <> '') ? get_option('permalink_structure') : '/') . "%postname%/" ;
                $wp_rewrite->set_permalink_structure( $permalink_structure );
                $permalink_structure = get_option('permalink_structure');
                
                flush_rewrite_rules();
                break;
            case 'sq_warnings_off':
                SQ_Tools::saveOptions('ignore_warn', 1);
                break;
            
            
          }
            
            
        }
}


?>
<?php

class SQ_Menu extends SQ_FrontController {

    /** @var array snippet */
    private $post_type;

    /** @var array snippet */
    var $options = array();

    /**
     * Called on plugin activation
     *
     * @global type $wpdb
     * @return type
     */
    public function checkActivation() {
        // Bail if no activation redirect
        if (!get_transient('sq_upgrade')) {
            return;
        }

        // Delete the redirect transient
        delete_transient('sq_upgrade');

        //activate the plugin
        $args = array();
        $args['type'] = 'act';
        SQ_Action::apiCall('sq/user/log', $args, 5);

        if (SQ_Tools::$options['sq_api'] == '') {
            wp_safe_redirect(admin_url('admin.php?page=sq_howto'));
        } else {
            wp_safe_redirect(admin_url('admin.php?page=sq_dashboard'));
        }
        exit();
    }

    /**
     * Creates the Setting menu in Wordpress
     */
    public function hookMenu() {
        $first_page = preg_replace('/\s/', '_', _SQ_NAME_);
        $this->post_type = array('post', 'page', 'movie', 'product', 'download', 'shopp_page_shopp-products');

        //add custom post types
        if (SQ_Tools::getIsset('post_type')) {
            if (SQ_Tools::getValue('post_type') <> '') {
                array_push($this->post_type, SQ_Tools::getValue('post_type'));
            }
        } elseif (SQ_Tools::getIsset('post')) {
            $post = get_post(SQ_Tools::getValue('post'));
            if (isset($post->post_type)) {
                array_push($this->post_type, $post->post_type);
            }
        } elseif (SQ_Tools::getIsset('id')) {
            $post = get_post(SQ_Tools::getValue('id'));
            if (isset($post->post_type)) {
                array_push($this->post_type, $post->post_type);
            }
        }

        if (SQ_Tools::$options['sq_api'] == '') {
            $first_page = 'sq_howto';
        } else {
            $first_page = 'sq_dashboard';
        }

        /* add the plugin menu in admin */
        if (current_user_can('administrator')) {
            SQ_Tools::checkErrorSettings(true);
            //check if activated
            $this->checkActivation();
            //activate the cron job if not exists
            if (!wp_get_schedule('sq_processCron')) {
                wp_schedule_event(time(), 'hourly', 'sq_processCron');
            }
        }

        //Push the Analytics Check
        $analytics_alert = 0;
        if (SQ_ObjController::getModel('SQ_Post')->countKeywords() > 0) {
            $analytics_alert = ((SQ_Tools::$options['sq_analytics'] == 0) ? 1 : 0);
        }
        SQ_Tools::$errors_count += $analytics_alert;
        ///////////////

        $this->model->addMenu(array(ucfirst(_SQ_NAME_),
            'Squirrly' . SQ_Tools::showNotices(SQ_Tools::$errors_count, 'errors_count'),
            'edit_posts',
            $first_page,
            null,
            _SQ_THEME_URL_ . 'img/menu_icon_16.png'
        ));
        if (SQ_Tools::$options['sq_api'] == '') {
            $this->model->addSubmenu(array($first_page,
                ucfirst(_SQ_NAME_) . __(' getting started', _SQ_PLUGIN_NAME_),
                __('Getting started', _SQ_PLUGIN_NAME_),
                'edit_posts',
                'sq_howto',
                array(SQ_ObjController::getBlock('SQ_BlockHelp'), 'init')
            ));
        }
        if (SQ_Tools::$options['sq_api'] <> '') {
            $this->model->addSubmenu(array($first_page,
                ucfirst(_SQ_NAME_) . __(' dashboard', _SQ_PLUGIN_NAME_),
                __('Dashboard', _SQ_PLUGIN_NAME_),
                'edit_posts',
                'sq_dashboard',
                array(SQ_ObjController::getBlock('SQ_BlockDashboard'), 'init')
            ));

            $this->model->addSubmenu(array($first_page,
                ucfirst(_SQ_NAME_) . __(' post list', _SQ_PLUGIN_NAME_),
                __('Performance <br />Analytics', _SQ_PLUGIN_NAME_) . SQ_Tools::showNotices($analytics_alert, 'errors_count'),
                'edit_posts',
                'sq_posts',
                array(SQ_ObjController::getBlock('SQ_BlockPostsAnalytics'), 'init')
            ));

            $this->model->addSubmenu(array($first_page,
                ucfirst(_SQ_NAME_) . __(' account info', _SQ_PLUGIN_NAME_),
                __('Account Info', _SQ_PLUGIN_NAME_),
                'administrator',
                'sq_account',
                array(SQ_ObjController::getBlock('SQ_BlockAccount'), 'init')
            ));

            $this->model->addSubmenu(array($first_page,
                ucfirst(_SQ_NAME_) . __(' settings', _SQ_PLUGIN_NAME_),
                __('SEO Settings', _SQ_PLUGIN_NAME_) . SQ_Tools::showNotices(SQ_Tools::$errors_count, 'errors_count'),
                'administrator',
                preg_replace('/\s/', '_', _SQ_NAME_),
                array($this, 'showMenu')
            ));
        }

        $this->model->addSubmenu(array($first_page,
            __('Make money with ', _SQ_PLUGIN_NAME_) . ucfirst(_SQ_NAME_),
            __('Make money', _SQ_PLUGIN_NAME_),
            'administrator',
            'sq_affiliate',
            array(SQ_ObjController::getBlock('SQ_BlockAffiliate'), 'init')
        ));

        foreach ($this->post_type as $type)
            $this->model->addMeta(array('post' . _SQ_NAME_,
                ucfirst(_SQ_NAME_),
                array(SQ_ObjController::getController('SQ_Post'), 'init'),
                $type,
                'side',
                'high'
            ));

        //Add the Rank in the Posts list
        $postlist = SQ_ObjController::getController('SQ_PostsList');
        if (is_object($postlist))
            $postlist->init();
    }

    /**
     * Show the menu content after click event
     *
     * @return void
     */
    public function showMenu() {

        SQ_Tools::checkErrorSettings();
        /* Force call of error display */
        SQ_ObjController::getController('SQ_Error', false)->hookNotices();


        /* Get the options from Database */
        $this->options = SQ_Tools::$options;
        SQ_ObjController::getBlock('SQ_BlockSupport')->init();
        parent::init();
    }

    /**
     * Called when Post action is triggered
     *
     * @return void
     */
    public function action() {
        parent::action();


        switch (SQ_Tools::getValue('action')) {

            case 'sq_settings_update':
                if (SQ_Tools::getValue('sq_use') == '')
                    return;

                SQ_Tools::saveOptions('sq_use', (int) SQ_Tools::getValue('sq_use'));
                SQ_Tools::saveOptions('sq_auto_title', (int) SQ_Tools::getValue('sq_auto_title'));
                SQ_Tools::saveOptions('sq_auto_description', (int) SQ_Tools::getValue('sq_auto_description'));
                SQ_Tools::saveOptions('sq_auto_canonical', (int) SQ_Tools::getValue('sq_auto_canonical'));
                SQ_Tools::saveOptions('sq_auto_sitemap', (int) SQ_Tools::getValue('sq_auto_sitemap'));
                SQ_Tools::saveOptions('sq_auto_meta', (int) SQ_Tools::getValue('sq_auto_meta'));
                SQ_Tools::saveOptions('sq_auto_favicon', (int) SQ_Tools::getValue('sq_auto_favicon'));
                SQ_Tools::saveOptions('sq_auto_facebook', (int) SQ_Tools::getValue('sq_auto_facebook'));
                SQ_Tools::saveOptions('sq_auto_twitter', (int) SQ_Tools::getValue('sq_auto_twitter'));

                $sq_twitter_account = SQ_Tools::getValue('sq_twitter_account');
                if ($sq_twitter_account <> '')
                    if (strpos($sq_twitter_account, '@') === false)
                        $sq_twitter_account = '@' . $sq_twitter_account;
                SQ_Tools::saveOptions('sq_twitter_account', $sq_twitter_account);

                SQ_Tools::saveOptions('sq_auto_seo', (int) SQ_Tools::getValue('sq_auto_seo'));
                SQ_Tools::saveOptions('sq_fp_title', SQ_Tools::getValue('sq_fp_title'));
                SQ_Tools::saveOptions('sq_fp_description', SQ_Tools::getValue('sq_fp_description'));
                SQ_Tools::saveOptions('sq_fp_keywords', SQ_Tools::getValue('sq_fp_keywords'));

                SQ_Tools::saveOptions('sq_google_country', SQ_Tools::getValue('sq_google_country'));

                SQ_Tools::saveOptions('sq_google_plus', SQ_Tools::getValue('sq_google_plus'));
                SQ_Tools::saveOptions('sq_google_wt', $this->model->checkGoogleWTCode(SQ_Tools::getValue('sq_google_wt')));
                SQ_Tools::saveOptions('sq_google_analytics', $this->model->checkGoogleAnalyticsCode(SQ_Tools::getValue('sq_google_analytics')));
                SQ_Tools::saveOptions('sq_facebook_insights', $this->model->checkFavebookInsightsCode(SQ_Tools::getValue('sq_facebook_insights')));
                SQ_Tools::saveOptions('sq_bing_wt', $this->model->checkBingWTCode(SQ_Tools::getValue('sq_bing_wt')));
                SQ_Tools::saveOptions('sq_alexa', $this->model->checkBingWTCode(SQ_Tools::getValue('sq_alexa')));


                SQ_Tools::saveOptions('ignore_warn', (int) SQ_Tools::getValue('ignore_warn'));
                SQ_Tools::saveOptions('sq_keyword_help', (int) SQ_Tools::getValue('sq_keyword_help'));
                SQ_Tools::saveOptions('sq_keyword_information', (int) SQ_Tools::getValue('sq_keyword_information'));
                SQ_Tools::saveOptions('sq_sla', (int) SQ_Tools::getValue('sq_sla'));

                //update_option('blog_public', (int)SQ_Tools::getValue('sq_google_index'));

                /* if there is an icon to upload */
                if (!empty($_FILES['favicon'])) {

                    $return = $this->model->addFavicon($_FILES['favicon']);
                    if ($return['favicon'] <> '')
                        SQ_Tools::saveOptions('favicon', $return['favicon']);
                    if ($return['name'] <> '')
                        SQ_Tools::saveOptions('favicon_tmp', $return['name']);
                    if ($return['message'] <> '')
                        define('SQ_MESSAGE_FAVICON', $return['message']);
                }

                /* Generate the sitemap */
                if (SQ_Tools::getValue('sq_use'))
                    add_action('admin_footer', array(SQ_ObjController::getController('SQ_Sitemap', false), 'generateSitemap'), 9999, 1);

                //empty the cache on settings changed
                SQ_Tools::emptyCache();
                break;
            case 'sq_save_analytics':

                SQ_Tools::saveOptions('sq_analytics_code', SQ_Tools::getValue('sq_analytics_code'));
                SQ_Tools::emptyCache();
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
                $permalink_structure = ((get_option('permalink_structure') <> '') ? get_option('permalink_structure') : '/') . "%postname%/";
                $wp_rewrite->set_permalink_structure($permalink_structure);
                $permalink_structure = get_option('permalink_structure');

                flush_rewrite_rules();
                break;
            case 'sq_fix_ogduplicate':
                SQ_Tools::saveOptions('sq_auto_facebook', 0);
                break;
            case 'sq_fix_tcduplicate':
                SQ_Tools::saveOptions('sq_auto_twitter', 0);
                break;
            case 'sq_fix_descduplicate':
                SQ_Tools::saveOptions('sq_auto_description', 0);
                SQ_Tools::saveOptions('sq_auto_seo', 1);
                break;

            case 'sq_warnings_off':
                SQ_Tools::saveOptions('ignore_warn', 1);
                break;
            case 'sq_get_snippet':
                if (SQ_Tools::getValue('url') <> '') {
                    $url = SQ_Tools::getValue('url');
                } else {
                    $url = get_bloginfo('url');
                }
                $snippet = SQ_Tools::getSnippet($url);

                /* if((int)SQ_Tools::getValue('post_id') > 0)
                  $snippet['url'] = get_permalink((int)SQ_Tools::getValue('post_id'));
                 */
                echo json_encode($snippet);
                exit();
        }
    }

}
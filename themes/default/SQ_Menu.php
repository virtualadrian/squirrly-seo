<div id="sq_settings" >
    <form id="sq_settings_form" name="settings" action="" method="post" enctype="multipart/form-data">
    <?php if(!isset($view->options['sq_api']) || !isset($view->options['sq_howto'])) {?>
    <div id="sq_settings_howto">
        <div id="sq_settings_howto_title" ><?php _e('With Squirrly SEO, your Wordpress will get Perfect SEO on each article you write.', _PLUGIN_NAME_); ?></div>
        <div id="sq_settings_howto_body">
            <p><span><?php _e('SEO Software', _PLUGIN_NAME_); ?></span><?php _e('delivered as a plugin for Wordpress. <br /><br />We connect your wordpress with Squirrly, so that we can find the best SEO opportunities, give you reports and analyse your competitors.', _PLUGIN_NAME_); ?></p>
            <p><object width="420" height="315"><param name="movie" value="http://www.youtube.com/v/HYTcdLXNhhw?hl=en_US&amp;version=3"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/HYTcdLXNhhw?hl=en_US&amp;version=3" type="application/x-shockwave-flash" width="420" height="315" allowscriptaccess="always" allowfullscreen="true"></embed></object></p> 
            <a id="sq_settings_button" href="post-new.php"><?php echo  __( 'Write a new post with Squirrly', _PLUGIN_NAME_); ?></a> 
            <div id="sq_settings_howto_close" class="sq_close" >x</div>
        </div>
    </div>
    <?php }?>
    <?php if(isset($view->options['sq_api'])) {?>
        <div id="sq_userinfo"></div>
        <script type="text/javascript">
           jQuery(document).ready(function() {  
                sq_getUserInfo('<?php echo _SQ_API_URL_ ?>', '<?php echo SQ_Tools::$options['sq_api']?>');
           });
        </script>
    <?php }?>
        
    <div id="sq_settings_title" ><?php _e('Squirrly settings', _PLUGIN_NAME_); ?> <input type="submit" name="sq_update" value="<?php _e('Save settings', _PLUGIN_NAME_)?> &raquo;" /> </div>
    <div id="sq_settings_body">
      
      
         <div id="sq_settings_left" >  
         <fieldset style="display: none">
            <legend><?php _e(ucfirst(_PLUGIN_NAME_) . ' API', _PLUGIN_NAME_); ?></legend>
            <div>
             <p>
              <?php _e('API Key:', _PLUGIN_NAME_); ?><input type="text" name="sq_api" value="<?php echo ((isset($view->options['sq_api']) && $view->options['sq_api']) ? $view->options['sq_api'] : '')?>" size="60" /> 
             </p>
            </div>
        </fieldset>
          
        <fieldset>
            <legend><?php _e('Let Squirrly automatically optimize my blog', _PLUGIN_NAME_); ?></legend>
            <div>

                <div class="sq_option_content">
                   <div class="sq_switch">
                     <input id="sq_use_on" type="radio" class="sq_switch-input" name="sq_use"  value="1" <?php echo ((isset($view->options['sq_use']) && $view->options['sq_use'] == 1) ? "checked" : '')?> />
                     <label for="sq_use_on" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _PLUGIN_NAME_); ?></label>
                     <input id="sq_use_off" type="radio" class="sq_switch-input" name="sq_use" value="0" <?php echo ((!isset($view->options['sq_use']) ||  !$view->options['sq_use']) ? "checked" : '')?> />
                     <label for="sq_use_off" class="sq_switch-label sq_switch-label-on"><?php _e('No', _PLUGIN_NAME_); ?></label>
                     <span class="sq_switch-selection"></span>
                   </div>
               </div>
                
                  <ul id="sq_settings_sq_use" class="sq_settings_info">
                      <span ><?php _e('What does Squirrly automatically do for SEO?', _PLUGIN_NAME_); ?></span>
                       
                      <li>
                          <?php 
                              $auto_option = false;
                              if((!isset($view->options['sq_auto_canonical']) || (isset($view->options['sq_auto_canonical']) && $view->options['sq_auto_canonical'] == 1)))
                                  $auto_option = true;
                          ?>
                          <div class="sq_option_content sq_option_content_small">
                            <div class="sq_switch" style="<?php echo ((!isset($view->options['sq_use']) || (isset($view->options['sq_use']) && $view->options['sq_use'] == 0)) ? 'display:none;' : ''); ?>">
                              <input id="sq_auto_canonical1" type="radio" class="sq_switch-input" name="sq_auto_canonical"  value="1" <?php echo ($auto_option ? "checked" : '')?> />
                              <label for="sq_auto_canonical1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _PLUGIN_NAME_); ?></label>
                              <input id="sq_auto_canonical0" type="radio" class="sq_switch-input" name="sq_auto_canonical" value="0" <?php echo (!$auto_option ? "checked" : '')?> />
                              <label for="sq_auto_canonical0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _PLUGIN_NAME_); ?></label>
                              <span class="sq_switch-selection"></span>
                            </div>
                            <span><?php _e('Add <strong>canonical</strong> link in home page', _PLUGIN_NAME_); ?></span>
                          </div>
                      </li>
                      <li>
                          <?php 
                              $auto_option = false;
                              if((!isset($view->options['sq_auto_sitemap']) || (isset($view->options['sq_auto_sitemap']) && $view->options['sq_auto_sitemap'] == 1)))
                                  $auto_option = true;
                          ?>
                          <div class="sq_option_content sq_option_content_small">
                            <div class="sq_switch" style="<?php echo ((!isset($view->options['sq_use']) || (isset($view->options['sq_use']) && $view->options['sq_use'] == 0)) ? 'display:none;' : ''); ?>">
                              <input id="sq_auto_sitemap1" type="radio" class="sq_switch-input" name="sq_auto_sitemap"  value="1" <?php echo ($auto_option ? "checked" : '')?> />
                              <label for="sq_auto_sitemap1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _PLUGIN_NAME_); ?></label>
                              <input id="sq_auto_sitemap0" type="radio" class="sq_switch-input" name="sq_auto_sitemap" value="0" <?php echo (!$auto_option ? "checked" : '')?> />
                              <label for="sq_auto_sitemap0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _PLUGIN_NAME_); ?></label>
                              <span class="sq_switch-selection"></span>
                            </div>
                            <span><?php _e('Add the <strong>XML Sitemap</strong> for search engines', _PLUGIN_NAME_); ?>: <strong><?php echo get_bloginfo('siteurl') . '/sitemap.xml' ?></strong></span>
                          </div>
                      </li>
                      <li>
                          <?php 
                              $auto_option = false;
                              if((!isset($view->options['sq_auto_meta']) || (isset($view->options['sq_auto_meta']) && $view->options['sq_auto_meta'] == 1)))
                                  $auto_option = true;
                          ?>
                          <div class="sq_option_content sq_option_content_small">
                            <div class="sq_switch" style="<?php echo ((!isset($view->options['sq_use']) || (isset($view->options['sq_use']) && $view->options['sq_use'] == 0)) ? 'display:none;' : ''); ?>">
                              <input id="sq_auto_meta1" type="radio" class="sq_switch-input" name="sq_auto_meta"  value="1" <?php echo ($auto_option ? "checked" : '')?> />
                              <label for="sq_auto_meta1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _PLUGIN_NAME_); ?></label>
                              <input id="sq_auto_meta0" type="radio" class="sq_switch-input" name="sq_auto_meta" value="0" <?php echo (!$auto_option ? "checked" : '')?> />
                              <label for="sq_auto_meta0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _PLUGIN_NAME_); ?></label>
                              <span class="sq_switch-selection"></span>
                            </div>
                            <span><?php _e('Add the required METAs for home page (<strong>icon, author, language, dc publisher</strong>, etc.)', _PLUGIN_NAME_); ?></span>
                          </div>
                      </li>
                      <li>
                          <?php 
                              $auto_option = false;
                              if((!isset($view->options['sq_auto_favicon']) || (isset($view->options['sq_auto_favicon']) && $view->options['sq_auto_favicon'] == 1)))
                                  $auto_option = true;
                          ?>
                          <div class="sq_option_content sq_option_content_small">
                            <div class="sq_switch" style="<?php echo ((!isset($view->options['sq_use']) || (isset($view->options['sq_use']) && $view->options['sq_use'] == 0)) ? 'display:none;' : ''); ?>">
                              <input id="sq_auto_favicon1" type="radio" class="sq_switch-input" name="sq_auto_favicon"  value="1" <?php echo ($auto_option ? "checked" : '')?> />
                              <label for="sq_auto_favicon1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _PLUGIN_NAME_); ?></label>
                              <input id="sq_auto_favicon0" type="radio" class="sq_switch-input" name="sq_auto_favicon" value="0" <?php echo (!$auto_option ? "checked" : '')?> />
                              <label for="sq_auto_favicon0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _PLUGIN_NAME_); ?></label>
                              <span class="sq_switch-selection"></span>
                            </div>
                            <span><?php _e('Add the <strong>favicon</strong> and the <strong>icon for Apple devices</strong>.', _PLUGIN_NAME_); ?></span>
                          </div>
                     </li>
                  </ul>
              
            
            </div>
        </fieldset>  

        <fieldset id="sq_title_description_keywords" style="<?php echo ((!isset($view->options['sq_use']) || (isset($view->options['sq_use']) && $view->options['sq_use'] == 0)) ? 'display:none;' : ''); ?>">
            <legend><?php _e('First page optimization (Title, Description, Keywords)', _PLUGIN_NAME_); ?></legend>
            <ul id="sq_settings_sq_use" class="sq_settings_info">
                <li>
                    <?php 
                        $auto_option = false;
                        if((!isset($view->options['sq_auto_title']) || (isset($view->options['sq_auto_title']) && $view->options['sq_auto_title'] == 1)))
                            $auto_option = true;
                    ?>
                    <div class="sq_option_content sq_option_content_small">
                      <div class="sq_switch" style="<?php echo ((!isset($view->options['sq_use']) || (isset($view->options['sq_use']) && $view->options['sq_use'] == 0)) ? 'display:none;' : ''); ?>">
                        <input id="sq_auto_title1" type="radio" class="sq_switch-input" name="sq_auto_title"  value="1" <?php echo ($auto_option ? "checked" : '')?> />
                        <label for="sq_auto_title1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _PLUGIN_NAME_); ?></label>
                        <input id="sq_auto_title0" type="radio" class="sq_switch-input" name="sq_auto_title" value="0" <?php echo (!$auto_option ? "checked" : '')?> />
                        <label for="sq_auto_title0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _PLUGIN_NAME_); ?></label>
                        <span class="sq_switch-selection"></span>
                      </div>
                      <span><?php _e('Add the correct <strong>title</strong> in the home page', _PLUGIN_NAME_); ?></span>
                    </div>
                </li>
                <li>
                    <?php 
                        $auto_option = false;
                        if((!isset($view->options['sq_auto_description']) || (isset($view->options['sq_auto_description']) && $view->options['sq_auto_description'] == 1)))
                            $auto_option = true;
                    ?>
                    <div class="sq_option_content sq_option_content_small">
                      <div class="sq_switch" style="<?php echo ((!isset($view->options['sq_use']) || (isset($view->options['sq_use']) && $view->options['sq_use'] == 0)) ? 'display:none;' : ''); ?>">
                        <input id="sq_auto_description1" type="radio" class="sq_switch-input" name="sq_auto_description"  value="1" <?php echo ($auto_option ? "checked" : '')?> />
                        <label for="sq_auto_description1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _PLUGIN_NAME_); ?></label>
                        <input id="sq_auto_description0" type="radio" class="sq_switch-input" name="sq_auto_description" value="0" <?php echo (!$auto_option ? "checked" : '')?> />
                        <label for="sq_auto_description0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _PLUGIN_NAME_); ?></label>
                        <span class="sq_switch-selection"></span>
                      </div>
                      <span><?php _e('Add the correct <strong>description</strong> and <strong>keywords</strong> in all pages', _PLUGIN_NAME_); ?></span>
                    </div>
                </li>   
            </ul>
            
            <?php 
                
                $auto_option = false;
                if((!isset($view->options['sq_fp_title']) || $view->options['sq_fp_title'] == '') || (isset($view->options['sq_auto_seo']) && $view->options['sq_auto_seo'] == 1))
                 $auto_option = true;   
            ?>
            <div id="sq_snippet">
                <div id="sq_snippet_name"><?php _e('Squirrly Snippet',_PLUGIN_NAME_)?></div>
                
                <ul id="sq_snippet_ul">
                    <li id="sq_snippet_title"></li>
                    <li id="sq_snippet_url"></li>
                    <li id="sq_snippet_description"></li>
                    
                </ul>
                <div id="sq_snippet_disclaimer" <?php echo (!$auto_option ? '' : 'style="display: none;"')?>><?php _e('If you don\'t see any changes in custom optimization, check if another SEO plugin affects Squirrly SEO',_PLUGIN_NAME_)?></div>
            </div>
            
            
            <div class="sq_option_content">
                <div class="sq_switch">
                  <input id="sq_automatically" type="radio" class="sq_switch-input" name="sq_auto_seo" value="1" <?php echo ($auto_option ? "checked" : '')?> />
                  <label for="sq_automatically" class="sq_switch-label sq_switch-label-off"><?php _e('Auto', _PLUGIN_NAME_); ?></label>
                  <input id="sq_customize" type="radio" class="sq_switch-input" name="sq_auto_seo"  value="0" <?php echo (!$auto_option ? "checked" : '')?> />
                  <label for="sq_customize" class="sq_switch-label sq_switch-label-on"><?php _e('Custom', _PLUGIN_NAME_); ?></label>
                  <span class="sq_switch-selection"></span>
                </div>
            </div>
            
           <div id="sq_customize_settings" <?php echo (!$auto_option ? '' : 'style="display: none;"')?>>

             <p class="withborder">
              <?php _e('Title:', _PLUGIN_NAME_); ?><input type="text" name="sq_fp_title" value="<?php echo ((isset($view->options['sq_fp_title']) && $view->options['sq_fp_title'] <> '') ? $view->options['sq_fp_title'] : '')?>" size="75" /> 
              <span class="sq_settings_info"><?php _e('Tips: Length 10-75 chars', _PLUGIN_NAME_); ?></span>
             </p>
             <p class="withborder">
              <?php _e('Description:', _PLUGIN_NAME_); ?><textarea name="sq_fp_description" cols="70" rows="3" ><?php echo ((isset($view->options['sq_fp_description']) && $view->options['sq_fp_description'] <> '') ? $view->options['sq_fp_description'] : '')?></textarea>
              <span class="sq_settings_info"><?php _e('Tips: Length 70-165 chars', _PLUGIN_NAME_); ?></span>
             </p>
             <p class="withborder">
              <?php _e('Keywords:', _PLUGIN_NAME_); ?><input type="text" name="sq_fp_keywords" value="<?php echo ((isset($view->options['sq_fp_keywords']) && $view->options['sq_fp_keywords'] <> '') ? $view->options['sq_fp_keywords'] : '')?>" size="70" /> 
              <span class="sq_settings_info"><?php _e('Tips: 2-4 keywords', _PLUGIN_NAME_); ?></span>
             </p>
           </div>  
        </fieldset>
                     
        <fieldset>
            <legend><?php _e('Squirrly Options', _PLUGIN_NAME_); ?></legend>
            <div class="sq_option_content">
                <div class="sq_switch">
                  <input id="ignore_warn_yes" class="sq_switch-input" type="radio" name="ignore_warn" value="0" <?php echo ((!isset($view->options['ignore_warn']) ||  !$view->options['ignore_warn']) ? "checked" : '')?> />
                  <label for="ignore_warn_yes" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _PLUGIN_NAME_); ?></label>
                  <input id="sq_ignore_warn" class="sq_switch-input" type="radio" name="ignore_warn" value="1" <?php echo ((isset($view->options['ignore_warn']) && $view->options['ignore_warn'] == 1) ? "checked" : '')?> />
                  <label for="sq_ignore_warn" class="sq_switch-label sq_switch-label-on"><?php _e('No', _PLUGIN_NAME_); ?></label>
                  <span class="sq_switch-selection"></span>
                </div>
                <span><?php _e('Let Squirrly warn me if there are errors related to SEO settings', _PLUGIN_NAME_); ?></span>
            </div>

            <div class="sq_option_content">
                <div class="sq_switch">
                  <input id="sq_keyword_help1" type="radio" class="sq_switch-input" name="sq_keyword_help" value="1" <?php echo ((!isset($view->options['sq_keyword_help']) ||  $view->options['sq_keyword_help'] == 1) ? "checked" : '')?> />
                  <label for="sq_keyword_help1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _PLUGIN_NAME_); ?></label>
                  <input id="sq_keyword_help0" type="radio" class="sq_switch-input" name="sq_keyword_help"  value="0" <?php echo ((isset($view->options['sq_keyword_help']) && $view->options['sq_keyword_help'] == 0) ? "checked" : '')?> />
                  <label for="sq_keyword_help0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _PLUGIN_NAME_); ?></label>
                  <span class="sq_switch-selection"></span>
                </div>
                <span><?php _e('Show <strong>"Enter a keyword"</strong> bubble when posting a new article.', _PLUGIN_NAME_); ?></span>
            </div>
            
            <div class="sq_option_content">
                <div class="sq_switch">
                  <input id="sq_keyword_information1" type="radio" class="sq_switch-input" name="sq_keyword_information" value="1" <?php echo ((isset($view->options['sq_keyword_information']) && $view->options['sq_keyword_information'] == 1) ? "checked" : '')?> />
                  <label for="sq_keyword_information1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _PLUGIN_NAME_); ?></label>
                  <input id="sq_keyword_information0" type="radio" class="sq_switch-input" name="sq_keyword_information"  value="0" <?php echo ((!isset($view->options['sq_keyword_information']) || $view->options['sq_keyword_information'] == 0) ? "checked" : '')?> />
                  <label for="sq_keyword_information0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _PLUGIN_NAME_); ?></label>
                  <span class="sq_switch-selection"></span>
                </div>
                <span><?php _e('Always show <strong>Keyword Informations</strong> about the selected keyword.', _PLUGIN_NAME_); ?></span>
            </div>
       </fieldset> 
                  

        

        </div>
          
        <div id="sq_settings_right">
           
            
           <fieldset>
            <legend><?php _e('Change the Website Icon', _PLUGIN_NAME_); ?></legend>
            <div>
		<p>
                    <?php _e('File types: JPG, JPEG, GIF and PNG.', _PLUGIN_NAME_); ?>
                </p>
		<p>
                    <?php _e('Upload file:', _PLUGIN_NAME_); ?><br />
                    <?php if(file_exists(ABSPATH.'/favicon.ico')){ ?>
                    <img src="<?php echo get_bloginfo('siteurl') . '/favicon.ico' . '?' . time() ?>"  style="float: left; margin-top: 5px; width: 20px; height: 20px;" />
                    <?php }?>
                    <input type="file" name="favicon" id="favicon" style="float: left;" />
                    <input type="submit" name="sq_update" value="<?php _e('Upload', _PLUGIN_NAME_)?>" style="float: left; margin-top: 0;" /> 
                    <span class="sq_settings_info"><?php echo ((defined('SQ_MESSAGE_FAVICON')) ? SQ_MESSAGE_FAVICON : '')?></span>
               </p>	
            </div>
            <span class="sq_settings_info"><?php _e('If you don\'t see the new icon in your browser, empty the browser cache and refresh the page.', _PLUGIN_NAME_); ?></span>
          </fieldset>
            
          <fieldset>
            <legend><?php _e('Tool for Search Engines', _PLUGIN_NAME_); ?></legend>
            <div>
             <p class="withborder withcode">
              <span class="sq_icon sq_icon_googleplus"></span>
              <?php _e('Google Plus URL:', _PLUGIN_NAME_); ?><br /><strong><input type="text" name="sq_google_plus" value="<?php echo ((isset($view->options['sq_google_plus']) && $view->options['sq_google_plus'] <> '') ? $view->options['sq_google_plus'] : '')?>" size="60" /> (e.g. https://plus.google.com/00000000000000/posts)</strong>
             </p>
             <p class="withborder withcode">
              <span class="sq_icon sq_icon_googlewt"></span>
              <?php echo sprintf(__('Google META verification code for %sWebmaster Tool%s`:', _PLUGIN_NAME_), '`<a href="http://maps.google.com/webmasters/" target="_blank">','</a>'); ?><br><strong>&lt;meta name="google-site-verification" content=" <input type="text" name="sq_google_wt" value="<?php echo ((isset($view->options['sq_google_wt']) && $view->options['sq_google_wt'] <> '') ? $view->options['sq_google_wt'] : '')?>" size="15" /> " /&gt;</strong>
             </p>
             <p class="withborder withcode">
              <span class="sq_icon sq_icon_googleanalytics"></span>
              <?php echo sprintf(__('Google  %sAnalytics ID%s`:', _PLUGIN_NAME_), '`<a href="http://maps.google.com/analytics/" target="_blank">','</a>'); ?><br><strong><input type="text" name="sq_google_analytics" value="<?php echo ((isset($view->options['sq_google_analytics']) && $view->options['sq_google_analytics'] <> '') ? $view->options['sq_google_analytics'] : '')?>" size="15" /> (e.g. UA-XXXXXXX-XX)</strong>
             </p>
             <p class="withborder withcode">
              <span class="sq_icon sq_icon_facebookinsights"></span>
              <?php echo sprintf(__('Facebook META code (for %sInsights%s )`:', _PLUGIN_NAME_), '`<a href="http://www.facebook.com/insights/" target="_blank">','</a>'); ?><br><strong>&lt;meta property="fb:admins" content=" <input type="text" name="sq_facebook_insights" value="<?php echo ((isset($view->options['sq_facebook_insights']) && $view->options['sq_facebook_insights'] <> '') ? $view->options['sq_facebook_insights'] : '')?>" size="15" /> " /&gt;</strong>
             </p>
             <p class="withcode">
              <span class="sq_icon sq_icon_bingwt"></span>
              <?php echo sprintf(__('Bing META code (for %sWebmaster Tool%s )`:', _PLUGIN_NAME_), '`<a href="http://www.bing.com/toolbox/webmaster/" target="_blank">','</a>'); ?><br><strong>&lt;meta name="msvalidate.01" content=" <input type="text" name="sq_bing_wt" value="<?php echo ((isset($view->options['sq_bing_wt']) && $view->options['sq_bing_wt'] <> '') ? $view->options['sq_bing_wt'] : '')?>" size="15" /> " /&gt;</strong>
             </p>
            </div>
        </fieldset>            
       </div>
          
       <div id="sq_settings_submit">   
        <input type="hidden" name="action" value="sq_settings_update" /> 
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(_SQ_NONCE_ID_); ?>" />
        <input type="submit" name="sq_update" value="<?php _e('Save settings', _PLUGIN_NAME_)?> &raquo;" /> 
      </div>
     

    </div>
    </form>
    
</div>
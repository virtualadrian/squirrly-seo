
jQuery('#sq_options_support').find('span').live('click',function(){jQuery('.sq_options_support_popup').show();jQuery('.sq_options_feedback_popup').hide();});jQuery("#sq_options_close").live('click',function(){jQuery('.sq_options_support_popup').hide();});jQuery('#sq_options_feedback').find('span').live('click',function(){jQuery('.sq_options_feedback_popup').show();jQuery("#sq_options_feedback").find('.sq_push').hide();jQuery('.sq_options_support_popup').hide();});jQuery("#sq_options_feedback_close").live('click',function(){jQuery('.sq_options_feedback_popup').hide();});jQuery("#sq_feedback_0").live('click',function(){jQuery('#sq_feedback_msg').show();});jQuery("#sq_feedback_1").live('click',function(){jQuery('#sq_feedback_msg').show();});jQuery("#sq_feedback_2").live('click',function(){jQuery("#sq_feedback_submit").trigger('click');for(i=0;i<5;i++)jQuery('#sq_options_feedback').find('.sq_icon').removeClass('sq_label_feedback_'+i);jQuery('#sq_options_feedback').find('.sq_icon').addClass('sq_label_feedback_2');});jQuery("#sq_feedback_3").live('click',function(){jQuery("#sq_feedback_submit").trigger('click');for(i=0;i<5;i++)jQuery('#sq_options_feedback').find('.sq_icon').removeClass('sq_label_feedback_'+i);jQuery('#sq_options_feedback').find('.sq_icon').addClass('sq_label_feedback_3');});jQuery("#sq_feedback_4").live('click',function(){jQuery("#sq_feedback_submit").trigger('click');for(i=0;i<5;i++)jQuery('#sq_options_feedback').find('.sq_icon').removeClass('sq_label_feedback_'+i);jQuery('#sq_options_feedback').find('.sq_icon').addClass('sq_label_feedback_4');});jQuery("#sq_feedback_submit").live('click',function(){jQuery('#sq_feedback_msg').hide();jQuery('#sq_options_feedback_error').html('<p class="sq_minloading" style="margin:0 auto; padding:2px;"></p>')
jQuery('#sq_feedback_submit').attr("disabled","disabled");document.cookie="sq_feedback_face="+jQuery("input[name=sq_feedback_face]:radio:checked").val()+"; expires="+(60*12)+"; path=/";jQuery.getJSON(sqQuery.ajaxurl,{action:'sq_feedback',feedback:jQuery("input[name=sq_feedback_face]:radio:checked").val(),message:jQuery("textarea[name=sq_feedback_message]").val(),nonce:sqQuery.nonce}).success(function(responce){jQuery('#sq_feedback_submit').removeAttr("disabled");jQuery('#sq_feedback_submit').val('Send feedback');jQuery("textarea[name=sq_feedback_message]").val('');if(typeof responce.message!='undefined'){jQuery('#sq_options_feedback_error').removeClass('sq_error').addClass('sq_message').html(responce.message);}else
jQuery('#sq_options_feedback_error').removeClass('sq_error').html('');}).error(function(){jQuery('#sq_feedback_submit').removeAttr("disabled");jQuery('#sq_feedback_submit').val('Send feedback');jQuery('#sq_feedback_submit').removeClass('sq_minloading');jQuery('#sq_options_feedback_error').addClass('sq_error').removeClass('sq_message').html('Could not send the feedback');});});jQuery("#sq_support_submit").live('click',function(){jQuery('#sq_options_support_error').html('<p class="sq_minloading" style="margin:0 auto; padding:2px;"></p>')
jQuery('#sq_support_submit').attr("disabled","disabled");jQuery.getJSON(sqQuery.ajaxurl,{action:'sq_support',message:jQuery("textarea[name=sq_support_message]").val(),nonce:sqQuery.nonce}).success(function(responce){jQuery('#sq_support_submit').removeAttr("disabled");jQuery("textarea[name=sq_support_message]").val('');if(typeof responce.message!='undefined'){jQuery('#sq_options_support_error').removeClass('sq_error').addClass('sq_message').html(responce.message);}else
jQuery('#sq_options_support_error').removeClass('sq_error').html('');}).error(function(){jQuery('#sq_support_submit').removeAttr("disabled");jQuery('#sq_support_submit').val('Send feedback');jQuery('#sq_support_submit').removeClass('sq_minloading');jQuery('#sq_options_support_error').addClass('sq_error').removeClass('sq_message').html('Could not send the feedback');});});
<?php	
	/*
		Plugin Name: CWD Captcha
		Description: This plugin allows user to add new captcha field into formidable form.
		Version: 1.0
		Author: Coral Web Designs
		Author URI: http://coralwebdesigns.com/
	*/
	session_start();
	add_filter('frm_pro_available_fields', 'add_pro_field');
	function add_pro_field($fields){
		$fields['cwd_captcha'] = __('CWD Captcha'); // the key for the field and the label
		return $fields;
	}

	add_action('frm_display_added_fields', 'show_the_admin_field');
	function show_the_admin_field($field){
		if ( $field['type'] != 'cwd_captcha' ) {
			return;
		}

		$field_name = 'item_meta['. $field['id'] .']';
		?>
		
		<div class="frm_html_field_placeholder">
		<div class="howto button-secondary frm_html_field">This field will show captcha and type the catcha into text field. <br/>View your form to see it in action.</div>
		</div> <?php
	}

	add_action('frm_form_fields', 'show_my_front_field', 10, 2);
	function show_my_front_field($field, $field_name){
  		if ( $field['type'] != 'cwd_captcha' ) {
    		return;
  		}
  		$field['value'] = stripslashes_deep($field['value']);
  		$terms=get_terms('cwd_captcha',array('hide_empty'=>false));
  		?>
  		<input id="field_<?php echo $field['field_key'] ?>" name="item_meta[<?php echo $field['id'] ?>]" type="text">
  		<a href="#" class="captcha-change-image">Not readable? Change text.</a>
		<span class="captcha-image-holder"><img src="<?php echo plugins_url('cwd-captcha/generate-captcha.php'); ?>" class="captcha-image" alt="captcha txt"></span>
		<script>
		    jQuery(document).ready(function(){
			    jQuery('body').on('click','.captcha-change-image',function(){
			        jQuery.ajax({
			            'success':function(html){
			                jQuery(".captcha-image-holder img").attr("src",html)
			            },
			            'url':'<?php echo get_bloginfo("url");?>/wp-admin/admin-ajax.php?action=change_captcha',
			            'cache':false
			        });
			        return false;
			    });
		    });
    	</script>
		<?php
	}
	add_filter( 'frm_validate_field_entry', 'validate_my_field', 9, 3 );
	function validate_my_field($errors, $field, $value) {		
		if($field->type=='cwd_captcha'){
			if(!isset($value) || empty($value)){				
				$errors[$field->name]=$field->blank;
			}
			else{				
				if (empty($_SESSION['captcha']) || trim(strtolower($value)) != $_SESSION['captcha']) {
                	$captcha_message = "Invalid captcha";
                	$errors[$field->name] = $captcha_message;
            	} else{
                	$captcha_message = "ok";
            	}
			}
		}
	    return $errors;
	}
	add_action('wp_ajax_nopriv_change_captcha','change_captcha');
	add_action('wp_ajax_change_captcha','change_captcha');
	function change_captcha(){
	  echo get_bloginfo("url")."/wp-content/plugins/artbees-captcha/generate-captcha.php?".rand(999999,9999999999999);
	}
?>
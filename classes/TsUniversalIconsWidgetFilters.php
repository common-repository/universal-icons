<?php
/**
 * The general settings page is the page.
 * @since 1.0.0
 * @author Rahul Taiwala
 */
class TsUniversalIconsWidgetFilters
{
	
	/**
	 * 
	 *
	 * @since 1.1.0
	 */
	static function init()
	{
		// Only initialize in admin
        add_filter('dynamic_sidebar_params',array(__CLASS__,'widget_parameter_hack'),99,1);
		if (!is_admin())
		{
			return;
		}

		add_filter('widget_update_callback',array(__CLASS__,'update_call'),99,4);
        
        add_filter('widget_form_callback',array(__CLASS__,'widget_form_hack'),99,2);
	}
    
    /**
    *Function to return the instance with the inserted Icon Field
    */
    static function update_call($instance, $new_instance, $old_instance,$that){
        return($new_instance);
    }
    
    /**
    * Function to insert the Icon before_title, after_title, before_widget, after_widget
    * Currently only after_title is supported but soon there will be an additional option to select the             * position
    */
    static function widget_parameter_hack($param){
        global $wp_registered_widgets;//Needed to get the Widget Option Name & Id Base to get the settings
        $opt_name='';
        
        $option_name=$wp_registered_widgets[$param[0]['widget_id']]['callback'][0]->option_name;
        
        $idbase=$wp_registered_widgets[$param[0]['widget_id']]['callback'][0]->id_base;
       
        $icon_val=self::universal_icon_get_widgets_settings($option_name,$idbase);
        
        $widget_number=$param[1]['number'];
        
        $icon=$icon_val[$widget_number]['uicon']!==''?$icon_val[$widget_number]['uicon']:'';
        
        $size=$icon_val[$widget_number]['icon-size']!==''?$icon_val[$widget_number]['icon-size']:'1x';
        
        
        $icn=''!==$icon ? "<i class='fa fa-$size $icon universal_icon'></i>":'';
        
        $position=$icon_val[$widget_number]['icon-position']!==''?$icon_val[$widget_number]['icon-position']:'before';
        
        if('after'==$position){
            $param[0]['after_title'] = $icn.$param[0]['after_title'];//icon added to the after_title argument 
        }else{
            $param[0]['before_title'] .= $icn;//icon added to the before_title argument 
        }
        return $param; // return the modified parameters
    }
    
    /**
    * Function to insert the icon selection field in the Widget Forms
    *
    */
    static function widget_form_hack($instance,$that){
        $opt_name='';
        $icon_val=$that->get_settings();
        $icon=$icon_val[$that->number]['uicon']!==''?$icon_val[$that->number]['uicon']:'';
        $position=$icon_val[$that->number]['icon-position']!==''?$icon_val[$that->number]['icon-position']:'';
        $size=$icon_val[$that->number]['icon-size']!==''?$icon_val[$that->number]['icon-size']:'';
        
        ?>
        <p>
        <label for="<?php echo $that->get_field_id('uicon'); ?>"><?php _e('Select Icon', 'icon_hack'); ?></label>
        <?php
        $output = '<select id="'. $that->get_field_id('uicon').'" name="'.$that->get_field_name('uicon').'">';
        $output .='<option value=""></option>';
        $cnt=0;
        foreach(TsIconsUniversal::universal_icon_lists() as $key=>$value) {
            if($cnt>=30) break;
            $output .= '<option value="'.$key.'" '.selected( $icon, $key, false ).'>'.htmlspecialchars($value).'</option>';
            $cnt++;
        }
        $output .= '</select>';
        echo $output;
        echo "</p>";
        ?>
        <p>
            <label for="<?php echo $that->get_field_id('icon-position'); ?>"><?php _e('Icon Position', 'icon_hack'); ?></label>
            <?php
            $output = '<select id="'. $that->get_field_id('icon-position').'" name="'.$that->get_field_name('icon-position').'">';
            $output .='<option value=""></option>';
            
            $output .= '<option value="after" '.selected( $position, "after", false ).'>After Title</option>';
            $output .= '<option value="before" '.selected( $position, "before", false ).'>Before Title</option>';
            
            $output .= '</select>';
            echo $output;
            ?>
        </p>
        <p>
            <label for="<?php echo $that->get_field_id('icon-size'); ?>"><?php _e('Icon Size', 'icon_hack'); ?></label>
            <?php
            $output = '<select id="'. $that->get_field_id('icon-size').'" name="'.$that->get_field_name('icon-size').'">';
            $output .='<option value=""></option>';
            
            $output .= '<option value="1x" '.selected( $size, "1x", false ).'>1x</option>';
            $output .= '<option value="2x" '.selected( $size, "2x", false ).'>2x</option>';
            
            
            $output .= '</select>';
            echo $output;
            ?>
        </p>
       
        <?php
        return $instance;
    }
    
    /**
    *Function to get the Widget's Setting
    */
    static function universal_icon_get_widgets_settings($option_name,$id_base) {

        $settings = get_option( $option_name );

        /*if ( false === $settings && isset( $alt_option_name ) ) {
            $settings = get_option( $alt_option_name );
        }*/

        if ( ! is_array( $settings ) && ! ( $settings instanceof ArrayObject || $settings instanceof ArrayIterator ) ) {
            $settings = array();
        }

        if ( ! empty( $settings ) && ! isset( $settings['_multiwidget'] ) ) {
            // Old format, convert if single widget.
            $settings = wp_convert_widget_settings( $id_base, $option_name, $settings );
        }

        unset( $settings['_multiwidget'], $settings['__i__'] );
        return $settings;
    }
	

	
}
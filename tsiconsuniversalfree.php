<?php
/*
 Plugin Name: Universal Icons - Free
 Plugin URI: http://www.techsarathy.com
 Description: Universal Icons let you insert Icons easily Before and After Widgets Title.
 Author: Rahul Taiwala
 Author URI: http://www.rktaiwala.in
 Version: 1.0.0
 License: GPLv2 or later
 */

class TsIconsUniversal
{
	/** @var string $version */
	static $version = '1.0.0';
    static $icon_fonts_url=array(
        'fontawesome'=>array(
            'url'=>'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css',
            'external'=>true
        ),
        'foundation'=>array(
            'url'=>'/font/foundation-icons.css',
            'external'=>false
        )
    );
	/**
	 * Bootstraps the application by assigning the right functions to
	 * the right action hooks.
	 *
	 * @since 1.0.0
	 */
	static function bootStrap()
	{
        
		self::autoInclude();
        TsUniversalIconsWidgetFilters::init();
        
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueueBackendScripts'));
        add_action( 'wp_enqueue_scripts', array(__CLASS__,'frontEndScripts') );
        //delete_transient('universal_icon_list');
	}
    
    /**
	 * Includes backend script.
	 *
	 * Should always be called on the admin_enqueue_scrips hook.
	 *
	 * @since 1.0.0
	 */
	static function enqueueBackendScripts()
	{
		// Function get_current_screen() should be defined
		if (!function_exists('get_current_screen'))
		{
			return;
		}

		$currentScreen = get_current_screen();

		// Enqueue 3.5 uploader
		if (is_admin())
		{
            foreach(self::$icon_fonts_url as $key=>$icon_font){
                if($icon_font['external']){
                    $cssFile=$icon_font['url'];
                }else{
                    $cssFile=self::getPluginUrl().$icon_font['url'];
                }
                wp_enqueue_style($key,$cssFile);
            }
            
		}

		
	}
    
    static function frontEndScripts()
	{
		
        foreach(self::$icon_fonts_url as $key=>$icon_font){
            if($icon_font['external']){
                $cssFile=$icon_font['url'];
            }else{
                $cssFile=self::getPluginUrl().$icon_font['url'];
            }
            wp_enqueue_style($key,$cssFile);
        }
		
	}
	/**
	 * Returns url to the base directory of this plugin.
	 *
	 * @since 1.0.0
	 * @return string pluginUrl
	 */
	static function getPluginUrl()
	{
		return plugins_url('', __FILE__);
	}

	/**
	 * Returns path to the base directory of this plugin
	 *
	 * @since 1.0.0
	 * @return string pluginPath
	 */
	static function getPluginPath()
	{
		return dirname(__FILE__);
	}
    
    /**
    * Function to get the list of Icon Class or Name from the Font CSS file 
    *
    */
    static function universal_icon_lists(){
        if ( false === ( $cssFinal = get_transient( 'universal_icon_list' ) ) ) {
            $notToBe=array('@font-face','.fa','.fa-2x','.fa-3x','.fa-lg','.fa-4x','.fa-5x','@-webkit-keyframes');
            $cssFinal=array();
            foreach(self::$icon_fonts_url as $icon_font){
                if($icon_font['external']){
                    $cssFile=$icon_font['url'];
                }else{
                    $cssFile=self::getPluginUrl().$icon_font['url'];
                }
                $cssData=file_get_contents($cssFile);
                $cssData=preg_replace('/[\r\n]+/', '', $cssData);
                $cssData=preg_replace('/\/\*.*\*\//', '', $cssData);
                $cssData=preg_replace('/\{[^\}]*\}/', '|', $cssData);
                $cssData=preg_replace('/:before/', '|', $cssData);
                $cssClasses=explode('|',$cssData);
                foreach($cssClasses as $key=>$value){
                    if(substr_count($value,'.')>1){
                        unset($cssClasses[$key]);

                    }elseif(in_array($value,$notToBe)){
                        unset($cssClasses[$key]);
                   }elseif(substr_count($value,'>')>=1){
                        unset($cssClasses[$key]);
                   }elseif(strpos($value,'.')!==0){
                        unset($cssClasses[$key]);
                   }

                }
                
                foreach($cssClasses as $vl){
                    if(strpos($vl,'.fa-')==0){
                        $cssFinal[substr($vl,1)]=substr($vl,4);
                    }else{
                        $cssFinal[substr($vl,1)]=$vl;
                    }
                }
                asort($cssFinal);
                set_transient( 'universal_icon_list', $cssFinal, 1 * WEEK_IN_SECONDS );
            }
        }
        return $cssFinal;
    }
    
	/**
	 * This function will load classes automatically on-call.
	 *
	 * @since 1.0.0
	 */
	static function autoInclude()
	{
		if (!function_exists('spl_autoload_register'))
		{
			return;
		}

		function TsIconsUniversalAutoLoader($name)
		{
			$name = str_replace('\\', DIRECTORY_SEPARATOR, $name);
			$file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . $name . '.php';

			if (is_file($file))
			{
				require_once $file;
			}
		}

		spl_autoload_register('TsIconsUniversalAutoLoader');
	}
    
    
}

/**
 * Initialize Universal Icons Plugin
 */
TsIconsUniversal::bootStrap();
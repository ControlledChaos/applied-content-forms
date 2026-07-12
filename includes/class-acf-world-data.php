<?php

if(!defined('ABSPATH'))
    exit;

class ACF_World_Data{

    public $countries;
    public $languages;
    public $currencies;

    function __construct(){

        // Data
        $this->countries  = acf_include('includes/data/countries.php');
        $this->languages  = acf_include('includes/data/languages.php');
        $this->currencies = acf_include('includes/data/currencies.php');

        // Localize Names
        if(function_exists('locale_get_display_region')){

            // Get Locale
            $locale = acf_get_locale();

            // Loop
            foreach(array_keys($this->countries) as $code){

                $this->countries[$code]['localized'] = locale_get_display_region("-$code", $locale);

            }

        }

    }

}

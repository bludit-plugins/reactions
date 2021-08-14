<?php
declare(strict_types=1);
/*
 |  BLU-TOOLS   A bunch of useful Bludit Tools.
 |  @file       ./system/functions.php
 |  @author     SamBrishes <sam@pytes.net>
 |  @version    1.0.0 [1.0.0] - Stable
 |
 |  @website    https://github.com/pytesNET/blu-tools
 |  @license    X11 / MIT License
 |  @copyright  Copyright © 2019 - 2020 pytesNET <info@pytes.net>
 */

    /*
     |  S18N :: TRANSLATE STRING
     |  @since  0.1.0
     |
     |  @param  string  The respective string to translate.
     |
     |  @return string  The translated and formated string.
     */
    if(!function_exists("s18n__")) {
        function s18n__(string $string): string {
            global $L;
            $hash = "s18n-" . md5(strtolower($string));
            $value = $L->g($hash);

            if($hash === $value){
                $value = $string;
            }
            return $value;
        }
    }

    /*
     |  S18N :: TRANSLATE & PRINT STRING
     |  @since  0.1.0
     |
     |  @param  string  The respective string to translate.
     |
     |  @return <print>
     */
    if(!function_exists("s18n_e")) {
        function s18n_e(string $string): void {
            print(s18n__($string));
        }
    }

    /*
     |  FORM :: GET SELECTED STRING
     |  @since  0.1.0
     |
     |  @param  bool    The value of the <option> field or a boolean.
     |  @param  multi   The value to compare with.
     |  @param  bool    TRUE to print `selected="selected"`, FALSE to return it as string.
     |
     |  @return multi   The respective string or null.
     */
    if(!function_exists("bt_selected")) {
        function bt_selected(/* string | bool */ $field, /* string | bool */ $compare = true, bool $print = true): ?string {
            if($field === $compare) {
                $selected = 'selected="selected"';
            } else {
                $selected = '';
            }
            if(!$print){
                return $selected;
            }
            print($selected);
            return null;
        }
    }

    /*
     |  FORM :: GET CHECKED STRING
     |  @since  0.1.0
     |
     |  @param  bool    The value of the <input /> field or a boolean.
     |  @param  multi   The value to compare with.
     |  @param  bool    TRUE to print `checked="checked"`, FALSE to return it as string.
     |
     |  @return multi   The respective string or null.
     */
    if(!function_exists("bt_checked")) {
        function bt_checked(/* string | bool */ $field, /* string | bool */ $compare = true, bool $print = true): ?string {
            if($field === $compare) {
                $checked = 'checked="checked"';
            } else {
                $checked = '';
            }
            if(!$print){
                return $checked;
            }
            print($checked);
            return null;
        }
    }

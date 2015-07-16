<?php /**
 * @package DeMomentSomTres_Franquicia
 */
/*
 Plugin Name: DeMomentSomTres Franquicia
 Plugin URI: http://demomentsomtres.com/english/wordpress-plugins/demomentsomtres-labels/
 Description: DeMomentSomTres Franquicia
 Version: 1.0.1
 Author: DeMomentSomTres
 Author URI: http://www.DeMomentSomTres.com
 License: GPLv2 or later
 */

/*
 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License
 as published by the Free Software Foundation; either version 2
 of the License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

require_once (dirname(__FILE__) . '/lib/class-tgm-plugin-activation.php');

define('DMS3FRANQUICIA_TEXT_DOMAIN', 'demomentsomtres-franquicia');

$dms3_franquicia = new DeMomentSomTresFranquicia();

class DeMomentSomTresFranquicia {
    const TEXT_DOMAIN = DMS3FRANQUICIA_TEXT_DOMAIN;
    const TYPE_STORE = 'franchise-store';
    const METABOXESPREFIX = 'dms3Franchise';
    const FIELD_TOWN = 'dms3FranchiseTown';
    const FIELD_ADDRESS = 'dmsFranchiseAddress';
    const FIELD_CONTACT = 'dms3FranchiseContact';
    const FIELD_PHONE = 'dms3FranchisePhone';
    const FIELD_EMAIL = 'dms3FranchiseEmail';
    const FIELD_ZIP = 'dms3FranchiseZipCode';
    const FIELD_FACEBOOK = 'dms3FranchiseFB';
    const FIELD_TWITTER = 'dms3FranchiseTW';
    const FIELD_MAP = 'dms3FranchiseMap';
    const FIELD_IMAGES = 'dms3FranchiseImages';
    const FIELD_CONTENTCAT = 'dms3FranchiseCategory';
    const FIELD_EVENTSCAT = 'dms3FranchiseEventCategory';
    const FIELD_THUMBNAILID = '_thumbnail_id';
    const IMAGESIZENAME = 'dms3-franchise';
    const PARAMETER_IMAGEWIDTH = '400';

    private $pluginURL;
    private $pluginPath;
    private $langDir;

    /**
     * @since 1.0
     */
    function __construct() {
        $this -> pluginURL = plugin_dir_url(__FILE__);
        $this -> pluginPath = plugin_dir_path(__FILE__);
        $this -> langDir = dirname(plugin_basename(__FILE__)) . '/languages';

        add_action('plugins_loaded', array(
            $this,
            'plugin_init'
        ));
        add_action('tgmpa_register', array(
            $this,
            'required_plugins'
        ));
        add_action('init', array(
            $this,
            'posttypes'
        ), 0);

        add_filter('rwmb_meta_boxes', array(
            $this,
            'metaboxes'
        ));

        add_shortcode("dms3-franchise-gallery", array(
            $this,
            "shortcode_gallery"
        ));
        add_shortcode("dms3-franchise-events", array(
            $this,
            "shortcode_events"
        ));
        add_shortcode("dms3-franchise-posts", array(
            $this,
            "shortcode_posts"
        ));
        add_shortcode("dms3-franchise-address", array(
            $this,
            "shortcode_address"
        ));
        add_shortcode("dms3-franchise-contact", array(
            $this,
            "shortcode_contact"
        ));
        add_shortcode("dms3-franchise-facebook", array(
            $this,
            "shortcode_facebook"
        ));
        add_shortcode("dms3-franchise-twitter", array(
            $this,
            "shortcode_twitter"
        ));
        add_shortcode("dms3-franchise-email", array(
            $this,
            "shortcode_email"
        ));
        add_shortcode("dms3-franchise-phone", array(
            $this,
            "shortcode_phone"
        ));
        add_shortcode("dms3-franchise-map", array(
            $this,
            "shortcode_map"
        ));

        add_image_size(self::IMAGESIZENAME, 9999, self::PARAMETER_IMAGEWIDTH, false);
        // add_action('load_textdomain',array($this,'debug_load_textdomain'),10,2); //debug only
    }

    function debug_load_textdomain( $domain , $mofile  ){
        echo "Trying ",$domain," at ",$mofile,"<br />\n";
    }

    /**
     * @since 1.0
     */
    function plugin_init() {
        load_plugin_textdomain(DMS3FRANQUICIA_TEXT_DOMAIN, false, $this -> langDir);
    }

    /**
     * @since 1.0
     */
    function required_plugins() {
        $plugins = array(
            array(
                'name' => 'Meta Box',
                'slug' => 'meta-box',
                'required' => true
            ),
            array(
                'name' => 'Events Manager',
                'slug' => 'events-manager',
                'required' => false
            ),
            array(
                'name' => "Canvas Gallery",
                'slug' => 'wc-gallery',
                'required' => 'false',
            ),
            array(
                "name" => "DeMomentSomTres Display Post Shortcode",
                "slug" => "demomentsomtres-display-posts-shortcode",
                'required' => 'true',
            )
        );
        $config = array(
            'default_path' => '', // Default absolute path to pre-packaged plugins.
            'menu' => 'tgmpa-install-plugins', // Menu slug.
            'has_notices' => true, // Show admin notices or not.
            'dismissable' => false, // If false, a user cannot dismiss the nag message.
            'dismiss_msg' => __('Some plugins are missing!', self::TEXT_DOMAIN), // If 'dismissable' is false, this message will be output at top of nag.
            'is_automatic' => false, // Automatically activate plugins after installation or not.
            'message' => __('This are the required plugins', self::TEXT_DOMAIN), // Message to output right before the plugins table.
            'strings' => array(
                'page_title' => __('Install Required Plugins', self::TEXT_DOMAIN),
                'menu_title' => __('Install Plugins', self::TEXT_DOMAIN),
                'installing' => __('Installing Plugin: %s', self::TEXT_DOMAIN), // %s = plugin name.
                'oops' => __('Something went wrong with the plugin API.', self::TEXT_DOMAIN),
                'notice_can_install_required' => _n_noop('This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', self::TEXT_DOMAIN), // %1$s = plugin name(s).
                'notice_can_install_recommended' => _n_noop('This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', self::TEXT_DOMAIN), // %1$s = plugin name(s).
                'notice_cannot_install' => _n_noop('Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', self::TEXT_DOMAIN), // %1$s = plugin name(s).
                'notice_can_activate_required' => _n_noop('The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', self::TEXT_DOMAIN), // %1$s = plugin name(s).
                'notice_can_activate_recommended' => _n_noop('The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', self::TEXT_DOMAIN), // %1$s = plugin name(s).
                'notice_cannot_activate' => _n_noop('Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', self::TEXT_DOMAIN), // %1$s = plugin name(s).
                'notice_ask_to_update' => _n_noop('The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', self::TEXT_DOMAIN), // %1$s = plugin name(s).
                'notice_cannot_update' => _n_noop('Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', self::TEXT_DOMAIN), // %1$s = plugin name(s).
                'install_link' => _n_noop('Begin installing plugin', 'Begin installing plugins', self::TEXT_DOMAIN),
                'activate_link' => _n_noop('Begin activating plugin', 'Begin activating plugins', self::TEXT_DOMAIN),
                'return' => __('Return to Required Plugins Installer', self::TEXT_DOMAIN),
                'plugin_activated' => __('Plugin activated successfully.', self::TEXT_DOMAIN),
                'complete' => __('All plugins installed and activated successfully. %s', self::TEXT_DOMAIN), // %s = dashboard link.
                'nag_type' => 'error' // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
            )
        );
        // echo '<pre>'.print_r($config,true).'</pre>';exit;
        tgmpa($plugins, $config);
    }

    /**
     * @since 1.0
     */
    function posttypes() {

        $labels = array(
            'name' => _x('Stores', 'Post Type General Name', self::TEXT_DOMAIN),
            'singular_name' => _x('Store', 'Post Type Singular Name', self::TEXT_DOMAIN),
            'menu_name' => __('Stores', self::TEXT_DOMAIN),
            'name_admin_bar' => __('Stores', self::TEXT_DOMAIN),
            'parent_item_colon' => __('Parent Store:', self::TEXT_DOMAIN),
            'all_items' => __('All Stores', self::TEXT_DOMAIN),
            'add_new_item' => __('Add New Store', self::TEXT_DOMAIN),
            'add_new' => __('Add New', self::TEXT_DOMAIN),
            'new_item' => __('New Store', self::TEXT_DOMAIN),
            'edit_item' => __('Edit Store', self::TEXT_DOMAIN),
            'update_item' => __('Update Store', self::TEXT_DOMAIN),
            'view_item' => __('View Store', self::TEXT_DOMAIN),
            'search_items' => __('Search Store', self::TEXT_DOMAIN),
            'not_found' => __('Not found', self::TEXT_DOMAIN),
            'not_found_in_trash' => __('Not found in trash', self::TEXT_DOMAIN),
        );
        $args = array(
            'label' => __('franchise-store', self::TEXT_DOMAIN),
            'description' => __('Franchise Store', self::TEXT_DOMAIN),
            'labels' => $labels,
            'supports' => array(
                'title',
                'trackbacks',
                'revisions',
                'editor'
            ),
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            // 'menu_position' => 25, -- WooCommerce compatibilty - v1.0.1
            'menu_icon' => 'dashicons-store',
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'post',
        );
        register_post_type(self::TYPE_STORE, $args);

    }

    /**
     * @since 1.0
     */
    function metaboxes($metaboxes) {
        $metaboxes[] = array(
            'id' => 'dms3-franchise-store-gallery',
            'title' => __('Store information', self::TEXT_DOMAIN),
            'pages' => array(self::TYPE_STORE),
            'context' => 'normal',
            'priority' => 'high',
            'fields' => array(
                array(
                    'name' => __('Featured Image', self::TEXT_DOMAIN),
                    'type' => 'image_advanced',
                    'max_file_uploads' => 1,
                    'id' => self::FIELD_THUMBNAILID,
                ),
                array(
                    'name' => __("Location", self::TEXT_DOMAIN),
                    'desc' => __('Store contact information', self::TEXT_DOMAIN),
                    'type' => 'heading',
                ),
                array(
                    'name' => __('Store Address', self::TEXT_DOMAIN),
                    'desc' => __('Street and numero', self::TEXT_DOMAIN),
                    'type' => 'text',
                    'id' => self::FIELD_ADDRESS,
                    'size' => 100,
                ),
                array(
                    'name' => __('Store Town', self::TEXT_DOMAIN),
                    'type' => 'text',
                    'id' => self::FIELD_TOWN,
                    'size' => 100,
                ),
                array(
                    'name' => __('Store Zip Code', self::TEXT_DOMAIN),
                    'type' => 'text',
                    'id' => self::FIELD_ZIP,
                    'size' => 10,
                ),
                array(
                    'name' => __('Store Position', self::TEXT_DOMAIN),
                    'desc' => __('The name of the store', self::TEXT_DOMAIN),
                    'type' => 'map',
                    'id' => self::FIELD_MAP
                ),
                array(
                    'name' => __("Contact", self::TEXT_DOMAIN),
                    'desc' => __('Store contact information', self::TEXT_DOMAIN),
                    'type' => 'heading',
                ),
                array(
                    'name' => __("Contact person", self::TEXT_DOMAIN),
                    'desc' => __('Store contact person', self::TEXT_DOMAIN),
                    'type' => 'text',
                    'id' => self::FIELD_CONTACT,
                    'size' => 50,
                ),
                array(
                    'name' => __("eMail", self::TEXT_DOMAIN),
                    'desc' => __('Store eMail Address', self::TEXT_DOMAIN),
                    'type' => 'email',
                    'id' => self::FIELD_EMAIL,
                    'size' => 50,
                ),
                array(
                    'name' => __("Phone", self::TEXT_DOMAIN),
                    'desc' => __('Store phone', self::TEXT_DOMAIN),
                    'type' => 'text',
                    'id' => self::FIELD_PHONE,
                    'size' => 50,
                ),
                array(
                    'name' => __("Facebook", self::TEXT_DOMAIN),
                    'desc' => __('Full facebook URL', self::TEXT_DOMAIN),
                    'type' => 'url',
                    'id' => self::FIELD_FACEBOOK,
                    'size' => 75,
                ),
                array(
                    'name' => __("Twitter", self::TEXT_DOMAIN),
                    'desc' => __('Store Twitter Username (without @)', self::TEXT_DOMAIN),
                    'type' => 'text',
                    'id' => self::FIELD_TWITTER,
                    'size' => 50,
                ),
                array(
                    'name' => __("Gallery", self::TEXT_DOMAIN),
                    'desc' => 'Images that will be included on the store page',
                    'type' => 'heading',
                ),
                array(
                    'name' => __('Images', self::TEXT_DOMAIN),
                    'desc' => __('Upload, select and sort the images for the store', self::TEXT_DOMAIN),
                    'id' => self::FIELD_IMAGES,
                    'type' => 'image_advanced'
                )
            )
        );
        $metaboxes[] = array(
            'id' => 'dms3-franchise-store-relationships',
            'title' => __('Store WordPress Integration', self::TEXT_DOMAIN),
            'pages' => array(self::TYPE_STORE),
            'context' => 'normal',
            'priority' => 'high',
            'fields' => array(
                array(
                    'name' => __("Categories", self::TEXT_DOMAIN),
                    'type' => 'heading',
                ),
                array(
                    'name' => __('Content category', self::TEXT_DOMAIN),
                    'id' => self::FIELD_CONTENTCAT,
                    'type' => 'taxonomy_advanced',
                    'multiple' => true,
                    'js_options' => array("width" => "100%"),
                    'options' => array(
                        'taxonomy' => 'category',
                        // How to show taxonomy: 'checkbox_list' (default) or 'checkbox_tree', 'select_tree', select_advanced or 'select'. Optional
                        'type' => 'select_advanced',
                        'args' => array(),
                    ),
                ),
                array(
                    'name' => __('Events category', self::TEXT_DOMAIN),
                    'id' => self::FIELD_EVENTSCAT,
                    'type' => 'taxonomy_advanced',
                    'multiple' => true,
                    'js_options' => array("width" => "100%"),
                    'options' => array(
                        'taxonomy' => 'event-categories',
                        // How to show taxonomy: 'checkbox_list' (default) or 'checkbox_tree', 'select_tree', select_advanced or 'select'. Optional
                        'type' => 'select_advanced',
                        'args' => array()
                    ),
                )
            )
        );
        return $metaboxes;
    }

    function shortcode_gallery($atts) {
        global $post;

        // Attributes
        extract(shortcode_atts(array(
            'display' => 'owlautowidth',
            'link' => 'none',
            'captions' => 'hide',
            'size' => self::IMAGESIZENAME,
            'hidecontrols' => false,
            'post_id' => $post -> ID
        ), $atts));

        $ids = get_post_meta($post_id, self::FIELD_IMAGES, false);
        if (count($ids) == 0) :
            return;
        endif;
        $ids = implode(",", $ids);
        $shortcode = "[gallery display='$display' link='$link' captions='$captions' size='$size' hidecontrols='$hidecontrols' ids=' . $ids . ']";
        return do_shortcode($shortcode);
    }

    function shortcode_map($atts) {
        global $post;

        // Attributes
        extract(shortcode_atts(array(
            'post_id' => $post -> ID,
            'width' => '100%',
            'height' => '480px',
            'zoom' => 14,
            'marker' => true,
            'marker_title' => '',
            'info_window' => true,
            'info_window_content' => ''
        ), $atts));

        if ($post_id != $post -> ID) :
            $thePost = get_post($post -> ID);
        else :
            $thePost = $post;
        endif;

        if ($marker && ($marker_title == "")) :
            $marker_title = $thePost -> post_title;
        endif;
        if ($info_window) :
            if ($info_window_content == "") :
                $address = do_shortcode("[dms3-franchise-address post_id='$post_id']");
                $info_window_content = "<h3>{$thePost->post_title}</h3>" . $address;
            endif;
        else :
            $info_window_content = "";
        endif;

        // Code
        $args = array(
            'type' => 'map',
            'width' => $width, // Map width, default is 640px. You can use '%' or 'px'
            'height' => $height, // Map height, default is 480px. You can use '%' or 'px'
            'zoom' => $zoom, // Map zoom, default is the value set in admin, and if it's omitted - 14
            'marker' => $marker, // Display marker? Default is 'true',
            'marker_title' => $marker_title, // Marker title when hover
            'info_window' => $info_window_content, // Info window content, can be anything. HTML allowed.
        );
        // return "<pre>" . print_r($args, true) . "</pre>";
        return rwmb_meta(self::FIELD_MAP, $args, $post_id);
    }

    function shortcode_address($atts) {
        global $post;

        // Attributes
        extract(shortcode_atts(array(
            'post_id' => $post -> ID,
            'showZip' => 'before',
            'separator' => '<br/>'
        ), $atts));

        $address = get_post_meta($post_id, self::FIELD_ADDRESS, true);
        $zip = get_post_meta($post_id, self::FIELD_ZIP, true);
        $city = get_post_meta($post_id, self::FIELD_TOWN, true);

        $result = "<div class='dms3-franchise-address'>";
        $result .= $address . $separator;
        if ($showZip == 'before') :
            $result .= $zip . "&nbsp;" . $city;
        else :
            $result .= $city . "&nbsp;" . $zip;
        endif;
        $result .= "</div>";
        return $result;
    }

    function shortcode_contact($atts) {
        global $post;

        $field = self::FIELD_CONTACT;
        $class = "dms3-franchise-contact";

        // Attributes
        extract(shortcode_atts(array('post_id' => $post -> ID, ), $atts));

        $content = get_post_meta($post_id, $field, true);

        if ($content == '')
            return '';
        $result = "<div class='$class'>";
        $result .= $content;
        $result .= "</div>";
        return $result;
    }

    function shortcode_email($atts) {
        global $post;

        $field = self::FIELD_EMAIL;
        $class = "dms3-franchise-email";

        // Attributes
        extract(shortcode_atts(array('post_id' => $post -> ID, ), $atts));

        $content = get_post_meta($post_id, $field, true);

        if ($content == '')
            return '';
        $result = "<div class='$class'>";
        $result .= "<a href='mailto:$content'>$content</a>";
        $result .= "</div>";
        return $result;
    }

    function shortcode_phone($atts) {
        global $post;

        $field = self::FIELD_PHONE;
        $class = "dms3-franchise-phone";

        // Attributes
        extract(shortcode_atts(array('post_id' => $post -> ID, ), $atts));

        $content = get_post_meta($post_id, $field, true);

        if ($content == '')
            return '';
        $result = "<div class='$class'>";
        $result .= "<a href='tel:$content' target='_blank'>$content</a>";
        $result .= "</div>";
        return $result;
    }

    function shortcode_facebook($atts) {
        global $post;

        $field = self::FIELD_FACEBOOK;
        $class = "dms3-franchise-facebook";

        // Attributes
        extract(shortcode_atts(array('post_id' => $post -> ID, ), $atts));

        $content = get_post_meta($post_id, $field, true);

        if ($content == '')
            return '';
        $result = "<div class='$class'>";
        $result .= "<a href='$content' target='_blank'>facebook</a>";
        $result .= "</div>";
        return $result;
    }

    function shortcode_twitter($atts) {
        global $post;

        $field = self::FIELD_TWITTER;
        $class = "dms3-franchise-twitter";

        // Attributes
        extract(shortcode_atts(array('post_id' => $post -> ID, ), $atts));

        $content = get_post_meta($post_id, $field, true);

        if ($content == '')
            return '';
        $result = "<div class='$class'>";
        $result .= "<a href='https://twitter.com/$content' target='_blank'>@$content</a>";
        $result .= "</div>";
        return $result;
    }

    function shortcode_posts($atts) {
        global $post;

        $field = self::FIELD_CONTENTCAT;
        $class = "dms3-franchise-posts";

        // Attributes
        extract(shortcode_atts(array(
            'post_id' => $post -> ID,
            'posts_per_page' => 3,
            'image_size' => medium
        ), $atts));

        $content = get_post_meta($post_id, $field, true);
        $content = explode(",", $content);
        $slugs = array();
        foreach ($content as $tid) :
            $term = get_term_by("id", $tid, "category");
            if ($term) :
                $slugs[] = $term -> slug;
            endif;
        endforeach;
        if (count($slugs) == 0)
            return '';
        $slugs = implode(",", $slugs);
        $shortcode = "[display-posts wrapper='div' wrapper_class='$class' category='$slugs' posts_per_page='$posts_per_page' image_size='$image_size']";
        $result = do_shortcode($shortcode);
        return $result;
    }

    function shortcode_events($atts) {
        global $post;

        // Attributes
        extract(shortcode_atts(array(
            'post_id' => $post -> ID,
            'posts_per_page' => 3,
            'scope' => "future",
            'event_text'=>"<div class='event'>".__("#_EVENTLINK will take place at #_LOCATIONLINK on #_EVENTDATES at #_EVENTTIMES",self::TEXT_DOMAIN)."</div>",
        ), $atts));


        $field = self::FIELD_EVENTSCAT;
        $class = "dms3-franchise-events";

        $content = get_post_meta($post_id, $field, true);
        $shortcode = "[events_list scope='$scope' category='$content' limit='$posts_per_page']".$event_text.'[/events_list]';
        $result = "<div class='$class'>".do_shortcode($shortcode)."</div>";
        return $result;
    }

}

<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Inventor_Schools_Post_Type_Schools
 *
 * @class Inventor_Schools_Post_Type_Schools
 * @package Inventor/Classes/Post_Types
 * @author Pragmatic Mates
 */
class Inventor_Schools_Post_Type_Schools {
    /**
     * Initialize custom post type
     *
     * @access public
     * @return void
     */
    public static function init() {        
        add_action( 'init', array( __CLASS__, 'definition' ), 11 );
        add_action( 'cmb2_init', array( __CLASS__, 'fields' ) );
        add_filter( 'inventor_shop_allowed_listing_post_types', array( __CLASS__, 'allowed_purchasing' ) );
        add_filter( 'inventor_claims_allowed_listing_post_types', array( __CLASS__, 'allowed_claiming' ) );
        add_filter( 'inventor_compare_metaboxes', function( $metaboxes ) {
            return array_merge( $metaboxes, array( 'attributes' ) );
        } );
    }

    /**
     * Defines if post type can be purchased
     *
     * @access public
     * @param array $post_types
     * @return array
     */
    public static function allowed_purchasing( $post_types ) {
        $post_types[] = 'school';
        return $post_types;
    }

    /**
     * Defines if post type can be claimed
     *
     * @access public
     * @param array $post_types
     * @return array
     */
    public static function allowed_claiming( $post_types ) {
        $post_types[] = 'school';
        return $post_types;
    }

    /**
     * Custom post type definition
     *
     * @access public
     * @return void
     */
    public static function definition() {
        $labels = array(
            'name'                  => __( 'Schools', 'inventor-school' ),
            'singular_name'         => __( 'School', 'inventor-school' ),
            'add_new'               => __( 'Add New School', 'inventor-school' ),
            'add_new_item'          => __( 'Add New School', 'inventor-school' ),
            'edit_item'             => __( 'Edit School', 'inventor-school' ),
            'new_item'              => __( 'New School', 'inventor-school' ),
            'all_items'             => __( 'Schools', 'inventor-school' ),
            'view_item'             => __( 'View School', 'inventor-school' ),
            'search_items'          => __( 'Search School', 'inventor-school' ),
            'not_found'             => __( 'No Schools found', 'inventor-school' ),
            'not_found_in_trash'    => __( 'No School Found in Trash', 'inventor-school' ),
            'parent_item_colon'     => '',
            'menu_name'             => __( 'School', 'inventor-school' ),
            'icon'                  => apply_filters( 'inventor_listing_type_icon', 'inventor-poi-single-house', 'school' )
        );

        register_post_type( 'school',
            array(
                'labels'            => $labels,
                'show_in_menu'	    => 'listings',
                'supports'          => array( 'title', 'editor', 'thumbnail', 'comments', 'author' ),
                'has_archive'       => true,
                'rewrite'           => array( 'slug' => _x( 'properties', 'URL slug', 'inventor-school' ) ),
                'public'            => true,
                'show_ui'           => true,
                'show_in_rest'      => true,
                'categories'        => array(),
            )
        );
    }

    /**
     * Return array of contract type options
     *
     * @access public
     * @return array
     */
    public static function contract_options() {
        return array(
            ''      => '',
            'RENT'  => __( 'Rent', 'inventor-school' ),
            'SALE'  => __( 'Sale', 'inventor-school' ),
        );
    }

    /**
     * Returns contract type display value
     *
     * @access public
     * @param string $key
     * @return string
     */
    public static function get_contract_type( $key ) {
        $contract_options = self::contract_options();
        return $contract_options[ $key ];
    }

    /**
     * Defines custom fields
     *
     * @access public
     * @return array
     */
    public static function fields() {
        $post_type = 'school';

        if ( ! is_admin() ) {
            Inventor_Post_Types::add_metabox( $post_type, array( 'general' ) );
        }

        // Details
        $metabox_id = INVENTOR_LISTING_PREFIX . INVENTOR_SCHOOLS_PREFIX . 'details';

        $details = new_cmb2_box( array(
            'id'            => $metabox_id,
            'title'         => __( 'Details', 'inventor-school' ),
            'object_types'  => array( $post_type ),
            'context'       => 'normal',
            'priority'      => 'high',
            'show_in_rest'  => true,
        ) );

        $field_id = INVENTOR_LISTING_PREFIX . INVENTOR_SCHOOLS_PREFIX . 'type';
        if ( apply_filters( 'inventor_metabox_field_enabled', true, $metabox_id, $field_id, $post_type ) ) {
            $details->add_field( array(
                'name'              => __( 'School type', 'inventor-school' ),
                'id'                => $field_id,
                'type'              => 'taxonomy_select',
                'taxonomy'          => 'school_types',
            ) );
        }

        $field_id = INVENTOR_LISTING_PREFIX . INVENTOR_SCHOOLS_PREFIX . 'reference';
        if ( apply_filters( 'inventor_metabox_field_enabled', true, $metabox_id, $field_id, $post_type ) ) {
            $details->add_field( array(
                'name'              => __( 'Reference', 'inventor-school' ),
                'id'                => $field_id,
                'type'              => 'text_small',
            ) );
        }

        $field_id = INVENTOR_LISTING_PREFIX . INVENTOR_SCHOOLS_PREFIX . 'year_built';
        if ( apply_filters( 'inventor_metabox_field_enabled', true, $metabox_id, $field_id, $post_type ) ) {
            $details->add_field( array(
                'name'              => __( 'Year built', 'inventor-school' ),
                'id'                => $field_id,
                'type'              => 'text_small',
                'attributes'        => apply_filters( 'inventor_metabox_field_attributes', array(
                    'type'              => 'number',
                    'pattern'           => '\d*',
                    'min'               => 0
                ), $metabox_id, $field_id, $post_type ),
            ) );
        }

        $field_id = INVENTOR_LISTING_PREFIX . INVENTOR_SCHOOLS_PREFIX . 'contract_type';
        if ( apply_filters( 'inventor_metabox_field_enabled', true, $metabox_id, $field_id, $post_type ) ) {
            $details->add_field( array(
                'name'              => __( 'Contract type', 'inventor-school' ),
                'id'                => $field_id,
                'type'              => 'select',
                'options'           => self::contract_options()
            ) );
        }

        // Attributes

        $metabox_id = INVENTOR_LISTING_PREFIX . INVENTOR_SCHOOLS_PREFIX . 'attributes';

        $attributes = new_cmb2_box( array(
            'id'            => $metabox_id,
            'title'         => __( 'Attributes', 'inventor-school' ),
            'object_types'  => array( 'school' ),
            'context'       => 'normal',
            'priority'      => 'high',
            'show_in_rest'  => true,
        ) );

        $field_id = INVENTOR_LISTING_PREFIX . INVENTOR_SCHOOLS_PREFIX . 'rooms';
        if ( apply_filters( 'inventor_metabox_field_enabled', true, $metabox_id, $field_id, $post_type ) ) {
            $attributes->add_field( array(
                'name'              => __( 'Rooms', 'inventor-school' ),
                'id'                => $field_id,
                'type'              => 'text_small',
                'attributes'        => apply_filters( 'inventor_metabox_field_attributes', array(
                    'type'              => 'number',
                    'pattern'           => '\d*',
                    'min'               => 0
                ), $metabox_id, $field_id, $post_type ),
            ) );
        }

        $field_id = INVENTOR_LISTING_PREFIX . INVENTOR_SCHOOLS_PREFIX . 'beds';
        if ( apply_filters( 'inventor_metabox_field_enabled', true, $metabox_id, $field_id, $post_type ) ) {
            $attributes->add_field( array(
                'name'              => __( 'Beds', 'inventor-school' ),
                'id'                => $field_id,
                'type'              => 'text_small',
                'attributes'        => apply_filters( 'inventor_metabox_field_attributes', array(
                    'type'              => 'number',
                    'pattern'           => '\d*',
                    'min'               => 0
                ), $metabox_id, $field_id, $post_type ),
            ) );
        }

        $field_id = INVENTOR_LISTING_PREFIX . INVENTOR_SCHOOLS_PREFIX . 'baths';
        if ( apply_filters( 'inventor_metabox_field_enabled', true, $metabox_id, $field_id, $post_type ) ) {
            $attributes->add_field( array(
                'name'              => __( 'Baths', 'inventor-school' ),
                'id'                => $field_id,
                'type'              => 'text_small',
                'attributes'        => apply_filters( 'inventor_metabox_field_attributes', array(
                    'type'              => 'number',
                    'pattern'           => '\d*',
                    'min'               => 0
                ), $metabox_id, $field_id, $post_type ),
            ) );
        }

        $field_id = INVENTOR_LISTING_PREFIX . INVENTOR_SCHOOLS_PREFIX . 'garages';
        if ( apply_filters( 'inventor_metabox_field_enabled', true, $metabox_id, $field_id, $post_type ) ) {
            $attributes->add_field( array(
                'name'              => __( 'Garages', 'inventor-school' ),
                'id'                => $field_id,
                'type'              => 'text_small',
                'attributes'        => apply_filters( 'inventor_metabox_field_attributes', array(
                    'type'              => 'number',
                    'pattern'           => '\d*',
                    'min'               => 0
                ), $metabox_id, $field_id, $post_type ),
            ) );
        }

        $field_id = INVENTOR_LISTING_PREFIX . INVENTOR_SCHOOLS_PREFIX . 'parking_lots';
        if ( apply_filters( 'inventor_metabox_field_enabled', true, $metabox_id, $field_id, $post_type ) ) {
            $attributes->add_field( array(
                'name'              => __( 'Parking lots', 'inventor-school' ),
                'id'                => $field_id,
                'type'              => 'text_small',
                'attributes'        => apply_filters( 'inventor_metabox_field_attributes', array(
                    'type'              => 'number',
                    'pattern'           => '\d*',
                    'min'               => 0
                ), $metabox_id, $field_id, $post_type ),
            ) );
        }

        $field_id = INVENTOR_LISTING_PREFIX . INVENTOR_SCHOOLS_PREFIX . 'home_area';
        if ( apply_filters( 'inventor_metabox_field_enabled', true, $metabox_id, $field_id, $post_type ) ) {
            $attributes->add_field( array(
                'name'              => __( 'Home area', 'inventor-school' ),
                'id'                => $field_id,
                'description'       => sprintf( __( 'In %s. (Enter value without unit)', 'inventor-school' ), get_theme_mod( 'inventor_measurement_area_unit', 'sqft' ) ),
                'type'              => 'text_small',
                'attributes'        => apply_filters( 'inventor_metabox_field_attributes', array(
                    'type'              => 'number',
                    'step'              => 'any',
                    'min'               => 0,
                    'pattern'           => '\d*(\.\d*)?',
                ), $metabox_id, $field_id, $post_type ),
            ) );
        }

        $field_id = INVENTOR_LISTING_PREFIX . INVENTOR_SCHOOLS_PREFIX . 'lot_area';
        if ( apply_filters( 'inventor_metabox_field_enabled', true, $metabox_id, $field_id, $post_type ) ) {
            $attributes->add_field( array(
                'name'              => __( 'Lot area', 'inventor-school' ),
                'id'                => $field_id,
                'description'       => sprintf( __( 'In %s. (Enter value without unit)', 'inventor-school' ), get_theme_mod( 'inventor_measurement_area_unit', 'sqft' ) ),
                'type'              => 'text_small',
                'attributes'        => apply_filters( 'inventor_metabox_field_attributes', array(
                    'type'              => 'number',
                    'step'              => 'any',
                    'min'               => 0,
                    'pattern'           => '\d*(\.\d*)?',
                ), $metabox_id, $field_id, $post_type ),
            ) );
        }

        $field_id = INVENTOR_LISTING_PREFIX . INVENTOR_SCHOOLS_PREFIX . 'lot_dimensions';
        if ( apply_filters( 'inventor_metabox_field_enabled', true, $metabox_id, $field_id, $post_type ) ) {
            $attributes->add_field( array(
                'name'              => __( 'Lot dimensions', 'inventor-school' ),
                'id'                => $field_id,
                'type'              => 'text',
                'description'       => __( 'e.g. 20x30, 20x30x40, 20x30x40x50', 'inventor-school' ),
            ) );
        }

        $field_id = INVENTOR_LISTING_PREFIX . INVENTOR_SCHOOLS_PREFIX . 'amenities';
        if ( apply_filters( 'inventor_metabox_field_enabled', true, $metabox_id, $field_id, $post_type ) ) {
            $attributes->add_field( array(
                'name'              => __( 'Amenities', 'inventor-school' ),
                'id'                => $field_id,
                'type'              => 'taxonomy_multicheck_hierarchy',
                'taxonomy'          => 'school_amenities',
                'skip'              => true,
            ) );
        }

        Inventor_Post_Types::add_metabox( 'school', array( 'banner', 'gallery', 'video', 'location', 'price', 'contact', 'flags', 'listing_category' ) );
        Inventor_Post_Types::add_metabox( 'school', array( 'Inventor_Schools_Post_Type_Schools::floor_plans' ) );
        Inventor_Post_Types::add_metabox( 'school', array( 'Inventor_Schools_Post_Type_Schools::public_facilities' ) );
        Inventor_Post_Types::add_metabox( 'school', array( 'Inventor_Schools_Post_Type_Schools::valuation' ) );
    }

    /**
     * Metabox for public facilities
     *
     * @access public
     * @param $post_type
     * @return array
     */
    public static function metabox_floor_plans( $post_type ) {
        $metabox_id = INVENTOR_LISTING_PREFIX . INVENTOR_SCHOOLS_PREFIX . 'floor_plans_box';
        $field_id = INVENTOR_LISTING_PREFIX . INVENTOR_SCHOOLS_PREFIX . 'floor_plans';

        $cmb = new_cmb2_box( array(
            'id'            => $metabox_id,
            'title'			=> apply_filters( 'inventor_metabox_title', __( 'Floor plans', 'inventor-school' ), $metabox_id, $post_type ),
            'description'   => apply_filters( 'inventor_metabox_description', __( 'Upload some floor plans images.', 'inventor-school' ), $metabox_id, $post_type ),
            'object_types'  => array( $post_type ),
            'context'       => 'normal',
            'priority'      => 'high',
            'skip'          => true,
            'show_in_rest'  => true,
        ) );

        $field_name = __( 'Floor plans', 'inventor-school' );

        $cmb->add_field( array(
            'id'            => $field_id,
            'type'          => 'file_list',
            'name'          => apply_filters( 'inventor_metabox_field_name', $field_name, $metabox_id, $field_id, $post_type ),
            'description'   => apply_filters( 'inventor_metabox_field_description', null, $metabox_id, $field_id, $post_type ),
            'default'       => apply_filters( 'inventor_metabox_field_default', null, $metabox_id, $field_id, $post_type ),
            'attributes' 	=> apply_filters( 'inventor_metabox_field_attributes', array(), $metabox_id, $field_id, $post_type )
        ) );
    }

    /**
     * Metabox for public facilities
     *
     * @access public
     * @param $post_type
     * @return array
     */
    public static function metabox_public_facilities( $post_type ) {
        $metabox_id = INVENTOR_LISTING_PREFIX . INVENTOR_SCHOOLS_PREFIX . 'public_facilities_box';
        $field_id = INVENTOR_LISTING_PREFIX . INVENTOR_SCHOOLS_PREFIX . 'public_facilities';

        if ( ! is_admin() ) {
            add_filter( 'cmb2_override_' . $field_id . '_group_meta_value', array( __CLASS__, 'set_default_value' ) , 0, 4 );
        }

        $facilities_default = apply_filters( 'inventor_metabox_field_default', null, $metabox_id, $field_id, $post_type );

        new_cmb2_box( array(
            'id'            => $metabox_id,
            'title'         => __( 'Public facilities', 'inventor-school' ),
            'object_types'  => array( 'school' ),
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true,
            'show_in_rest'  => true,
            'skip'          => true,
            'fields'        => array(
                // group
                array(
                    'id'                => $field_id,
                    'type'              => 'group',
                    'post_type'         => $post_type,
                    'custom_value'	    => $facilities_default,
                    'options'     	    => array(
                        'group_title'   => __( 'Public Facility', 'inventor-school' ),
                        'add_button'    => __( 'Add Another Public Facility', 'inventor-school' ),
                        'remove_button' => __( 'Remove Public Facility', 'inventor-school' ),
                    ),
                    'fields'            => array(
                        // group fields
                        array(
                            'id'                => INVENTOR_LISTING_PREFIX . INVENTOR_SCHOOLS_PREFIX . 'public_facilities_key',
                            'name'              => __( 'Key', 'inventor-school' ),
                            'type'              => 'text',
                            'custom_value'	    => $facilities_default,
                        ),
                        array(
                            'id'                => INVENTOR_LISTING_PREFIX . INVENTOR_SCHOOLS_PREFIX . 'public_facilities_value',
                            'name'              => __( 'Value', 'inventor-school' ),
                            'type'              => 'text',
                            'custom_value'	    => $facilities_default,
                        ),
                    ),
                ),
            ),
        ) );
    }

    /**
     * Metabox for valuation
     *
     * @access public
     * @param $post_type
     * @return array
     */
    public static function metabox_valuation( $post_type ) {
        $metabox_id = INVENTOR_LISTING_PREFIX . INVENTOR_SCHOOLS_PREFIX . 'valuation_box';
        $field_id = INVENTOR_LISTING_PREFIX . INVENTOR_SCHOOLS_PREFIX . 'valuation';

        if ( ! is_admin() ) {
            add_filter('cmb2_override_' . $field_id . '_group_meta_value', array( __CLASS__, 'set_default_value' ) , 0, 4);
        }

        $valuation_default = apply_filters( 'inventor_metabox_field_default', null, $metabox_id, $field_id, $post_type );

        new_cmb2_box( array(
            'id'            => $metabox_id,
            'title'         => __( 'Valuation', 'inventor-school' ),
            'object_types'  => array( 'school' ),
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true,
            'show_in_rest'  => true,
            'skip'          => true,
            'fields'        => array(
                // group
                array(
                    'id'                => $field_id,
                    'type'              => 'group',
                    'custom_value'	    => $valuation_default,
                    'post_type'         => 'school',
                    'options'     	    => array(
                        'group_title'   => __( 'Valuation', 'inventor-school' ),
                        'add_button'    => __( 'Add Another Valuation', 'inventor-school' ),
                        'remove_button' => __( 'Remove Valuation', 'inventor-school' ),
                    ),
                    'fields'            => array(
                        // group fields
                        array(
                            'id'                => INVENTOR_LISTING_PREFIX . INVENTOR_SCHOOLS_PREFIX . 'valuation_key',
                            'name'              => __( 'Key', 'inventor-school' ),
                            'type'              => 'text',
                            'custom_value'	    => $valuation_default,
                        ),
                        array(
                            'id'                => INVENTOR_LISTING_PREFIX . INVENTOR_SCHOOLS_PREFIX . 'valuation_value',
                            'name'              => __( 'Value', 'inventor-school' ),
                            'type'              => 'text',
                            'custom_value'	    => $valuation_default,
                        ),
                    ),
                ),
            ),
        ) );
    }

    /**
     * Set default data for field
     *
     * @access public
     * @param $data
     * @param $object_id
     * @param $a
     * @param $field
     * @return mixed
     */
    public static function set_default_value( $data, $object_id, $a, $field ) {
        if ( ! empty( $field->args['custom_value'] ) ) {
            return $field->args['custom_value'];
        }

        return $data;
    }
}

Inventor_Schools_Post_Type_Schools::init();

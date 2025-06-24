<?php
/**
 * Admin settings page for Fortified Chat.
 *
 * @package FortifiedChat
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

class Fortified_Chat_Settings_Page {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'settings_init' ) );
    }

    /**
     * Add the admin menu item.
     */
    public function add_admin_menu() {
        add_options_page(
            __( 'Fortified Chat Settings', 'fortified-chat' ),
            __( 'Fortified Chat', 'fortified-chat' ),
            'manage_options',
            'fortified_chat_settings',
            array( $this, 'render_settings_page' )
        );
    }

    /**
     * Initialize settings, sections, and fields.
     */
    public function settings_init() {
        // Register a setting group
        register_setting( 'fortified_chat_settings_group', 'fortified_chat_options', array( $this, 'sanitize_settings' ) );

        // Add a settings section for Jobber API
        add_settings_section(
            'fortified_chat_jobber_api_section',
            __( 'Jobber API Configuration', 'fortified-chat' ),
            array( $this, 'jobber_api_section_callback' ),
            'fortified_chat_settings_group' // Page slug where this section will be displayed
        );

        // Add Jobber API Key field
        add_settings_field(
            'jobber_api_key',
            __( 'Jobber API Key', 'fortified-chat' ),
            array( $this, 'render_jobber_api_key_field' ),
            'fortified_chat_settings_group', // Page slug
            'fortified_chat_jobber_api_section' // Section ID
        );

        // Add Jobber Account ID field (example, might be needed depending on API)
        add_settings_field(
            'jobber_account_id',
            __( 'Jobber Account ID (Optional)', 'fortified-chat' ),
            array( $this, 'render_jobber_account_id_field' ),
            'fortified_chat_settings_group',
            'fortified_chat_jobber_api_section'
        );
    }

    /**
     * Sanitize settings data.
     *
     * @param array $input The input data.
     * @return array Sanitized data.
     */
    public function sanitize_settings( $input ) {
        $sanitized_input = array();
        if ( isset( $input['jobber_api_key'] ) ) {
            $sanitized_input['jobber_api_key'] = sanitize_text_field( $input['jobber_api_key'] );
        }
        if ( isset( $input['jobber_account_id'] ) ) {
            $sanitized_input['jobber_account_id'] = sanitize_text_field( $input['jobber_account_id'] );
        }
        // Add more sanitization as needed for other fields
        return $sanitized_input;
    }

    /**
     * Callback for the Jobber API section.
     */
    public function jobber_api_section_callback() {
        echo '<p>' . __( 'Enter your Jobber API credentials below. These are required for the plugin to schedule appointments.', 'fortified-chat' ) . '</p>';
    }

    /**
     * Render the Jobber API Key field.
     */
    public function render_jobber_api_key_field() {
        $options = get_option( 'fortified_chat_options' );
        $api_key = isset( $options['jobber_api_key'] ) ? $options['jobber_api_key'] : '';
        echo '<input type="text" id="jobber_api_key" name="fortified_chat_options[jobber_api_key]" value="' . esc_attr( $api_key ) . '" class="regular-text">';
        echo '<p class="description">' . __( 'Your Jobber API Key.', 'fortified-chat' ) . '</p>';
    }

    /**
     * Render the Jobber Account ID field.
     */
    public function render_jobber_account_id_field() {
        $options = get_option( 'fortified_chat_options' );
        $account_id = isset( $options['jobber_account_id'] ) ? $options['jobber_account_id'] : '';
        echo '<input type="text" id="jobber_account_id" name="fortified_chat_options[jobber_account_id]" value="' . esc_attr( $account_id ) . '" class="regular-text">';
        echo '<p class="description">' . __( 'Optional: Your Jobber Account ID, if required by the API.', 'fortified-chat' ) . '</p>';
    }

    /**
     * Render the settings page.
     */
    public function render_settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields( 'fortified_chat_settings_group' ); // Output security fields for the registered setting group
                do_settings_sections( 'fortified_chat_settings_group' ); // Output the sections and fields for the page
                submit_button( __( 'Save Settings', 'fortified-chat' ) );
                ?>
            </form>
        </div>
        <?php
    }
}

?>

<?php
/**
 * Plugin Name: Fortified Chat
 * Plugin URI: https://fortifiedplumbinganddrain.com/
 * Description: A chat plugin for Fortified Plumbing to answer questions and schedule appointments via Jobber.
 * Version: 0.1.0
 * Author: Jules AI Agent
 * Author URI: https://example.com/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: fortified-chat
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Define plugin constants
define( 'FORTIFIED_CHAT_VERSION', '0.1.0' );
define( 'FORTIFIED_CHAT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'FORTIFIED_CHAT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include admin settings page
require_once FORTIFIED_CHAT_PLUGIN_DIR . 'includes/admin/settings-page.php';
require_once FORTIFIED_CHAT_PLUGIN_DIR . 'includes/class-fortified-chat-knowledge-base.php';
require_once FORTIFIED_CHAT_PLUGIN_DIR . 'includes/class-fortified-chat-jobber-api.php';

// Activation and deactivation hooks (optional for now, can add later if needed for setup/cleanup)
// register_activation_hook( __FILE__, 'fortified_chat_activate' );
// register_deactivation_hook( __FILE__, 'fortified_chat_deactivate' );

// function fortified_chat_activate() {
//     // Actions to run on activation
// }

// function fortified_chat_deactivate() {
//     // Actions to run on deactivation
// }

/**
 * Initialize the plugin.
 * Loads admin settings.
 */
function fortified_chat_init() {
    if ( is_admin() ) {
        new Fortified_Chat_Settings_Page();
    }
    // Other initialization code will go here
    // e.g., enqueueing scripts and styles for the chat interface, shortcode registration
}
add_action( 'plugins_loaded', 'fortified_chat_init' );

// More plugin functionality will be added below and in included files.

/**
 * Enqueue scripts and styles for the public-facing chat interface.
 */
function fortified_chat_enqueue_public_assets() {
    // Enqueue Dashicons, as they are used in the chatbox HTML
    wp_enqueue_style( 'dashicons' );

    wp_enqueue_style(
        'fortified-chat-public-css',
        FORTIFIED_CHAT_PLUGIN_URL . 'public/css/fortified-chat-public.css',
        array(), // Dependencies
        FORTIFIED_CHAT_VERSION,
        'all'
    );

    wp_enqueue_script(
        'fortified-chat-public-js',
        FORTIFIED_CHAT_PLUGIN_URL . 'public/js/fortified-chat-public.js',
        array(), // Dependencies
        FORTIFIED_CHAT_VERSION,
        true // Load in footer
    );

    // Pass data to JavaScript, like AJAX URL, nonce, and translatable strings
    wp_localize_script(
        'fortified-chat-public-js',
        'fortifiedChatPublic', // Object name in JavaScript
        array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'fortified_chat_nonce' ),
            'i18n'    => array(
                'openChat'     => __( 'Open Chat', 'fortified-chat' ),
                'closeChat'    => __( 'Close Chat', 'fortified-chat' ),
                'botThinking'  => __( 'Thinking...', 'fortified-chat' ),
                'errorMessage' => __( 'Sorry, an error occurred. Please try again.', 'fortified-chat' ),
                // Add more translatable strings here as needed
            ),
        )
    );
}
add_action( 'wp_enqueue_scripts', 'fortified_chat_enqueue_public_assets' );


/**
 * Register the shortcode to display the chatbox.
 *
 * Usage: [fortified_chat]
 */
function fortified_chat_shortcode() {
    ob_start();
    // Ensure the path is correct.
    // Using FORTIFIED_CHAT_PLUGIN_DIR to ensure it's an absolute path.
    $chatbox_template = FORTIFIED_CHAT_PLUGIN_DIR . 'public/partials/chatbox-display.php';

    if ( file_exists( $chatbox_template ) ) {
        include $chatbox_template;
    } else {
        // Fallback or error message if the template file is missing
        echo '<!-- Fortified Chat: chatbox-display.php not found. -->';
    }
    return ob_get_clean();
}
add_shortcode( 'fortified_chat', 'fortified_chat_shortcode' );


/**
 * Handle AJAX request for sending a message.
 */
function fortified_chat_handle_send_message() {
    // Verify nonce for security
    check_ajax_referer( 'fortified_chat_nonce', 'nonce' );

    // Get the message from the POST request
    $message = isset( $_POST['message'] ) ? sanitize_text_field( wp_unslash( $_POST['message'] ) ) : '';

    if ( empty( $message ) ) {
        wp_send_json_error( array( 'reply' => __( 'Empty message received.', 'fortified-chat' ) ) );
        return;
    }

    // Initialize Knowledge Base
    $kb = new Fortified_Chat_Knowledge_Base();
    $reply = '';
    $message_lower = strtolower( $message );

    // Session/State Management using Transients
    // IMPORTANT: The current session management uses a single, global transient key ('fortified_chat_user_session').
    // This is a simplification for the current development exercise and WILL NOT WORK correctly for concurrent users
    // on a live site, as their sessions would overwrite each other.
    // A production-ready plugin would need to implement a user-specific session identifier
    // (e.g., a unique token generated in JS, passed with each AJAX request, and used in the transient key)
    // to handle multiple simultaneous chat sessions independently.
    $session_id = 'fortified_chat_user_session'; // Simplified: In real app, make this unique per user.
    $session_data = get_transient( $session_id );
    if ( false === $session_data ) {
        $session_data = array( 'state' => 'initial', 'appointment_details' => array() );
    }

    // --- Appointment Flow Logic ---
    if ( $session_data['state'] !== 'initial' && !preg_match( '/\b(cancel|stop|nevermind|exit)\b/i', $message_lower ) ) {
        switch ( $session_data['state'] ) {
            case 'awaiting_name':
                $session_data['appointment_details']['name'] = ucwords( sanitize_text_field( $message ) );
                $session_data['state'] = 'awaiting_phone';
                $reply = __( 'Thanks, ', 'fortified-chat' ) . $session_data['appointment_details']['name'] . '! ' . __( 'What is your phone number?', 'fortified-chat' );
                break;
            case 'awaiting_phone':
                // Basic phone validation (can be improved)
                if ( preg_match( '/^[0-9\s\-\(\)\+]+$/', $message ) ) {
                    $session_data['appointment_details']['phone'] = sanitize_text_field( $message );
                    $session_data['state'] = 'awaiting_address';
                    $reply = __( 'Got it. And what is the service address?', 'fortified-chat' );
                } else {
                    $reply = __( 'That doesn\'t look like a valid phone number. Please enter a valid phone number.', 'fortified-chat' );
                }
                break;
            case 'awaiting_address':
                $session_data['appointment_details']['address'] = sanitize_text_field( $message );
                $session_data['state'] = 'awaiting_issue';
                $reply = __( 'Great. Please briefly describe the plumbing issue or the service you need.', 'fortified-chat' );
                break;
            case 'awaiting_issue':
                $session_data['appointment_details']['issue'] = sanitize_text_field( $message );
                $session_data['state'] = 'awaiting_datetime';
                $reply = __( 'Okay. Do you have a preferred date and time for the appointment? Or would you like us to call you to schedule?', 'fortified-chat' );
                break;
            case 'awaiting_datetime':
                $session_data['appointment_details']['datetime_preference'] = sanitize_text_field( $message );
                $session_data['state'] = 'confirming_appointment';
                // Confirmation message
                $details = $session_data['appointment_details'];
                $reply = __( 'Alright, let\'s confirm: ', 'fortified-chat' ) . "\n";
                $reply .= __( 'Name: ', 'fortified-chat' ) . $details['name'] . "\n";
                $reply .= __( 'Phone: ', 'fortified-chat' ) . $details['phone'] . "\n";
                $reply .= __( 'Address: ', 'fortified-chat' ) . $details['address'] . "\n";
                $reply .= __( 'Issue: ', 'fortified-chat' ) . $details['issue'] . "\n";
                $reply .= __( 'Preferred Time: ', 'fortified-chat' ) . $details['datetime_preference'] . "\n\n";
                $reply .= __( 'Does this look correct? (Yes/No)', 'fortified-chat' );
                break;
            case 'confirming_appointment':
                if ( preg_match( '/\b(yes|yeah|correct|yep|ok|sure)\b/i', $message_lower ) ) {
                    $jobber_api = new Fortified_Chat_Jobber_API();
                    $client_details = array(
                        'name'    => $session_data['appointment_details']['name'],
                        'phone'   => $session_data['appointment_details']['phone'],
                        'address' => $session_data['appointment_details']['address'],
                        // 'email' => '', // Could add an email collection step if desired
                    );

                    $client_result = $jobber_api->create_client( $client_details );

                    if ( $client_result['success'] ) {
                        $job_details = array(
                            'issue'                 => $session_data['appointment_details']['issue'],
                            'datetime_preference' => $session_data['appointment_details']['datetime_preference'],
                        );
                        $job_result = $jobber_api->create_job_request( $client_result['client_id'], $job_details );

                        if ( $job_result['success'] ) {
                            $reply = __( 'Thank you! Your appointment request has been successfully submitted to Jobber. We will contact you shortly to confirm the schedule.', 'fortified-chat' );
                            if ( WP_DEBUG && isset($client_result['data_sent']) && isset($job_result['data_sent']) ) {
                                $reply .= "\nDEBUG: Client ID: " . esc_html($client_result['client_id']) . ", Job ID: " . esc_html($job_result['job_id']);
                            }
                             delete_transient( $session_id ); // Clear session
                        } else {
                            // Failed to create job
                            $reply = __( 'We were able to note your details, but there was an issue submitting your service request to our system. Please call us directly at ', 'fortified-chat' ) . $kb->get_info('phone') . __( ' to complete your booking. Error: ', 'fortified-chat' ) . esc_html( $job_result['error'] );
                            // Don't delete transient, user might want to retry or we might log this
                        }
                    } else {
                        // Failed to create client
                        $reply = __( 'There was an issue saving your contact details. Please call us directly at ', 'fortified-chat' ) . $kb->get_info('phone') . __( ' to schedule your appointment. Error: ', 'fortified-chat' ) . esc_html( $client_result['error'] );
                        // Don't delete transient
                    }
                    $session_data['state'] = 'initial'; // Reset state even on partial failure, to avoid loops.
                } elseif ( preg_match( '/\b(no|nope|wrong|incorrect)\b/i', $message_lower ) ) {
                    $reply = __( 'Okay, let\'s start over with the appointment details. What is your name?', 'fortified-chat' );
                    $session_data['state'] = 'awaiting_name';
                    $session_data['appointment_details'] = array(); // Reset details
                } else {
                    $reply = __( 'Please answer "Yes" or "No" to confirm the details.', 'fortified-chat' );
                }
                break;
            default:
                // Should not happen if states are managed correctly
                $reply = $kb->get_info('default_reply');
                $session_data['state'] = 'initial'; // Reset state
                break;
        }
    }
    // --- End Appointment Flow Logic ---
    // --- General Knowledge Base Queries (if not in an appointment flow) ---
    elseif ( $session_data['state'] === 'initial' || preg_match( '/\b(cancel|stop|nevermind|exit)\b/i', $message_lower ) ) {
        if (preg_match( '/\b(cancel|stop|nevermind|exit)\b/i', $message_lower )) {
            if ($session_data['state'] !== 'initial') {
                $reply = __("Okay, I've cancelled the current appointment request. How else can I help?", 'fortified-chat');
            } else {
                $reply = __("Sure, how can I help you?", 'fortified-chat');
            }
            $session_data['state'] = 'initial';
            $session_data['appointment_details'] = array();
        }
        // Greeting
        elseif ( preg_match( '/\b(hello|hi|hey|greetings|good morning|good afternoon|good evening)\b/i', $message_lower ) ) {
            $reply = $kb->get_random_greeting();
        }
        // Services
        elseif ( preg_match( '/\b(service|services|offer|do you do|can you fix|repair)\b/i', $message_lower ) ) {
            // (Logic for services - kept for brevity, same as before)
            if ( preg_match( '/\b(list|all|what kind|types of)\b/i', $message_lower ) ) {
                $all_services = $kb->get_all_services_list();
                $reply = __( 'We offer a variety of plumbing services, including: ', 'fortified-chat' ) . "\n - " . implode("\n - ", $all_services) . "\n\n" . $kb->get_info('contact_prompt');
            } else {
                $reply = $kb->get_services_summary() . " " . __( 'Are you interested in residential or commercial services, or a specific issue like water heaters or drain cleaning?', 'fortified-chat' );
            }
        }
        // Service Areas
        elseif ( preg_match( '/\b(area|areas|where|locations|service area|towns|cities)\b/i', $message_lower ) ) {
            $reply = $kb->get_service_areas_text();
        }
        // Payment Options
        elseif ( preg_match( '/\b(payment|pay|accept|cash|card|credit card|paypal)\b/i', $message_lower ) ) {
            $reply = $kb->get_payment_options_text();
        }
        // Contact Information
        elseif ( preg_match( '/\b(contact|phone|email|address|call you)\b/i', $message_lower ) ) {
            $reply = $kb->get_contact_details();
        }
        // Experience
        elseif ( preg_match( '/\b(experience|how long|experienced)\b/i', $message_lower ) ) {
            $reply = $kb->get_info('experience');
        }
        // Specials
        elseif ( preg_match( '/\b(special|specials|deal|offer|coupon|discount)\b/i', $message_lower ) ) {
            $reply = $kb->get_info('specials');
        }
        // FAQs
        elseif ( preg_match( '/\b(faq|leaks|toilet leak|garbage disposal|water heater benefits)\b/i', $message_lower ) ) {
            // (Logic for FAQs - kept for brevity, same as before)
            $faq_reply = null;
            if (stripos($message_lower, 'leak') !== false) $faq_reply = $kb->search_faq('leak');
            if (!$faq_reply && stripos($message_lower, 'toilet') !== false) $faq_reply = $kb->search_faq('toilet');
            if ($faq_reply) $reply = $faq_reply;
            else $reply = __( "I can answer some general plumbing questions. For specific issues, it's often best to call us.", 'fortified-chat' );
        }
        // Appointment Initiation
        elseif ( preg_match( '/\b(appointment|schedule|book|visit|come out)\b/i', $message_lower ) ) {
            $session_data['state'] = 'awaiting_name';
            $session_data['appointment_details'] = array(); // Clear any old details
            $reply = __( 'I can help you schedule an appointment! To start, what is your name?', 'fortified-chat' );
        }
        // Thank you / Bye
        elseif ( preg_match( '/\b(thank you|thanks|bye|goodbye|cheers)\b/i', $message_lower ) ) {
            $reply = __( 'You\'re welcome! Have a great day. Feel free to reach out if you need more help.', 'fortified-chat' );
        }
        // Default / Fallback
        else {
            $reply = $kb->get_info('default_reply') . "\n" . sprintf( __( "You asked: \"%s\"", 'fortified-chat' ), esc_html( $message ) );
        }
    }
    // --- End General Knowledge Base Queries ---

    // Save the session data
    set_transient( $session_id, $session_data, HOUR_IN_SECONDS ); // Store for 1 hour

    wp_send_json_success( array( 'reply' => $reply ) );
}
// Hook for logged-in users
add_action( 'wp_ajax_fortified_chat_send_message', 'fortified_chat_handle_send_message' );
// Hook for non-logged-in users (important for a public chat)
add_action( 'wp_ajax_nopriv_fortified_chat_send_message', 'fortified_chat_handle_send_message' );


/**
 * Load plugin textdomain for internationalization.
 */
function fortified_chat_load_textdomain() {
    load_plugin_textdomain( 'fortified-chat', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'init', 'fortified_chat_load_textdomain' );

?>

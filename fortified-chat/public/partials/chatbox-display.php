<?php
/**
 * Provides the HTML structure for the chatbox.
 *
 * This file is to be included where the chatbox needs to be displayed,
 * likely via a shortcode or an action hook.
 *
 * @package FortifiedChat
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
?>

<div id="fortified-chat-container" class="fortified-chat-closed">
    <div id="fortified-chat-header">
        <span id="fortified-chat-title"><?php _e( 'Chat with Us!', 'fortified-chat' ); ?></span>
        <button id="fortified-chat-toggle-button">
            <span class="dashicons dashicons-format-chat" aria-label="<?php _e( 'Open Chat', 'fortified-chat' ); ?>"></span>
            <span class="dashicons dashicons-no-alt" aria-label="<?php _e( 'Close Chat', 'fortified-chat' ); ?>" style="display:none;"></span>
        </button>
    </div>
    <div id="fortified-chat-body">
        <div id="fortified-chat-messages">
            <!-- Messages will be appended here by JavaScript -->
            <div class="fortified-chat-message bot">
                <p><?php _e( 'Hello! How can I help you today?', 'fortified-chat' ); ?></p>
            </div>
        </div>
        <div id="fortified-chat-input-area">
            <input type="text" id="fortified-chat-input" placeholder="<?php _e( 'Type your message...', 'fortified-chat' ); ?>" aria-label="<?php _e( 'Chat input', 'fortified-chat' ); ?>">
            <button id="fortified-chat-send-button" aria-label="<?php _e( 'Send Message', 'fortified-chat' ); ?>">
                <span class="dashicons dashicons-sender"></span>
            </button>
        </div>
    </div>
</div>

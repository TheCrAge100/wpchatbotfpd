=== Fortified Chat ===
Contributors: Jules AI Agent
Tags: chat, chatbot, jobber, appointment, customer service, plumbing
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A chat plugin for Fortified Plumbing to answer questions and schedule appointments via Jobber.

== Description ==

Fortified Chat provides a website chat interface for businesses, initially tailored for Fortified Plumbing. It aims to:

*   Answer frequently asked questions based on a configurable knowledge base.
*   Guide users through an appointment scheduling process.
*   Integrate with Jobber to send appointment details (NOTE: Jobber API integration is currently a placeholder).

This plugin is currently in an early development stage.

== Installation ==

1.  Upload the `fortified-chat` directory to the `/wp-content/plugins/` directory.
2.  Activate the plugin through the 'Plugins' menu in WordPress.
3.  Go to Settings > Fortified Chat to configure the Jobber API Key.
4.  Use the shortcode `[fortified_chat]` on any page or post to display the chatbox.

== Frequently Asked Questions ==

= Is this plugin ready for production use? =

Not yet. Key considerations:
*   **Jobber API Integration:** The code for actual Jobber API calls (`class-fortified-chat-jobber-api.php`) contains placeholders. You will need to fill in the correct API endpoints, request payloads, and response handling based on official Jobber API documentation.
*   **Concurrent User Sessions:** The current appointment booking flow uses a global transient for session management. This will not work for multiple concurrent users. A user-specific session token mechanism is required for production use.
*   **Knowledge Base:** The knowledge base is currently hardcoded in `class-fortified-chat-knowledge-base.php`. For more flexibility, this could be made configurable via the admin interface.

= How do I configure the Jobber API Key? =

Go to Settings > Fortified Chat in your WordPress admin area. You will find fields to enter your Jobber API Key and an optional Account ID.

== Screenshots ==

(No screenshots included in this version)

== Changelog ==

= 0.1.0 - Initial Release =
*   Basic chat interface (HTML, CSS, JS).
*   Shortcode `[fortified_chat]` to display chatbox.
*   Admin settings page for Jobber API key.
*   Knowledge base class to answer basic questions.
*   Multi-turn appointment booking flow with session management (using transients - see production note above).
*   Placeholder Jobber API integration for creating clients and job requests.

== Upgrade Notice ==

(No upgrade notices for this initial version)

== Developer Notes ==

*   **Jobber API:** The file `includes/class-fortified-chat-jobber-api.php` needs to be updated with actual Jobber API endpoints, request structures, and authentication details. The current implementation simulates API calls when `WP_DEBUG` is true.
*   **Session Management:** The AJAX handler `fortified_chat_handle_send_message` in `fortified-chat.php` uses a global transient `fortified_chat_user_session` for managing appointment flow state. This needs to be replaced with a user-specific session mechanism (e.g., client-side generated token passed with AJAX) for production to support concurrent users.
*   **Text Domain:** `fortified-chat` for translations. `languages` folder is the target for `.mo` files.

---
This readme.txt is a starting point and would typically be expanded with more detail.

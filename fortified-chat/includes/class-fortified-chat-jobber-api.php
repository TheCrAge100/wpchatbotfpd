<?php
/**
 * Jobber API Integration for Fortified Chat.
 *
 * Handles communication with the Jobber API.
 * NOTE: This implementation contains placeholders for API endpoints and data structures
 * as the official Jobber API documentation was not accessible at the time of development.
 * These will need to be updated with actual Jobber API details.
 *
 * @package FortifiedChat
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

class Fortified_Chat_Jobber_API {

    private $api_key;
    private $account_id; // May or may not be needed
    // TODO: Replace with actual Jobber API base URL
    private $api_base_url = 'https://api.getjobber.com/api/graphql'; // Common guess for GraphQL, or /v1/ for REST

    public function __construct() {
        $options = get_option( 'fortified_chat_options' );
        $this->api_key = isset( $options['jobber_api_key'] ) ? $options['jobber_api_key'] : '';
        $this->account_id = isset( $options['jobber_account_id'] ) ? $options['jobber_account_id'] : ''; // Optional
    }

    /**
     * Placeholder: Create a new client in Jobber.
     *
     * @param array $client_details Associative array of client details (name, phone, email, address).
     * @return array Result of the API call (e.g., ['success' => true, 'client_id' => '123'] or ['success' => false, 'error' => 'message']).
     */
    public function create_client( $client_details ) {
        if ( empty( $this->api_key ) ) {
            return array( 'success' => false, 'error' => __( 'Jobber API Key is not configured.', 'fortified-chat' ) );
        }

        // TODO: Replace with actual Jobber API endpoint and payload for creating a client.
        $endpoint = $this->api_base_url . '/clients'; // Placeholder

        // Placeholder payload structure - this will likely need significant adjustment
        $payload = array(
            'client' => array(
                'name' => $client_details['name'],
                'phones' => array( array( 'number' => $client_details['phone'], 'primary' => true ) ),
                // 'email' => isset($client_details['email']) ? $client_details['email'] : null, // Assuming email is optional
                'billing_address' => array( // Jobber might have specific address fields
                    'street' => $client_details['address'],
                    // 'city' => '', // These would need to be parsed or collected separately
                    // 'province' => '',
                    // 'postal_code' => ''
                )
            )
        );

        // Simulate API call
        // $response = wp_remote_post( $endpoint, array(
        //     'headers' => array(
        //         'Authorization' => 'Bearer ' . $this->api_key,
        //         'Content-Type' => 'application/json',
        //         // 'X-Jobber-Account-ID' => $this->account_id, // If needed
        //     ),
        //     'body' => json_encode( $payload ),
        //     'timeout' => 30,
        // ));

        // if ( is_wp_error( $response ) ) {
        //     return array( 'success' => false, 'error' => $response->get_error_message() );
        // }

        // $response_code = wp_remote_retrieve_response_code( $response );
        // $response_body = json_decode( wp_remote_retrieve_body( $response ), true );

        // if ( $response_code >= 200 && $response_code < 300 && isset($response_body['client']['id']) ) {
        //     return array( 'success' => true, 'client_id' => $response_body['client']['id'] );
        // } else {
        //     $error_message = isset($response_body['error']) ? $response_body['error'] : __( 'Failed to create client in Jobber.', 'fortified-chat');
        //     if(isset($response_body['errors'])) $error_message = json_encode($response_body['errors']);
        //     return array( 'success' => false, 'error' => $error_message, 'details' => $response_body );
        // }

        // --- Fallback for testing without live API ---
        if ( WP_DEBUG ) {
            // Simulate a successful client creation for testing purposes
            return array( 'success' => true, 'client_id' => 'simulated_client_' . time(), 'data_sent' => $payload );
        }
        return array( 'success' => false, 'error' => __( 'Jobber API for client creation is not yet fully implemented. Needs actual API endpoint and payload.', 'fortified-chat' ) );
    }

    /**
     * Placeholder: Create a new job or request in Jobber.
     *
     * @param string $client_id The ID of the client in Jobber.
     * @param array $job_details Associative array of job details (issue description, preferred_datetime).
     * @return array Result of the API call.
     */
    public function create_job_request( $client_id, $job_details ) {
        if ( empty( $this->api_key ) ) {
            return array( 'success' => false, 'error' => __( 'Jobber API Key is not configured.', 'fortified-chat' ) );
        }

        // TODO: Replace with actual Jobber API endpoint and payload for creating a job/request.
        // This could be a 'request', 'job', 'task', or 'assessment' depending on Jobber's terminology.
        $endpoint = $this->api_base_url . '/requests'; // Placeholder, could be /jobs

        // Placeholder payload structure - this will likely need significant adjustment
        $payload = array(
            'request' => array( // or 'job'
                'client_id' => $client_id,
                'title' => 'Chat Plugin Appointment Request', // Or use part of the issue
                'description' => $job_details['issue'],
                'preferred_timing_notes' => $job_details['datetime_preference'],
                // Jobber might have structured fields for preferred dates/times, or custom fields
            )
        );

        // Simulate API call (similar to create_client)
        // $response = wp_remote_post( $endpoint, array(...) );
        // ... handle response ...

        // --- Fallback for testing without live API ---
        if ( WP_DEBUG ) {
            // Simulate a successful job creation
            return array( 'success' => true, 'job_id' => 'simulated_job_' . time(), 'data_sent' => $payload );
        }
        return array( 'success' => false, 'error' => __( 'Jobber API for job/request creation is not yet fully implemented. Needs actual API endpoint and payload.', 'fortified-chat' ) );
    }
}
?>

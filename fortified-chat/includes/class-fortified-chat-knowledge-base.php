<?php
/**
 * Knowledge Base for Fortified Chat.
 *
 * Stores information about Fortified Plumbing and provides methods to query it.
 *
 * @package FortifiedChat
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

class Fortified_Chat_Knowledge_Base {

    private $data;

    public function __construct() {
        $this->data = array(
            'company_name' => 'Fortified Plumbing & Drain LLC',
            'phone' => '(734) 961-5698',
            'email' => 'Fortifiedplumbing@outlook.com',
            'address' => '37140 Goddard Rd, Romulus, MI 48174',
            'availability' => 'We offer 24/7 Emergency Service.',
            'experience' => 'We have over 25 years of plumbing experience in the Michigan area.',
            'introduction' => 'Plumbing problems can be very frustrating. We suggest consulting us for your plumbing problems. We can help you understand what’s going on before we start any repair.',

            'service_areas' => array(
                'ALLEN PARK, MI', 'ANN ARBOR, MI', 'BELLEVILLE, MI', 'BROWNSTOWN, MI',
                'CANTON, MI', 'DEARBORN, MI', 'DEARBORN HEIGHTS, MI', 'FARMINGTON HILLS, MI',
                'FLAT ROCK, MI', 'LIVONIA, MI', 'MILAN, MI', 'NOVI, MI', 'PLYMOUTH, MI',
                'ROMULUS, MI', 'SOUTHGATE, MI', 'SALINE, MI', 'TAYLOR, MI', 'WAYNE, MI',
                'WESTLAND, MI', 'YPSILANTI, MI'
            ),

            'payment_options' => array(
                'Cash', 'MasterCard', 'Visa', 'American Express', 'PayPal', 'JCB', 'Discover'
            ),

            'services' => array(
                'general_summary' => 'We offer a wide range of plumbing services including repairs, installations, and emergency services for both residential and commercial properties. This includes work on water heaters, drains, sewers, and general service and repairs.',
                'residential' => array(
                    'Back-flow testing, maintenance, and repairs',
                    'Dishwasher repairs and replacement',
                    'Faucet and sink repair and replacement',
                    'Leak location and repair',
                    'Major and minor kitchen and bathroom remodels',
                    'Natural and propane gas line repairs',
                    'Sink garbage disposals',
                    'Toilet repair and replacement',
                    'Drain cleaning & hydro-jetting',
                    'Water heaters (tank-less and traditional)',
                    'Water softeners'
                ),
                'commercial' => array(
                    'Water Heaters (traditional and tankless)',
                    'Water Softeners',
                    'Disposals, Dishwashers, and Water Fountains Repair and Replacement',
                    'Urinal and Toilet Repair and Replacement',
                    'Leak Location and Repair',
                    'Faucet Repair and Replacement',
                    'Major and Minor Kitchen and Bath Remodels',
                    'Back flow Testing, maintenance, and repairs',
                    'Hydrojetting & drain-cleaning',
                    'Camera inspection',
                    'Maintenance'
                ),
                'additional_from_homepage' => array(
                    'Customer Service Warranties',
                    'Water Line Repair and Replacement',
                    'Water Supply - Service Drainage',
                    'Toilets, Urinals, and Flush Valves',
                    'Water Tank installation',
                    'Frozen Pipe Repair',
                    'Clogged Drains',
                    'Complete Re-Pipe Services',
                    'Main-line Service Repair',
                    'Many More Services Upgrades'
                )
            ),

            'why_choose_us' => array(
                'Locally Owned & Operated.',
                'Available 24/7 for emergencies.',
                'Full Service Plumbers for residential and commercial needs.',
                'Unparalleled Expertise and Rapid Response.',
                'Fully Licensed, Bonded & Insured.',
                'Excellent Customer Service is a priority.',
                'Our plumbers are trustworthy, educated, and licensed.',
                'We provide efficient and ethical assessments.',
                'You\'ll receive detailed explanations of options.',
                'We use the latest plumbing technologies while maintaining family values.',
                'We provide honest recommendations and ensure work is done right the first time.'
            ),

            'faqs' => array(
                array(
                    'q' => 'Can leaks get bigger over time?',
                    'a' => 'Yes, definitely. Water leaking from pipes or fixtures can cause corrosion. Even a tiny pinhole leak can grow larger and potentially cause significant damage to your home if not addressed.'
                ),
                array(
                    'q' => 'What should I do if my toilet leaks at the base?',
                    'a' => 'A toilet leaking at the base often means the wax ring needs replacement or the t-bolts holding it down are loose. While the website mentions tightening the bolts and replacing the ring, this can be tricky. We recommend calling us at ' . '(734) 961-5698' . ' for professional help to ensure it\'s fixed correctly and avoid potential water damage.'
                ),
                array(
                    'q' => 'How do I fix a clogged garbage disposal?',
                    'a' => 'If your garbage disposal is jammed, first make sure it\'s turned off and disconnected from power. The website suggests using a hex wrench to manually dislodge obstructions. However, for safety and to ensure it\'s properly fixed, we advise calling us. We can quickly and safely resolve the issue.'
                ),
                array(
                    'q' => 'What are the benefits of a new water heater?',
                    'a' => 'A new water heater, especially a modern tankless model, can offer several benefits like saving money on energy bills, saving space, and providing continuous hot water. We have over 25 years of experience with installing and repairing both tank and tankless water heaters and can help you choose the best option for your needs.'
                ),
                 array(
                    'q' => 'What areas do you service?',
                    'a' => 'We service the following areas in Michigan: ' . implode(', ', $this->data['service_areas']) . '. If you\'re nearby but don\'t see your city, give us a call at ' . $this->data['phone'] . ' to check!'
                ),
                array(
                    'q' => 'What payment options do you accept?',
                    'a' => 'We accept Cash, MasterCard, Visa, American Express, PayPal, JCB, and Discover.'
                )
            ),
            'specials' => 'We often have specials, especially for new customers! Please ask about our current offers when you call us or when we schedule your appointment. You can reach us at ' . '(734) 961-5698' . '.',
            'contact_prompt' => 'For any specific questions or to schedule a service, please call us at ' . '(734) 961-5698' . ' or you can request an appointment through this chat.',
            'default_reply' => "I can help with questions about our services, service areas, and help you schedule an appointment. How can I assist you today? You can also call us directly at " . '(734) 961-5698' . "."
        );
    }

    public function get_info( $key ) {
        return isset( $this->data[$key] ) ? $this->data[$key] : null;
    }

    public function get_random_greeting() {
        $greetings = [
            "Hello! How can Fortified Plumbing assist you today?",
            "Hi there! What can I help you with regarding your plumbing needs?",
            "Welcome to Fortified Plumbing chat! How can I help?",
        ];
        return $greetings[array_rand($greetings)];
    }

    public function get_service_areas_text() {
        return "We service the following areas in Michigan: " . implode(', ', $this->data['service_areas']) . ". If you're nearby but don't see your city, give us a call at " . $this->data['phone'] . " to check!";
    }

    public function get_payment_options_text() {
        return "We accept the following payment methods: " . implode(', ', $this->data['payment_options']) . ".";
    }

    public function get_services_summary() {
        return $this->data['services']['general_summary'];
    }

    public function get_all_services_list() {
        $all_services = array_merge(
            $this->data['services']['residential'],
            $this->data['services']['commercial'],
            $this->data['services']['additional_from_homepage']
        );
        // Remove duplicates that might arise from merging
        return array_unique($all_services);
    }

    public function search_faq( $query_term ) {
        $found_faqs = [];
        foreach ( $this->data['faqs'] as $faq ) {
            if ( stripos( $faq['q'], $query_term ) !== false || stripos( $faq['a'], $query_term ) !== false ) {
                $found_faqs[] = $faq['a']; // Return the answer
            }
        }
        if (!empty($found_faqs)) {
            return implode("\n\n", $found_faqs);
        }
        return null;
    }

    public function get_contact_details() {
        return "You can reach Fortified Plumbing & Drain LLC at:\nPhone: {$this->data['phone']}\nEmail: {$this->data['email']}\nAddress: {$this->data['address']}\nWe also offer 24/7 Emergency Service.";
    }
}
?>

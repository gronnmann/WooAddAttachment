<?php
/**
 * Admin functionality
 *
 * @package WooAddAttachment
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class WooAddAttachment_Admin {
    public function init() {
        // Add admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Register settings
        add_action('admin_init', array($this, 'register_settings'));

        // Add scripts and styles
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_submenu_page(
            'woocommerce',
            'Email Attachments',
            'Email Attachments',
            'manage_woocommerce',
            'wc-email-attachments',
            array($this, 'render_settings_page')
        );
    }

    public function register_settings() {
        register_setting(
            'waa_settings',
            'waa_attachments',
            array(
                'type' => 'array',
                'sanitize_callback' => array($this, 'sanitize_attachments'),
                'default' => array(),
            )
        );
    }

    /**
     * Sanitize attachments
     *
     * @param array $input The input array
     * @return array Sanitized array
     */
    public function sanitize_attachments($input) {
        if (!is_array($input)) {
            return array();
        }
        
        $sanitized = array();
        foreach ($input as $attachment_id) {
            $sanitized[] = absint($attachment_id);
        }
        
        return array_filter($sanitized);
    }

    /**
     * Enqueue admin scripts and styles
     *
     * @param string $hook Current admin page
     */
    public function enqueue_scripts($hook) {
        if ('woocommerce_page_wc-email-attachments' !== $hook) {
            return;
        }

        // Enqueue WordPress media scripts
        wp_enqueue_media();

        // Enqueue custom JS
        wp_enqueue_script(
            'waa-admin',
            WAA_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            WAA_VERSION,
            true
        );

        // Enqueue custom CSS
        wp_enqueue_style(
            'waa-admin',
            WAA_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            WAA_VERSION
        );

        // Localize script
        wp_localize_script('waa-admin', 'waaData', array(
            'title' => 'Select Attachment',
            'button' => 'Add to Emails',
            'nonce' => wp_create_nonce('waa_admin_nonce'),
        ));
    }

    /**
     * Render settings page
     */
    public function render_settings_page() {
        $attachments = get_option('waa_attachments', array());
        ?>
        <div class="wrap waa-settings">
            <h1>WooCommerce Email Attachments</h1>
            
            <p>Configure attachments to include in WooCommerce order confirmation emails.</p>
            
            <form method="post" action="options.php">
                <?php settings_fields('waa_settings'); ?>
                
                <div class="waa-attachments-section">
                    <h2>Email Attachments</h2>
                    
                    <div class="waa-attachments-list">
                        <div id="waa-attachments-container">
                            <?php
                            if (!empty($attachments)) {
                                foreach ($attachments as $attachment_id) {
                                    $attachment_title = get_the_title($attachment_id);
                                    $attachment_url = wp_get_attachment_url($attachment_id);
                                    ?>
                                    <div class="waa-attachment-item" data-id="<?php echo esc_attr($attachment_id); ?>">
                                        <input type="hidden" name="waa_attachments[]" value="<?php echo esc_attr($attachment_id); ?>">
                                        <span class="waa-attachment-title"><?php echo esc_html($attachment_title); ?></span>
                                        <a href="<?php echo esc_url($attachment_url); ?>" target="_blank" class="waa-view-link button button-secondary">View</a>
                                        <button type="button" class="waa-remove-button button button-secondary">Remove</button>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                        
                        <div class="waa-no-attachments" <?php echo !empty($attachments) ? 'style="display:none;"' : ''; ?>>
                            No attachments have been added yet.
                        </div>
                        
                        <p>
                            <button type="button" class="button button-primary" id="waa-add-attachment">Add Attachment</button>
                        </p>
                    </div>
                </div>
                
                <div class="waa-email-types">
                    <h2>Email Types</h2>
                    <p>These attachments will be included in the following email type:</p>
                    <ul>
                        <li>Order Completed emails (Order Confirmation)</li>
                    </ul>
                </div>
                
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
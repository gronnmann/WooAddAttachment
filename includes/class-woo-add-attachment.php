<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class WooAddAttachment {
    /**
     * @var WooAddAttachment
     */
    private static $instance = null;

    /**
     * @var WooAddAttachment_Admin
     */
    public $admin;

    public function init() {
        if (is_admin()) {
            require_once WAA_PLUGIN_DIR . 'admin/class-woo-add-attachment-admin.php';
            $this->admin = new WooAddAttachment_Admin();
            $this->admin->init();
        }

        add_filter('woocommerce_email_attachments', array($this, 'add_email_attachments'), 10, 3);
    }

    /**
     * Add attachments to WooCommerce emails
     *
     * @param array $attachments Existing attachments
     * @param string $email_id Email ID
     * @param object $order Order object
     * @return array Modified attachments
     */
    public function add_email_attachments($attachments, $email_id, $order) {
        // Only order completed email
        if ($email_id !== 'customer_completed_order') {
            return $attachments;
        }

        // Get configured attachments
        $configured_attachments = get_option('waa_attachments', array());
        
        if (empty($configured_attachments)) {
            return $attachments;
        }

        // Add each configured attachment
        foreach ($configured_attachments as $attachment_id) {
            $file_path = get_attached_file($attachment_id);
            if ($file_path && file_exists($file_path)) {
                $attachments[] = $file_path;
            }
        }

        return $attachments;
    }

    /**
     * Get plugin instance
     *
     * @return WooAddAttachment
     */
    public static function get_instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
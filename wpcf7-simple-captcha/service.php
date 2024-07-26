<?php

if (!class_exists('WPCF7_Service')) {
    return;
}

class WPCF7_SIMPLE_CAPTCHA extends WPCF7_Service {

    private static $instance;
    private $options;


    public static function get_instance() {
        if (empty(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }


    private function __construct() {
        $this->options = get_option('wpcf7_simple_captcha_options');

        if (empty($this->options) || !is_array($this->options)) {
            $this->init_options();
        }
    }


    public function get_captcha_fields() {
        return $this->option('captcha_fields', []);
    }


    public function get_nonce_field() {
        return $this->option('nonce_field');
    }


    public function generate_hidden_fields($formId = 0) {
        $fields = [
            '_wp_http_referer' => esc_url(remove_query_arg('_wp_http_referer')),
        ];

        $nonceField = $this->get_nonce_field();
        if (!empty($nonceField)) {
            $fields[esc_attr($nonceField)] = wp_create_nonce('wpcf7_sc_' . $formId);
        }

        return $fields;
    }


    public function generate_human_hidden_fields() {
        $html = '';

        $captchaFields = $this->get_captcha_fields();
        foreach ($captchaFields as $captchaField) {
            $field = esc_attr($captchaField);
            $html .= '<input type="text" id="' . $field . '" name="' . $field . '" value="" style="display:none" autocomplete="off" />';
        }

        return $html;
    }


    public function verify($nonce, $captcha, $formId) {
        if (!$this->is_active()) {
            return true;
        }

        // This isnt human
        if (empty($nonce) || !empty($captcha)) {
            return false;
        }

        // Check to see the nonce is valid
        return wp_verify_nonce($nonce, 'wpcf7_sc_' . $formId);
    }


    public function get_title() {
        return __('Simple CAPTCHA', 'contact-form-7');
    }


    public function is_active() {
        return $this->option('is_enabled');
    }


    public function get_categories() {
        return array('spam_protection');
    }


    public function icon() {
    }


    public function link() {
        echo wpcf7_link(
            'https://github.com/GLOKON/wpcf7-simple-captcha',
            'github.com/GLOKON/wpcf7-simple-captcha'
       );
    }


    public function load($action = '') {
        if ('setup' == $action and 'POST' == $_SERVER['REQUEST_METHOD']) {
            check_admin_referer('wpcf7-simple-captcha-setup');

            if (!empty($_POST['reset'])) {
                $this->reset_data();
                $redirect_to = $this->menu_page_url('action=setup');
            } else {
                $isEnabled = trim($_POST['is_enabled'] ?? 'false') == 'true';
                $captchaFields = trim($_POST['captcha_fields'] ?? '');
                $nonceField = trim($_POST['nonce_field'] ?? '');

                if (!empty($nonceField)) {
                    $this->options['is_enabled'] = $isEnabled;
                    $this->options['captcha_fields'] = [];
                    if (!empty($captchaFields)) {
                        $this->options['captcha_fields'] = array_filter(array_map('trim', explode(',', $captchaFields)));
                    }

                    $this->options['nonce_field'] = $nonceField;
                    $this->save_data();

                    $redirect_to = $this->menu_page_url(array(
                        'message' => 'success',
                    ));
                } else {
                    $redirect_to = $this->menu_page_url(array(
                        'action' => 'setup',
                        'message' => 'invalid',
                    ));
                }
            }

            wp_safe_redirect($redirect_to);
            exit();
        }
    }


    public function display($action = '') {
        echo sprintf(
            '<p>%s</p>',
            esc_html(__("Simple CAPTCHA protects you against automated spam. With Contact Form 7&#8217;s Simple CAPTCHA, you can prevent bots from sending spam, without sending any additional data to external services.", 'contact-form-7'))
       );

        echo sprintf(
            '<p><strong>%s</strong></p>',
            wpcf7_link(
                __('https://github.com/GLOKON/wpcf7-simple-captcha', 'contact-form-7'),
                __('Simple CAPTCHA', 'contact-form-7')
           )
       );

        if ($this->is_active()) {
            echo sprintf(
                '<p class="dashicons-before dashicons-yes">%s</p>',
                esc_html(__("Simple CAPTCHA is active on this site.", 'contact-form-7'))
           );
        }

        if ('setup' == $action) {
            $this->display_setup();
        } else {
            echo sprintf(
                '<p><a href="%1$s" class="button">%2$s</a></p>',
                esc_url($this->menu_page_url('action=setup')),
                esc_html(__('Setup Integration', 'contact-form-7'))
           );
        }
    }


    public function admin_notice($message = '') {
        if ('invalid' == $message) {
            echo sprintf(
                '<div class="notice notice-error"><p><strong>%1$s</strong>: %2$s</p></div>',
                esc_html(__("Error", 'contact-form-7')),
                esc_html(__("The `Nonce Field` is required.", 'contact-form-7')));
        }

        if ('success' == $message) {
            echo sprintf('<div class="notice notice-success"><p>%s</p></div>',
                esc_html(__('Settings saved.', 'contact-form-7')));
        }
    }


    private function init_options() {
        $this->options = [
            'is_enabled' => false,
            'captcha_fields' => ['captcha', 'recaptcha'],
            'nonce_field' => 'sc_nonce',
        ];
        $this->save_data();
    }


    private function option($option, $default = false) {
        if (!array_key_exists($option, $this->options)) {
            return $default;
        }

        $option = $this->options[$option];

        return (empty($option) ? $default : $option);
    }


    private function menu_page_url($args = '') {
        $args = wp_parse_args($args, []);

        $url = menu_page_url('wpcf7-integration', false);
        $url = add_query_arg(['service' => 'simple-captcha'], $url);

        if (!empty($args)) {
            $url = add_query_arg($args, $url);
        }

        return $url;
    }


    private function save_data() {
        update_option('wpcf7_simple_captcha_options', $this->options);
    }


    private function reset_data() {
        $this->options = [];
        $this->save_data();
    }


    private function display_setup() {
        ?>
        <form method="post" action="<?php echo esc_url($this->menu_page_url('action=setup')); ?>">
            <?php wp_nonce_field('wpcf7-simple-captcha-setup'); ?>
            <table class="form-table">
                <tbody>
                <tr>
                    <th scope="row"><label for="is_enabled"><?php echo esc_html(__('Use Integration?', 'contact-form-7')); ?></label></th>
                    <td><select id="is_enabled" name="is_enabled" class="regular-text code"><?php
                        echo sprintf('<option value="true"%s>Yes</option>',
                            esc_attr($this->option('is_enabled') ? ' selected="selected"' : '')
                        );
                        echo sprintf('<option value="false"%s>No</option>',
                            esc_attr(!$this->option('is_enabled') ? ' selected="selected"' : '')
                        );
                        ?></select></td>
                </tr>
                <tr>
                    <th scope="row"><label for="captcha_fields"><?php echo esc_html(__('CAPTCHA Field(s) (Separate multiple fields by a `,`)', 'contact-form-7')); ?></label></th>
                    <td><?php
                        echo sprintf(
                            '<input type="text" aria-required="false" value="%1$s" id="captcha_fields" name="captcha_fields" class="regular-text code" />',
                            esc_attr(implode(', ', $this->option('captcha_fields', [])))
                        );
                        ?></td>
                </tr>
                <tr>
                    <th scope="row"><label for="nonce_field"><?php echo esc_html(__('Nonce Field', 'contact-form-7')); ?></label></th>
                    <td><?php
                        echo sprintf(
                            '<input type="text" aria-required="false" value="%1$s" id="nonce_field" name="nonce_field" class="regular-text code" />',
                            esc_attr($this->option('nonce_field'))
                        );
                        ?></td>
                </tr>
                </tbody>
            </table>
            <?php
            submit_button(__('Save Changes', 'contact-form-7'));
            ?>
        </form>
        <?php
    }
}

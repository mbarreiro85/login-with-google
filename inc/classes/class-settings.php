<?php
/**
 * Settings class.
 *
 * @package wp-google-login
 */

namespace WP_Google_Login\Inc;

use WP_Google_Login\Inc\Traits\Singleton;

/**
 * Class Settings
 */
class Settings {

	use Singleton;

	/**
	 * Plugin constructor.
	 */
	protected function __construct() {
		$this->setup_hooks();
	}

	/**
	 * To setup actions/filters.
	 *
	 * @return void
	 */
	public function setup_hooks() {

		/**
		 * Actions
		 */
		add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
		add_action( 'admin_init', [ $this, 'settings_init' ] );
	}

	/**
	 * Add admin menu.
	 *
	 * @return void
	 */
	public function add_admin_menu() {
		if ( current_user_can( 'manage_options' ) ) {
			add_filter( 'plugin_action_links', [ $this, 'plugin_action_links' ], 10, 2 );
		}
		add_options_page( 'WP Google Login', 'WP Google Login', 'manage_options', 'wp-google-login', [ $this, 'options_page' ] );
	}

	/**
	 * Adds a "Settings" link to this plugin's entry on the plugin list.
	 *
	 * @param array  $links Array of links for plugin actions.
	 * @param string $file Path to the plugin file relative to the plugins directory.
	 *
	 * @return array $links  Array of links for plugin actions.
	 */
	public function plugin_action_links( $links, $file ) {
		if ( 'wp-google-login/wp-google-login.php' === $file ) {
			$links[] = "<a href='options-general.php?page=wp-google-login'>" . __( 'Settings' ) . '</a>';
		}

		return $links;
	}

	/**
	 * Add admin menu.
	 *
	 * @return void
	 */
	public function settings_init() {
		register_setting( 'wp_google_login', 'wp_google_login_settings' );

		add_settings_section(
			'wp_google_login_section',
			__( 'WP Google Login Settings', 'wp-google-login' ),
			[ $this, 'settings_section_callback' ],
			'wp_google_login'
		);

		add_settings_field(
			'wp_google_login_client_id',
			__( 'Client ID', 'wp-google-login' ),
			[ $this, 'wp_google_login_client_id_render' ],
			'wp_google_login',
			'wp_google_login_section',
			[ 'label_for' => 'client-id' ]
		);

		add_settings_field(
			'wp_google_login_client_secret',
			__( 'Client Secret', 'wp-google-login' ),
			[ $this, 'wp_google_login_client_secret_render' ],
			'wp_google_login',
			'wp_google_login_section',
			[ 'label_for' => 'client-secret' ]
		);

		add_settings_field(
			'wp_google_login_whitelisted_domains',
			__( 'Whitelisted Domains', 'wp-google-login' ),
			[ $this, 'wp_google_login_whitelisted_domains_render' ],
			'wp_google_login',
			'wp_google_login_section',
			[ 'label_for' => 'whitelisted-domains' ]
		);

		add_settings_field(
			'wp_google_login_enable_registration',
			__( 'Enable Google Login Registration', 'wp-google-login' ),
			[ $this, 'wp_google_login_enable_registrationr' ],
			'wp_google_login',
			'wp_google_login_section',
			[ 'label_for' => 'enable-registration' ]
		);
	}

	/**
	 * Render Client ID settings field.
	 *
	 * @return void
	 */
	public function wp_google_login_client_id_render() {
		$client_id = wp_google_login_get_client_id();
		$disabled  = '';
		if ( defined( 'WP_GOOGLE_LOGIN_CLIENT_ID' ) ) {
			$disabled = 'disabled';
		}
		?>
		<input type='text' name='wp_google_login_settings[client_id]' <?php echo esc_attr( $disabled ); ?> value='<?php echo esc_attr( $client_id ); ?>' >
		<?php

	}

	/**
	 * Render Client Secret settings field.
	 *
	 * @return void
	 */
	public function wp_google_login_client_secret_render() {
		$client_secret = wp_google_login_get_client_secret();
		$disabled      = '';
		if ( defined( 'WP_GOOGLE_LOGIN_SECRET' ) ) {
			$disabled = 'disabled';
		}
		?>
		<input type='text' name='wp_google_login_settings[client_secret]' <?php echo esc_attr( $disabled ); ?> value='<?php echo esc_attr( $client_secret ); ?>'>
		<?php

	}

	/**
	 * Render Whitelisted Domains settings field.
	 *
	 * @return void
	 */
	public function wp_google_login_whitelisted_domains_render() {
		$whitelisted_domains = wp_google_login_get_whitelisted_domains();
		$disabled            = '';
		if ( defined( 'WP_GOOGLE_LOGIN_WHITELIST_DOMAINS' ) ) {
			$disabled = 'disabled';
		}
		?>
		<input type='text' name='wp_google_login_settings[whitelisted_domains]' <?php echo esc_attr( $disabled ); ?> value='<?php echo esc_attr( $whitelisted_domains ); ?>'>
        <p class="description"><?php esc_html_e( 'Optional, Seperate by Comma.', 'wp-google-login' ); ?></p>
		<?php

	}

	/**
	 * Render Google Login Registration settings field.
	 *
	 * @return void
	 */
	public function wp_google_login_enable_registrationr(  ) {
		$registration_enabled = wp_google_login_is_registration_enabled();
		$disabled             = '';
		if ( defined( 'WP_GOOGLE_LOGIN_USER_REGISTRATION' ) ) {
			$disabled = 'disabled';
		}
		?>
        <input type='hidden' name='wp_google_login_settings[registration_enabled]' value='0' <?php echo esc_attr( $disabled ); ?> >
        <input type='checkbox' name='wp_google_login_settings[registration_enabled]' <?php echo esc_attr( checked( $registration_enabled ) ); ?> <?php echo esc_attr( $disabled ); ?> value='1'>
        <?php esc_html_e( 'Enable Registration', 'wp-google-login' ); ?>
		<?php

	}

	/**
	 * Settings Section callback
	 *
	 * @return void
	 */
	public function settings_section_callback() {
		echo wp_kses_post( __( '<p>If you do not have Project and Credentials, you can create one from <a target="_blank" href="https://console.developers.google.com/apis/dashboard">here</a>.</p>', 'wp-google-login' ) );
	}

	/**
	 * Render option page.
	 *
	 * @return void
	 */
	public function options_page() {
		?>
		<div class="wrap">
			<form action='options.php' method='post'>
				<?php
				settings_fields( 'wp_google_login' );
				do_settings_sections( 'wp_google_login' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}
}

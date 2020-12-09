<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Quriobot {

	public function __construct()
	{

    }

    const VERSION = '2.2.7';

	public function init()
	{
		$this->init_admin();
    	$this->enqueue_script();
    	$this->enqueue_admin_styles();
	}

	public function init_admin() {
        $args = array(
            'type' => 'array',
        );
		register_setting( 'quriobot', 'quriobot_path', $args );
		register_setting( 'quriobot', 'quriobot_init', $args );
    	add_action( 'admin_menu', array( $this, 'create_nav_page' ) );
	}

	public function create_nav_page() {
		add_options_page(
		  esc_html__( 'Quriobot', 'quriobot' ),
		  esc_html__( 'Quriobot', 'quriobot' ),
		  'manage_options',
		  'quriobot_settings',
		  array($this,'admin_view')
		);
	}

	public static function admin_view()
	{
		require_once plugin_dir_path( __FILE__ ) . '/../admin/views/settings.php';
	}

	public static function quriobot_script()
	{
		$quriobot_path = get_option( 'quriobot_path' );
		$quriobot_init = get_option( 'quriobot_init' );
		$is_admin = is_admin();

		$quriobot_path = trim($quriobot_path);
		if (!$quriobot_path && !$quriobot_init) {
			return;
		}

		if ( $is_admin ) {
			return;
        }

        $prepareValue = function($item) {
            $item = trim($item);
            return [
                "use" => $item,
                "language" => strtolower(str_replace('_', '-', get_locale())),
            ];
        };
        $qbOptions = array_unique(array_map($prepareValue, explode(PHP_EOL, $quriobot_path)), SORT_REGULAR);
        $code = $quriobot_init ? $quriobot_init : 'window.qbOptions = window.qbOptions.concat('.json_encode($qbOptions).');';
        echo '
<script type="text/javascript">
    if (!Array.isArray(window.qbOptions)) {
        window.qbOptions = []
    }
    '.$code. '
</script>
<script type="text/javascript" src="https://botsrv.com/website/js/widget2.b328cdb6.js" integrity="sha384-QZcI3aREVddUqgL+Oq3gfz3kUhk3/ASvVBrFMeH8qx74fpsbS0PHazUpILUVn9ot" crossorigin="anonymous" defer></script>
';
    }

	private function enqueue_script() {
		add_action( 'wp_head', array($this, 'quriobot_script'), 100);
	}

    private function enqueue_admin_styles() {
        add_action( 'admin_enqueue_scripts', array($this, 'quriobot_admin_styles' ) );
    }

    public static function quriobot_admin_styles() {
        wp_register_style( 'quriobot_custom_admin_style', plugins_url( '../admin/static/quriobot-admin.css', __FILE__ ), array(), '20190701', 'all' );
        wp_enqueue_style( 'quriobot_custom_admin_style' );
    }

}

?>

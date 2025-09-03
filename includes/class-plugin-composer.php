<?php
/**
 * Plugin Composer class for structured plugin composition.
 *
 * @package WP-Autoplugin
 * @since 1.6.1
 * @version 1.6.1
 * @link https://wp-autoplugin.com
 * @license GPL-2.0+
 * @license https://www.gnu.org/licenses/gpl-2.0.html
 */

namespace WP_Autoplugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin Composer class.
 * 
 * Handles structured composition of WordPress plugins using predefined templates
 * and components that can be combined and modified.
 */
class Plugin_Composer {

	/**
	 * AI API in use.
	 *
	 * @var API
	 */
	private $ai_api;

	/**
	 * Available plugin components.
	 *
	 * @var array
	 */
	private $components;

	/**
	 * Constructor.
	 *
	 * @param API $ai_api The AI API in use.
	 */
	public function __construct( $ai_api ) {
		$this->ai_api = $ai_api;
		$this->initialize_components();
	}

	/**
	 * Initialize available plugin components.
	 *
	 * @return void
	 */
	private function initialize_components() {
		$this->components = [
			'admin_page' => [
				'name' => 'Admin Page',
				'description' => 'Creates an admin page in WordPress dashboard',
				'template' => 'admin_page',
				'dependencies' => []
			],
			'shortcode' => [
				'name' => 'Shortcode',
				'description' => 'Adds shortcode functionality',
				'template' => 'shortcode',
				'dependencies' => []
			],
			'widget' => [
				'name' => 'Widget',
				'description' => 'Creates a WordPress widget',
				'template' => 'widget',
				'dependencies' => []
			],
			'post_type' => [
				'name' => 'Custom Post Type',
				'description' => 'Registers a custom post type',
				'template' => 'post_type',
				'dependencies' => []
			],
			'meta_box' => [
				'name' => 'Meta Box',
				'description' => 'Adds meta box to post editor',
				'template' => 'meta_box',
				'dependencies' => []
			],
			'settings' => [
				'name' => 'Settings Page',
				'description' => 'Creates a settings page with options',
				'template' => 'settings',
				'dependencies' => []
			],
			'api_endpoint' => [
				'name' => 'REST API Endpoint',
				'description' => 'Creates custom REST API endpoints',
				'template' => 'api_endpoint',
				'dependencies' => []
			],
			'database' => [
				'name' => 'Database Table',
				'description' => 'Creates and manages custom database tables',
				'template' => 'database',
				'dependencies' => []
			]
		];
	}

	/**
	 * Analyze user input and suggest plugin composition.
	 *
	 * @param string $input The plugin description from user.
	 *
	 * @return string|WP_Error JSON response with suggested composition.
	 */
	public function analyze_and_compose( $input ) {
		$components_list = '';
		foreach ( $this->components as $key => $component ) {
			$components_list .= "- {$key}: {$component['name']} - {$component['description']}\n";
		}

		$prompt = <<<PROMPT
			Analyze the following WordPress plugin description and suggest the best composition using available components:

			Plugin Description:
			```
			$input
			```

			Available Components:
			$components_list

			Based on the description, suggest which components should be used and how they should be configured. Your response should be a valid JSON object with the following structure:

			{
				"plugin_name": "Suggested plugin name",
				"description": "Brief description of what the plugin will do",
				"suggested_components": [
					{
						"component": "component_key",
						"purpose": "Why this component is needed",
						"configuration": {
							"key": "value pairs specific to this component"
						}
					}
				],
				"composition_plan": "Detailed plan of how these components will work together",
				"user_flows": "Description of main user workflows with the plugin"
			}

			Guidelines:
			- Only suggest components that are actually needed for the described functionality
			- Provide specific configuration details for each component
			- Explain how the components will work together
			- Keep the composition as simple as possible while meeting requirements

			Return ONLY the JSON response without any explanation or markdown formatting.
			PROMPT;

		return $this->ai_api->send_prompt( $prompt, '', [ 'response_format' => [ 'type' => 'json_object' ] ] );
	}

	/**
	 * Generate plugin code using the composed structure.
	 *
	 * @param array $composition The plugin composition from analyze_and_compose.
	 *
	 * @return string|WP_Error
	 */
	public function generate_composed_plugin( $composition ) {
		$plugin_mode = get_option( 'wp_autoplugin_plugin_mode', 'simple' );
		
		if ( 'complex' === $plugin_mode ) {
			return $this->generate_complex_composed_plugin( $composition );
		} else {
			return $this->generate_simple_composed_plugin( $composition );
		}
	}

	/**
	 * Generate a simple single-file plugin using composition.
	 *
	 * @param array $composition The plugin composition.
	 *
	 * @return string|WP_Error
	 */
	private function generate_simple_composed_plugin( $composition ) {
		$components_details = '';
		if ( isset( $composition['suggested_components'] ) ) {
			foreach ( $composition['suggested_components'] as $component ) {
				$comp_key = $component['component'];
				if ( isset( $this->components[ $comp_key ] ) ) {
					$components_details .= "Component: {$this->components[ $comp_key ]['name']}\n";
					$components_details .= "Purpose: {$component['purpose']}\n";
					if ( isset( $component['configuration'] ) ) {
						$components_details .= "Configuration: " . wp_json_encode( $component['configuration'] ) . "\n";
					}
					$components_details .= "\n";
				}
			}
		}

		$composition_json = wp_json_encode( $composition, JSON_PRETTY_PRINT );

		$prompt = <<<PROMPT
			Generate a complete WordPress plugin based on the following composition:

			Composition Plan:
			```
			$composition_json
			```

			Component Details:
			$components_details

			Requirements:
			- Create a single PHP file containing all functionality
			- Follow WordPress coding standards
			- Use appropriate WordPress hooks and actions
			- Include inline CSS and JavaScript if needed
			- Use "WP-Autoplugin" as the plugin author with Author URI: https://wp-autoplugin.com
			- Do not add the final closing "?>" tag
			- Ensure all components work together seamlessly
			- Include proper error handling and security measures

			Return ONLY the complete PHP plugin code without any explanation or markdown formatting.
			PROMPT;

		return $this->ai_api->send_prompt( $prompt );
	}

	/**
	 * Generate a complex multi-file plugin using composition.
	 *
	 * @param array $composition The plugin composition.
	 *
	 * @return string|WP_Error JSON response with project structure.
	 */
	private function generate_complex_composed_plugin( $composition ) {
		$components_details = '';
		if ( isset( $composition['suggested_components'] ) ) {
			foreach ( $composition['suggested_components'] as $component ) {
				$comp_key = $component['component'];
				if ( isset( $this->components[ $comp_key ] ) ) {
					$components_details .= "Component: {$this->components[ $comp_key ]['name']}\n";
					$components_details .= "Purpose: {$component['purpose']}\n";
					if ( isset( $component['configuration'] ) ) {
						$components_details .= "Configuration: " . wp_json_encode( $component['configuration'] ) . "\n";
					}
					$components_details .= "\n";
				}
			}
		}

		$composition_json = wp_json_encode( $composition, JSON_PRETTY_PRINT );

		$prompt = <<<PROMPT
			Generate a detailed project structure for a WordPress plugin based on the following composition:

			Composition Plan:
			```
			$composition_json
			```

			Component Details:
			$components_details

			Create a project structure that properly organizes the components into separate files and directories. Your response should be a valid JSON object with the following structure:

			{
				"plugin_name": "Plugin name from composition",
				"design_and_architecture": "Overall architecture description",
				"detailed_feature_description": "Detailed feature descriptions based on components",
				"user_interface": "UI description based on selected components",
				"user_flows": "User workflow descriptions",
				"security_considerations": "Security measures for the components",
				"testing_plan": "How to test the composed plugin",
				"project_structure": {
					"directories": ["array", "of", "needed", "directories"],
					"files": [
						{
							"path": "file/path.php",
							"type": "php|css|js",
							"description": "File purpose and component it implements",
							"component": "component_key"
						}
					]
				}
			}

			Guidelines:
			- Organize files logically based on component types
			- Use separate files for different components when beneficial
			- Include proper directory structure for assets, admin, includes
			- Only create files that are necessary for the selected components
			- Ensure the structure supports the component dependencies

			Return ONLY the JSON response without any explanation or markdown formatting.
			PROMPT;

		return $this->ai_api->send_prompt( $prompt, '', [ 'response_format' => [ 'type' => 'json_object' ] ] );
	}

	/**
	 * Modify an existing plugin by adding or removing components.
	 *
	 * @param string $plugin_code The existing plugin code.
	 * @param array  $modifications The modifications to apply.
	 *
	 * @return string|WP_Error
	 */
	public function modify_plugin( $plugin_code, $modifications ) {
		$modifications_json = wp_json_encode( $modifications, JSON_PRETTY_PRINT );

		$prompt = <<<PROMPT
			Modify the following WordPress plugin code based on the requested changes:

			Current Plugin Code:
			```
			$plugin_code
			```

			Requested Modifications:
			```
			$modifications_json
			```

			Apply the modifications while:
			- Maintaining existing functionality that isn't being changed
			- Following WordPress coding standards
			- Ensuring all components work together
			- Preserving security measures
			- Keeping the code clean and well-organized

			Return ONLY the modified plugin code without any explanation or markdown formatting.
			PROMPT;

		return $this->ai_api->send_prompt( $prompt );
	}

	/**
	 * Get available components for frontend display.
	 *
	 * @return array
	 */
	public function get_components() {
		return $this->components;
	}

	/**
	 * Validate a component configuration.
	 *
	 * @param string $component_key The component key.
	 * @param array  $configuration The configuration to validate.
	 *
	 * @return bool|WP_Error
	 */
	public function validate_component( $component_key, $configuration ) {
		if ( ! isset( $this->components[ $component_key ] ) ) {
			return new \WP_Error( 'invalid_component', 'Component does not exist: ' . $component_key );
		}

		// Basic validation - can be extended based on component requirements
		if ( ! is_array( $configuration ) ) {
			return new \WP_Error( 'invalid_configuration', 'Configuration must be an array' );
		}

		return true;
	}
}
<?php
/**
 * Admin view for the Generate Autoplugin page.
 *
 * @package WP-Autoplugin
 * @since 1.0.0
 * @version 1.0.5
 * @link https://wp-autoplugin.com
 * @license GPL-2.0+
 * @license https://www.gnu.org/licenses/gpl-2.0.html
 */

namespace WP_Autoplugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wp-autoplugin-admin-container">
	<div class="wrap wp-autoplugin step-1-generation">
		<h1><?php esc_html_e( 'Generate Plugin', 'wp-autoplugin' ); ?></h1>
		<?php if ( get_option( 'wp_autoplugin_use_composer', false ) ) : ?>
			<div class="composer-info">
				<p><?php esc_html_e( 'Plugin Composer is enabled. Your plugin will be analyzed and composed using structured components for better organization.', 'wp-autoplugin' ); ?></p>
			</div>
		<?php endif; ?>
		<form method="post" action="" id="generate-plan-form">
			<?php wp_nonce_field( 'generate_plan', 'generate_plan_nonce' ); ?>
			<p><?php esc_html_e( 'Enter a description of the plugin you want to generate:', 'wp-autoplugin' ); ?></p>
			<input name="plugin_description" id="plugin_description" type="text" size="100" />
			<?php submit_button( __( 'Generate Plan', 'wp-autoplugin' ), 'primary button-hero', 'generate_plan', false ); ?>
		</form>
		<div id="generate-plan-message" class="autoplugin-message"></div>
	</div>
	<div class="wrap wp-autoplugin step-2-plan" style="display: none;">
		<h1><?php esc_html_e( 'Generated Plan', 'wp-autoplugin' ); ?></h1>
		<form method="post" action="" id="generate-code-form">
			<?php wp_nonce_field( 'generate_code', 'generate_code_nonce' ); ?>
			<p><?php esc_html_e( 'Review or edit the generated plan:', 'wp-autoplugin' ); ?></p>
			<!-- The plan contains the following parts: plugin_name, design_and_architecture, detailed_feature_description, user_interface, security_considerations, testing_plan -->
			<!-- A part can contain multiple lines of text or another nested part -->
			<!-- We will display it as an accordion with each part in a separate section -->
			
			<!-- Composer components display (when composer mode is enabled) -->
			<div id="composer_components_container" style="display: none;">
				<h3><?php esc_html_e( 'Suggested Components', 'wp-autoplugin' ); ?></h3>
				<div id="composer_components_list"></div>
				<div class="composer-modify-section">
					<button type="button" id="modify-components" class="button"><?php esc_html_e( 'Modify Components', 'wp-autoplugin' ); ?></button>
				</div>
			</div>
			
			<div id="plugin_plan_container"></div>
			<div class="autoplugin-actions">
				<button type="button" id="edit-description" class="button"><?php esc_html_e( '&laquo; Edit Description', 'wp-autoplugin' ); ?></button>
				<?php submit_button( __( 'Generate Plugin Code', 'wp-autoplugin' ), 'primary', 'generate_code', false ); ?>
			</div>
		</form>
		<div id="generate-code-message" class="autoplugin-message"></div>
	</div>
	<div class="wrap wp-autoplugin step-3-code" style="display: none;">
		<h1><?php esc_html_e( 'Generated Plugin Code', 'wp-autoplugin' ); ?></h1>
		<form method="post" action="" id="create-plugin-form">
			<?php wp_nonce_field( 'create_plugin', 'create_plugin_nonce' ); ?>
			
			<!-- Simple plugin mode -->
			<div id="simple-plugin-content">
				<p><?php esc_html_e( 'The plugin code has been generated successfully. You can review and edit the plugin file before activating it:', 'wp-autoplugin' ); ?></p>
				<textarea name="plugin_code" id="plugin_code" rows="20" cols="100"></textarea>
			</div>
			
			<!-- Complex plugin mode -->
			<div id="complex-plugin-content" style="display: none;">
				<p><?php esc_html_e( 'You can review and edit the generated code before installing:', 'wp-autoplugin' ); ?></p>
				
				<div class="generation-progress" style="display: none;">
					<div class="progress-bar-container">
						<div class="progress-bar" id="file-generation-progress"></div>
					</div>
					<span class="progress-text" id="progress-text"><?php esc_html_e( 'Generating files...', 'wp-autoplugin' ); ?></span>
				</div>

				<div class="code-review-section" id="code-review-section" style="display: none;">
					<div class="review-progress">
						<div class="progress-text" id="review-progress-text"><?php esc_html_e( 'AI is reviewing the complete codebase...', 'wp-autoplugin' ); ?></div>
					</div>
					
					<div class="review-results" id="review-results" style="display: none;">
						<h4><?php esc_html_e( 'Code Review Results', 'wp-autoplugin' ); ?></h4>
						<div class="review-summary" id="review-summary"></div>
						
						<div class="review-suggestions" id="review-suggestions" style="display: none;">
							<h5><?php esc_html_e( 'Suggested Improvements', 'wp-autoplugin' ); ?></h5>
							<div class="suggestions-list" id="suggestions-list"></div>
							
							<div class="review-actions" style="margin-top: 15px;">
								<button type="button" id="apply-suggestions" class="button button-primary">
									<?php esc_html_e( 'Apply Suggestions', 'wp-autoplugin' ); ?>
								</button>
								<button type="button" id="skip-review" class="button">
									<?php esc_html_e( 'Skip Review', 'wp-autoplugin' ); ?>
								</button>
							</div>
						</div>
					</div>
				</div>
				
				<div class="generated-files-container">
					<div class="files-tabs" id="files-tabs">
						<!-- File tabs will be populated by JavaScript -->
					</div>
					
					<div class="file-content" id="file-content">
						<!-- File editors will be populated by JavaScript -->
					</div>
				</div>
			</div>
			
			<div class="autoplugin-code-warning">
				<strong><?php esc_html_e( 'Warning:', 'wp-autoplugin' ); ?></strong> <?php esc_html_e( 'AI-generated code may be unstable or insecure; use only after careful review and testing.', 'wp-autoplugin' ); ?>
			</div>

			<div class="autoplugin-actions">
				<button type="button" id="edit-plan" class="button"><?php esc_html_e( '&laquo; Edit Plan', 'wp-autoplugin' ); ?></button>
				<?php submit_button( __( 'Install Plugin', 'wp-autoplugin' ), 'primary', 'create_plugin', false ); ?>
			</div>
		</form>
		<div id="create-plugin-message" class="autoplugin-message"></div>
	</div>
<?php $this->admin->output_admin_footer(); ?>
</div>

<style>
/* Plugin Composer Styles */
.composer-info {
	background: #e7f3ff;
	border: 1px solid #72aee6;
	border-radius: 4px;
	padding: 12px 16px;
	margin-bottom: 20px;
}

.composer-info p {
	margin: 0;
	color: #1d2327;
}

#composer_components_container {
	background: #fff;
	border: 1px solid #ccd0d4;
	border-radius: 4px;
	padding: 20px;
	margin-bottom: 20px;
}

#composer_components_container h3 {
	margin-top: 0;
	margin-bottom: 15px;
	color: #1d2327;
}

#composer_components_list {
	margin-bottom: 15px;
}

.composer-component {
	background: #f9f9f9;
	border: 1px solid #ddd;
	border-radius: 4px;
	padding: 12px 15px;
	margin-bottom: 10px;
	position: relative;
}

.composer-component h4 {
	margin: 0 0 8px 0;
	color: #2271b1;
	font-size: 14px;
}

.composer-component p {
	margin: 0 0 8px 0;
	color: #50575e;
	font-size: 13px;
}

.composer-component .component-purpose {
	font-style: italic;
	color: #646970;
}

.composer-component .component-config {
	background: #fff;
	border: 1px solid #ddd;
	border-radius: 3px;
	padding: 8px;
	margin-top: 8px;
	font-family: monospace;
	font-size: 12px;
	color: #1d2327;
}

.composer-modify-section {
	border-top: 1px solid #ddd;
	padding-top: 15px;
	text-align: center;
}

.composer-component-selector {
	background: #fff;
	border: 1px solid #ccd0d4;
	border-radius: 4px;
	padding: 15px;
	margin-bottom: 15px;
}

.component-option {
	display: flex;
	align-items: center;
	margin-bottom: 10px;
	padding: 8px;
	border-radius: 3px;
	transition: background-color 0.2s;
}

.component-option:hover {
	background-color: #f6f7f7;
}

.component-option input[type="checkbox"] {
	margin-right: 10px;
}

.component-option .component-info {
	flex-grow: 1;
}

.component-option .component-name {
	font-weight: 600;
	color: #1d2327;
	margin-bottom: 4px;
}

.component-option .component-description {
	color: #50575e;
	font-size: 13px;
	margin: 0;
}

.component-configuration {
	margin-left: 30px;
	margin-top: 10px;
	padding: 10px;
	background: #f9f9f9;
	border-radius: 3px;
	display: none;
}

.component-configuration.active {
	display: block;
}

.component-configuration label {
	display: block;
	margin-bottom: 8px;
	font-weight: 500;
	color: #1d2327;
}

.component-configuration input,
.component-configuration textarea {
	width: 100%;
	margin-bottom: 10px;
	padding: 6px 8px;
	border: 1px solid #ddd;
	border-radius: 3px;
}

.component-configuration textarea {
	height: 60px;
	resize: vertical;
}

/* Composer Modal Styles */
.composer-modal {
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background: rgba(0, 0, 0, 0.5);
	z-index: 10000;
	display: flex;
	align-items: center;
	justify-content: center;
}

.composer-modal-content {
	background: #fff;
	border-radius: 4px;
	padding: 20px;
	max-width: 600px;
	width: 90%;
	max-height: 80vh;
	overflow-y: auto;
	box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.composer-modal h3 {
	margin-top: 0;
	margin-bottom: 20px;
	color: #1d2327;
}

.composer-modal-actions {
	border-top: 1px solid #ddd;
	padding-top: 15px;
	margin-top: 20px;
	text-align: right;
}

.composer-modal-actions .button {
	margin-left: 10px;
}
</style>
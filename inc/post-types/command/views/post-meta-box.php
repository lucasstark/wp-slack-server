<?php

$options = get_post_meta( $post->ID, 'slack_command_options', true );

if ( ! is_array( $options ) ) {
	$options = array(
		'command_token' => '',
		'command_name'  => '',
		'actions'       => null
	);
}

$choices = array(
	'no'  => 'No',
	'yes' => 'Yes'
);

?>


<table class="form-table wp-slack-server-control-table">
	<tbody>
	<tr valign="top">
		<td>
			<div class="wp-slack-server-control first">
				<div class="wp-slack-server-control-label">
					<label><?php _e( 'Command Name', 'slack' ); ?></label>
					<p class="description">
						<?php _e( 'The command name. Commands should be a single word (and can\'t be blank). Examples: /deploy, /ack', 'slack' ); ?>
					</p>
				</div>
				<div class="wp-slack-server-input">
					<input type="text" class="regular-text" name="slack_command_option[command_name]"
					       id="slack_command_option[command_name]"
					       value="<?php echo esc_attr( $options['command_name'] ); ?>">

				</div>
			</div>
		</td>
	</tr>
	<tr valign="top">
		<td>
			<div class="wp-slack-server-control">
				<div class="wp-slack-server-control-label">
					<label for="slack_command_option[command_token]"><?php _e( 'Command Token', 'slack' ); ?></label>
					<p class="description">
						<?php _e( 'Your command token. The token can be found at the configuration section of your Slash Command', 'slack' ); ?>
					</p>
				</div>
				<div class="wp-slack-server-input">
					<input type="text" class="regular-text" name="slack_command_option[command_token]"
					       id="slack_command_option[command_token]"
					       value="<?php echo esc_attr( $options['command_token'] ); ?>">
				</div>
			</div>
		</td>
	</tr>
	<tr valign="top">
		<td>
			<div class="wp-slack-server-control">
				<div class="wp-slack-server-control-label">
					<label><?php _e( 'Actions', 'slack' ); ?></label>
					<p class="description">
						<?php _e('Define a response filter function to call and assoicated strings / regular expressions to respond to.'); ?>
					</p>
				</div>
			</div>
		</td>
	</tr>
	<tr valign="top">
		<td>

		<div class="wp-slack-server-control-list">

			<div class="wp-slack-server-control-wrap">
				<div class="wp-slack-server-control-header">
					<ul>
						<li class="control-order"><strong>Order</strong></li>
						<li class="control-label"><strong>Response Filter Name</strong></li>
					</ul>
				</div>
			</div>

			<?php
			if ( ! empty( $options['actions'] ) ) : ?>
				<?php foreach ( $options['actions'] as $index => $action ) : ?>
					<div class="wp-slack-server-control-wrap">
						<div class="wp-slack-server-control-header">
							<ul>
								<li class="control-order"><?php echo $index + 1; ?></li>
								<li class="control-label"><a
										href="#"><?php echo esc_html( $action['response_function'] ); ?></a></li>
							</ul>
						</div>
						<div class="wp-slack-server-control-inputs">
							<div class="wp-slack-server-control">
								<div class="wp-slack-server-control-label">
									<label><?php _e( 'Response Filter Name', 'slack' ); ?></label>
									<p class="description"><?php _e( 'The name of the filter to call.  This is your custom function which will return messages for Slack', 'slack' ); ?></p>
								</div>
								<div class="wp-slack-server-input">
									<input type="text" class="widefat response-function"
									       name="slack_command_option[actions][response_function][]"
									       value="<?php echo esc_attr( $action['response_function'] ); ?>"/>
								</div>
							</div>
							<div class="wp-slack-server-control">
								<div class="wp-slack-server-control-label">
									<label><?php _e( 'Delayed Response', 'slack' ); ?></label>
									<p class="description"><?php _e( 'Choose yes if your response function will take longer than 3000 Milliseconds to complete.', 'slack' ); ?></p>
								</div>
								<div class="wp-slack-server-input">
									<select name="slack_command_option[actions][delayed_reponse][]">
										<?php foreach ( $choices as $value => $label ) : ?>
											<option <?php selected( $value == $action['delayed_response'] ); ?>
												value="<?php echo $value; ?>"><?php echo $label; ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
							<div class="wp-slack-server-control">
								<div class="wp-slack-server-control-label">
									<label><?php _e( 'Utterances', 'slack' ); ?></label>
									<p class="description"><?php _e( 'Enter the utterances that this command will respond to.  You can use PHP Regular expression syntax.    Example /show me jokes about (.*)/', 'slack' ); ?>
										<br/><br/><?php _e( 'Enter one per line.', 'slack' ); ?></p>
								</div>
								<div class="wp-slack-server-input">
										<textarea name="slack_command_option[actions][utterances][]" class="widefat"
										          rows="8"><?php echo esc_textarea( $action['utterances'] ); ?></textarea>
								</div>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>

			<div class="wp-slack-server-control-wrap empty-wrap">
				<div class="wp-slack-server-control-header">
					<ul>
						<li class="control-order">1</li>
						<li class="control-label"><a href="#"></a></li>
					</ul>
				</div>
				<div class="wp-slack-server-control-inputs">
					<div class="wp-slack-server-control">
						<div class="wp-slack-server-control-label">
							<label><?php _e( 'Response Filter Name', 'slack' ); ?></label>
							<p class="description"><?php _e( 'The name of the filter to call.  This is your custom function which will return messages for Slack', 'slack' ); ?></p>
						</div>
						<div class="wp-slack-server-input">
							<input type="text" class="widefat response-function"
							       name="slack_command_option[actions][response_function][]"/>
						</div>
					</div>
					<div class="wp-slack-server-control">
						<div class="wp-slack-server-control-label">
							<label><?php _e( 'Delayed Response', 'slack' ); ?></label>
							<p class="description"><?php _e( 'Choose yes if your response function will take longer than 3000 Milliseconds to complete.', 'slack' ); ?></p>
						</div>
						<div class="wp-slack-server-input">
							<select name="slack_command_option[actions][delayed_reponse][]">
								<?php foreach ( $choices as $value => $label ) : ?>
									<option value="<?php echo $value; ?>"><?php echo $label; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="wp-slack-server-control">
						<div class="wp-slack-server-control-label">
							<label><?php _e( 'Utterances', 'slack' ); ?></label>
							<p class="description"><?php _e( 'Enter the utterances that this command will respond to.  You can use PHP Regular expression syntax.    Example /show me jokes about (.*)/', 'slack' ); ?>
								<br/><br/><?php _e( 'Enter one per line.', 'slack' ); ?></p>
						</div>
						<div class="wp-slack-server-input">
									<textarea name="slack_command_option[actions][utterances][]" class="widefat"
									          rows="8"></textarea>
						</div>
					</div>
				</div>
			</div>

			<ul class="wp-slack-server-control-tfoot">
				<li>
					<a href="#" class="button button-primary button-large add-action">+ Add Action</a>
				</li>
			</ul>

		</div>

		</td>
	</tr>
	</tbody>
</table>
<script type="text/javascript">
	jQuery(document).ready(function ($) {
		$('.remove-row').on('click', function () {
			$(this).parents('tr').remove();
			return false;
		});
	});
</script>


<script type="text/javascript">
	(function ($) {

		$('.wp-slack-server-control-list').on('click', '.control-label', function (e) {
			e.preventDefault();

			var $this = $(this);
			var container = $this.closest('.wp-slack-server-control-wrap');
			var inputs = container.find('.wp-slack-server-control-inputs');

			if (container.hasClass('open')) {
				inputs.slideUp(function () {
					container.removeClass('open');
				});

			} else {
				inputs.slideDown();
				container.addClass('open');
			}

		}).on('keyup', '.response-function', function(){
				var container = $(this).closest('.wp-slack-server-control-wrap');
			    container.find('.wp-slack-server-control-header .control-label a').text($(this).val());
		});

		$('.add-action').on('click', function (e) {
			e.preventDefault();
			var count = $('.wp-slack-server-control-wrap').length - 1;

			var row = $('.empty-wrap.wp-slack-server-control-wrap').clone(true);
			var inputs = row.find('.wp-slack-server-control-inputs');
			row.find('.wp-slack-server-control-header .control-order').html('<strong>' + count + '</strong>');
			row.insertAfter('.wp-slack-server-control-list > div:last');
			row.removeClass('empty-wrap');

			inputs.slideDown();
			row.addClass('open');
			return false;
		});

	})(jQuery);

</script>

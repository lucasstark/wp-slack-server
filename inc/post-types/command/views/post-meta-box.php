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
				</div>
			</div>
		</td>
	</tr>
	<tr valign="top">
		<td>
			<table id="repeatable-fieldset-one" width="100%">
				<tbody>
				<?php
				if ( ! empty( $options['actions'] ) ) :
					foreach ( $options['actions'] as $index => $action ) :
						?>
						<tr>
							<td>
								<div class="wp-slack-server-control">
									<div class="wp-slack-server-control-label">
										<label><?php _e( 'Response Filter Name', 'slack' ); ?></label>
										<p class="description"><?php _e( 'The name of the filter to call.  This is your custom function which will return messages for Slack', 'slack' ); ?></p>
									</div>
									<div class="wp-slack-server-input">
										<input type="text" class="widefat"
										       name="slack_command_option[actions][response_function][]"
										       value="<?php echo esc_attr( $action['response_function']); ?>"/>
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
							</td>

							<td class="wp-slack-server-control-row-remove"><a class="button remove-row"
							                                                  href="#">Remove</a>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td>
							<div class="wp-slack-server-control">
								<div class="wp-slack-server-control-label">
									<label><?php _e( 'Response Filter Name', 'slack' ); ?></label>
									<p class="description"><?php _e( 'The name of the filter to call.  This is your custom function which will return messages for Slack', 'slack' ); ?></p>
								</div>
								<div class="wp-slack-server-input">
									<input type="text" class="widefat"
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
						</td>

						<td class="wp-slack-server-control-row-remove"><a class="button remove-row" href="#">Remove</a>
						</td>
					</tr>
				<?php endif; ?>

				<!-- empty hidden one for jQuery -->
				<tr class="empty-row screen-reader-text">
					<td>
						<div class="wp-slack-server-control">
							<div class="wp-slack-server-control-label">
								<label><?php _e( 'Response Filter Name', 'slack' ); ?></label>
								<p class="description"><?php _e( 'The name of the filter to call.  This is your custom function which will return messages for Slack', 'slack' ); ?></p>
							</div>
							<div class="wp-slack-server-input">
								<input type="text" class="widefat"
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
					</td>

					<td class="wp-slack-server-control-row-remove"><a class="button remove-row" href="#">Remove</a>
					</td>
				</tr>
				</tbody>
			</table>
			<p><a id="add-row" class="button" href="#">Add another</a></p>
		</td>
	</tr>
	</tbody>
</table>
<script type="text/javascript">
	jQuery(document).ready(function ($) {
		$('#add-row').on('click', function () {
			var row = $('.empty-row.screen-reader-text').clone(true);
			row.removeClass('empty-row screen-reader-text');
			row.insertBefore('#repeatable-fieldset-one tbody>tr:last');
			return false;
		});

		$('.remove-row').on('click', function () {
			$(this).parents('tr').remove();
			return false;
		});
	});
</script>

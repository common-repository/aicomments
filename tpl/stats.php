	<form id="aicomments-stat">
		<div class="title2"><?php _e( 'Statistic', 'wpai-comments' ); ?></div>
		<button name="step" value="<?=date('Y-m-d')?>|<?=date('Y-m-d')?>"><?php _e( 'Day', 'wpai-comments' ); ?></button>
		<button name="step" value="<?=date('Y-m-d', time() - 60*60*24*7)?>|<?=date('Y-m-d')?>"><?php _e( 'Week', 'wpai-comments' ); ?></button>
		<button name="step" value="<?=date('Y-m-d', time() - 60*60*24*30)?>|<?=date('Y-m-d')?>"><?php _e( 'Month', 'wpai-comments' ); ?></button>
		<br />
		
		<div class="stat-fix">
		<input type="date" name="dateStart" />
		<input type="date" name="dateEnd" />
		<button><?php _e( 'Get raport', 'wpai-comments' ); ?></button>
		</div>
		
		
	</form>
	
	
	<?php if( ! isset( $_COOKIE['wpai_auth'] ) && ! $this->options->token ){ ?>
		<div id="area-chat"></div>
		<div class="wpai-tabs">
			<div class="wpai-tab active" data-action="signIn"><?php _e( 'Sign in', 'wpai-comments' ); ?></div>
			<div class="wpai-tab" data-action="signUp"><?php _e( 'Sign up', 'wpai-comments' ); ?></div>
		</div>

		<form method="POST" class="wpai-form" id="wpai-sign" data-action="signIn">
			<div id="wpai-errors-messages"></div>
			<p><?php _e( 'If you have registered and already entered the key, you do not need to log in again.', 'wpai-comments' ); ?></p>
			<div class="row">
				<div><?php _e( 'Mail', 'wpai-comments' ); ?></div>
				<input type="email" name="email" required />
			</div>
			
			<div class="row">
				<div><?php _e( 'Your password', 'wpai-comments' ); ?></div>
				<input type="password" name="password" required />
			</div>
			
			<div class="row password2">
				<div><?php _e( 'Repeat password', 'wpai-comments' ); ?></div>
				<input type="password" name="password2" />
			</div>
			
			<div class="row">
				<button></button>
			</div>
		</form>
	<?php } ?>
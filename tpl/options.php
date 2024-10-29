<div id="wpai-comments-settings">
	<div class="wpai-comments-header">
		<div class="wpai-comments-logo-block inline">
			
		</div>

		<div class="wpai-comments-info inline">
			<?php if( isset( $info->limit ) ){ ?>
				<div class="wpai-comments-symbols inline">
					<div id="wpai-comments-symbols-text"><?php _e( 'Remaining characters:', 'wpai-comments' ); ?></div>
					<div id="aicomments-symbols"><?php echo number_format( (int) $info->limit, 0, ' ', ' ' )?></div>
				</div>
			<?php } ?>
		</div>
			<div class="wpai-comments-telegram inline">
				<div id="wpai-comments-doc"></div>

				<div class="wpai-comments-help-block">
					<div id="wpai-comments-title"><?php _e( 'Need help?', 'wpai-comments' ); ?></div>
					<div onclick="window.open('https://t.me/aicomments', '_blank')" id="telegram"><?php _e( 'Write to Telegram', 'wpai-comments' ); ?></div>
				</div>
			</div>
		</div>
	

    <div class="flexbox">

		<form method="POST" class="wpai-comments-form">
			<?php if( ! @$this->options->token ){ ?>
				<div class="license">
				
					<div class="none-license-block">
						<label class="title"><?php _e( 'Enter the API key. To get a key with free symbols, write to us via', 'wpai-comments' ); ?><a href="https://t.me/aicomments/" target="_blank"><?php _e( 'Telegram', 'wpai-comments' ); ?></a>.</label>
						
						<div class="none-license-block-row"><div class="fix-none-license-block-row"><input name="token" /></div></div>
						<div class="none-license-block-row"><div class="fix-none-license-block-row"><button name="save"><?php _e( 'Save', 'wpai-comments' ); ?></button></div></div>
					</div>
					
						
					
				</div>
				
				
				
			<?php } else { ?>

				<div class="f-right-flex">
				<div class="wpai-api-key f-right">
					<div class="api-key-header">
						<span><?php _e( 'API Key', 'wpai-comments' ); ?></span>
					</div>

					<div class="api-key-body">
						<input name="token" value="<?php echo esc_attr( @$this->options->token )?>" />
					
						<div class="api-key-body-2">
							<?php if( @$info->limit > 0 ){ ?>
								<div id="api-key-active"><?php _e( 'Plugin is active', 'wpai-comments' ); ?></div>
							<?php } else { ?>
								<div id="api-key-deactive"><?php _e( 'Plugin is not active', 'wpai-comments' ); ?></div>
							<?php } ?>							
							<div class="aicomments-savekey"><button name="save" id="aicomments-savekey"><?php _e( 'Save', 'wpai-comments' ); ?></button></div>
						</div>	
						
						<?php if( @$info->disabled ){ ?>
							<div class="wpai-comments-host-disabled">
								<?php _e( "There is a problem with your site's availability for generating comments. You need to remove the restrictions, then deactivate and reactivate the plugin.", 'wpai-comments' ); ?>
							</div>	
						<?php } ?>
						
					</div>


					<div class="aicomments-buy-form">
						<div class="title"><?php _e( 'Buy symbols', 'wpai-comments' ); ?></div>

						<?php if( isset( $info->price_label ) ){ ?>
							<div class="aicomments-price-label"><?php echo esc_attr( $info->price_label ); ?></div>
						<?php } ?>

						<div class="fix-buy-form">
						<input type="number" step="100" id="out_summ" placeholder="<?php _e( '50 $', 'wpai-comments' ); ?>" />
						<button type="button" id="aicomments-buy"><?php _e( 'Buy', 'wpai-comments' ); ?></button>
						</div>
					</div>

				</div>
				</div>


				<div class="wpai-comments-option">
					<div class="wpai-comments-row flex first_block">


						<div class="input-block inline">
							<label class="title2"><?php _e( 'Number of comments per day', 'wpai-comments' ); ?></label>
							<input type="number" name="count" min=1 max=999 value="<?php echo esc_attr( @$this->options->count ? (int) @$this->options->count : 1 )?>" />
						</div>

						<div class="input-block inline">
							<label class="title2"><?php _e( 'Text source for generation', 'wpai-comments' ); ?></label>
							<select name="theme">
								<option value="h1" <?php echo esc_attr( @$this->options->theme == 'h1' ? 'selected' : '' )?>><?php _e( 'H1', 'wpai-comments' ); ?></option>
								<option value="title" <?php echo esc_attr( @$this->options->theme == 'title' ? 'selected' : '' )?>><?php _e( 'Title (Yoast SEO)', 'wpai-comments' ); ?></option>
								<option value="aioseop" <?php echo esc_attr( @$this->options->theme == 'aioseop' ? 'selected' : '' )?>><?php _e( 'Title (All In Seo Pack)', 'wpai-comments' ); ?></option>
							</select>
						</div>

						<div class="input-block inline">
							<label class="title2"><?php _e( 'Send for moderation?', 'wpai-comments' ); ?></label>
							<select name="approve">
								<option value="1" <?php echo esc_attr( @$this->options->approve == 1 ? 'selected' : '' )?>><?php _e( 'Yes', 'wpai-comments' ); ?></option>
								<option value="0" <?php echo esc_attr( @$this->options->approve == 0 ? 'selected' : '' )?>><?php _e( 'No', 'wpai-comments' ); ?></option>
							</select>
						</div>
					</div>

					<div class="wpai-comments-row flex block1">

						<label class="title"><?php _e( 'Request template (Promts)', 'wpai-comments' ); ?></label>
						<span class="info-label-text"><?php _e( 'The quality of the comment and how it will turn out depends on the template. Using several different templates, you can get a variety of comments that are different from each other. In the template, you can change the length of the comment, its tone, or adapt it to the theme of the site.<br />By default, the page title is inserted at the end of the template. To insert h1 or title in any arbitrary place, use the variables {h1} or {title}. Then the title will be inserted in the place where the variable is.', 'wpai-comments' ); ?></span>
						<div class="input-block">
							<label class="wpai-fs-14pt"><input type="checkbox" name="rand_tpl" <?php echo esc_attr( @$this->options->rand_tpl ? 'checked' : '' )?> /><?php _e( 'Использовать шаблоны в случайном порядке', 'wpai-comments' ); ?></label>
						</div>

						<div class="input-block">
							<button type="button" id="wpai-set-default-promts"><?php _e( 'Set defaults promts', 'wpai-comments' ); ?></button>

							<div id="tpl-items">
								<?php if( @$this->options->tpls ){ ?>
									<?php foreach( $this->options->tpls as $tpl ){ ?>
										<div class="tpl-item"><textarea name="tpls[]"><?php echo esc_attr( $tpl )?></textarea><i></i></div>
									<?php } ?>
								<?php } ?>
							</div>
						</div>

						<button id="addTpl" type="button"><?php _e( 'Add', 'wpai-comments' ); ?></button>
					</div>

					<div class="wpai-comments-row flex block2">

						<div class="input-block">
							<label class="wpai-fs-14pt">
								<input type="checkbox" name="comments_reply" <?php echo esc_attr( @$this->options->comments_reply ) ? 'checked' : '' ?> />
								<?php _e( 'Reply to comments by randomly selecting one of the previously published ones on the page', 'wpai-comments' ); ?>
							</label>
						</div>

						<div class="tpl-item">
							<label class="wpai-fs-14pt">
								<div class="promt-fix"><?php _e( 'Prompt to reply to comments:', 'wpai-comments' ); ?></div>
								<textarea name="comments_reply_promt"><?php echo esc_textarea( @$this->options->comments_reply_promt ? $this->options->comments_reply_promt : _e( 'Write a response to the comment in simple, conversational language. Use jargon, but do not use swear words. The answer must be reasoned, think of a case from your life on the topic of the comment. The volume of the response to the comment is no more than 50 words. The comment to which you need to write a response: {comment}', 'wpai-comments' ) ) ?></textarea>
							</label>
						</div>

						<div class="input-block inline">
							<label class="wpai-fs-14pt">
								<div class="promt-fix2"><?php _e( 'Limit on the number of replies to comments per knock', 'wpai-comments' ); ?></div>
								<input type="number" name="comments_reply_count" min=1 max=999 value="<?php echo esc_attr( $this->options->comments_reply_count ? (int) $this->options->comments_reply_count : 1 )?>" />
							</label>
						</div>

					</div>

					<div class="wpai-comments-row mt-20">
						<label class="title"><?php _e( 'Post type', 'wpai-comments' ); ?></label>
						<span class="info-label-text"><?php _e( 'Select the types of pages on which to post comments or reviews', 'wpai-comments' ); ?></span>

						<div class="wpai-comments-rowrow"><?php if( $types = get_post_types( [ 'public' => true ] ) ){ unset( $types['attachment'] ); ?>
							<?php foreach( $types as $type ){?>
								<label class="wpai-fs-14pt"><input type="checkbox" name="post_type[]" value="<?php echo esc_attr( $type )?>" <?php echo esc_attr( @in_array($type, ( @$this->options->post_type ? @$this->options->post_type : [] ) ) ? 'checked' : '' )?> /> <?php echo esc_html( $type )?></label>
							<?php } ?>
						<?php } ?></div>
					</div>
			


					<div class="wpai-comments-row mt-60">
						<label class="title"><?php _e( 'Commentators', 'wpai-comments' ); ?></label>
						<span class="info-label-text"><?php _e( 'Each name on a new line', 'wpai-comments' ); ?></span>

						<textarea name="authors"><?php echo esc_textarea( $this->options->authors )?></textarea>
					</div>

					<div class="wpai-comments-row mt-60">
						<label class="title"><?php _e( 'List of links to post', 'wpai-comments' ); ?></label>
						<span class="info-label-text"><?php _e( 'Each URL on a new line. If you leave the field blank, comments and reviews will be published on all selected page types.', 'wpai-comments' ); ?></span>

						<textarea name="urls"><?php echo esc_textarea( @$this->options->urls )?></textarea>
					</div>


					<div class="wpai-comments-row fixbotton">
						<div><button name="save"><?php _e( 'Save', 'wpai-comments' ); ?></button></div>
					</div>
				</div>
			<?php } ?>
		</form>
		
	</div>

	<div class="wpai-comments-row">
	<?php include __DIR__ .'/stats.php'; ?>

	</div>
</div>
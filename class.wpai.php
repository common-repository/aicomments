<?php

class WPAI{

	private $options;
	
	private $api	= 'https://aiwppost.com/';
	
	function __construct(){
		if( ! $this->options = get_option('_neyro_comments') )
			$this->options = new stdClass();

		add_action('admin_menu',						[$this, 'menu']);
		add_action('wp_ajax_neyroComments',				[$this, 'neyroComments']);
		add_action('wp_ajax_nopriv_neyroComments',		[$this, 'neyroComments']);
		add_action('wp_ajax_aiCommentReply',			[$this, 'aiCommentReply']);
		add_action('wp_ajax_nopriv_aiCommentReply',		[$this, 'aiCommentReply']);
		add_action('wp_ajax_neyroCommentsCron',			[$this, 'neyroCommentsCron']);
		add_action('wp_ajax_nopriv_neyroCommentsCron',	[$this, 'neyroCommentsCron']);
		
		add_action('wp_ajax_sign',						[$this, 'sign']);
		add_action('wp_ajax_getStat',					[$this, 'getStat']);
		add_action('wp_ajax_aicomments_buy',			[$this, 'buy']);
		add_action('wp_ajax_defaultPromts',				[$this, 'defaultPromts']);
		
		add_filter('https_ssl_verify',					'__return_false');
		add_action('plugins_loaded',					[$this, 'langs']);
		add_action('activated_plugin',					[$this, 'init'], 10, 2 );
	}

	public function init(){		
		if( ! $this->options )
			$this->setDefaultPromts();
		
		if( isset( $this->options->token ) )
			$this->activation( $this->options->token );
	}
	
	public function defaultPromts(){
		$this->setDefaultPromts();
		wp_die( json_encode( $this->options->tpls ) );
	}
	
	private function setDefaultPromts(){
		$this->options->tpls = file( __DIR__ .'/tmp/promts' );
		update_option( '_neyro_comments', $this->options );
	}
	
	public function langs(){
		load_theme_textdomain('wpai-comments', plugin_dir_path( __FILE__ ) .'/lang' );
	}
	
	public function menu(){
		$hook = add_menu_page('AIcomments', 'AIcomments', 'level_10', 'wpai-comments', [$this, 'options'], 'dashicons-admin-comments', 777);
		add_action("admin_print_scripts-$hook",		[$this, 'scripts'] );		
	}
	
	public function options(){
		
		if( isset( $_POST['save'] ) ){
			if( isset( $_POST['token'] ) )
				$this->activation( $_POST['token'] );
		
			if( isset( $_POST['token'] ) )
				$this->options->token = sanitize_text_field( $_POST['token'] );
			
			if( isset( $_POST['count'] ) )
				$this->options->count = (int) sanitize_text_field( $_POST['count'] );
			
			if( isset( $_POST['theme'] ) && in_array( $_POST['theme'], [ 'title', 'h1', 'aioseop' ] ) )
				$this->options->theme = sanitize_text_field( $_POST['theme'] );
				
			if( isset( $_POST['approve'] ) )
				$this->options->approve = (int) (bool) sanitize_text_field( $_POST['approve'] );

			if( isset( $_POST['post_type'] ) )
				$this->options->post_type = array_map('sanitize_text_field', $_POST['post_type'] );
			
			if( isset( $_POST['urls'] ) )
				$this->options->urls = sanitize_textarea_field( $_POST['urls'] );
			
			if( isset( $_POST['authors'] ) )
				$this->options->authors = sanitize_textarea_field( $_POST['authors'] );
			
			$this->options->rand_tpl = (int) (bool) @$_POST['rand_tpl'];
			$this->options->comments_reply = (int) (bool) @$_POST['comments_reply'];
			
			if( isset( $_POST['comments_reply_promt'] ) )
				$this->options->comments_reply_promt = sanitize_textarea_field( $_POST['comments_reply_promt'] );
			
			if( isset( $_POST['comments_reply_count'] ) )
				$this->options->comments_reply_count = (int) $_POST['comments_reply_count'];
			
			if( isset( $_POST['tpls'] ) )
				$this->options->tpls = array_map('sanitize_text_field', $_POST['tpls'] );
			
			update_option('_neyro_comments', $this->options );
		}
		
		if( ! @$this->options->authors )
			$this->options->authors = $this->getStaticAuthors();
		
		if( @$this->options->token )
			$info = $this->getInfo( $this->options->token );
		
		include dirname(__FILE__) . '/tpl/options.php';
	}
	
	public function scripts(){
		wp_enqueue_style('wpai-comments', plugin_dir_url( __FILE__ ) .'assetst/css/style.css', false, '1.0.0', 'all');
		wp_enqueue_script('wpai-comments', plugin_dir_url( __FILE__ ) .'assetst/js/app.js', [ 'jquery' ], false, false );
		
		wp_enqueue_script('google-charts', 'https://www.gstatic.com/charts/loader.js', [ 'jquery' ], false, false );
		
		wp_localize_script('wpai-comments', 'wpaicomments', [ 'ajaxurl' => admin_url('admin-ajax.php') ] );
	}
		
	public function buy(){
		wp_die( $this->wpcurl( $this->api, [ 'token' => $this->options->token, 'action' => 'getPayUrl', 'out_summ' => $_REQUEST['out_summ'] ] ) );
	}
	
	public function getStat(){
		wp_die( $this->wpcurl( $this->api, [ 'token' => $this->options->token, 'action' => 'getStat', 'host' => $this->getHost(), 'dateStart' => $_REQUEST['dateStart'], 'dateEnd' => $_REQUEST['dateEnd'] ] ) );
	}
	
	public function sign(){
		if( ! empty( $_POST ) ){
			@$_POST['action'] = @$_POST['act'];
			wp_die( $this->wpCurl( $this->api, $_POST ) );
		}
		
		wp_die('{"success":"false"}');
	}
	
	private function activation( $token ){
		$this->wpcurl( $this->api, [ 'host' => sanitize_url( $this->getHost() ), 'action' => 'activation', 'token' => sanitize_text_field( $token ) ] );
	}
	
	private function getInfo( $token ){
		if( $info = json_decode( $this->wpcurl( $this->api, [ 'action' => 'getInfo', 'token' => sanitize_text_field( $token ), 'host' => sanitize_url( $this->getHost() ) ] ) ) )
			return $info;
		
		return false;
	}
	
	public function aiCommentReply(){
		if( ( $id = (int) $_REQUEST['id'] ) && $_REQUEST['text'] ){
			if( $comment = get_comment( $id ) ){
				
				if( ! ( $author = @$_REQUEST['author'] ) )
					$author = $this->getRandAuthor();
				
				$args = [
					'comment_parent'	=> $id,
					'comment_post_ID'	=> $comment->comment_post_ID,
					'comment_content'	=> wp_kses_post( $_REQUEST['text'] ),
					'comment_author'	=> sanitize_text_field( $author ),
					'comment_approved'	=> $this->options->approve == 1 ? 0 : 1,
				];
				
				if( $comment_id = wp_insert_comment( $args ) )
					wp_die('{"success":"true", "comment_id":"'. $comment_id .'"}');
				
				wp_die('{"error":"error_inser_comment"}');
			} else
				wp_die('{"error":"comment_parent_not_fount"}');
		}
		wp_die('{"success":"false"}');
	}
	
	public function neyroComments(){
		if( ( $post_id = (int) $_REQUEST['id'] ) && $_REQUEST['text'] ){
			if( $post = get_post( $post_id ) ){
			
				if( ! ( $author = @$_REQUEST['author'] ) )
					$author = $this->getRandAuthor();
			
				$args = [
					'comment_author'	=> sanitize_text_field( $author ),
					'comment_content'	=> wp_kses_post( $_REQUEST['text'] ),
					'comment_post_ID'	=> $post_id,
					'comment_approved'	=> $this->options->approve == 1 ? 0 : 1,
				];
				
				if( $comment_id = wp_insert_comment( $args ) )
					wp_die('{"success":"true", "comment_id":"'. $comment_id .'"}');
				
				wp_die('{"error":"error_inser_comment"}');
			} else
				wp_die('{"error":"post_not_fount"}');
		}
		wp_die('{"success":"false"}');
	}
	
	private function getRandAuthor(){
		if( ! @$this->options->authors )
			$this->options->authors = $this->getStaticAuthors();
			
		if( ( $names = explode("\n", $this->options->authors ) ) )
			return $names[ rand(0, count( $names ) -1 ) ];
		
		return false;
	}
	
	private function getStaticAuthors(){
		$file = __DIR__ .'/tmp/names';
		if( file_exists( $file ) )
			return implode("\n", array_map('trim', file( $file ) ) );

		return null;
	}
	
	public function neyroCommentsCron(){
		global $wpdb;
		
		if( @$this->options->urls ){
			$ids = [];
			$this->options->urls = explode("\n", sanitize_textarea_field( $this->options->urls ) );
			
			foreach( $this->options->urls as $url ){
				if( ( $id = url_to_postid( sanitize_url( $url ) ) ) )
					$args['include'][] = $id;
			}
		}
		
		if( @$this->options->post_type )
			$args['post_type'] = array_map('sanitize_text_field', $this->options->post_type );
		
		$args['orderby'] = 'rand';
		$args['posts_per_page'] = 1;		
		
		if( @$this->options->comments_reply ){
			$cArgs = [ 'orderby' => 'rand', 'no_found_rows' => true, 'cache_results' => false, 'fields' => 'all' ];
			
			if( $args['post_type'] )
				$cArgs['post_type'] = $args['post_type'];
			
			if( $args['include'] )
				$cArgs['post__in'] = $args['include'];
			
			if( $comments = get_comments( $cArgs ) ){
				
				shuffle( $comments );
				$this->createCommentReply( [ 'limit' => $this->options->comments_reply_count, 'token' => $this->options->token, 'promt' => $this->options->comments_reply_promt, 'comment_id' => $comments[0]->comment_ID, 'comment' => $comments[0]->comment_content ] );

			}
		}
		
		if( $posts = get_posts( $args ) ){
			$post = $posts[0];
			
			$post->h1 = $post->post_title;
			
			if( $this->options->theme == 'title' ){
				if( ( $title = get_post_meta($post->ID, '_yoast_wpseo_title', true) ) )
					$post->post_title = $title;
			} elseif( $this->options->theme == 'aioseop' ){
				if( ( $title = get_post_meta($post->ID, '_aioseop_title', true) ) )
					$post->post_title = $title;
			}
			
			if( ! ( $post->title = get_post_meta($post->ID, '_yoast_wpseo_title', true) ) )
				$post->title = get_post_meta($post->ID, '_aioseop_title', true);
			
			$cTpls = count( $this->options->tpls ? $this->options->tpls : [] ) - 1;
			
			if( @$this->options->rand_tpl ){
			
				$tpl = $this->options->tpls[ rand(0, $cTpls ) ];
				
			} else {
				$cTpl = (int) get_option('_wpai_ctpl');
			
				if( $cTpl > $cTpls )
					$cTpl = 0;
				
				$tpl = $this->options->tpls[ $cTpl ];
				
				$cTpl++;
				update_option('_wpai_ctpl', $cTpl);
			}
			
			if( $this->createComment( $post, $this->options->token, $tpl, $this->options->count ) ){
			
				wp_die('{"success":"true", "count": "'. $this->options->count .'"}');
			
			} else
				wp_die('{"error":"error create comment"}');
		} else
			wp_die('{"error":"no posts"}');
			
		wp_die('{"success":"false"}');
	}
	
	private function createCommentReply( $args ){
		
		if( ! is_object( $args ) )
			$args = (object) $args;
		
		if( ! $args->limit || ! $args->comment || ! $args->comment_id || ! $args->token )
			return false;
		
		$args = [
				'limit'			=> $args->limit,
				'host' 			=> $this->getHost(),
				'action'		=> 'createCommentReply',
				'token'			=> sanitize_text_field( $args->token ),
				'promt'			=> sanitize_text_field( $args->promt ), 
				'comment'		=> sanitize_text_field( $args->comment ), 
				'author' 		=> sanitize_text_field( $this->getRandAuthor() ),
				'callback'		=> sanitize_url( admin_url( 'admin-ajax.php' ) .'?action=aiCommentReply&id='. $args->comment_id ),
			];
		
		return json_decode( $this->wpcurl( $this->api, $args ) );
		
	}
	
	private function createComment( $post, $token, $tpl = false, $limit = 0 ){
		if( ! $post->ID || ! $post->post_title )
			return false;

		$args = [
				'limit'			=> $limit,
				'host' 			=> $this->getHost(),
				'action'		=> 'createComment',
				'token'			=> sanitize_text_field( $token ),
				'tpl'			=> sanitize_text_field( $tpl ), 
				'h1'			=> sanitize_text_field( $post->h1 ),
				'title'			=> sanitize_text_field( $post->title ),
				'posts_title'	=> sanitize_text_field( $post->post_title ),
				'author' 		=> sanitize_text_field( $this->getRandAuthor() ),
				'callback'		=> sanitize_url( admin_url( 'admin-ajax.php' ) .'?action=neyroComments&id='. $post->ID ),
			];
		
		return json_decode( $this->wpcurl( $this->api, $args ) );
	}
	
	private function getHost(){
		$host = get_option('siteurl');
		
		if( $_SERVER['HTTPS'] == 'on' )
			$host = str_replace('http://', 'https://', $host);
			
		return $host;
	}
	
	private function wpcurl( $url, $args = [] ){
		if( ! empty( $args ) )
			$args = [ 'body' => $args ];
		
		$data = wp_remote_request( $url, $args );
		
		return @$data['body'];
	}

}

?>
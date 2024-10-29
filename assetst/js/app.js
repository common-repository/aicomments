(function($){

	const app = {
	
		init: () => {
			app.events();
		},
		
		events: () => {
			$(document).on('click', '#addTpl', app.addTpl);
			$(document).on('click', '.tpl-item i', app.removeTpl);
			
			$(document).on('click', '#aicomments-buy', app.buy);
			$(document).on('click', '.wpai-tab', app.tabs);
			$(document).on('submit', '#wpai-sign', app.sign);
			$(document).on('submit', '#aicomments-stat', app.getStat);
			$(document).on('click', 'button[name="step"]', app.statStep);
			$(document).on('click', '#wpai-set-default-promts', app.defaultPromts);
		},
		
		defaultPromts: async () => {
			let e = $('#tpl-items');
			let tpls = await app.request( { action: 'defaultPromts' } );
			
			if( tpls.length && e.length ){
				e.html('');
				
				for( let k in tpls )
					e.append('<div class="tpl-item"><textarea name="tpls[]">'+ tpls[ k ] +'</textarea><i></i></div>');	
			}
		},
		
		statStep: async function( event ){
			let e = $(this);
			date = e.val().split('|');
			$('input[name="dateStart"]').val( date[0] );
			$('input[name="dateEnd"]').val( date[1] );
		},
		
		buy: async () => {
			let buy = await app.request( { 'out_summ': $('#out_summ').val(), action: 'aicomments_buy' } );
			
			if( buy.pay_url ){
				$('body').append('<a id="test" href="'+ buy.pay_url +'" target="_blank"></a>');

				setTimeout(() => { 
					test.click();
					test.remove();
				}, 1)
			}
		},
		
		getStat: async function( event ){
			event.preventDefault();
		
			let e = $(this);
			let args = app.getFormData( e );
			let stats = await app.request( Object.assign( args, { action: 'getStat' } ) );
			
			if( $('#tokens-stats').length )
				$('#area-chat').html('');
				$('#tokens-stats').remove();
			
			if( ! Object.keys( stats ).length ){
				e.after('<div id="tokens-stats"><h3>Данных не найдено!</h3></div>');
				return;
			}
			
			e.after('<div id="tokens-stats"><h3>Tokens: '+ stats.total +'</h3></div>');
			
			google.charts.load('current', {'packages':['corechart']});			
			google.charts.setOnLoadCallback( () => {
				let day = Object.keys( stats ).length < 3;
				args = [ [ 'Date', 'Symbols'] ];
				
				if( day )
					args = [ [ 'Host', 'Symbols'] ];
				
				for( k in stats ){
					if( k == 'total' )
						continue;
				
					if( day ){
						for( let i in stats[ k ] ){
							if( i == 'total' )
								continue;
							
							args.push( [ i, parseInt( stats[ k ][ i ] ) ] );
							
							$('#tokens-stats').append('<div>'+ i +': '+ stats[ k ][ i ] +'</div>');
						}
					} else {
						args.push( [ k, parseInt( stats[ k ].total ) ] );
						$('#tokens-stats').append('<div>'+ k +': '+ stats[ k ].total +'</div>');
					}
				}
				
				data = google.visualization.arrayToDataTable( args );
				
				new google.visualization.LineChart( document.getElementById('area-chat') ).draw(
					data, 
					{
					  title: '',
					  hAxis: {title: ' ',  titleTextStyle: {color: '#333'}},
					  vAxis: {minValue: 0}
					});
			});
		
			return false;
		},

		sign: async function( event ){
			event.preventDefault();
			
			let e = $(this);
			let args = app.getFormData( e );
			let act = e.attr('data-action');
			
			let auth = await app.request( Object.assign( args, { act: act, action: 'sign' } ) );
			
			if( auth.message )
				$('#wpai-errors-messages').html( auth.message );
			
			if( auth.auth ){
				$('#wpai-errors-messages').text('');
				document.cookie = 'wpai_auth=true';
				location.reload();
			}
			return false;
		},

		getFormData: ( e ) => {
			data = {};

			e.serializeArray().map(( e ) => {
				if( ( val = e.value.trim() ) )
					data[ e.name ] = val;
			});
			return data;
		},
		
		tabs: function(){
			let e = $(this);
			
			$('#wpai-errors-messages').text('');
			$('#wpai-sign').attr('data-action', e.data('action'))
			$('.wpai-tab').removeClass('active');
			e.addClass('active');
		},
		
		removeTpl: function(){
			$(this).closest('.tpl-item').remove();
		},
		
		addTpl: () => {
			$('#tpl-items').append('<div class="tpl-item"><textarea name="tpls[]"></textarea><i></i></div>');
		},
		
		request: ( args = {} ) => {
			return new Promise( resolve => $.ajax({ url: wpaicomments.ajaxurl, type: 'POST', data: args, dataType: 'json', success: data => resolve( data ) }) )
		}
	}
	
	app.init();
	
})(jQuery)
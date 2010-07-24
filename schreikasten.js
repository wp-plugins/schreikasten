//Play sound
var Sound = new Object();
Sound.play = function Sound_play(src) {
	this.stop();
	var elm;
	if (typeof document.all != "undefined") {
		elm = document.createElement("bgsound");
		elm.src = src;
	}
	else {
		elm = document.createElement("object");
		elm.setAttribute("data",src);
		elm.setAttribute("type","audio/x-wav");
		elm.setAttribute("controller","true");
	}
	document.body.appendChild(elm);
	this.elm = elm;
	return true;
};

Sound.stop = function Sound_stop() {
	if (this.elm) {
		this.elm.parentNode.removeChild(this.elm);
		this.elm = null;
	}
};

//Semaphore class
	function sk_Semaphore() {
		var me = this; //Just to use me instead of this if a recursived function is used
		
		var status = true; //The green or red light
		me.using = 0;
	
		//Function to set green
		me.setGreen = function() { status = true; };
	
		//Is green?
		me.isGreen = function() { return status; };
	
		//Function to set red
		me.setRed = function() { status = false; };
	
		//Is red?
		me.isRed = function() { return !status; };
		
	}
	
	var loading_sk_img = new Image(); 
	loading_sk_img.src = sk_url+'/wp-content/plugins/schreikasten/img/loading.gif';
	var sk_sack = new sack(sk_url+'/wp-admin/admin-ajax.php' );
	var sk_sack_action = new sack(sk_url+'/wp-admin/admin-ajax.php' );
	
	function sk_feed( page, rand, semaphore, timer )
	{
		if( semaphore.isGreen() ) {
			
			semaphore.setRed();
			
			//Our plugin sack configuration
			sk_sack.execute = 0;
			sk_sack.method = 'POST';
			sk_sack.setVar( 'action', 'sk_ajax' );
			//sk_sack.element = 'sk_content'+rand;
			
			//The ajax call data
			sk_sack.setVar( 'page', page );
			sk_sack.setVar( 'rand', rand );
			sk_sack.setVar( 'size' , document.getElementById('sk_size'+rand).value );
			
			sk_sack.onCompletion = function() {
				var page = sk_sack.vars['page'][0];
				var rand = sk_sack.vars['rand'][0];
				var doc = document.getElementById('sk_content'+rand);
				doc.innerHTML = sk_sack.response;
				semaphore.setGreen();
				if(timer) {
					var count = document.getElementById('sk_count'+rand).value;
					var int_count = document.getElementById('sk_int_count'+rand).value;
					var last = document.getElementById('sk_int_last'+rand).value;
					if(count != int_count) {
						document.getElementById('sk_count'+rand).value = int_count;
						if(!sk_hasFocus) sk_alternateTitle(sk_title_message + last, document.title);
						Sound.play(sk_wav);
					}
				}
				if(document.getElementById('sk_page'+rand).value!=page) {
					var aux = document.getElementById('throbber-page'+rand);
					aux.setAttribute('class', 'throbber-page-on');
					aux.setAttribute('className', 'throbber-page-on'); //IE sucks
				} else {
					sk_sack.xmlhttp.abort();
					sk_sack.reset();
				}
			};
			
			sk_sack.runAJAX();
			
		} else {
			setTimeout(function (){ sk_feed( page, rand, semaphore, timer ); }, 300);
		}
		return true;
	}
	// end of JavaScript function sk_feed
	
	function sk_add( alias, email, text, skfor, rand, semaphore)
	{
		sk_sack.xmlhttp.abort();
		sk_sack.reset();
		semaphore.setRed();
		
		//Our plugin sack configuration
		sk_sack_action.execute = 0;
		sk_sack_action.method = 'POST';
		sk_sack_action.setVar( 'action', 'sk_ajax_add' );
		sk_sack_action.element = 'sk_content'+rand;
		
		//The ajax call data
		sk_sack_action.setVar( 'alias', alias );
		sk_sack_action.setVar( 'email', email );
		sk_sack_action.setVar( 'text', text );
		sk_sack_action.setVar( 'skfor', skfor );
		sk_sack_action.setVar( 'rand', rand );
		sk_sack_action.setVar( 'size' , document.getElementById('sk_size'+rand).value );
		
		sk_sack_action.onCompletion = function() {
			var rand = sk_sack_action.vars['rand'][0];
			var doc = document.getElementById('sk_content'+rand);
			doc.innerHTML = sk_sack_action.response;
			sk_sack.xmlhttp.abort();
			sk_sack.reset();
			semaphore.setGreen();
			document.getElementById('sk_count'+rand).value = int_count = document.getElementById('sk_int_count'+rand).value;
		
		};
		
		sk_sack_action.runAJAX();
			
		return true;
		
	}
	
	function sk_action( id, sk_action, rand, semaphore)
	{
		sk_sack.xmlhttp.abort();
		sk_sack.reset();
		semaphore.setRed();
		
		//Our plugin sack configuration
		sk_sack_action.execute = 0;
		sk_sack_action.method = 'POST';
		sk_sack_action.setVar( 'action', 'sk_ajax_action' );
		sk_sack_action.element = 'sk_content'+rand;
		
		//The ajax call data
		sk_sack_action.setVar( 'id', id );
		sk_sack_action.setVar( 'sk_action', sk_action );
		sk_sack_action.setVar( 'rand', rand );
		sk_sack_action.setVar( 'page', document.getElementById('sk_page'+rand).value );
		sk_sack_action.setVar( 'size', document.getElementById('sk_size'+rand).value );
		
		sk_sack_action.onCompletion = function() {
			var rand = sk_sack_action.vars['rand'][0];
			var doc = document.getElementById('sk_content'+rand);
			doc.innerHTML = sk_sack_action.response;
			sk_sack.xmlhttp.abort();
			sk_sack.reset();
			semaphore.setGreen();
		};
		
		sk_sack_action.runAJAX();
			
		return true;
		
	}

<script type='text/javascript'>
	/* <![CDATA[ */
	
	function sk_timer%rand%() {
		var sk_timer_div = document.getElementById('sk_timer%rand%');
		if(sk_timer_div.value) clearTimeout(sk_timer_div.value);
		timer_id=setTimeout( 'sk_refresh%rand%(true);' , %time% );
		sk_timer_div.value=timer_id;
	}
	
	function sk_refresh%rand%(timer) {
		var sk_timer_div = document.getElementById('sk_timer%rand%');%show_timer%
		sk_feed( document.getElementById('sk_page%rand%').value, %rand%, sk_semaphore%rand%, false, timer);
	}
	
	function for_delete%rand%() {
		document.getElementById("sk_for_id%rand%").value='0';
		document.getElementById("sk_for_name%rand%").innerHTML='';
		document.getElementById("sk_for_tr%rand%").className='sk-for-nai';
	}
	
	function for_set%rand%(id, name) {
		document.getElementById("sk_for_id%rand%").value=id;
		document.getElementById("sk_for_name%rand%").innerHTML=name;
		var text=document.getElementsByName("sk_text%rand%")[0].value;
		if(%qa%) {
			document.getElementsByName("sk_text%rand%")[0].value='%answer%: '+text;
		} else {
			document.getElementsByName("sk_text%rand%")[0].value='%for% '+name+' - '+text;
		}
		document.getElementById("sk_for_tr%rand%").className='sk-for-yep';
	}
	
	function sk_replyDelete%rand%(id, text) {
		var aux = document.getElementById('sk-%rand%-'+id);
		aux.setAttribute('class', 'sk-reply sk-reply-on');
		aux.setAttribute('className', 'sk-reply sk-reply-on');
		if(confirm(text)) {
			sk_action(id, 'delete', %rand%, sk_semaphore%rand%);
		} else {
			aux.setAttribute('class', 'sk-reply');
			aux.setAttribute('className', 'sk-reply'); //IE sucks
		}
	}
	
	function sk_pressButton%rand%() {
		var alias=document.getElementsByName("sk_alias%rand%")[0].value;
		var text=document.getElementsByName("sk_text%rand%")[0].value;
		var email=document.getElementsByName("sk_email%rand%")[0].value;
		var skfor=document.getElementsByName("sk_for_id%rand%")[0].value;
		if(text.length>%maxchars%-1) {
			alert('%lenght%');
			return false;
		}%ask_email%%email_in_text%
		document.getElementById('th_sk_alias%rand%').innerHTML = alias.replace(/&/gi,"&amp;");
		var aux_text=text.replace(/\n/g,"<br>"); 
		document.getElementById('th_sk_text%rand%').innerHTML = aux_text.replace(/&/gi,"&amp;");
		if(%chat%){
			document.getElementById('throbber-page%rand%').setAttribute('class','throbber-page-on');
			document.getElementById('throbber-page%rand%').setAttribute('className','throbber-page-on'); //IE sucks
		} else {
			document.getElementById('throbber-img%rand%').setAttribute('class','throbber-img-on');
			document.getElementById('throbber-img%rand%').setAttribute('className','throbber-img-on'); //IE sucks
			document.getElementById('throbber-img%rand%').style.visibility='visible';
		}
		email=email.replace(/&amp;/gi,"y");
		alias=alias.replace(/&/gi,"%26");
		text=text.replace(/&/gi,"%26");
		for_delete%rand%();
		document.getElementById('sk_page%rand%').value=1;
		document.getElementsByName('sk_text%rand%')[0].value='';
		sk_add( alias, email, text, skfor, %rand%, sk_semaphore%rand% );%message%
	}
/* ]]> */
</script>
%sk_general%
<script type='text/javascript'>
	window.onblur = function () {sk_hasFocus = false;}
	window.onfocus = function () {sk_hasFocus = true; document.title = sk_old_title;}
	var sk_semaphore%rand%=new sk_Semaphore();
	sk_semaphore%rand%.setGreen();
	%have_for%%show_timer%
</script>

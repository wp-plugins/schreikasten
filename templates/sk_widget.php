<script type='text/javascript'>
	/* <![CDATA[ */
	
	
	function sk_timer%rand%() {
		var sk_timer_div = document.getElementById('sk_timer%rand%');
		if(sk_timer_div.value) clearTimeout(sk_timer_div.value);
		timer_id=setTimeout( 'sk_refresh%rand%();' , %time% );
		sk_timer_div.value=timer_id;
	}
	
	function sk_refresh%rand%() {
		var sk_timer_div = document.getElementById('sk_timer%rand%');%show_timer%
		sk_feed( document.getElementsByName('sk_page%rand%')[0].value, %rand%);
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
		document.getElementsByName("sk_text%rand%")[0].value='%for% '+name+' - '+text;
		document.getElementById("sk_for_tr%rand%").className='sk-for-yep';
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
		document.getElementById('th_sk_text%rand%').innerHTML = text.replace(/&/gi,"&amp;");
		document.getElementById('throbber-img%rand%').setAttribute('class','throbber-img-on');
		document.getElementById('throbber-img%rand%').setAttribute('className','throbber-img-on'); //IE sucks
		document.getElementById('throbber-img%rand%').style.visibility='visible';
		
		email=email.replace(/&amp;/gi,"y");
		alias=alias.replace(/&/gi,"%26");
		text=text.replace(/&/gi,"%26");
		for_delete%rand%();
		document.getElementsByName('sk_page%rand%')[0].value=1;
		document.getElementsByName('sk_text%rand%')[0].value='';
		sk_add( alias, email, text, skfor, %rand% );%message%
	}
/* ]]> */
</script>
<a name='sk_top'></a>%form_table%%form_button%
<div id='sk_content%rand%'>
	%first_comments%
	%first_page_selector%
</div>
<script type='text/javascript'>
	%have_for%%show_timer%
</script>

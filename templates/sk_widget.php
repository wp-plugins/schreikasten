<script type='text/javascript'>
	/* <![CDATA[ */
	
	var mm_add%rand% = new minimax('%uri_skadd%', 'sk_content%rand%');
	var mm_get%rand% = new minimax('%uri_sk%', 'sk_content%rand%');
	
	function sk_timer%rand%() {
		var sk_timer_div = document.getElementById('sk_timer%rand%');
		if(sk_timer_div.value) clearTimeout(sk_timer_div.value);
		timer_id=setTimeout( 'sk_refresh%rand%();' , %time% );
		sk_timer_div.value=timer_id;
	}
	
	function sk_refresh%rand%() {
		var sk_timer_div = document.getElementById('sk_timer%rand%');%show_timer%
		mm_get%rand%.post('nonce=%nonce%&page='+document.getElementsByName('sk_page%rand%')[0].value+'&rand=%rand%');		
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
		document.getElementById('throbber-img%rand%').style.visibility='visible';
		
		email=email.replace(/&amp;/gi,"y");
		alias=alias.replace(/&/gi,"%26");
		text=text.replace(/&/gi,"%26");
		var post = "nonce=%nonce%&alias="+alias+"&email="+email+"&text="+text+"&for="+skfor+"&rand=%rand%";
		for_delete%rand%();
		document.getElementsByName('sk_page%rand%')[0].value=1;
		document.getElementsByName('sk_text%rand%')[0].value='';
		mm_add%rand%.post(post);%message%
	}
/* ]]> */
</script>
<a name='sk_top'></a>%form_table%%form_button%
<div id='sk_content%rand%'>
	%first_comments%
	%first_page_selector%
</div>
<script type='text/javascript'>
	var sk_semaphore%rand%=new Semaphore();
	mm_add%rand%.setSemaphore(sk_semaphore%rand%);
	mm_add%rand%.setThrobber('throbber-img%rand%', 'throbber-img-on', 'throbber-img-off');
	mm_get%rand%.setSemaphore(sk_semaphore%rand%);
	mm_get%rand%.setThrobber('throbber-page%rand%', 'throbber-page-on', 'throbber-page-off');%have_for%%show_timer%
</script>

window.onload = function()
{

    const checkbox = document.getElementById('international')
    if ( !!checkbox){
    checkbox.addEventListener('change', (event) => {
      if (event.target.checked) {
        console.log('checked')

        document.getElementById('i4x').classList.add('international');
        document.getElementById('i5x').classList.add('international');
        document.getElementById('i6x').classList.add('international');

      } else {
        console.log('not checked')
        document.getElementById('i4x').classList.remove('international');
        document.getElementById('i5x').classList.remove('international');
        document.getElementById('i6x').classList.remove('international');

      }
    })
  }

  var sp1 = document.getElementById("sp1");
  if(sp1){
    sp1.addEventListener("click", function(){
      textareavalue = jQuery('#td45').val();
      var  va  = 'p';
      jQuery('#td45').val(textareavalue + ' [' + va + ']')
    });

     jQuery("select#shortselect").change(function(){
      textareavalue = jQuery('#td45').val();
      va = jQuery(this).val();
      jQuery('#td45').val(textareavalue + ' [' + va + ']')
    });

    jQuery("select#shortselect2").change(function(){
      textareavalue = jQuery('#td45').val();
      va = jQuery(this).val();
      jQuery('#td45').val(textareavalue + ' [' + va + ']')

    });


  }


    jQuery(function(){

    jQuery('.formsubmit').on('click', function(e){
			att = jQuery(this).attr('alert');
			if(att != ''){
				alert(att);
			}

      jQuery(this).parent().submit();
      });
      jQuery('.handlediv').on('click', function(e){//when content of metabox couldnt be open
        //jQuery(this).parent().toggleClass('closed');
        aria = jQuery(this).attr('aria-expanded');
        if(aria=='true'){
          //jQuery(this).attr('aria-expanded', 'false');
        }
        else{
          //jQuery(this).attr('aria-expanded', 'true');
        }
      });

		function ukr(obj) {
		    obj = obj.replace(/[^а-яА-ЯіІїЇєЄ ]/ig,'');
			return obj
		}


       jQuery('.checkforminputs').on('click', function(e){

	       	val1 = jQuery('#sender_name').val();

	       	val2 = jQuery('#recipient_name').val();

	        if  (val1 != ukr(val1) )  {
	          	e.preventDefault();
	          	alert('П. І. Б відправника  повинно бути написане кирилицею. виправіть це та повторіть спробу'); // pop alert message
	          	//jQuery('#sender_name').val( ukr(val1) );
	          	//return false;
	        }
	        else if (val2 != ukr(val2) ) {
	          	e.preventDefault();
	          	alert('П. І. Б отримувача повинно бути написане кирилицею. виправіть це та повторіть спробу'); // pop alert message
	          	//jQuery('#recipient_name').val( ukr(val2) );
	          	//return false;
	        }
	        else{

	        	//return true;

	        }


        //jQuery('#recipient_name');
        //jQuery('#sender_name');

      });


      jQuery('#invoice_other_fields .inside .button').on('click', function(e){



        text = jQuery(this).text();
        console.log(text);
        if(text == ' Друк накладної'){
          text = 'Ви дійсно бажаєте друкувати накладну';
          console.log('text1');
        }
        if(text == ' Друк стікера'){
          text = 'Ви дійсно бажаєте друкувати стікер';
          console.log('text2');
        }
        if(text == 'Відпралення...'){
          text = 'Ви дійсно бажаєте Відправити на e-mail';
          console.log('text3');
        }
             if(!confirm(text + '?')){
                 e.preventDefault();
                 alert("Операцію відхилено");
             }
      });
      if (jQuery('#MyDate').length > 0) {//fix adminbar button freeze on page with jquery datepicker

          jQuery('#MyDate').datepicker();
          jQuery('#MyDate').datepicker("option", "dateFormat", "dd.mm.yy");

        wpbar = document.getElementById('wp-admin-bar-menu-toggle');
        wpbar.addEventListener('click', function(){
          document.getElementById('wpwrap').classList.toggle('wp-responsive-open');
        });
      }

      textw = "Ваше замовлення #[NOVAPOSHTA_ORDER] вже сформоване і буде відправлено [NOVAPOSHTA_DATE] Новою Поштою. \n\nНомер накладної\: [NOVAPOSHTA_TTN]";
      jQuery("#morkvanp_email_editor_id").text(textw);
      if ( jQuery('#mceu_24').length > 0 ){
        jQuery('#mceu_24').hide();
        jQuery('#morkvanp_email_editor_id').show();
        //jQuery('#wp-morkvanp_email_editor_id-editor-tools').hide();
      }
    });



   var MyDiv1 = document.getElementById("messagebox");
    if(MyDiv1){
        var h = MyDiv1.getAttribute('data');
        //h-=20;
        //var MyDiv2 = document.getElementById('messagebox');
        //MyDiv2.innerHTML = MyDiv1.innerHTML;
        //MyDiv2.style.height = h + 'px';
        MyDiv1.style.height = h + 'px';
				MyDiv1.style.padding = 8 + 'px';
        //MyDiv1.childNodes[0].style.padding = 0 ;
        //MyDiv2.classList.add('error');
    }

    var MyDiv3 = document.getElementById("nnnid");
    if(MyDiv3){
        MyDiv3 = document.getElementById("nnnid");
        var h = 182 + 'px';
        console.log(h);
        var MyDiv4 = document.getElementById('messagebox');
        MyDiv4.innerHTML = MyDiv3.innerHTML;
        MyDiv4.style.height = h;
        MyDiv4.style.padding = '8px';
        MyDiv4.classList.add('updated');

    }


}

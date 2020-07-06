function calculprice(nomchamp)
{
	var int1=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 2});
	var calculprix = $('#inputpht'+nomchamp).val()*$('#inputqte'+nomchamp).val()*(100-$('#inputreduc'+nomchamp).val())/100;
	$('#tdtotht'+nomchamp).text(int1.format(calculprix));
	/*Calcul du total*/
	var res=0;
	$('#tablelines').find('tr').each(function() {
	       var tothtline= $(this).find('td[id*="tdtotht"]').text();
		   tothtline = clearInt(tothtline);
	       res = res + +tothtline;
	});
	$('#total').text(res);
}

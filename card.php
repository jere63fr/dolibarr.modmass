<?php
/* Copyright (C) 2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2018 Jérémie Ter-Heide <jeremie@ter-heide.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *   	\file       card.php
 *		\ingroup    modmass
 *		\brief      Page to 
 */

//if (! defined('NOREQUIREUSER'))          define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))            define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))           define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))          define('NOREQUIRETRAN','1');
//if (! defined('NOSCANGETFORINJECTION'))  define('NOSCANGETFORINJECTION','1');			// Do not check anti CSRF attack test
//if (! defined('NOSCANPOSTFORINJECTION')) define('NOSCANPOSTFORINJECTION','1');		// Do not check anti CSRF attack test
//if (! defined('NOCSRFCHECK'))            define('NOCSRFCHECK','1');			// Do not check anti CSRF attack test done when option MAIN_SECURITY_CSRF_WITH_TOKEN is on.
//if (! defined('NOSTYLECHECK'))           define('NOSTYLECHECK','1');			// Do not check style html tag into posted data
//if (! defined('NOTOKENRENEWAL'))         define('NOTOKENRENEWAL','1');		// Do not check anti POST attack test
//if (! defined('NOREQUIREMENU'))          define('NOREQUIREMENU','1');			// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))          define('NOREQUIREHTML','1');			// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))          define('NOREQUIREAJAX','1');         // Do not load ajax.lib.php library
//if (! defined("NOLOGIN"))                define("NOLOGIN",'1');				// If this page is public (can be called outside logged session)

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include($_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php");
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include(substr($tmp, 0, ($i+1))."/main.inc.php");
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php");
// Try main.inc.php using relative path
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT . '/comm/propal/class/propal.class.php';
require_once DOL_DOCUMENT_ROOT . '/compta/facture/class/facture.class.php';
require_once DOL_DOCUMENT_ROOT . '/supplier_proposal/class/supplier_proposal.class.php';
require_once DOL_DOCUMENT_ROOT . '/fourn/class/fournisseur.facture.class.php';
require_once DOL_DOCUMENT_ROOT . '/fourn/class/fournisseur.commande.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/modules/supplier_proposal/modules_supplier_proposal.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/supplier_proposal.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/invoice.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/fourn.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/propal.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT . '/product/class/html.formproduct.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/doleditor.class.php';
require_once 'lib/modmass.lib.php';

$nbrows=ROWS_2;
if (! empty($conf->global->MAIN_INPUT_DESC_HEIGHT)) $nbrows=$conf->global->MAIN_INPUT_DESC_HEIGHT;
$enable=(isset($conf->global->FCKEDITOR_ENABLE_DETAILS)?$conf->global->FCKEDITOR_ENABLE_DETAILS:0);

$morejs=array("/modmass/js/modmass.js");

$langs->load("modmass@modmass");

$id = ($_GET['id'] ? $_GET['id'] : $_GET['id']); // For backward compatibility
$ref = $_GET['ref'];
$type= $_GET['type'];

llxHeader('',$langs->trans("Modmass"),'','','','',$morejs);
$form = new Form($db);
$userstatic=new User($db);
$formproduct=new FormProduct($db);
$situation=false;
$wcol1="50%";
switch ($type){
	case 'propal':
		$object = new Propal($db);
		if ($id > 0 || ! empty($ref)) {
			$ret = $object->fetch($id, $ref);
			$edtxtf='edit';
		}
		$head = propal_prepare_head($object);
		$pathact = '../../comm/propal/card.php?id='.$id;
		break;
	case 'invoice':
		$object = new Facture($db);
		if ($id > 0 || ! empty($ref)) {
			$ret = $object->fetch($id, $ref);
		}
		if($object->type==5){
			$situation=true;
			$wcol1="40%";
		}
		$head = facture_prepare_head($object);
		$pathact = '../../compta/facture/card.php?facid='.$id;
		$edtxtf='view';
		break;
	case 'supplier_proposal':
		$object = new SupplierProposal($db);
		if ($id > 0 || ! empty($ref)) {
			$ret = $object->fetch($id, $ref);
		}
		$head = supplier_proposal_prepare_head($object);
		$pathact = '../../supplier_proposal/card.php?id='.$id;
		$edtxtf='view';
		break;
	case 'FactureFournisseur':
		$object = new FactureFournisseur($db);
		if ($id > 0 || ! empty($ref)) {
			$ret = $object->fetch($id, $ref);
		}
		$head = facturefourn_prepare_head($object);
		$pathact = '../../fourn/facture/card.php?id='.$id;
		$edtxtf='view';
		break;
	case 'CommandeFournisseur':
		$object = new CommandeFournisseur($db);
		if ($id > 0 || ! empty($ref)) {
			$ret = $object->fetch($id, $ref);
		}
		$head = ordersupplier_prepare_head($object);
		$pathact = '../../fourn/commande/card.php?id='.$id;
		$edtxtf='view';
		break;
}
$extrafields = new ExtraFields($db);
$productstatic=new Product($db);
//Choix des champs modifiable
$formconfirm = '';
$html1 = new Form($db);
if($situation){
	$formconfirm = $html1->formconfirm($_SERVER["PHP_SELF"].'?id='.$id.'&type='.$type,$langs->transnoentities("choixdescol"),'','confmod',
	array(array('label'=>$langs->transnoentities("Description") ,'type'=>'checkbox', 'name'=>'coldesc', 'value'=>false),
	array('label'=>$langs->transnoentities("vat") ,'type'=>'checkbox', 'name'=>'colvat', 'value'=>false),
	array('label'=>$langs->transnoentities("qte") ,'type'=>'checkbox', 'name'=>'colqte', 'value'=>false),
	array('label'=>$langs->transnoentities("Unit") ,'type'=>'checkbox', 'name'=>'colunit', 'value'=>false),
	array('label'=>$langs->transnoentities("puht") ,'type'=>'checkbox', 'name'=>'colpuht', 'value'=>false),
	array('label'=>$langs->transnoentities("reduc") ,'type'=>'checkbox', 'name'=>'colreduc', 'value'=>false),
	array('label'=>$langs->transnoentities("Progress") ,'type'=>'checkbox', 'name'=>'colprcava', 'value'=>true),
	array('label'=>$langs->transnoentities("champspe") ,'type'=>'checkbox', 'name'=>'colopt', 'value'=>false))
	,'no',1,300,500);
}
else{
	//$formconfirm = '';
	//$html1 = new Form($db);
	$formconfirm = $html1->formconfirm($_SERVER["PHP_SELF"].'?id='.$id.'&type='.$type,$langs->transnoentities("choixdescol"),'','confmod',
	array(array('label'=>$langs->transnoentities("Description") ,'type'=>'checkbox', 'name'=>'coldesc', 'value'=>false),
	array('label'=>$langs->transnoentities("vat") ,'type'=>'checkbox', 'name'=>'colvat', 'value'=>false),
	array('label'=>$langs->transnoentities("qte") ,'type'=>'checkbox', 'name'=>'colqte', 'value'=>false),
	array('label'=>$langs->transnoentities("Unit") ,'type'=>'checkbox', 'name'=>'colunit', 'value'=>false),
	array('label'=>$langs->transnoentities("puht") ,'type'=>'checkbox', 'name'=>'colpuht', 'value'=>false),
	array('label'=>$langs->transnoentities("reduc") ,'type'=>'checkbox', 'name'=>'colreduc', 'value'=>false),
	array('label'=>$langs->transnoentities("champspe") ,'type'=>'checkbox', 'name'=>'colopt', 'value'=>false))
	,'no',1,300,500);
}
if($_GET['action']=="choixcol"){
	print $formconfirm;
}
$colopt='view';
$coldesc='dolibarr_readonly';
$ro=1;
$colvat='disabled';
$colqte='disabled';
$colpuht='disabled';
$colreduc='disabled';
$colprcava='disabled';
$colunit='disabled';
if($_GET['coldesc']=="on"&$_GET['confirm']=="yes"){
	$coldesc='dolibarr_details';
	$ro=0;
}
if($_GET['colopt']=="on"&$_GET['confirm']=="yes"){
	$colopt='edit';
	$ro=0;
}
if($_GET['colvat']=="on"&$_GET['confirm']=="yes"){
	$colvat='';
}
if($_GET['colqte']=="on"&$_GET['confirm']=="yes"){
	$colqte='';
}
if($_GET['colunit']=="on"&$_GET['confirm']=="yes"){
	$colunit='';
}
if($_GET['colpuht']=="on"&$_GET['confirm']=="yes"){
	$colpuht='';
}
if($_GET['colreduc']=="on"&$_GET['confirm']=="yes"){
	$colreduc='';
}
if($_GET['colprcava']=="on"&$_GET['confirm']=="yes"){
	$colprcava='';
}
// Load object

dol_fiche_head($head, 'modmass', $langs->trans($type), 0, 'order');

 print '<table class="border" width="100%">'."\n";
// Ref
 print '<tr><td width="18%">'.$langs->trans("Ref").'</td><td colspan="3">';
 print $form->showrefnav($object,'ref','',1,'ref','ref');
 print "</td></tr></table></div>"."\n";
if($object->brouillon){
	echo '<a href="'.$_SERVER["PHP_SELF"].'?id='.$id.'&action=choixcol&type='.$type.'">'.$langs->transnoentities("choixdescol").'</a>';
	print '<form action="'.$pathact.'&action=modmass" method="post" id="formligne">'."\n";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">'."\n";
	print '<input type="hidden" name="action" value="modifline">'."\n";
	print '<table id="tablelines" class="noborder noshadow" width="100%">'."\n";
	print '<tr class="liste_titre nodrag nodrop">';
	print '<td width="'.$wcol1.'"><label for="">'.$langs->transnoentities("Description").'</label></td>';
	print '<td align="right" width="10%"><label for="vat">'.$langs->transnoentities("vat").'</label></td>'."\n";
	print '<td align="right" width="10%"><label for="qty">'.$langs->transnoentities("qte").'</label></td>'."\n";
	print '<td align="right" width="10%"><label for="qty">'.$langs->transnoentities("Unit").'</label></td>'."\n";
	print '<td align="right" width="10%"><label for="puht">'.$langs->transnoentities("puht").'</label></td>'."\n";
	print '<td align="right" width="10%"><label for="reduc">'.$langs->transnoentities("reduc").'</label></td>'."\n";
	if($situation){
		print '<td align="right" width="10%"><label for="prcava">'.$langs->transnoentities("Progress").'</label></td>'."\n";
	}
	print '<td align="right" width="10%"><label for="totht">'.$langs->transnoentities("totalht").'</label></td></tr>'."\n";
	$i = 1;
	$nbclass = 1;
	$tabindex=0;
	foreach ($object->lines as $line) {
		if($line->product_type<>9){
			$tabindex+=1;
			if ($nbclass==0){
				$classe='pair';
			}
			else{
				$classe='impair';
			}
			$i=$line->id;
			$productstatic->id=$line->fk_product;
			$productstatic->ref=$line->product_ref;
			$productstatic->type=$line->product_type;
			$extrafields = new ExtraFields($db);
			$extralabels=$extrafields->fetch_name_optionals_label($line->table_element);
			$line->fetch_optionals($line->rowid,$extralabels);
		
			if($line->product_ref<>''){
				
				print '<tr class='.$classe.'><td>'.$productstatic->getNomUrl(1,'',16).'-'.$line->product_label.'</td></tr>';
			}
			else{
				//print '<tr class='.$classe.'><td>'.$line->desc.'</td></tr>';
			}
			//Description
			print '<tr class='.$classe.'><td width="'.$wcol1.'">';
			$doleditor=new DolEditor('product_desc'.$i,$line->desc,'',100,$coldesc,'In',false,true,$enable,$nbrows,100,$ro);
			$doleditor->Create();
			//TVA
			print '</td>';
			print '<td align="right" width="10%" id="vat'.$i.'"><input '.$colvat.' style="text-align : right;" name="vat'.$i.'" id="inputvat'.$i.'" value="'.$line->tva_tx.'" tabindex='.$tabindex.'></td>'."\n";
			$tabindex+=1;
			if(!empty($colvat)){
				print '<input type="hidden" name="vat'.$i.'" value="'.$line->tva_tx.'">'."\n";
			}
			//QTE
			print '<td align="right" width="10%" id="tdqty'.$i.'"><input '.$colqte.' style="text-align : right;" name="qty'.$i.'" id="inputqte'.$i.'" value="'.$line->qty.'" onblur="calculprice('.$i.')" tabindex='.$tabindex.'></td>'."\n";
			$tabindex+=1;
			if(!empty($colqte)){
				print '<input type="hidden" name="qty'.$i.'" value="'.$line->qty.'">'."\n";
			}
			//UNITE
			print '<td align="right" width="10%" id="tdunit'.$i.'">';
			$empty=1;
			if($line->fk_unit){
				$empty=0;
			}
			print jth_selectUnits($line->fk_unit, "units".$i, $empty, $colunit, $tabindex);
			print '</td>'."\n";
			$tabindex+=1;
			if(!empty($colunit)){
				print '<input type="hidden" name="units'.$i.'" value="'.$line->fk_unit.'">'."\n";
			}
			//PUHT
			print '<td align="right" width="10%" id="tdpuht'.$i.'"><input '.$colpuht.' style="text-align : right;" name="puht'.$i.'" id="inputpht'.$i.'" value="'.price2num($line->subprice).'" onblur="calculprice('.$i.')" tabindex='.$tabindex.'></td>'."\n";
			$tabindex+=1;
			if(!empty($colpuht)){
				print '<input type="hidden" name="puht'.$i.'" value="'.price2num($line->subprice).'">'."\n";
			}
			//Remise
			print '<td align="right" width="10%" id="reduc'.$i.'"><input '.$colreduc.' style="text-align : right;" name="reduc'.$i.'" id="inputreduc'.$i.'" value="'.$line->remise_percent.'" onblur="calculprice('.$i.')" tabindex='.$tabindex.'></td>'."\n";
			if(!empty($colreduc)){
				print '<input type="hidden" name="reduc'.$i.'" value="'.$line->remise_percent.'">'."\n";
			}
			//Cas des situation Progression
			if($situation){
				$tabindex+=1;
				print '<td align="right" width="10%" id="prcava'.$i.'"><input '.$colprcava.' style="text-align : right;" name="prcava'.$i.'" id="inputprcava'.$i.'" value="'.$line->situation_percent.'" tabindex='.$tabindex.'></td>'."\n";
				
			}
			print '<td align="right" width="10%" id="tdtotht'.$i.'" style="font-weight: bold;" name="tdtotht">'.price2num($line->total_ht,'MU').'</td></tr>'."\n";
			print '<tr class='.$classe.'>'.$line->showOptionals($extrafields,$colopt,0,$i).'</tr>'."\n";
			if ($nbclass>0){
				$nbclass-=1;
			}
			else{
				$nbclass+=1;
			}
		}
		else{
			if($line->desc<>''){
				print '<tr class="liste_titre"><td colspan=9>'.$line->desc.'</td></tr>';
			}
		}
	}
	print '<tr class="pair"><td></td>'."\n";
	print '<td align="right" width="30"></td>'."\n";
	print '<td align="right" width="30"></td>'."\n";
	print '<td align="right" width="30"></td>'."\n";
	print '<td align="right" width="30"></td>'."\n";
	print '<td align="right" width="30" style="font-weight: bold;">'.$langs->transnoentities("total").'</td>'."\n";
	print '<td align="right" width="30" id="total" style="font-weight: bold;">'.price2num($object->total_ht).'</td></tr>'."\n";
	print '</table>'."\n";
	print '<table width="100%"><tr><td align="center"><input type="submit" class="butAction" value="'.$langs->transnoentities("Modifier").'" tabindex='.$tabindex.'>&nbsp;</td></tr></table>';
	print '</form>';
}
llxFooter();
?>
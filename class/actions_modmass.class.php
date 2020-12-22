<?php


class ActionsModmass
{ 
	/**
	 * Overloading the doActions function : replacing the parent's function with the one below
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    &$object        The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          &$action        Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	
	function addMoreActionsButtons($parameters, &$object, &$action, $hookmanager)
	{
		global $langs;
		global $db;
		global $user;
		$langs->load("modmass@modmass");
		if (in_array('propalcard', explode(':', $parameters['context'])))
		{	
			if($object->brouillon && $user->rights->modmass->writepropalprcglob){
				print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=prcglobal">'.$langs->transnoentities("appprcglob").'</a></div>';
				$popup= new Form($db);
				if($action=="prcglobal"){
					print $popup->form_confirm($_SERVER["PHP_SELF"].'?id='.$object->id,$langs->transnoentities("appprcglob"),$langs->transnoentities("appprcglobtxt"),'appprc',array(array('label'=>'%','type'=>'text','name'=>'prcapp','value'=>'0')),'no',1,300,500);
				}
			}
		}
 
	}
	function doActions($parameters, &$object, &$action, $hookmanager)
	{
		global $langs;
		$langs->load("modmass@modmass");
		if (in_array('invoicecard', explode(':', $parameters['context'])))
		{
			if($action=='modmass'){
				global $db;
				$extrafieldsline = new ExtraFields($db);
				foreach ($object->lines as $line) {
					if($line->product_type<>9){
						$qty=$_POST['qty'.$line->rowid];
						$description=$_POST['product_desc'.$line->rowid];
						$pu_ht=$_POST['puht'.$line->rowid];
						$remise=$_POST['reduc'.$line->rowid];
						$date_start=$line->date_start;
						$date_end=$line->date_end;
						$vat_rate=$_POST['vat'.$line->rowid];
						$localtax1_rate=$line->localtax1_tx;
						$localtax2_rate=$line->localtax2_tx;
						$info_bits=$line->info_bits;
						$type=$line->product_type;
						$parentline=$line->fk_parent_line;
						$fournprice=$line->fk_fournprice;
						$buyingprice=$line->pa_ht ;
						$label=$line->label;
						$special_code=$line->special_code;
						$skip=$line->skip_update_total;
						$label=$line->label;
						if($object->type==5){
							$situation_percent=$_POST['prcava'.$line->rowid];
						}
						else{
							$situation_percent=100;
						}
						$fkunit=$_POST['units'.$line->rowid];
						// Extrafields
						$extralabelsline = $extrafieldsline->fetch_name_optionals_label($object->table_element_line);
						$array_options = $extrafieldsline->getOptionalsFromPost($extralabelsline,$line->rowid);
						
						$result = $object->updateline($line->rowid, $description, $pu_ht, $qty, $remise, $date_start, $date_end, $vat_rate,$localtax1,$localtax2,'HT',$info_bits,$type,$parentline,0,$fournprice,$buyingprice,$label,$special_code, $array_options,$situation_percent, $fkunit);
					}
					
				}
			}
		}
		if (in_array('propalcard', explode(':', $parameters['context'])))
		{
			if($action=='modmass'){
				global $db;
				$extrafieldsline = new ExtraFields($db);
				foreach ($object->lines as $line) {
					if($line->product_type<>9){
						$qty=$_POST['qty'.$line->rowid];
						$description=$_POST['product_desc'.$line->rowid];
						$pu_ht=$_POST['puht'.$line->rowid];
						$remise=$_POST['reduc'.$line->rowid];
						$date_start=$line->date_start;
						$date_end=$line->date_end;
						$vat_rate=$_POST['vat'.$line->rowid];
						$localtax1_rate=$line->localtax1_tx;
						$localtax2_rate=$line->localtax2_tx;
						$info_bits=$line->info_bits;
						$type=$line->product_type;
						$parentline=$line->fk_parent_line;
						$fournprice=$line->fk_fournprice;
						$buyingprice=$line->pa_ht ;
						$label=$line->label;
						$special_code=$line->special_code;
						$skip=$line->skip_update_total;
						$label=$line->label;
						$fkunit=$_POST['units'.$line->rowid];
						// Extrafields
						$extralabelsline = $extrafieldsline->fetch_name_optionals_label($object->table_element_line);
						$array_options = $extrafieldsline->getOptionalsFromPost($extralabelsline,$line->rowid);
						$result = $object->updateline($line->rowid, $pu_ht, $qty, $remise, $vat_rate, $localtax1_rate, $localtax2_rate, $description, 'HT' ,$info_bits ,$special_code,$parentline, $skip,$fournprice, $buyingprice,$label,$type,$date_start, $date_end, $array_option, $fkunit);
					}
				}
			}
			elseif($action=='appprc'){
				global $db;
				foreach ($object->lines as $line) {
					if($line->product_type<>9){
						$qty=$line->qty;
						$description=$line->desc;
						$pu_ht=$line->subprice*(100 + $_GET['prcapp']) / 100;
						$remise=$line->remise_percent;
						$date_start=$line->date_start;
						$date_end=$line->date_end;
						$vat_rate=$line->tva_tx;
						$localtax1_rate=$line->localtax1_tx;
						$localtax2_rate=$line->localtax2_tx;
						$info_bits=$line->info_bits;
						$type=$line->product_type;
						$parentline=$line->fk_parent_line;
						$fournprice=$line->fk_fournprice;
						$buyingprice=$line->pa_ht ;
						$label=$line->label;
						$special_code=$line->special_code;
						$skip=$line->skip_update_total;
						$label=$line->label;
						
						$result = $object->updateline($line->rowid, $pu_ht, $qty, $remise, $vat_rate, $localtax1_rate, $localtax2_rate, $description, 'HT' ,$info_bits ,$special_code,$parentline, $skip,$fournprice, $buyingprice,$label,$type,$date_start, $date_end, $array_option);
					}
				}
				
			}
		}
		if (in_array('supplier_proposalcard', explode(':', $parameters['context'])))
		{
			if($action=='modmass'){
				global $db;
				$extrafieldsline = new ExtraFields($db);
				foreach ($object->lines as $line) {
					if($line->product_type<>9){
						$qty=$_POST['qty'.$line->rowid];
						$description=$_POST['product_desc'.$line->rowid];
						$pu_ht=$_POST['puht'.$line->rowid];
						$remise=$_POST['reduc'.$line->rowid];
						$date_start=$line->date_start;
						$date_end=$line->date_end;
						$vat_rate=$_POST['vat'.$line->rowid];
						$localtax1_rate=$line->localtax1_tx;
						$localtax2_rate=$line->localtax2_tx;
						$info_bits=$line->info_bits;
						$type=$line->product_type;
						$parentline=$line->fk_parent_line;
						$fournprice=$line->fk_fournprice;
						$buyingprice=$line->pa_ht ;
						$label=$line->label;
						$special_code=$line->special_code;
						$skip=$line->skip_update_total;
						$label=$line->label;
						$reffourn=$line->ref_fourn;
						$fkunit=$_POST['units'.$line->rowid];
						// Extrafields
						$extralabelsline = $extrafieldsline->fetch_name_optionals_label($object->table_element_line);
						$array_options = $extrafieldsline->getOptionalsFromPost($extralabelsline,$line->rowid);
						$result = $object->updateline($line->rowid, $pu_ht, $qty, $remise, $vat_rate, $localtax1_rate, $localtax2_rate, $description, 'HT' ,$info_bits ,$special_code,$parentline, $skip,$fournprice, $buyingprice,$label,$type, $array_option, $reffourn, $fkunit);
					}
				}
			}
		}
		if (in_array('ordersuppliercard', explode(':', $parameters['context'])))
		{
			if($action=='modmass'){
				global $db;
				$extrafieldsline = new ExtraFields($db);
				foreach ($object->lines as $line) {
					if($line->product_type<>9){
						$qty=$_POST['qty'.$line->id];
						$description=$_POST['product_desc'.$line->rowid];
						$pu_ht=$_POST['puht'.$line->id];
						$remise=$_POST['reduc'.$line->id];
						$date_start=$line->date_start;
						$date_end=$line->date_end;
						$vat_rate=$_POST['vat'.$line->id];
						$localtax1_rate=$line->localtax1_tx;
						$localtax2_rate=$line->localtax2_tx;
						$info_bits=$line->info_bits;
						$type=$line->product_type;
						$parentline=$line->fk_parent_line;
						$fournprice=$line->fk_fournprice;
						$buyingprice=$line->pa_ht ;
						$label=$line->label;
						$special_code=$line->special_code;
						$skip=$line->skip_update_total;
						$label=$line->label;
						$reffourn=$line->ref_supplier;
						$puhtdev=$line->multicurrency_subprice;
						$fkunit=$_POST['units'.$line->id];
						// Extrafields
						$extralabelsline = $extrafieldsline->fetch_name_optionals_label($object->table_element_line);
						$array_options = $extrafieldsline->getOptionalsFromPost($extralabelsline,$line->rowid);
						$result = $object->updateline($line->id, $description, $pu_ht, $qty, $remise, $vat_rate, $localtax1_rate, $localtax2_rate, 'HT' ,$info_bits , $type, 0, $date_start, $date_end, $array_option, $fkunit, $puhtdev, $reffourn);
					}
				}
			}
		}
		if (in_array('invoicesuppliercard', explode(':', $parameters['context'])))
		{
			if($action=='modmass'){
				global $db;
				$extrafieldsline = new ExtraFields($db);
				foreach ($object->lines as $line) {
					if($line->product_type<>9){
						$qty=$_POST['qty'.$line->rowid];
						$description=$_POST['product_desc'.$line->rowid];
						$pu_ht=$_POST['puht'.$line->rowid];
						$remise=$_POST['reduc'.$line->rowid];
						$date_start=$line->date_start;
						$date_end=$line->date_end;
						$vat_rate=$_POST['vat'.$line->rowid];
						$localtax1_rate=$line->localtax1_tx;
						$localtax2_rate=$line->localtax2_tx;
						$info_bits=$line->info_bits;
						$type=$line->product_type;
						$parentline=$line->fk_parent_line;
						$fournprice=$line->fk_fournprice;
						$buyingprice=$line->pa_ht ;
						$label=$line->label;
						$special_code=$line->special_code;
						$skip=$line->skip_update_total;
						$label=$line->label;
						$reffourn=$line->ref_supplier;
						$puhtdev=$line->multicurrency_subprice;
						$fkunit=$_POST['units'.$line->rowid];
						$idprd=$line->fk_product;
						// Extrafields
						$extralabelsline = $extrafieldsline->fetch_name_optionals_label($object->table_element_line);
						$array_options = $extrafieldsline->getOptionalsFromPost($extralabelsline,$line->rowid);
						$result = $object->updateline($line->rowid, $description, $pu_ht, $vat_rate, $localtax1_rate, $localtax2_rate, $qty, $idprd, 'HT' ,$info_bits , $type,  $remise, 0, $date_start, $date_end, $array_option, $fkunit, $puhtdev);
					}
				}
			}
		}
	}
}
?>
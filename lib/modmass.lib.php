<?php
function jth_selectUnits($selected = '', $htmlname = 'units', $showempty=0, $enable='', $tabindex='')
    {
      global $langs, $db;
  
      $langs->load('products');
  
      $return= '<select class="flat" id="'.$htmlname.'" name="'.$htmlname.'" tabindex="'.$tabindex.'" '.$enable.'>';
  
      $sql = 'SELECT rowid, label, code from '.MAIN_DB_PREFIX.'c_units';
      $sql.= ' WHERE active > 0';
  
      $resql = $db->query($sql);
      if($resql && $db->num_rows($resql) > 0)
      {
        if ($showempty) $return .= '<option value="none"></option>';
  
        while($res = $db->fetch_object($resql))
        {
          if ($selected == $res->rowid)
          {
            $return.='<option value="'.$res->rowid.'" selected>'.($langs->trans('unit'.$res->code)!=$res->label?$langs->trans('unit'.$res->code):$res->label).'</option>';
          }
          else
         {
           $return.='<option value="'.$res->rowid.'">'.($langs->trans('unit'.$res->code)!=$res->label?$langs->trans('unit'.$res->code):$res->label).'</option>';
        }
       }
       $return.='</select>';
    }
     return $return;
   }
?>
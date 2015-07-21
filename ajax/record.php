<?php
/*
 -------------------------------------------------------------------------
 Printercounters plugin for GLPI
 Copyright (C) 2014 by the Printercounters Development Team.
 -------------------------------------------------------------------------

 LICENSE

 This file is part of Printercounters.

 Printercounters is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 Printercounters is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with Printercounters. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------  */

define('GLPI_ROOT', '../../..');

include (GLPI_ROOT."/inc/includes.php");

Session::checkLoginUser();
//Html::header_nocache();

$_POST['itemtype'] = isset($_POST['itemtype']) ? $_POST['itemtype'] : 'printer';
$_POST['items_id'] = isset($_POST['items_id']) ? $_POST['items_id'] : 0;
$record = new PluginPrintercountersRecord($_POST['itemtype'], $_POST['items_id']);

if (!isset($_POST['action'])) {
   $_POST['action'] = $_GET['action'];
}

switch($_POST['action']){
   case 'immediateRecord':
      header('Content-Type: application/json; charset=UTF-8"');
      list($messages, $error) = $record->immediateRecord($_POST['items_id'], $_POST['itemtype']);
      echo json_encode(array('message'    => implode('</br>', $messages), 
                             'error'      => $error));
      break;
   
   case 'showManualRecord':
      header("Content-Type: text/html; charset=UTF-8");
      $record->showManualRecord($_POST['items_id'], $_POST['itemtype'], $_POST['records_id'], $_POST['rand'], $_POST['addLowerRecord']);
      break;
   
   case 'setManualRecord':
      header('Content-Type: application/json; charset=UTF-8"');
      list($messages, $error) = $record->setManualRecord($_POST['items_id'], $_POST['itemtype'], $_POST['counters'], $_POST['records_id'], $_POST['addLowerRecord']);
      echo json_encode(array('message'    => implode('</br>', $messages), 
                             'error'      => $error));
      break;
   
   case 'SNMPSet':
      header('Content-Type: application/json; charset=UTF-8"');
      $snmpset = new PluginPrintercountersSnmpset();
      list($messages, $error) = $snmpset->snmpSet($_POST['items_id'], $_POST['itemtype']);
      echo json_encode(array('message'    => implode('</br>', $messages), 
                             'error'      => $error));
      break;
   
   case 'updateGlobalTco':
      header('Content-Type: application/json; charset=UTF-8"');
      list($messages, $result, $error) = $record->updateGlobalTco($_POST['items_id'], $_POST['itemtype']);
      echo json_encode(array('result'    => $result,
                             'message'   => $messages, 
                             'error'     => $error));
      break;
   
   case 'updateCounterPosition':
      header('Content-Type: application/json; charset=UTF-8"');
      list($messages, $error) = $record->updateCounterPosition($_POST['items_id'], $_POST['itemtype']);
      echo json_encode(array('message'    => implode('</br>', $messages), 
                             'error'      => $error));
      break;
   
   case 'initAjaxMassiveAction':
      header("Content-Type: text/html; charset=UTF-8");
      Html::header(__('Printer'), $_SERVER['PHP_SELF']);
      $item_recordmodel = new PluginPrintercountersItem_Recordmodel();
      $item_recordmodel->initMassiveActionsProcess();
      Html::footer();
      break;
   
   case 'ajaxMassiveAction':
      header("Content-Type: text/html; charset=UTF-8");
      $item_recordmodel = new PluginPrintercountersItem_Recordmodel();
      $item_recordmodel->doMassiveActionProcess();
      break;
   
   case 'ajaxMassiveActionTimeOut':
      header("Content-Type: text/html; charset=UTF-8");
      $item_recordmodel = new PluginPrintercountersItem_Recordmodel();
      $item_recordmodel->massiveActionTimeOut();
      break;
}

?>
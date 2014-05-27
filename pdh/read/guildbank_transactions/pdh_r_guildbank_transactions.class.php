<?php
/*
 * Project:     EQdkp GuildBank
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2005
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2005-2014 Wallenium
 * @link        http://eqdkp-plus.com
 * @package     guildbank
 * @version     $Rev$
 *
 * $Id$
 */

if (!defined('EQDKP_INC'))
{
  die('Do not access this file directly.');
}

if (!class_exists('pdh_r_guildbank_transactions')){
	class pdh_r_guildbank_transactions extends pdh_r_generic{

		public static function __shortcuts() {
			$shortcuts = array('money' => 'gb_money');
			return array_merge(parent::$shortcuts, $shortcuts);
		}

		private $data;
		private $startvalues;
		private $summ;
		private $itemcost;

		public $hooks = array(
			'guildbank_items_update'
		);

		public $presets = array(
			'gb_tdate'		=> array('date',		array('%trans_id%'), array()),
			'gb_titem_itt'	=> array('item_itt',	array('%trans_id%', '%itt_lang%', '%itt_direct%', '%onlyicon%', '%noicon%'), array()),
			'gb_titem'		=> array('item',		array('%trans_id%'), array()),
			'gb_tbuyer'		=> array('char',		array('%trans_id%'), array()),
			'gb_tsubject'	=> array('subject',		array('%trans_id%'), array()),
			'gb_tbanker'	=> array('banker',		array('%trans_id%'), array()),
			'gb_tdkp'		=> array('dkp',			array('%trans_id%'), array()),
			'gb_tvalue'		=> array('value',		array('%trans_id%'), array()),
			'gb_tedit'		=> array('edit',		array('%trans_id%'), array()),
		);

		public function reset(){
			$this->pdc->del('pdh_guildbank_ta_table.transactions');
			$this->pdc->del('pdh_guildbank_ta_table.startvalues');
			$this->pdc->del('pdh_guildbank_ta_table.summ');
			$this->pdc->del('pdh_guildbank_ta_table.itemcost');
			unset($this->data);
			unset($this->startvalues);
			unset($this->summ);
			unset($this->itemcost);
		}

		public function init(){
			// try to get from cache first
			$this->data			= $this->pdc->get('pdh_guildbank_ta_table.transactions');
			$this->startvalues	= $this->pdc->get('pdh_guildbank_ta_table.startvalues');
			$this->summ			= $this->pdc->get('pdh_guildbank_ta_table.summ');
			$this->itemcost		= $this->pdc->get('pdh_guildbank_ta_table.itemcost');
			if($this->data !== NULL && $this->startvalues !== NULL && $this->summ !== NULL && $this->itemcost !== NULL){
				return true;
			}

			// empty array as default
			$this->data = $this->startvalues = $this->summ = $this->itemcost = array();

			$sql = 'SELECT * FROM `__guildbank_transactions` ORDER BY ta_id ASC;';
			$result = $this->db->query($sql);
			if ($result){
				// add row by row to local copy
				while (($row = $result->fetchAssoc())){
					$this->data[(int)$row['ta_id']] = array(
						'id'			=> (int)$row['ta_id'],
						'banker'		=> (int)$row['ta_banker'],
						'char'			=> (int)$row['ta_char'],
						'item'			=> (int)$row['ta_item'],
						'dkp'			=> (int)$row['ta_dkp'],
						'value'			=> (int)$row['ta_value'],
						'subject'		=> $row['ta_subject'],
						'date'			=> (int)$row['ta_date'],
						'startvalue'	=> (int)$row['ta_startvalue'],
					);
					$this->summ[(int)(int)$row['ta_banker']] += (int)$row['ta_value'];
					$this->startvalues[(int)$row['ta_startvalue']] = $row['ta_value'];
					if((int)$row['ta_item'] > 0){
						$this->itemcost[(int)$row['ta_item']] = $row['ta_value'];
					}
				}
				#$this->db->free_result($result);
			}

			// add data to cache
			$this->pdc->put('pdh_guildbank_ta_table.transactions',	$this->data,		null);
			$this->pdc->put('pdh_guildbank_ta_table.startvalues',	$this->startvalues,	null);
			$this->pdc->put('pdh_guildbank_ta_table.summ',			$this->summ,		null);
			$this->pdc->put('pdh_guildbank_ta_table.itemcost',		$this->itemcost,	null);
			return true;
		}

		public function get_id_list(){
			if (is_array($this->data)){
				return array_keys($this->data);
			}
			return array();
		}

		public function get_char($id, $raw=false){
			if($raw){
				return (isset($this->data[$id]) && $this->data[$id]['char'] > 0) ? $this->data[$id]['char'] : 0;
			}
			return (isset($this->data[$id]) && $this->data[$id]['char'] > 0) ? $this->pdh->get('member', 'name', array($this->data[$id]['char'])) : '--';
		}

		public function get_banker($id, $raw=false){
			if($raw){
				return (isset($this->data[$id]) && $this->data[$id]['banker'] > 0) ? $this->data[$id]['banker'] : 0;
			}
			return (isset($this->data[$id]) && $this->data[$id]['char'] > 0) ? $this->pdh->get('guildbank_banker', 'name', array($this->data[$id]['banker'])) : '--';
		}

		public function get_item($id, $raw=false){
			if($raw){
				return (isset($this->data[$id]) && $this->data[$id]['item'] > 0) ? $this->data[$id]['item'] : 0;
			}
			return (isset($this->data[$id]) && $this->data[$id]['item'] > 0) ? $this->pdh->get('guildbank_items', 'name', array($this->data[$id]['item'])) : '--';
		}

		public function get_item_itt($id, $lang=false, $direct=0, $onlyicon=0, $noicon=false, $in_span=false) {
			if(isset($this->data[$id]) && $this->data[$id]['item'] > 0){
				return $this->pdh->get('guildbank_items', 'itt_itemname', array($this->data[$id]['item'], $lang, $direct, $onlyicon, $noicon, $in_span));
			}
			return '--';
		}

		public function get_value($id, $raw=false){
			if($raw){
				return (isset($this->data[$id]) && $this->data[$id]['value'] > 0) ? $this->data[$id]['value'] : 0;
			}
			return $this->money->fields($this->data[$id]['value']);
		}

		public function get_itemvalue($itemid){
			return (isset($this->itemcost[$itemid]) && $this->itemcost[$itemid] > 0) ? $this->itemcost[$itemid] : 0;
		}

		public function get_transaction_id($itemid){
			if(is_array($this->data) && count($this->data) > 0){
				foreach($this->data as $ta_data){
					if($ta_data['item'] == $itemid){
						return $itemid;
					}
				}
			}
			return 0;
		}

		public function get_money_summ($bankid){
			return (isset($this->summ[$bankid]) && $this->summ[$bankid] > 0) ? $this->summ[$bankid] : 0;
		}

		public function get_money_summ_all(){
			return array_sum($this->summ);
		}

		public function get_money($bankid){
			return (isset($this->startvalues[$bankid]) && $this->startvalues[$bankid] > 0) ? $this->startvalues[$bankid] : 0;
		}

		public function get_dkp($id){
			return (isset($this->data[$id]) && $this->data[$id]['dkp'] > 0) ? $this->data[$id]['dkp'] : 0;
		}
		
		public function get_deletename($id){
			if($id > 0){
				return $this->get_subject($id).' - '.$this->get_item($id);
			}
			return 'undefined';
		}

		public function get_itemdkp($itemid){
			if(is_array($this->data) && count($this->data) > 0){
				foreach($this->data as $ta_data){
					if($ta_data['item'] == $itemid){
						return (isset($this->data[$ta_data['id']]) && $this->data[$ta_data['id']]['dkp'] > 0) ? $this->data[$ta_data['id']]['dkp'] : 0;
					}
				}
			}
		}

		public function get_startvalue($id){
			return (isset($this->data[$id]) && $this->data[$id]['startvalue'] > 0) ? $this->data[$id]['startvalue'] : 0;
		}

		public function get_date($id, $raw=false){
			if($raw){
				return (isset($this->data[$id]) && $this->data[$id]['date'] > 0) ? $this->data[$id]['date'] : 0;
			}
			return (isset($this->data[$id]) && $this->data[$id]['date'] > 0) ? $this->time->user_date($this->data[$id]['date']) : '--';
		}

		public function get_subject($id){
			return (isset($this->data[$id]) && $this->data[$id]['subject']) ? (($this->user->lang($this->data[$id]['subject'])) ? $this->user->lang($this->data[$id]['subject']) : $this->data[$id]['subject']) : 'undefined';
		}

		public function get_edit($id){
			$mode	= ($this->get_item($id, true) > 0) ? 'edit_item' : 'edit_transaction';
			$myid 	= ($this->get_item($id, true) > 0) ? $this->get_item($id, true) : $id;
			return '<a href="javascript:'.$mode.'(\''.$myid.'\');"><i class="fa fa-pencil fa-lg" title="'.$this->user->lang('edit').'"></i></a>';
		}
  } //end class
} //end if class not exists
?>

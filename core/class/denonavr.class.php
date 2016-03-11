<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class denonavr extends eqLogic {
	/*     * *************************Attributs****************************** */

	/*     * ***********************Methode static*************************** */

	public static function cron15() {
		foreach (eqLogic::byType('denonavr', true) as $eqLogic) {
			$eqLogic->updateInfo();
		}
	}

	/*     * *********************Méthodes d'instance************************* */

	public function preInsert() {
		$this->setCategory('multimedia', 1);
	}

	public function preUpdate() {
		if ($this->getConfiguration('ip') == '') {
			throw new Exception(__('Le champs IP ne peut etre vide', __FILE__));
		}
	}

	public function postSave() {
		$cmd = $this->getCmd(null, 'power_state');
		if (!is_object($cmd)) {
			$cmd = new denonavrCmd();
			$cmd->setLogicalId('power_state');
			$cmd->setIsVisible(1);
			$cmd->setName(__('Etat', __FILE__));
		}
		$cmd->setType('info');
		$cmd->setSubType('binary');
		$cmd->setEqLogic_id($this->getId());
		$cmd->setDisplay('generic_type', 'ENERGY_STATE');
		$cmd->save();

		$cmd = $this->getCmd(null, 'input');
		if (!is_object($cmd)) {
			$cmd = new denonavrCmd();
			$cmd->setLogicalId('input');
			$cmd->setIsVisible(1);
			$cmd->setName(__('Entrée', __FILE__));
		}
		$cmd->setType('info');
		$cmd->setSubType('string');
		$cmd->setEqLogic_id($this->getId());
		$cmd->setDisplay('generic_type', 'GENERIC');
		$cmd->save();

		$cmd = $this->getCmd(null, 'volume');
		if (!is_object($cmd)) {
			$cmd = new denonavrCmd();
			$cmd->setLogicalId('volume');
			$cmd->setIsVisible(1);
			$cmd->setName(__('Volume', __FILE__));
		}
		$cmd->setType('info');
		$cmd->setSubType('numeric');
		$cmd->setEqLogic_id($this->getId());
		$cmd->setTemplate('dashboard', 'tile');
		$cmd->setTemplate('mobile', 'tile');
		$cmd->setUnite('dB');
		$cmd->setDisplay('generic_type', 'LIGHT_STATE');
		$cmd->save();

		$cmd = $this->getCmd(null, 'sound_mode');
		if (!is_object($cmd)) {
			$cmd = new denonavrCmd();
			$cmd->setLogicalId('sound_mode');
			$cmd->setIsVisible(1);
			$cmd->setName(__('Audio', __FILE__));
		}
		$cmd->setType('info');
		$cmd->setSubType('string');
		$cmd->setEqLogic_id($this->getId());
		$cmd->setDisplay('generic_type', 'GENERIC');
		$cmd->save();

		$cmd = $this->getCmd(null, 'volume_m');
		if (!is_object($cmd)) {
			$cmd = new denonavrCmd();
			$cmd->setLogicalId('volume_m');
			$cmd->setName(__('Volume-', __FILE__));
			$cmd->setIsVisible(1);
		}
		$cmd->setType('action');
		$cmd->setSubType('other');
		$cmd->setEqLogic_id($this->getId());
		$cmd->save();

		$cmd = $this->getCmd(null, 'volume_p');
		if (!is_object($cmd)) {
			$cmd = new denonavrCmd();
			$cmd->setLogicalId('volume_p');
			$cmd->setName(__('Volume+', __FILE__));
			$cmd->setIsVisible(1);
		}
		$cmd->setType('action');
		$cmd->setSubType('other');
		$cmd->setEqLogic_id($this->getId());
		$cmd->save();

		$cmd = $this->getCmd(null, 'on');
		if (!is_object($cmd)) {
			$cmd = new denonavrCmd();
			$cmd->setLogicalId('on');
			$cmd->setName(__('On', __FILE__));
			$cmd->setIsVisible(1);
		}
		$cmd->setType('action');
		$cmd->setSubType('other');
		$cmd->setEqLogic_id($this->getId());
		$cmd->setDisplay('generic_type', 'ENERGY_ON');
		$cmd->save();

		$cmd = $this->getCmd(null, 'off');
		if (!is_object($cmd)) {
			$cmd = new denonavrCmd();
			$cmd->setLogicalId('off');
			$cmd->setName(__('Off', __FILE__));
			$cmd->setIsVisible(1);
		}
		$cmd->setType('action');
		$cmd->setSubType('other');
		$cmd->setEqLogic_id($this->getId());
		$cmd->setDisplay('generic_type', 'ENERGY_OFF');
		$cmd->save();

		$cmd = $this->getCmd(null, 'refresh');
		if (!is_object($cmd)) {
			$cmd = new denonavrCmd();
			$cmd->setLogicalId('refresh');
			$cmd->setName(__('Rafraîchir', __FILE__));
			$cmd->setIsVisible(1);
		}
		$cmd->setType('action');
		$cmd->setSubType('other');
		$cmd->setEqLogic_id($this->getId());
		$cmd->save();

		$cmd = $this->getCmd(null, 'mute');
		if (!is_object($cmd)) {
			$cmd = new denonavrCmd();
			$cmd->setLogicalId('mute');
			$cmd->setName(__('Muet', __FILE__));
			$cmd->setIsVisible(1);
		}
		$cmd->setType('action');
		$cmd->setSubType('other');
		$cmd->setEqLogic_id($this->getId());
		$cmd->save();

		$convert = array('3' => '2', '8' => '2', '9' => '2', '6' => '5', '11' => '5', '12' => '5', '13' => '5');

		$inputModel = array(
			'1' => array(
				'SAT/CBL' => 'CBL/SAT',
				'DVD' => 'DVD/Blu-ray',
				'BD' => 'Blu-ray',
				'GAME' => 'Game',
				'AUX1' => 'AUX',
				'MPLAY' => 'Media Player',
				'USB/IPOD' => 'iPod/USB',
				'TV' => 'TV Audio',
				'TUNER' => 'Tuner',
				'NETHOME' => 'Online Music',
				'BT' => 'Bluetooth',
				'IRP' => 'Internet Radio',
			),
			'7' => array(
				'SAT/CBL' => 'CBL/SAT',
				'DVD' => 'DVD/Blu-ray',
				'GAME' => 'Game',
				'AUX1' => 'AUX',
				'MPLAY' => 'Media Player',
				'USB/IPOD' => 'iPod/USB',
				'TV' => 'TV Audio',
				'TUNER' => 'Tuner',
				'NETHOME' => 'Online Music',
				'BT' => 'Bluetooth',
				'IRP' => 'Internet Radio',
				'CD' => 'CD',
			),
			'2' => array(
				'SAT/CBL' => 'CBL/SAT',
				'DVD' => 'DVD/Blu-ray',
				'BD' => 'Blu-ray',
				'GAME' => 'Game',
				'AUX1' => 'AUX1',
				'AUX2' => 'AUX2',
				'MPLAY' => 'Media Player',
				'USB/IPOD' => 'iPod/USB',
				'TV' => 'TV Audio',
				'TUNER' => 'Tuner',
				'NETHOME' => 'Online Music',
				'BT' => 'Bluetooth',
				'IRP' => 'Internet Radio',
				'CD' => 'CD',
				'SERVER' => 'Media Server',
			),
			'10' => array(
				'SAT/CBL' => 'CBL/SAT',
				'DVD' => 'DVD/Blu-ray',
				'BD' => 'Blu-ray',
				'GAME' => 'Game',
				'AUX1' => 'AUX1',
				'AUX2' => 'AUX2',
				'MPLAY' => 'Media Player',
				'USB/IPOD' => 'iPod/USB',
				'TV' => 'TV Audio',
				'TUNER' => 'Tuner',
				'NETHOME' => 'Online Music',
				'BT' => 'Bluetooth',
				'IRP' => 'Internet Radio',
				'CD' => 'CD',
				'PHONO' => 'Phono',
			),
			'4' => array(
				'SAT/CBL' => 'CBL/SAT',
				'DVD' => 'DVD/Blu-ray',
				'BD' => 'Blu-ray',
				'GAME' => 'Game',
				'AUX1' => 'AUX1',
				'AUX2' => 'AUX2',
				'MPLAY' => 'Media Player',
				'USB/IPOD' => 'iPod/USB',
				'TV' => 'TV Audio',
				'TUNER' => 'Tuner',
				'NETHOME' => 'Online Music',
				'BT' => 'Bluetooth',
				'IRP' => 'Internet Radio',
				'CD' => 'CD',
				'PHONO' => 'Phono',
			),
			'5' => array(
				'SAT/CBL' => 'CBL/SAT',
				'DVD' => 'DVD/Blu-ray',
				'BD' => 'Blu-ray',
				'GAME' => 'Game',
				'AUX1' => 'AUX1',
				'AUX2' => 'AUX2',
				'MPLAY' => 'Media Player',
				'USB/IPOD' => 'iPod/USB',
				'TV' => 'TV Audio',
				'NETHOME' => 'Online Music',
				'BT' => 'Bluetooth',
				'IRP' => 'Internet Radio',
				'CD' => 'CD',
				'PHONO' => 'Phono',
			),
		);

		if ($this->getConfiguration('ip') != '') {
			$infos = $this->getAmpInfo();
			$model = $infos['ModelId'];
			if (isset($convert[$model])) {
				$model = $convert[$model];
			}
			if (isset($inputModel[$model])) {
				foreach ($inputModel[$model] as $key => $value) {
					$cmd = $this->getCmd(null, $key);
					if (!is_object($cmd)) {
						$cmd = new denonavrCmd();
						$cmd->setLogicalId($key);
						$cmd->setName($value);
						$cmd->setIsVisible(1);
					}
					$cmd->setType('action');
					$cmd->setSubType('other');
					$cmd->setEventOnly(1);
					$cmd->setEqLogic_id($this->getId());
					$cmd->save();
				}
			}
			$this->updateInfo();
		}

	}

	public function getAmpInfo() {
		$zone = '';
		if ($this->getConfiguration('zone', 'main') == 2) {
			$zone = '?ZoneName=ZONE2';
		}
		$request_http = new com_http('http://' . $this->getConfiguration('ip') . '/goform/formMainZone_MainZoneXml.xml' . $zone);
		$result = trim($request_http->exec());
		$xml = simplexml_load_string($result);
		$data = json_decode(json_encode(simplexml_load_string($result)), true);
		$data['VideoSelectLists'] = array();
		foreach ($xml->VideoSelectLists->value as $VideoSelectList) {
			$data['VideoSelectLists'][(string) $VideoSelectList["index"]] = (string) $VideoSelectList;
		}
		foreach ($data as $key => $value) {
			if (isset($value['value'])) {
				$data[$key] = $value['value'];
			}
		}
		return $data;
	}

	public function updateInfo() {
		if ($this->getConfiguration('ip') == '') {
			return;
		}
		try {
			$infos = $this->getAmpInfo();
		} catch (Exception $e) {
			return;
		}
		$cmd = $this->getCmd(null, 'power_state');
		if (is_object($cmd) && isset($infos['ZonePower'])) {
			$value = ($infos['ZonePower'] == 'OFF') ? 0 : 1;
			$value = $cmd->formatValue($value);
			if ($value != $cmd->execCmd(null, 2)) {
				$cmd->setCollectDate('');
				$cmd->event($value);
			}
		}

		$cmd = $this->getCmd(null, 'input');
		if (is_object($cmd) && isset($infos['InputFuncSelect'])) {
			$value = $cmd->formatValue($infos['InputFuncSelect']);
			if ($value != $cmd->execCmd(null, 2)) {
				$cmd->setCollectDate('');
				$cmd->event($value);
			}
		}

		$cmd = $this->getCmd(null, 'volume');
		if (is_object($cmd) && isset($infos['MasterVolume'])) {
			$value = $cmd->formatValue($infos['MasterVolume']);
			if ($value != $cmd->execCmd(null, 2)) {
				$cmd->setCollectDate('');
				$cmd->event($value);
			}
		}

		$cmd = $this->getCmd(null, 'sound_mode');
		if (is_object($cmd) && isset($infos['selectSurround'])) {
			$value = $cmd->formatValue($infos['selectSurround']);
			if ($value != $cmd->execCmd(null, 2)) {
				$cmd->setCollectDate('');
				$cmd->event($value);
			}
		}
	}

	/*     * **********************Getteur Setteur*************************** */
}

class denonavrCmd extends cmd {
	/*     * *************************Attributs****************************** */

	/*     * ***********************Methode static*************************** */

	/*     * *********************Methode d'instance************************* */

	public function execute($_options = array()) {
		$eqLogic = $this->getEqLogic();
		$zone = '';
		if ($eqLogic->getConfiguration('zone', 'main') == 2) {
			$zone = '&ZoneName=ZONE2';
		}
		if ($this->getLogicalId() == 'on') {
			$request_http = new com_http('http://' . $eqLogic->getConfiguration('ip') . '/MainZone/index.put.asp?cmd0=PutZone_OnOff%2FON' . $zone);
			$request_http->exec();
		} else if ($this->getLogicalId() == 'off') {
			$request_http = new com_http('http://' . $eqLogic->getConfiguration('ip') . '/MainZone/index.put.asp?cmd0=PutZone_OnOff%2FOFF' . $zone);
			$request_http->exec();
		} else if ($this->getLogicalId() == 'volume_p') {
			$request_http = new com_http('http://' . $eqLogic->getConfiguration('ip') . '/MainZone/index.put.asp?cmd0=PutMasterVolumeBtn%2F%3E' . $zone);
			$request_http->exec();
		} else if ($this->getLogicalId() == 'volume_m') {
			$request_http = new com_http('http://' . $eqLogic->getConfiguration('ip') . '/MainZone/index.put.asp?cmd0=PutMasterVolumeBtn%2F%3C' . $zone);
			$request_http->exec();
		} else if ($this->getLogicalId() == 'refresh') {

		} else if ($this->getLogicalId() == 'mute') {
			$request_http = new com_http('http://' . $eqLogic->getConfiguration('ip') . '/MainZone/index.put.asp?cmd0=PutVolumeMute/TOGGLE');
			$request_http->exec();
		} else {
			$request_http = new com_http('http://' . $eqLogic->getConfiguration('ip') . '/MainZone/index.put.asp?cmd0=PutZone_InputFunction%2F' . $this->getLogicalId() . $zone);
			$request_http->exec();
		}
		sleep(1);
		$eqLogic->updateInfo();
	}

	/*     * **********************Getteur Setteur*************************** */
}

?>

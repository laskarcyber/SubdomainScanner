<?php
namespace PUTS\SubdomainScanner;

class SubdomainScanner {
	protected $domain = null;

	protected $mh = null;
	protected $ch = array();

	protected $list = array();
	protected $permutation = false;

	protected $charset = array();
	protected $currentString = '';

	public function __construct($domain = null, $list = null) {
		if(!extension_loaded('curl')) {
			throw new \Exception(__METHOD__ . ': cURL must be installed.');
		}
		$this->mh = curl_multi_init();

		if($domain !== null) {
			$this->setDomain($domain);
		}
		if($list !== null) {
			$this->setList($list);
		}

		$this->charset['charset'] = array_merge(range('a', 'z'), range('A', 'Z'));
		$this->charset['charset'] = implode($this->charset['charset']);
		$this->charset['strlen'] = strlen($this->charset['charset']);
	}

	public function startScan() {
		$listCount = count($this->list);
		if($listCount == 0) {
			throw new \Exception(__METHOD__ . ': No definitions given.');
		} else if($this->domain === null) {
			throw new \Exception(__METHOD__ . ': Domain was not set.');
		} else {
			if($this->permutation == false) {
				for($i = 0; $i < $listCount; $i++) {
					$this->ch[$i] = curl_init($this->domainInfo['scheme'] . '://' . $this->list[$i] . '.' . $this->domain);
					curl_setopt_array($this->ch[$i], array(
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_NOBODY => true,
						CURLOPT_USERAGENT => 'Mozilla/5.0 (X11; Linux i686; rv:22.0) Gecko/20130226 Firefox/22.0',
						CURLOPT_TIMEOUT => 3,
						CURLOPT_CONNECTTIMEOUT => 5,
						CURLOPT_FOLLOWLOCATION => true
					));

					curl_multi_add_handle($this->mh, $this->ch[$i]);
				}

				$running = null;
				do {
					curl_multi_exec($this->mh, $running);
				} while($running > 0);

				$domainsFound = array();
				foreach($this->ch as $key => $handler) {
					if(curl_errno($handler) == 0) {
						if(curl_getinfo($handler, CURLINFO_HTTP_CODE) == 200) {
							echo 'Domain found: ' . $this->list[$key] . PHP_EOL;
							$domainsFound[] = $this->domainInfo['scheme'] . '://' . $str . '.' . $this->domain;
						}
					}
				}

				$domainsFoundCount = count($domainsFound);
				if($domainsFoundCount == 0) {
					echo 'No domains were found.' . PHP_EOL;
				} else if($domainsFoundCount == 1) {
					echo '1 domain was found.' . PHP_EOL;
				} else if($domainsFoundCount > 1) {
					echo $domainsFoundCount . ' domains found.' . PHP_EOL;
				}

				$newFile = implode(PHP_EOL, $domainsFound);
				$saveFile = strtolower(trim(fgets(STDIN)));
				if(substr($saveFile, 0, 1) == 'y') {
					file_put_contents('domains-found-' . $this->domain . '-'.time().'.txt', $newFile);
				} else {
					echo 'Ok.' . PHP_EOL;
				}
			} else {
				$domainsFound = array();

				for($i = 1; $i < 64; $i++) {
					$this->recurseString($i, 0, "");
					$str = $this->currentString;

					$ch = curl_init($this->domainInfo['scheme'] . '://' . $str . '.' . $this->domain);
					curl_setopt_array($ch, array(
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_NOBODY => true,
						CURLOPT_USERAGENT => 'Mozilla/5.0 (X11; Linux i686; rv:22.0) Gecko/20130226 Firefox/22.0',
						CURLOPT_TIMEOUT => 3,
						CURLOPT_CONNECTTIMEOUT => 5,
						CURLOPT_FOLLOWLOCATION => true
					));
					$data = curl_exec($ch);

					if(curl_errno($ch) == 0) {
						$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
						if($code == 200) {
							echo 'Domain found: ' . $this->domainInfo['scheme'] . '://' . $str . '.' . $this->domain . PHP_EOL;
							$domainsFound[] = $this->domainInfo['scheme'] . '://' . $str . '.' . $this->domain;
						} else {
							echo $this->domainInfo['scheme'] . '://' . $str . '.' . $this->domain . ': ' . $code . PHP_EOL;
						}
					} else {
						echo $this->domainInfo['scheme'] . '://' . $str . '.' . $this->domain . ': ' . curl_error($ch) . PHP_EOL;
					}
				}
			}
		}
	}

	private function recurseString($x, $y, $str) {
		for($i = 0; $i < $this->charset['strlen']; $i++) {
			if($y < $x - 1) {
				$this->recurseString($x, $y + 1, $str . $this->charset['charset'][$i]);
			}
			$this->currentString = $str . $this->charset['charset'][$i];
		}
		//return $str . $this->charset['charset'][$i];
	}

	public function setDomain($domain) {
		$domainInfo = parse_url($domain);
		if(!isset($domainInfo['host'])) {
			throw new \Exception(__METHOD__ . ': Invalid URL.');
		}
		if(!isset($domainInfo['scheme'])) {
			$domainInfo['scheme'] = 'http';
		}

		$this->domainInfo = $domainInfo;
		$this->domain = $this->domainInfo['host'];

		return true;
	}

	public function getDomain() {
		return $this->domain;
	}

	public function setList($file) {
		if(is_file($file)) {
			$this->list = file($file);
			foreach($this->list as $key => $value) {
				$this->list[$key] = trim($value);
			}
		} else {
			throw new \Exception(__METHOD__ . ': "' . $file . '" does not exist or you do not have permission to use it.');
		}
	}

	public function getList() {
		return $this->list;
	}

	public function setPermutation($bool) {
		$this->permutation = (bool) $bool;
	}

	public function getPermutation() {
		return $this->permutation;
	}
}
?>

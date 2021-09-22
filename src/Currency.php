<?php

namespace Currency;

class Currency
{

	private $currencies = [];

	public function __construct($db)
	{
		
		if(!($db instanceof \_class\dbSimple)){
			return false;
		}

		$this->currencies = [];

		$result = $db->select("SELECT * FROM ccy_rates");

		while ($row = $db->fetch_assoc($result)) {
			$this->currencies[$row['CCY']] = $row;
		}
	}

	public function getCurrencies()
	{
		return $this->currencies;
	}

	public function getCurrency($currency)
	{
		return isset($this->currencies[$currency]) ? $this->currencies[$currency] : false;
	}

	public function convert($value, $to, $from)
	{
		if (isset($this->currencies[$from])) {
			$from = $this->currencies[$from]['VALUE'];
		} else {
			$from = 1;
		}

		if (isset($this->currencies[$to])) {
			$to = $this->currencies[$to]['VALUE'];
		} else {
			$to = 1;
		}

		return $value * ($from / $to);
	}

	public function setValue($ccy, $value)
	{
		if(isset($this->currencies[$ccy])){
			$this->currencies[$ccy]['VALUE'] = $value;
			return true;
		}
		return false;
	}

	public function round($value, $key)
	{
		$current = $this->getCurrency($key);
		$decimal_place = $current['DECIMAL_PLACE'];
		$amount = round($value, (int)$decimal_place);
		return $amount;
	}

	public function format($value, $key)
	{
        $current = $this->getCurrency($key);

		$code = $current['CCY'];
		$title = $current['TYTLE'];
		$symbol = $current['SYMBOL'];
		// $symbol_right = $current['symbol_right'];
		$decimal_place = $current['DECIMAL_PLACE'];

		$amount = round($value, (int)$decimal_place);

		$string = '';
		$string .= number_format($amount, (int)$decimal_place, '.', ',');
		$string .= ' ' . $symbol ?: $code;

		return $string;
	}

}

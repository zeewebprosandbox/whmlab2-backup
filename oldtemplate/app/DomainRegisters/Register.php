<?php

namespace App\DomainRegisters;

class Register{

	public $tld;
	public $alias;
	public $domain;
	public $request;
	public $command;
	public $singleSearch = false;

	public function __construct($alias){		
		$this->alias = $alias;
	}

	public function run(){
		$getFile =  $this->getApiMethods($this->alias);

		$object = new $getFile($this->domain);
		$command = $this->command;

		$object->request = $this->request;
		$object->domain = $this->domain;
		$object->singleSearch = $this->singleSearch;

		if($this->tld){
			$object->tld = $this->tld;	
		}

		return $object->$command();
	}

	protected function getApiMethods($alias){

		$methods = [
			'Namecheap' => Namecheap::class,
			'NameSilo' => NameSilo::class,
			'Resell' => Resell::class,
			'ResellerClub' => ResellerClub::class,
			'NetEarthOne' => NetEarthOne::class,
			'LogicBoxes' => LogicBoxes::class,
			'Porkbun' => Porkbun::class,
		];

		// Case-insensitive lookup fallback to Namecheap to prevent Undefined array key errors
		foreach ($methods as $key => $class) {
			if (strcasecmp($key, (string)$alias) === 0) {
				return $class;
			}
		}

		return Namecheap::class;
	}

}

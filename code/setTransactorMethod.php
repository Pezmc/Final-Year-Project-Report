/**
 * Checks for an existing transactor, creating a new one if not
 * @var $name The raw transactor string
 */
public function setTransactor($name) {
	// Trim off extra whitespace
	$name = trim($name);
	$this->setRawTransactor($name);
	
	// Tidy the name
	$name = preg_replace('/\s_/', ' ', $name);
	$name = preg_replace('/\s+/', ' ', $name);
	$name = preg_replace('#[^\w\s/\\-.]#', '', $name);
	$name = trim($name, '-./\ ');
	
	// If last 2+ characters are numbers
	if(preg_match('#(?:[A-z]|\s){2,}(?:[\s]+)?(\d{2,})$#', $name, $matches, PREG_OFFSET_CAPTURE)) {
		if($matches[1][0]) {
			$this->setNumber($matches[1][0]);
			$name = trim(substr($name, 0, $matches[1][1]));
		}
	}

	// Existing global transactor mapping?
	$globalTransactorMap = GlobalTransactorMappingQuery::findOneByTransactorName($name);
	$foundGlobalMap = (boolean) $globalTransactorMap;
	if($foundGlobalMap) {
		$this->setGlobalTransactorMapping($globalTransactorMap);
	}
	
	// Create a new user mapping and persist it
	$currentUser = $this->getUserSession()->getUser();
	$userMap = UserTransactorMappingQuery::findOneByTransactorNameAndUser($name, $currentUser);
		
	if(empty($userMap) && !empty($name) && !$foundGlobalMap) {	
		$userMap = new UserTransactorMapping();
		$userMap->setName($name);
		$userMap->setUser($currentUser);
		$userMap->save();
	}
	
	if($userMap) $this->setUserTransactorMapping($userMap);
}
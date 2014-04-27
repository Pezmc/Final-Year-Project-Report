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
	$matches = array();
	if(preg_match('#(?:[A-z]|\s){2,}(?:[\s]+)?(\d{2,})$#', $name, $matches, PREG_OFFSET_CAPTURE)) {
		if($matches[1][0]) {
			$this->setNumber($matches[1][0]);
			$name = trim(substr($name, 0, $matches[1][1]));
		}
	}

	// Existing global transactor mapping?
	$globalTransactorMapping = GlobalTransactorMappingQuery::findOneByTransactorName($name);
	$foundGlobalMapping = (boolean) $globalTransactorMapping;
	if($foundGlobalMapping) {
		$this->setGlobalTransactorMapping($globalTransactorMapping);
	}
	
	// Create a new user mapping and persist it
	$currentUser = $this->getUserSession()->getUser();
	$userMapping = UserTransactorMappingQuery::findOneByTransactorNameAndUser($name, $currentUser);
		
	if(empty($userMapping) && !empty($name) && !$foundGlobalMapping) {	
		$userMapping = new UserTransactorMapping();
		$userMapping->setName($name);
		$userMapping->setUser($currentUser);
		$userMapping->save();
	}
	
	if($userMapping) $this->setUserTransactorMapping($userMapping);
}
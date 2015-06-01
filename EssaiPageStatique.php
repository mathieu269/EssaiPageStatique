<?php

class EssaiPageStatique extends plxPlugin {
	
	
	public function __construct($default_lang) {
		
		parent::__construct($default_lang);
		
		$nomPlugin = __CLASS__;
		$this->baseUrlPageVirtuelle = "$nomPlugin/pageVirtuelle";
		
		$this->titrePageVirtuelle = "Un titre avec des \" et des '";
		
		
		// gestion de la page virtuelle en fonction de l'URL
		$this->addHook("plxMotorPreChauffageBegin", "plxMotorPreChauffageBegin");
		// contenu de la page virtuelle
		$this->addHook("plxShowStaticContentBegin", "plxShowStaticContentBegin");
		
		// ajout de la page virtuelle dans le menu
		$this->addHook("plxShowStaticListEnd", "plxShowStaticListEnd");
		
	}
	
	
	public function plxMotorPreChauffageBegin() {
		
		$this->plxMotor = plxMotor::getInstance();
		
		if ($this->baseUrlPageVirtuelle === $this->plxMotor->get) {
			
			$this->plxMotor->mode = "static";
			$this->plxMotor->template = "static.php"; // template provenant du thème
			
			$nomPlugin = __CLASS__;
			$this->plxMotor->cible = $nomPlugin;
			
			// titre qui apparait dans la page
			$this->plxMotor->aStats[$this->plxMotor->cible] = array(
				"name" => $this->titrePageVirtuelle,
				"active" => 1,
				"menu" => "non",
				"url" => "",
				"title_htmltag" => "",
			);
			
			echo "<?php return TRUE;?>";
		}
	}
	
	
	public function plxShowStaticContentBegin() {
		
		if ($this->baseUrlPageVirtuelle !== $this->plxMotor->get) {
			return;
		}
		
		?>
			<p>
				Dans <?php echo __CLASS__;?>,
				 il est <?php echo date("H \h i");?>
			</p>
			
			<?php echo "<?php return TRUE;?>";?>
			
		<?php
		
	}
	
	
	public function plxShowStaticListEnd() {
		
		$positionMenu = 3;
		
		// préparation du menu
		
		$pageSelectionne = (
				("static" === $this->plxMotor->mode)
			&&	($this->baseUrlPageVirtuelle === $this->plxMotor->get)
		);
		$classeCss = $pageSelectionne ? "active" : "noactive";
		
		$lien = $this->plxMotor->urlRewrite("index.php?{$this->baseUrlPageVirtuelle}");
		
		$titreProtege = str_replace("\\\"", "\"", addslashes($this->titrePageVirtuelle));
		
		echo "<?php";
		echo "	array_splice(\$menus, $positionMenu, 0";
		echo "		, '<li><a class=\"static $classeCss\" href=\"$lien\" title=\"' . htmlspecialchars('$titreProtege') . '\">$titreProtege</a></li>'";
		echo "	);";
		echo "?>";
		
	}
	
}

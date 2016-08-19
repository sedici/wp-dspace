<?php
/**
 * Plugin Name: Sedici-Plugin
 * Plugin URI: http://sedici.unlp.edu.ar/
 * Description: This plugin connects the repository SEDICI in wordpress, with the purpose of showing the publications of authors or institutions
 * Version: 1.0
 * Author: SEDICI - Paula Salamone Lacunza
 * Author URI: http://sedici.unlp.edu.ar/
 * Copyright (c) 2015 SEDICI UNLP, http://sedici.unlp.edu.ar
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 */
?>
<?php
class Filter {
	protected $subtypes;
	protected $thesis;
	public function Filter(){
		$this->subtypes =  array(
                'article' => 'Articulo',
                'book' => 'Libro',
                'working_paper' =>  "Documento de trabajo",
                'technical_report' => "Informe tecnico",
                'conference_object' => "Objeto de conferencia",
                'revision' => "Revision",
                'work_specialization' => "Trabajo de especializacion",
                'preprint' => 'Preprint'   
            );
		$this->thesis= array ('master_thesis'=>  "Tesis de maestria",
                                      'phD_thesis'=>"Tesis de doctorado",
                                      'licentiate_thesis'=>"Tesis de grado"
                 );
	}
	public function subtypes() {
                $all_subtypes = array_merge($this->vectorSubtypes(),$this->vectorTesis());
                asort($all_subtypes);
                return $all_subtypes;
	}
	public function vectorTesis() {
		return $this->thesis;
	}
        public function vectorSubtypes (){
            return $this->subtypes;
        }
}
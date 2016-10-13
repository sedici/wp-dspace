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
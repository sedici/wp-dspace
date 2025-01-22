<?php
namespace Wp_dspace\Util\Query;

use Wp_dspace\Util\Query\httpQuery;

abstract class queryMaker
{
    protected $model;
    protected $order;
    protected $http_handler;

    public function __construct()
    {
        $this->model = new \Wp_dspace\Model\SimpleXMLModel();
        $this->order = new \Wp_dspace\Util\XmlOrder();
        $this->http_handler = new httpQuery();
    }

    
    #Builds the Query String. Returns the query (String)

    public abstract function buildQuery($handle, $author, $keywords, $subject, $degree, $max_results,$configuration,$all = "", $subtypes_selected= "");
    
    #Executes the Query. Returns an array of Wrappers (jsonWrapper or xmlWrapper)

    public abstract function getPublications($all, $queryStandar, $cache, $subtypes_selected, $max_results);
    

    /**
     * Obtiene el modelo asociado a la instancia.
     *
     * @return SimpleXMLModel
     */
    public function get_model()
    {
        return $this->model;
    }

    /**
     * Configura un valor de comparación en la instancia de XmlOrder.
     *
     * @param mixed $value El valor de comparación que se establecerá.
     * @return void
     */
    public function setCmp($value)
    {
        $this->order->setCmp($value);
    }
    
    /**
     * Divide una cadena de entrada en un array utilizando el delimitador ';'.
     *
     * @param string $imput La cadena de entrada que se desea dividir.
     * @return array Un array de cadenas resultante de dividir la entrada.
     */
    public function splitImputs($imput)
    {
        return explode(';', $imput);
    }

    /**
     * Agrupa varios atributos en un array asociativo.
     *
     * @param string $description Una descripción del recurso.
     * @param string $date La fecha asociada al recurso.
     * @param bool $show_author Indica si se debe mostrar el autor.
     * @param int $maxlenght La longitud máxima permitida para los textos.
     * @param bool $show_subtypes Indica si se deben mostrar los subtipos.
     * @param bool $share Indica si el recurso puede ser compartido.
     * @param bool $show_videos (Opcional) Indica si se deben incluir videos. Por defecto es `false`.
     * @param int|null $max_results (Opcional) El número máximo de resultados permitidos. Por defecto es `null`.
     * @return array Un array asociativo con los atributos agrupados.
     */
    function group_attributes($description, $date, $show_author, $maxlenght, $show_subtypes, $share, $show_videos = false, $max_results = null)
    {
        return (array(
            'description' => $description,
            'show_author' => $show_author,
            'max_lenght' => $maxlenght,
            'show_subtypes' => $show_subtypes,
            'share' => $share,
            'date' => $date,
            'show_videos' => $show_videos,
            'max_results' => $max_results,


        ));
    }
    
    
}

?>
# wp-dspace

## Plugin de Wordpress para conectar con respositorios DSpace

Este plugin de Wordpress permite a cualquier sitio web hecho en WP recuperar contenidos alojados en repositorios DSpace y mostrarlos
dentro de Widgets o como shortcodes dentro de páginas y posts. La recuperación de los contenidos puede realizarse mediante una expresión de búsqueda,
que se transforma a OpenSearch, o a partir de una colección particular del repositorio

## Instalación

```bash
cd workspace
git clone https://github.com/sedici/wp-dspace.git wp-dspae
```

(se trabaja sobre archivos)

```bash
git add archivo1 archivo2 ... archivoN
git commit -m 'comentarios sobre el commit'
```

(se sigue trabajando con otros archivos)

```bash
git add <lista de archivos cambiados>
git commit -m 'otros comentarios sobre este commit'
```

(una vez que ya tenemos código ESTABLE y listo para compartir)

```bash 
git push origin master
```


Si queremos actualizar nuestro repositorio local con datos que comiteó otro usuario a Github, ejecutamos:

```bash 
git pull origin master
```

## Configuración Shortcode

Shortcode para handle:
[get_publications_by_handle context="handle"]

Ejemplo de todas las publicaciones de PREBI-SEDICI:
[get_publications_by_handle context="10915/25293"  all=true]

Shortcode para autores:
[get_publications_by_author context="nombre autor"]

Ejemplo: Mostrar solo un articulo (max_results=1) con descripción limitada a 200 caracteres del autor  De Giusti, Marisa Raquel
[get_publications_by_author context=" De Giusti, Marisa Raquel"  article=true max_results=1 description=true max_lenght=200]


Opciones y sus valores por defecto:

'all' => false, // Poner en true para mostrar todos los documentos sin importar los subtipos

'max_results' => 0, // Limitar el numero a mostrar de documentos por subtipos

'max_lenght' => 0, // Poner un numero que limite la cantidad de caracteres a mostrar de la descripción

'description' => false, // Poner en true para mostrar un resumen del documento

'date' => false, // Poner en true para mostrar la fecha del documento

'show_author' => false, // Poner en true para mostrar los autores del documento


Subtipos de documentos:

'article'(articulos) => false

'preprint (preprint)' => false

'book' (libro) => false

'working_paper' (documento de trabajo) => false

'technical_report' (informe tecnico)=> false

'conference_object' (objeto de conferencia) => false

'revision' (revision) => false

'work_specialization' (trabajo de especializacion) => false

'thesis' (tesis de grado/maestria/doctorado) => false

'learning_object' (objeto de aprendizaje)=>false


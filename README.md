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

El shortcode para su correcto funcionamiento necesita alguno de los criterios de búsqueda: hable - author - keywords.
Y si se quieren las últimas publicaciones indistintamente del subtipo de documento (all=true), o si se desea filtrar por algun criterio (article=true).

[get_publications handle="Un/handle" author"Autor1 ; Autor2" keywords="Palabras ; claves" ]

Por ejemplo: Las publicaciones de la colección 10915/25293 sin importar el subtipo de documento.
[get_publications handle=10915/25293 all=true max_results=15 ]

Se puede acotar aún más los resultados, especificando uno/varios autores o palabras claves separados por ";" sin espacios en blanco entre nombres.
Ejemplo:
[get_publications handle=10915/25293 author="De Giusti, Marisa Raquel;Gonzalo Luján" keywords=" DSpace ; CMS" thesis=true  article=true description=true ]

Opciones y sus valores por defecto:

'all' => false, // Poner en true para mostrar todos los documentos sin importar los subtipos.

'max_results' => 0, // Limitar el numero a mostrar de documentos por subtipos, como máximo 100.

'max_lenght' => null, // Poner un numero que limite la cantidad de caracteres a mostrar de la descripción.

'description' => false, // Poner en true para mostrar un resumen de la publicación.

'date' => false, // Poner en true para mostrar la fecha de la publicación.

'show_author' => false, // Poner en true para mostrar los autores de la publicación.


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


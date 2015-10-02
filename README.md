# wp-dspace

## Plugin de Worespress para conectar con respositorios DSpace

Este plugin de Wordpress permite a cualquier sitio web hecho en WP recuperar contenidos alojados en repositorios DSpace y mostrarlos
dentro de Widgets o como shortcodes dentro de páginas y posts. La recuperación de los contenidos puede realizarse mediante una expresión de búsqueda,
que se transforma a OpenSearch, o a partir de una colección particular del repositorio

## Instalación

mkdir plugin
cd plugin
git clone https://github.com/sedici/wp-dspace.git .

(se trabaja sobre archivos)
git add <lista de archivos cambiados>
git commit -m 'comentarios sobre el commit'

(se sigue trabajando con otros archivos)
git add <lista de archivos cambiados>
git commit -m 'otros comentarios sobre este commit'

(una vez que ya tenemos código ESTABLE y listo para compartir)
git push origin master


Si queremos actualizar nuestro repositorio local con datos que comiteó otro usuario a Github, ejecutamos:

git pull origin master
